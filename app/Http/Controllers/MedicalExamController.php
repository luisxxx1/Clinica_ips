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
        'fonoaudiologia',
        'audiometria',
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
        return $this->normalizeAreaSlug((string) $roleName);
    }

    private function normalizeAreaSlug(string $area): string
    {
        $slug = Str::slug($area, '_');

        return match ($slug) {
            'medicina_general', 'medico', 'medica', 'medicina', 'valoracion_medica' => 'valoracion_medica',
            default => $slug,
        };
    }

    private function equivalentAreaSlugsForRole(string $area): array
    {
        return $area === 'audiometria'
            ? ['audiometria', 'fonoaudiologia']
            : [$area];
    }

    public function index()
    {
        $user = Auth::user();
        $userRoleName = $user?->role?->name ?? 'Invitado';
        $userArea = $userRoleName;
        $isAdmin = $userRoleName === 'Administrador';

        // Rechazar acceso al admin - solo doctores pueden hacer evaluaciones
        if ($isAdmin) {
            return redirect()->route('medical_exams.history')->with('info', 'Los administradores acceden solo a historiales clínicos.');
        }

        $userAreaSlug = $this->getAreaSlug($userRoleName);
        $searchAreas  = $this->equivalentAreaSlugsForRole($userAreaSlug);

        $query = MedicalExam::with(['student', 'results'])
            ->where('status', '!=', 'completado')
            ->whereHas('student')
            ->where(function ($builder) use ($searchAreas, $userRoleName) {
                foreach ($searchAreas as $area) {
                    $builder->orWhereJsonContains('requested_areas', $area);
                }

                $builder->orWhereJsonContains('requested_areas', $userRoleName);
            })
            ->latest();

        if ($userAreaSlug === 'audiometria') {
            $pendingExams = $query->get()->filter(function ($exam) {
                $requested = collect($exam->requested_areas ?? [])->map(fn ($a) => Str::slug((string) $a, '_'));
                $completed = $exam->results->pluck('area')->map(fn ($a) => Str::slug((string) $a, '_'));

                $targetAreas = $requested->intersect(['audiometria', 'fonoaudiologia'])->values();
                if ($targetAreas->isEmpty()) {
                    return false;
                }

                return $targetAreas->diff($completed)->isNotEmpty();
            })->values();
        } else {
            $pendingExams = $query
                ->whereDoesntHave('results', function ($q) use ($userAreaSlug) {
                    $q->where('area', $userAreaSlug);
                })
                ->get();
        }

        return view('medical_exams.index', compact('pendingExams', 'userRoleName', 'userArea'));
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

    public function evaluate(Request $request, MedicalExam $medical_exam)
    {
        $userRoleName = Auth::user()->role->name;
        $isAdmin = $userRoleName === 'Administrador';

        // Rechazar acceso al admin - solo doctores pueden hacer evaluaciones
        if ($isAdmin) {
            return redirect()->route('medical_exams.history')->with('error', 'Los administradores no pueden realizar evaluaciones médicas.');
        }

        $userArea = $this->getAreaSlug($userRoleName);
        $requestedAreas = collect($medical_exam->requested_areas)
            ->map(fn ($area) => Str::slug((string) $area, '_'));
        $resultsByLatest = $medical_exam->results
            ->sortByDesc('id')
            ->values();

        $completedAreas = $resultsByLatest
            ->pluck('area')
            ->map(fn ($area) => Str::slug((string) $area, '_'));

        $hasAccess = $requestedAreas->contains($userArea)
            || ($userArea === 'audiometria' && $requestedAreas->contains('fonoaudiologia'));

        if (!$hasAccess) {
            return redirect()->route('medical_exams.index')->with('error', 'Área no asignada.');
        }

        $evaluationArea = $userArea;
        $requestedArea = Str::slug((string) $request->query('area', ''), '_');

        if ($requestedArea !== '') {
            $canOpenRequestedArea = $requestedAreas->contains($requestedArea)
                && (
                    $requestedArea === $userArea
                    || ($userArea === 'audiometria' && in_array($requestedArea, ['audiometria', 'fonoaudiologia'], true))
                );

            if ($canOpenRequestedArea) {
                $evaluationArea = $requestedArea;
            }
        }

        if ($isAdmin && !$requestedAreas->contains($evaluationArea)) {
            $evaluationArea = $requestedAreas->diff($completedAreas)->first()
                ?? $requestedAreas->first()
                ?? 'valoracion_medica';
        }

        if ($userArea === 'audiometria') {
            $requestedAudioAreas = $requestedAreas->intersect(['audiometria', 'fonoaudiologia'])->values();

            if (!$requestedAudioAreas->contains($evaluationArea)) {
                $existingArea = $requestedAudioAreas
                    ->first(fn (string $area) => $completedAreas->contains($area));

                $evaluationArea = $requestedAudioAreas->diff($completedAreas)->first()
                    ?? $existingArea
                    ?? $requestedAudioAreas->first()
                    ?? 'audiometria';
            }
        }

        if ($requestedArea === '' && $requestedAreas->contains($evaluationArea)) {
            $existingResultForArea = $resultsByLatest
                ->first(fn ($result) => Str::slug((string) $result->area, '_') === $evaluationArea);

            if (!$existingResultForArea && $userArea !== 'audiometria') {
                $existingResultForArea = $resultsByLatest
                    ->first(fn ($result) => Str::slug((string) $result->area, '_') === $userArea);

                if ($existingResultForArea) {
                    $evaluationArea = Str::slug((string) $existingResultForArea->area, '_');
                }
            }
        }

        if (!view()->exists("medical_exams.evaluations.{$evaluationArea}")) {
            return back()->with('error', "No existe el formulario para: {$evaluationArea}");
        }

        if ($medical_exam->status === 'pendiente') {
            $medical_exam->update(['status' => 'en_proceso']);
        }

        // Mostrar bloque unificado de Audio+Fono cuando ambas áreas pertenecen al mismo flujo.
        $audioRequested = $requestedAreas->contains('audiometria');
        $fonoRequested = $requestedAreas->contains('fonoaudiologia');
        $showBothAudioExams = $audioRequested
            && $fonoRequested
            && in_array($evaluationArea, ['audiometria', 'fonoaudiologia'], true)
            && ($userArea === 'audiometria' || $isAdmin);

        $existingAudioResult = $resultsByLatest
            ->first(fn ($result) => Str::slug((string) $result->area, '_') === 'audiometria');
        $existingFonoResult = $resultsByLatest
            ->first(fn ($result) => Str::slug((string) $result->area, '_') === 'fonoaudiologia');

        $existingResult = $resultsByLatest
            ->first(fn ($result) => Str::slug((string) $result->area, '_') === $evaluationArea);

        $existingEvaluationData = is_array($existingResult?->data) ? $existingResult->data : [];
        $existingAudioNotes = $existingAudioResult?->notes;
        $existingFonoNotes = $existingFonoResult?->notes;

        if ($showBothAudioExams) {
            $existingEvaluationData = array_merge(
                is_array($existingAudioResult?->data) ? $existingAudioResult->data : [],
                is_array($existingFonoResult?->data) ? $existingFonoResult->data : [],
            );
        }

        return view('medical_exams.evaluate', [
            'exam' => $medical_exam,
            'userArea' => $evaluationArea,
            'existingEvaluationData' => $existingEvaluationData,
            'existingEvaluationNotes' => $showBothAudioExams ? null : $existingResult?->notes,
            'existingAudioNotes' => $existingAudioNotes,
            'existingFonoNotes' => $existingFonoNotes,
            'showBothAudioExams' => $showBothAudioExams,
        ]);
    }

    /**
     * GUARDA EVALUACIÓN
     */
    public function storeEvaluation(Request $request, MedicalExam $medical_exam)
    {
        $userRoleName = Auth::user()->role->name;
        $isAdmin = $userRoleName === 'Administrador';

        // Rechazar acceso al admin - solo doctores pueden realizar evaluaciones
        if ($isAdmin) {
            return redirect()->route('medical_exams.history')->with('error', 'Los administradores no pueden realizar evaluaciones médicas.');
        }

        $request->validate([
            'notes' => 'nullable|string|max:1000',
            'results' => 'nullable|array',
            'observations' => 'nullable|string|max:1000',
            'detalles' => 'nullable|string|max:1000',
        ]);

        $userArea = $this->getAreaSlug($userRoleName);
        $evaluationArea = Str::slug((string) $request->input('evaluation_area', ''), '_');
        $saveArea = $userArea;
        $saveBothAudio = $request->input('save_both_audio') === '1';

        $requestedAreas = collect($medical_exam->requested_areas)
            ->map(fn ($area) => Str::slug((string) $area, '_'));

        if ($userArea === 'audiometria' && in_array($evaluationArea, ['audiometria', 'fonoaudiologia'], true)) {
            $saveArea = $evaluationArea;
        }

        try {
            DB::beginTransaction();

            // ===== LÓGICA UNIFICADA PARA AUDIOMETRÍA + FONOAUDIOLOGÍA =====
            if ($saveBothAudio) {
                // Obtener todos los datos del formulario
                $allData = $request->all();

                // Separar datos de AUDIOMETRÍA desde el arreglo real `results`.
                $rawAudioResults = $request->input('results', []);
                if (!is_array($rawAudioResults)) {
                    $rawAudioResults = [];
                }

                $audioAllowedKeys = [
                    'oto_od', 'oto_oi',
                    'oto_od_cae', 'oto_oi_cae',
                    'oto_od_cerumen', 'oto_oi_cerumen',
                    'oto_od_membrana', 'oto_oi_membrana',
                    'dB_od_250', 'dB_od_500', 'dB_od_1000', 'dB_od_2000', 'dB_od_4000', 'dB_od_8000',
                    'dB_oi_250', 'dB_oi_500', 'dB_oi_1000', 'dB_oi_2000', 'dB_oi_4000', 'dB_oi_8000',
                    'diagnostico', 'proteccion',
                ];

                $audioEvaluationData = collect($rawAudioResults)
                    ->only($audioAllowedKeys)
                    ->filter(fn ($value) => $value !== null && $value !== '')
                    ->toArray();

                $audioNotes = trim((string) ($request->input('notes') ?? 'Evaluación clínica realizada.'));
                if ($audioNotes === '') {
                    $audioNotes = 'Evaluación clínica realizada.';
                }

                // Guardar AUDIOMETRÍA
                $chartPath = null;
                $pta_od = null;
                $pta_oi = null;

                if ($request->filled('audiogram_base64')) {
                    $chartPath = $this->saveMedicalImage($request->audiogram_base64, 'audiogramas', $medical_exam->id);
                    if ($chartPath) {
                        $audioEvaluationData['audiogram_path'] = $chartPath;
                    }
                }
                $pta_od = $this->calculatePTA($audioEvaluationData, 'od');
                $pta_oi = $this->calculatePTA($audioEvaluationData, 'oi');

                $audioPayload = [
                    'user_id' => Auth::id(),
                    'data' => $audioEvaluationData,
                    'notes' => $audioNotes,
                ];

                if (Schema::hasColumn('exam_results', 'chart_path')) {
                    $audioPayload['chart_path'] = $chartPath;
                }
                if (Schema::hasColumn('exam_results', 'pta_od')) {
                    $audioPayload['pta_od'] = $pta_od;
                }
                if (Schema::hasColumn('exam_results', 'pta_oi')) {
                    $audioPayload['pta_oi'] = $pta_oi;
                }

                $medical_exam->results()->updateOrCreate(
                    ['area' => 'audiometria'],
                    $audioPayload
                );

                // Separar datos de FONOAUDIOLOGÍA
                $fonoData = collect($allData)
                    ->only([
                        'articulacion', 'fluidez', 'voz', 'comprension', 'expresion',
                        'pragmatica', 'lectura', 'escritura',
                        'oido_derecho', 'oido_izquierdo'
                    ])
                    ->filter(fn($v) => $v !== null && $v !== '')
                    ->toArray();

                $fonoNotes = trim((string) ($request->input('observations') ?? 'Evaluación fonoaudiológica realizada.'));
                if ($fonoNotes === '') {
                    $fonoNotes = 'Evaluación fonoaudiológica realizada.';
                }

                $fonoPayload = [
                    'user_id' => Auth::id(),
                    'data' => $fonoData,
                    'notes' => $fonoNotes,
                ];

                $medical_exam->results()->updateOrCreate(
                    ['area' => 'fonoaudiologia'],
                    $fonoPayload
                );

            } else {
                // ===== LÓGICA ESTÁNDAR PARA EVALUACIÓN INDIVIDUAL =====
                // Compatibilidad: soportar results[...] y formularios legados con campos planos.
                $evaluationData = $request->input('results');
                if (!is_array($evaluationData) || empty($evaluationData)) {
                    $evaluationData = collect($request->all())
                        ->except(['_token', '_method', 'notes', 'observations', 'detalles', 'audiogram_base64', 'save_both_audio'])
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
                if ($saveArea === 'audiometria') {
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
                if ($saveArea === 'odontologia' && !empty($evaluationData['odontograma_path'])) {
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
                    ['area' => $saveArea],
                    $payload
                );
            }

            // Verificar si el circuito está completo
            $medical_exam->load('results');
            $areasRequeridas  = collect($medical_exam->requested_areas)->map(fn ($area) => Str::slug((string) $area, '_'));
            $areasCompletadas = $medical_exam->results->pluck('area')->map(fn ($area) => Str::slug((string) $area, '_'));

            $faltantes        = $areasRequeridas->diff($areasCompletadas);

            if ($faltantes->isEmpty()) {
                $medical_exam->update(['status' => 'completado']);
                $msg = '¡Circuito médico completado!';
            } else {
                $medical_exam->update(['status' => 'en_proceso']);
                $msg = $saveBothAudio ? 'Evaluaciones Audiometría y Fonoaudiología guardadas.' : "Valoración de {$saveArea} guardada.";
            }

            DB::commit();

            // Siempre volver a la bandeja de evaluaciones
            return redirect()->route('medical_exams.index')->with('success', $msg);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error Evaluation: " . $e->getMessage());
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
