<?php

namespace App\Http\Controllers;

use App\Models\MedicalExam;
use App\Models\ExamResult; // Importante: Asegúrate de que el modelo se llame así
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, DB, Storage, Log, Schema};
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;

class MedicalExamController extends Controller
{
    /**
     * Las 6 áreas del circuito médico.
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

    public function dashboard(Request $request)
    {
        $search = $request->input('search');

        $completedExams = MedicalExam::with(['student', 'results.specialist'])
            ->where('status', 'completado')
            ->when($search, function ($query, $search) {
                $query->whereHas('student', function ($q) use ($search) {
                    $q->where('first_name', 'LIKE', "%{$search}%")
                      ->orWhere('last_name', 'LIKE', "%{$search}%")
                      ->orWhere('document_number', 'LIKE', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('medical_exams.dashboard', compact('completedExams', 'search'));
    }

    private function getAreaSlug($roleName)
    {
        $slug = Str::slug($roleName, '_');
        $medicinaVariantes = ['medicina_general', 'medico', 'medica', 'medicina', 'valoracion_medica'];
        return in_array($slug, $medicinaVariantes) ? 'valoracion_medica' : $slug;
    }

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
            ->whereHas('student')
            ->latest()
            ->get();

        return view('medical_exams.index', compact('pendingExams', 'userArea'));
    }

    // -------------------------------------------------------------------------
    // ACCIONES DEL CIRCUITO
    // -------------------------------------------------------------------------

    public function store(Request $request)
    {
        $request->validate(['student_id' => 'required|exists:students,id']);

        try {
            DB::beginTransaction();
            MedicalExam::create([
                'student_id'      => $request->student_id,
                'status'          => 'en_proceso',
                'requested_areas' => $this->areasDelCircuito,
                'user_id'         => Auth::id(),
            ]);
            DB::commit();
            return redirect()->route('medical_exams.index')->with('success', 'Circuito iniciado.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function evaluate(MedicalExam $medical_exam)
    {
        $userArea = $this->getAreaSlug(Auth::user()->role->name);

        if (!collect($medical_exam->requested_areas)->contains($userArea) && Auth::user()->role->name !== 'Administrador') {
            return redirect()->route('medical_exams.index')->with('error', 'Área no asignada.');
        }

        if (!view()->exists("medical_exams.evaluations.{$userArea}")) {
            return back()->with('error', "No existe el formulario para: {$userArea}");
        }

        if ($medical_exam->status === 'pendiente') {
            $medical_exam->update(['status' => 'en_proceso']);
        }

        return view('medical_exams.evaluate', ['exam' => $medical_exam, 'userArea' => $userArea]);
    }

    /**
     * GUARDA EVALUACIÓN
     */
    public function storeEvaluation(Request $request, MedicalExam $medical_exam)
    {
        $request->validate([
            'notes' => 'nullable|string|max:1000',
            'results' => 'nullable|array',
            'observations' => 'nullable|string|max:1000',
            'detalles' => 'nullable|string|max:1000',
        ]);

        $userArea = $this->getAreaSlug(Auth::user()->role->name);

        try {
            DB::beginTransaction();

            // Compatibilidad: soportar results[...] y formularios legados con campos planos.
            $evaluationData = $request->input('results');
            if (!is_array($evaluationData) || empty($evaluationData)) {
                $evaluationData = collect($request->all())
                    ->except(['_token', '_method', 'notes', 'observations', 'detalles', 'audiogram_base64'])
                    ->toArray();
            }

            if (empty($evaluationData)) {
                return back()->withInput()->withErrors([
                    'results' => 'Debes registrar al menos un dato en la valoración antes de finalizar.',
                ]);
            }

            $notes = trim((string) (
                $request->input('notes')
                ?? $request->input('observations')
                ?? $request->input('detalles')
                ?? ''
            ));

            if ($notes === '') {
                $notes = 'Evaluación clínica realizada.';
            }

            $chartPath = null;
            $pta_od = null;
            $pta_oi = null;

            // --- LÓGICA DE AUDIOMETRÍA ---
            if ($userArea === 'audiometria') {
                if ($request->filled('audiogram_base64')) {
                    $chartPath = $this->saveMedicalImage($request->audiogram_base64, 'audiogramas', $medical_exam->id);
                    if ($chartPath) {
                        $evaluationData['audiogram_path'] = $chartPath; // Guardar en data para PDF fallback
                    }
                }
                $pta_od = $this->calculatePTA($evaluationData, 'od');
                $pta_oi = $this->calculatePTA($evaluationData, 'oi');
            }

            // --- LÓGICA DE ODONTOLOGÍA ---
            if ($userArea === 'odontologia' && !empty($evaluationData['odontograma_path'])) {
                $path = $this->saveMedicalImage($evaluationData['odontograma_path'], 'odontogramas', $medical_exam->id);
                if ($path) {
                    $evaluationData['odontograma_path'] = $path;
                    $chartPath = $path; // Guardamos en chart_path para consistencia en reportes
                }
            }

            // Guardar o actualizar resultado con compatibilidad de esquema.
            $payload = [
                'user_id' => Auth::id(),
                'data'    => $evaluationData,
                'notes'   => $notes,
            ];

            if (Schema::hasColumn('exam_results', 'chart_path')) {
                $payload['chart_path'] = $chartPath;
            }
            if (Schema::hasColumn('exam_results', 'pta_od')) {
                $payload['pta_od'] = $pta_od;
            }
            if (Schema::hasColumn('exam_results', 'pta_oi')) {
                $payload['pta_oi'] = $pta_oi;
            }

            $medical_exam->results()->updateOrCreate(
                ['area' => $userArea],
                $payload
            );

            // Verificar si el circuito está completo
            $medical_exam->load('results');
            $areasRequeridas  = collect($medical_exam->requested_areas);
            $areasCompletadas = $medical_exam->results->pluck('area');
            $faltantes        = $areasRequeridas->diff($areasCompletadas);

            if ($faltantes->isEmpty()) {
                $medical_exam->update(['status' => 'completado']);
                $msg = '¡Circuito médico completado!';
            } else {
                $medical_exam->update(['status' => 'en_proceso']);
                $msg = "Valoración de {$userArea} guardada.";
            }

            DB::commit();
            return redirect()->route('medical_exams.index')->with('success', $msg);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error Evaluation ($userArea): " . $e->getMessage());
            return back()->withInput()->with('error', 'Error al guardar la evaluación.');
        }
    }

    // -------------------------------------------------------------------------
    // REPORTES Y PDF
    // -------------------------------------------------------------------------

    public function downloadUnifiedReport(MedicalExam $medical_exam)
    {
        $roleName = Auth::user()->role->name ?? null;
        if (!in_array($roleName, ['Administrador', 'Admisión'])) {
            return back()->with('error', 'No tienes permisos.');
        }

        if ($medical_exam->status !== 'completado') {
            return back()->with('error', 'El circuito no está terminado.');
        }

        $medical_exam->load(['student', 'results.specialist']);
        $filename = "reporte_" . $medical_exam->student->document_number . ".pdf";

        return Pdf::loadView('pdf.unified_report', ['exam' => $medical_exam])
            ->setOption('isRemoteEnabled', true)
            ->download($filename);
    }

    public function report(MedicalExam $medical_exam)
    {
        $medical_exam->load(['student', 'results.specialist']);

        if ($medical_exam->results->isEmpty()) {
            return back()->with('error', 'No hay valoraciones.');
        }

        return Pdf::loadView('medical_exams.reports.full_history', [
            'exam'  => $medical_exam,
            'title' => 'HISTORIA CLÍNICA INTEGRAL',
            'date'  => now()->format('d/m/Y h:i A'),
        ])
        ->setPaper('letter', 'portrait')
        ->setOption('isRemoteEnabled', true)
        ->stream("HC_{$medical_exam->student->document_number}.pdf");
    }

    // -------------------------------------------------------------------------
    // MÉTODOS PRIVADOS DE APOYO
    // -------------------------------------------------------------------------

    private function calculatePTA($data, $ear)
    {
        $freqs = [500, 1000, 2000];
        $sum = 0;
        $count = 0;

        foreach ($freqs as $f) {
            $key = "dB_{$ear}_{$f}";
            if (isset($data[$key]) && is_numeric($data[$key])) {
                $sum += (float) $data[$key];
                $count++;
            }
        }

        return $count > 0 ? round($sum / $count, 2) : null;
    }

    private function saveMedicalImage($base64String, $folder, $examId)
    {
        try {
            if (Str::startsWith($base64String, 'data:image')) {
                $image = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64String));
                $fileName = "{$folder}/exam_{$examId}_" . time() . ".png";
                Storage::disk('public')->put($fileName, $image);
                return $fileName;
            }
            return null;
        } catch (\Exception $e) {
            Log::error("Error al guardar imagen en {$folder}: " . $e->getMessage());
            return null;
        }
    }
}
