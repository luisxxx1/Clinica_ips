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
     * Las 6 áreas del circuito médico.
     * Deben coincidir exactamente con los nombres de las vistas en:
     * resources/views/medical_exams/evaluations/{area}.blade.php
     */
    private array $areasDelCircuito = [
        'valoracion_medica',
        'odontologia',
        'optometria',
        'audiometria',
        'fonoaudiologia',
        'psicologia',
    ];

    // -------------------------------------------------------------------------
    // MÉTODOS DE CONSULTA Y NAVEGACIÓN
    // -------------------------------------------------------------------------

    /**
     * Muestra el historial completo de evaluaciones (Vista Admin/Auditoría).
     */
    public function history(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');

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
     * Vista del ADMINISTRADOR:
     * Muestra solo los exámenes donde los 6 médicos ya evaluaron al estudiante,
     * con todas las valoraciones cargadas para verlas completas.
     */
    public function dashboard(Request $request)
    {
        $search = $request->input('search');

        $completedExams = MedicalExam::with(['student', 'results.specialist'])
            ->where('status', 'completado')
            ->when($search, function ($query, $search) {
                $query->whereHas('student', function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                      ->orWhere('document_number', 'LIKE', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('medical_exams.dashboard', compact('completedExams', 'search'));
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
     * Muestra la lista de exámenes pendientes según el área del médico autenticado.
     */
    public function index()
    {
        $userArea     = Auth::user()->role->name ?? 'Invitado';
        $userAreaSlug = $this->getAreaSlug($userArea);

        $pendingExams = MedicalExam::with(['student', 'results'])
            ->where('status', '!=', 'completado')
            ->where(function ($query) use ($userArea, $userAreaSlug) {
                $query->whereJsonContains('requested_areas', $userAreaSlug)
                      ->orWhereJsonContains('requested_areas', $userArea);
            })
            ->whereDoesntHave('results', function ($q) use ($userAreaSlug) {
                $q->where('area', $userAreaSlug);
            })
            ->latest()
            ->get();

        return view('medical_exams.index', compact('pendingExams', 'userArea'));
    }

    // -------------------------------------------------------------------------
    // INICIA UN NUEVO CIRCUITO MÉDICO
    // -------------------------------------------------------------------------

    /**
     * Crea el examen y asigna las 6 áreas del circuito médico.
     */
    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
        ]);

        try {
            DB::beginTransaction();

            $exam = MedicalExam::create([
                'student_id'      => $request->student_id,
                'status'          => 'en_proceso',
                'requested_areas' => $this->areasDelCircuito,
                'user_id'         => Auth::id(), // FIX: Cambiado created_by por user_id para coincidir con la DB
            ]);

            DB::commit();

            return redirect()->route('medical_exams.index')
                ->with('success', 'Circuito médico iniciado correctamente para el estudiante.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'No se pudo iniciar la evaluación: ' . $e->getMessage());
        }
    }

    // -------------------------------------------------------------------------
    // EVALUACIÓN POR ÁREA
    // -------------------------------------------------------------------------

    /**
     * Muestra el formulario de evaluación según el área del médico autenticado.
     */
    public function evaluate(MedicalExam $medical_exam)
    {
        $userArea = $this->getAreaSlug(Auth::user()->role->name);

        if (
            !collect($medical_exam->requested_areas)->contains($userArea)
            && Auth::user()->role->name !== 'Administrador'
        ) {
            return redirect()->route('medical_exams.index')
                ->with('error', 'Tu área no está asignada a este examen.');
        }

        if (!view()->exists("medical_exams.evaluations.{$userArea}")) {
            return back()->with('error', "No se encontró el formulario técnico para: {$userArea}");
        }

        $medical_exam->load('student');

        return view('medical_exams.evaluate', [
            'exam'     => $medical_exam,
            'userArea' => $userArea,
        ]);
    }

    /**
     * Guarda la valoración del médico y verifica si el circuito ya está completo.
     */
    public function storeEvaluation(Request $request, MedicalExam $medical_exam)
    {
        $request->validate([
            'notes'        => 'nullable|string|max:1000',
            'observations' => 'nullable|string|max:1000',
        ]);

        $userArea = $this->getAreaSlug(Auth::user()->role->name);

        try {
            DB::beginTransaction();

            $evaluationData = $request->except(['_token', '_method', 'observations', 'notes', 'odontograma_imagen']);

            // Guardar imagen del odontograma si aplica
            if ($userArea === 'odontologia' && $request->filled('odontograma_imagen')) {
                $path = $this->saveOdontogramaImage($request->odontograma_imagen, $medical_exam->id);
                if ($path) {
                    $evaluationData['odontograma_path'] = $path;
                }
            }

            // Guardar o actualizar la valoración de esta área
            $medical_exam->results()->updateOrCreate(
                ['area' => $userArea],
                [
                    'user_id' => Auth::id(),
                    'data'    => $evaluationData,
                    'notes'   => $request->notes ?? $request->observations ?? 'Evaluación realizada correctamente.',
                ]
            );

            // Recargar resultados y verificar si el circuito está completo
            $medical_exam->load('results');

            $areasRequeridas  = collect($medical_exam->requested_areas ?? []);
            $areasCompletadas = $medical_exam->results->pluck('area');
            $areasFaltantes   = $areasRequeridas->diff($areasCompletadas);

            if ($areasFaltantes->isEmpty()) {
                // ✅ Los 6 médicos ya evaluaron → circuito completo
                $medical_exam->update(['status' => 'completado']);
                $msg = '¡Circuito médico completado! Todas las áreas han evaluado al estudiante.';
            } else {
                // 🔄 Aún faltan médicos por evaluar
                $medical_exam->update(['status' => 'en_proceso']);
                $restantes = $areasFaltantes->implode(', ');
                $msg = "Valoración de {$userArea} guardada. Áreas pendientes: {$restantes}.";
            }

            DB::commit();

            return redirect()->route('medical_exams.index')->with('success', $msg);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error en Evaluation: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Error procesando la evaluación médica.');
        }
    }

    // -------------------------------------------------------------------------
    // REPORTE PDF
    // -------------------------------------------------------------------------

    /**
     * Genera el PDF con el historial clínico completo del estudiante.
     */
    public function report(MedicalExam $medical_exam)
    {
        $medical_exam->load(['student', 'results.specialist']);

        if ($medical_exam->results->isEmpty()) {
            return back()->with('error', 'El examen no tiene valoraciones registradas para generar el reporte.');
        }

        // ✅ CORRECCIÓN: Se añadieron opciones para permitir la carga de imágenes locales y externas
        $pdf = Pdf::loadView('medical_exams.reports.full_history', [
            'exam'  => $medical_exam,
            'title' => 'HISTORIA CLÍNICA INTEGRAL',
            'date'  => now()->format('d/m/Y h:i A'),
        ])
        ->setPaper('letter', 'portrait')
        ->setOption('isRemoteEnabled', true)
        ->setOption('isHtml5ParserEnabled', true);

        return $pdf->stream("HC_{$medical_exam->student->document_number}.pdf");
    }

    // -------------------------------------------------------------------------
    // MÉTODOS PRIVADOS
    // -------------------------------------------------------------------------

    /**
     * Guarda la imagen del odontograma en almacenamiento público.
     */
    private function saveOdontogramaImage($base64String, $examId)
    {
        try {
            if (preg_match('/^data:image\/(\w+);base64,/', $base64String, $type)) {
                $image    = substr($base64String, strpos($base64String, ',') + 1);
                $type     = strtolower($type[1]);
                $image    = base64_decode($image);
                $fileName = "odontogramas/exam_{$examId}_" . now()->timestamp . ".{$type}";

                Storage::disk('public')->put($fileName, $image);
                return $fileName;
            }
            return null;
        } catch (\Exception $e) {
            Log::error('Error guardando Odontograma: ' . $e->getMessage());
            return null;
        }
    }
}
