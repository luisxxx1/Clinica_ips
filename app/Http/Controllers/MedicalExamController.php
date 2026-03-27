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
     * Las 6 Ã¡reas del circuito mÃ©dico.
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
    // MÃ‰TODOS DE CONSULTA Y NAVEGACIÃ“N
    // -------------------------------------------------------------------------

    /**
     * Muestra el historial completo de evaluaciones (Vista Admin/AuditorÃ­a).
     */
    public function history(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');
        $school = trim((string) $request->input('school'));

        $schools = DB::table('students')
            ->whereNotNull('previous_school')
            ->where('previous_school', '!=', '')
            ->distinct()
            ->orderBy('previous_school')
            ->pluck('previous_school');

        $completedExams = MedicalExam::with(['student', 'results'])
            ->whereHas('student')
            ->when($search, function ($query, $search) {
                $query->whereHas('student', function ($q) use ($search) {
                    $q->where('first_name', 'LIKE', "%{$search}%")
                      ->orWhere('last_name', 'LIKE', "%{$search}%")
                      ->orWhere('document_number', 'LIKE', "%{$search}%");
                });
            })
            ->when($school, function ($query, $school) {
                $query->whereHas('student', function ($q) use ($school) {
                    $q->where('previous_school', $school);
                });
            })
            ->when($status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('medical_exams.history', compact('completedExams', 'search', 'status', 'school', 'schools'));
    }

    /**
     * Vista del ADMINISTRADOR:
     * Muestra solo los exÃ¡menes donde los 6 mÃ©dicos ya evaluaron al estudiante,
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
     * Normaliza los nombres de Ã¡reas a slugs consistentes.
     */
    private function getAreaSlug($roleName)
    {
        $slug = Str::slug($roleName, '_');
        $medicinaVariantes = ['medicina_general', 'medico', 'medica', 'medicina', 'valoracion_medica'];

        return in_array($slug, $medicinaVariantes) ? 'valoracion_medica' : $slug;
    }

    /**
     * Muestra la lista de exÃ¡menes pendientes segÃºn el Ã¡rea del mÃ©dico autenticado.
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
            ->whereHas('student') // âœ… Solo exÃ¡menes de estudiantes NO eliminados
            ->latest()
            ->get();

        return view('medical_exams.index', compact('pendingExams', 'userArea'));
    }

    // -------------------------------------------------------------------------
    // INICIA UN NUEVO CIRCUITO MÃ‰DICO
    // -------------------------------------------------------------------------

    /**
     * Crea el examen y asigna las 6 Ã¡reas del circuito mÃ©dico.
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
                ->with('success', 'Circuito mÃ©dico iniciado correctamente para el estudiante.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'No se pudo iniciar la evaluaciÃ³n: ' . $e->getMessage());
        }
    }

    // -------------------------------------------------------------------------
    // EVALUACIÃ“N POR ÃREA
    // -------------------------------------------------------------------------

    /**
     * Muestra el formulario de evaluaciÃ³n segÃºn el Ã¡rea del mÃ©dico autenticado.
     * Si el circuito estÃ¡ en 'pendiente', cambia a 'en_proceso' (primer acceso mÃ©dico).
     */
    public function evaluate(MedicalExam $medical_exam)
    {
        $userArea = $this->getAreaSlug(Auth::user()->role->name);

        if (
            !collect($medical_exam->requested_areas)->contains($userArea)
            && Auth::user()->role->name !== 'Administrador'
        ) {
            return redirect()->route('medical_exams.index')
                ->with('error', 'Tu Ã¡rea no estÃ¡ asignada a este examen.');
        }

        if (!view()->exists("medical_exams.evaluations.{$userArea}")) {
            return back()->with('error', "No se encontrÃ³ el formulario tÃ©cnico para: {$userArea}");
        }

        // âœ… Cambiar de 'pendiente' a 'en_proceso' cuando el primer mÃ©dico accede
        if ($medical_exam->status === 'pendiente') {
            $medical_exam->update(['status' => 'en_proceso']);
        }

        $medical_exam->load('student');

        return view('medical_exams.evaluate', [
            'exam'     => $medical_exam,
            'userArea' => $userArea,
        ]);
    }

    /**
     * Guarda la valoraciÃ³n del mÃ©dico y verifica si el circuito ya estÃ¡ completo.
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

            // Guardar o actualizar la valoraciÃ³n de esta Ã¡rea
            $medical_exam->results()->updateOrCreate(
                ['area' => $userArea],
                [
                    'user_id' => Auth::id(),
                    'data'    => $evaluationData,
                    'notes'   => $request->notes ?? $request->observations ?? 'EvaluaciÃ³n realizada correctamente.',
                ]
            );

            // Recargar resultados y verificar si el circuito estÃ¡ completo
            $medical_exam->load('results');

            $areasRequeridas  = collect($medical_exam->requested_areas ?? []);
            $areasCompletadas = $medical_exam->results->pluck('area');
            $areasFaltantes   = $areasRequeridas->diff($areasCompletadas);

            if ($areasFaltantes->isEmpty()) {
                // âœ… Los 6 mÃ©dicos ya evaluaron â†’ circuito completo
                $medical_exam->update(['status' => 'completado']);
                $msg = 'Â¡Circuito mÃ©dico completado! Todas las Ã¡reas han evaluado al estudiante.';
            } else {
                // ðŸ”„ AÃºn faltan mÃ©dicos por evaluar
                $medical_exam->update(['status' => 'en_proceso']);
                $restantes = $areasFaltantes->implode(', ');
                $msg = "ValoraciÃ³n de {$userArea} guardada. Ãreas pendientes: {$restantes}.";
            }

            DB::commit();

            return redirect()->route('medical_exams.index')->with('success', $msg);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error en Evaluation: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Error procesando la evaluaciÃ³n mÃ©dica.');
        }
    }
    /**
     * Descarga PDF unificado con todas las evaluaciones del circuito
     */
    public function downloadUnifiedReport(MedicalExam $medical_exam)
    {
        $roleName = Auth::user()->role->name ?? null;
        if (!in_array($roleName, ['Administrador', 'Admisión'])) {
            return back()->with('error', 'Solo Administrador y Admisión pueden descargar el reporte unificado.');
        }

        $medical_exam->load(['student', 'results.specialist']);

        if ($medical_exam->status !== 'completado') {
            return back()->with('error', 'El circuito mÃ©dico aÃºn no estÃ¡ completado.');
        }

        $filename = "reporte-integral_" .
                   $medical_exam->student->first_name . "_" .
                   $medical_exam->student->last_name . "_" .
                   now()->format('d-m-Y_H-i') . ".pdf";

        $pdf = Pdf::loadView('pdf.unified_report', ['exam' => $medical_exam])
            ->setOption('margin-bottom', 0);

        return $pdf->download($filename);
    }

    // -------------------------------------------------------------------------
    // REPORTE PDF
    // -------------------------------------------------------------------------

    /**
     * Genera el PDF con el historial clÃ­nico completo del estudiante.
     */
    public function report(MedicalExam $medical_exam)
    {
        $medical_exam->load(['student', 'results.specialist']);

        if ($medical_exam->results->isEmpty()) {
            return back()->with('error', 'El examen no tiene valoraciones registradas para generar el reporte.');
        }

        // âœ… CORRECCIÃ“N: Se aÃ±adieron opciones para permitir la carga de imÃ¡genes locales y externas
        $pdf = Pdf::loadView('medical_exams.reports.full_history', [
            'exam'  => $medical_exam,
            'title' => 'HISTORIA CLÃNICA INTEGRAL',
            'date'  => now()->format('d/m/Y h:i A'),
        ])
        ->setPaper('letter', 'portrait')
        ->setOption('isRemoteEnabled', true)
        ->setOption('isHtml5ParserEnabled', true);

        return $pdf->stream("HC_{$medical_exam->student->document_number}.pdf");
    }

    // -------------------------------------------------------------------------
    // MÃ‰TODOS PRIVADOS
    // -------------------------------------------------------------------------

    /**
     * Guarda la imagen del odontograma en almacenamiento pÃºblico.
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



