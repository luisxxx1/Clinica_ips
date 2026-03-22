<?php

namespace App\Http\Controllers;

use App\Models\{MedicalExam, MedicalResult};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, DB, Storage, Log};
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;

class MedicalExamController extends Controller
{
    /**
     * Normaliza los nombres de áreas a slugs consistentes.
     */
    private function getAreaSlug($roleName)
    {
        $slug = Str::slug($roleName, '_');
        $medicinaVariantes = ['medicina_general', 'medico', 'medica', 'medicina', 'valoracion_medica'];

        return in_array($slug, $medicinaVariantes) ? 'valoracion_medica' : $slug;
    }

    /**
     * Muestra la lista de exámenes pendientes según el área del usuario.
     */
    public function index()
    {
        $userArea = Auth::user()->role->name ?? 'Invitado';
        $userAreaSlug = $this->getAreaSlug($userArea);

        // Optimizamos la consulta: Solo traemos lo que el especialista actual debe evaluar
        $pendingExams = MedicalExam::with(['student', 'results'])
            ->where('status', '!=', 'completado')
            ->where(function ($query) use ($userArea, $userAreaSlug) {
                $query->whereJsonContains('requested_areas', $userAreaSlug)
                      ->orWhereJsonContains('requested_areas', $userArea);
            })
            // Evitamos mostrar si este especialista ya guardó su parte (opcional, según flujo de la IPS)
            ->whereDoesntHave('results', function($q) use ($userAreaSlug) {
                $q->where('area', $userAreaSlug);
            })
            ->latest()
            ->get();

        return view('medical_exams.index', compact('pendingExams', 'userArea'));
    }
    /**
     * INICIA UN NUEVO CIRCUITO MÉDICO
     * Este es el método que falta y causa el error 500.
     */
    public function store(Request $request)
    {
        // 1. Validamos que el estudiante exista
        $request->validate([
            'student_id' => 'required|exists:students,id',
        ]);

        try {
            DB::beginTransaction();

            // 2. Definimos las áreas que deben evaluar al estudiante.
            // Puedes personalizar esta lista según lo que necesite la IPS.
            $areasRequeridas = [
                'valoracion_medica',
                'odontologia',
                'psicologia',
                'fisioterapia'
            ];

            // 3. Creamos el examen médico
            $exam = MedicalExam::create([
                'student_id'      => $request->student_id,
                'status'          => 'en_proceso', // Inicia inmediatamente
                'requested_areas' => $areasRequeridas, // Se guarda como JSON
                'created_by'      => Auth::id(),
            ]);

            DB::commit();

            return redirect()->route('medical_exams.index')
                ->with('success', 'Circuito médico iniciado correctamente para el estudiante.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'No se pudo iniciar la evaluación: ' . $e->getMessage());
        }
    }

    /**
     * Formulario de evaluación dinámica.
     */
    public function evaluate(MedicalExam $medical_exam)
    {
        $userArea = $this->getAreaSlug(Auth::user()->role->name);

        // Seguridad: Verificar si el examen realmente requiere esta área
        if (!collect($medical_exam->requested_areas)->contains($userArea) && Auth::user()->role->name !== 'Administrador') {
            return redirect()->route('medical_exams.index')->with('error', 'Tu área no está asignada a este examen.');
        }

        // Verificamos si existe la vista específica para el área
        if (!view()->exists("medical_exams.evaluations.{$userArea}")) {
            return back()->with('error', "No se encontró el formulario técnico para: {$userArea}");
        }

        return view('medical_exams.evaluate', compact('medical_exam', 'userArea'));
    }

    /**
     * Guarda la valoración y verifica el estado del circuito.
     */
    public function storeEvaluation(Request $request, MedicalExam $medical_exam)
    {
        $request->validate([
            'notes' => 'nullable|string|max:1000',
            'observations' => 'nullable|string|max:1000',
        ]);

        $userArea = $this->getAreaSlug(Auth::user()->role->name);

        try {
            DB::beginTransaction();

            // Limpiamos los datos del request para quedarnos solo con los campos técnicos del formulario
            $evaluationData = $request->except(['_token', '_method', 'observations', 'notes', 'odontograma_imagen']);

            // Manejo de Odontograma (Imagen Base64)
            if ($userArea === 'odontologia' && $request->filled('odontograma_imagen')) {
                $path = $this->saveOdontogramaImage($request->odontograma_imagen, $medical_exam->id);
                if ($path) $evaluationData['odontograma_path'] = $path;
            }

            // Guardado o Actualización de resultados
            $medical_exam->results()->updateOrCreate(
                ['area' => $userArea],
                [
                    'user_id' => Auth::id(),
                    'data'    => $evaluationData,
                    'notes'   => $request->notes ?? $request->observations ?? 'Evaluación realizada correctamente.',
                ]
            );

            // Verificación de Cierre de Circuito
            $medical_exam->load('results');
            
            // Lógica: Si el número de resultados coincide con las áreas solicitadas, completamos.
            $requestedCount = count($medical_exam->requested_areas ?? []);
            $resultsCount = $medical_exam->results->count();

            if ($resultsCount >= $requestedCount) {
                $medical_exam->update(['status' => 'completado']);
                $msg = "Circuito médico completado con éxito.";
            } else {
                $medical_exam->update(['status' => 'en_proceso']);
                $msg = "Valoración de {$userArea} guardada. Faltan áreas por evaluar.";
            }

            DB::commit();
            return redirect()->route('medical_exams.index')->with('success', $msg);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error en SnakeDEV Evaluation: " . $e->getMessage());
            return back()->withInput()->with('error', "Error procesando la evaluación médica.");
        }
    }

    /**
     * Generación de PDF (Stream).
     */
    public function report(MedicalExam $medical_exam)
    {
        $medical_exam->load(['student', 'results.specialist']);
        
        // Verificamos que tenga resultados para imprimir
        if ($medical_exam->results->isEmpty()) {
            return back()->with('error', 'El examen no tiene valoraciones registradas para generar el reporte.');
        }

        $pdf = Pdf::loadView('medical_exams.reports.full_history', [
            'exam' => $medical_exam,
            'title' => 'HISTORIA CLÍNICA INTEGRAL',
            'date' => now()->format('d/m/Y h:i A'),
        ])->setPaper('letter', 'portrait');

        return $pdf->stream("HC_{$medical_exam->student->document_number}.pdf");
    }

    /**
     * Almacenamiento privado de odontogramas.
     */
    private function saveOdontogramaImage($base64String, $examId)
    {
        try {
            if (preg_match('/^data:image\/(\w+);base64,/', $base64String, $type)) {
                $image = substr($base64String, strpos($base64String, ',') + 1);
                $type = strtolower($type[1]); // png, jpg, etc

                $image = base64_decode($image);
                $fileName = "odontogramas/exam_{$examId}_" . now()->timestamp . ".{$type}";
                
                Storage::disk('public')->put($fileName, $image);
                return $fileName;
            }
            return null;
        } catch (\Exception $e) {
            Log::error("Error guardando Odontograma: " . $e->getMessage());
            return null;
        }
    }
}