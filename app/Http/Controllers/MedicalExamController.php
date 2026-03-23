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
     * MÉTODOS DE CONSULTA Y NAVEGACIÓN (SnakeDEV Engine)
     */

    /**
     * Muestra el historial completo de evaluaciones (Vista Admin/Auditoría)
     * Corregido: Variable $exams cambiada a $completedExams para tu vista.
     */
    public function history(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');

        // Cambiamos el nombre de la variable de $exams a $completedExams
        // para que coincida con {{ $completedExams->total() }} de tu vista historial
        $completedExams = MedicalExam::with(['student', 'results'])
            ->when($search, function ($query, $search) {
                $query->whereHas('student', function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                      ->orWhere('document_number', 'LIKE', "%{$search}%");
                });
            })
            ->when($status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('medical_exams.history', compact('completedExams', 'search', 'status'));
    }

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

        $pendingExams = MedicalExam::with(['student', 'results'])
            ->where('status', '!=', 'completado')
            ->where(function ($query) use ($userArea, $userAreaSlug) {
                $query->whereJsonContains('requested_areas', $userAreaSlug)
                      ->orWhereJsonContains('requested_areas', $userArea);
            })
            ->whereDoesntHave('results', function($q) use ($userAreaSlug) {
                $q->where('area', $userAreaSlug);
            })
            ->latest()
            ->get();

        return view('medical_exams.index', compact('pendingExams', 'userArea'));
    }

    /**
     * INICIA UN NUEVO CIRCUITO MÉDICO
     */
    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
        ]);

        try {
            DB::beginTransaction();

            $areasRequeridas = [
                'valoracion_medica',
                'odontologia',
                'psicologia',
                'fisioterapia'
            ];

            $exam = MedicalExam::create([
                'student_id'      => $request->student_id,
                'status'          => 'en_proceso',
                'requested_areas' => $areasRequeridas,
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
     * Corregido: Variable $medical_exam pasada como 'exam' para odontologia.blade.php
     */
    public function evaluate(MedicalExam $medical_exam)
    {
        $userArea = $this->getAreaSlug(Auth::user()->role->name);

        if (!collect($medical_exam->requested_areas)->contains($userArea) && Auth::user()->role->name !== 'Administrador') {
            return redirect()->route('medical_exams.index')->with('error', 'Tu área no está asignada a este examen.');
        }

        if (!view()->exists("medical_exams.evaluations.{$userArea}")) {
            return back()->with('error', "No se encontró el formulario técnico para: {$userArea}");
        }

        $medical_exam->load('student');

        // Cambiamos la clave a 'exam' para que {{ $exam->student->name }} funcione en Odontología
        return view('medical_exams.evaluate', [
            'exam' => $medical_exam,
            'userArea'     => $userArea
        ]);
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

            $evaluationData = $request->except(['_token', '_method', 'observations', 'notes', 'odontograma_imagen']);

            if ($userArea === 'odontologia' && $request->filled('odontograma_imagen')) {
                $path = $this->saveOdontogramaImage($request->odontograma_imagen, $medical_exam->id);
                if ($path) $evaluationData['odontograma_path'] = $path;
            }

            $medical_exam->results()->updateOrCreate(
                ['area' => $userArea],
                [
                    'user_id' => Auth::id(),
                    'data'    => $evaluationData,
                    'notes'   => $request->notes ?? $request->observations ?? 'Evaluación realizada correctamente.',
                ]
            );

            $medical_exam->load('results');

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
                $type = strtolower($type[1]);

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
