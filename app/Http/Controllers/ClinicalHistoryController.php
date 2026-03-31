<?php

namespace App\Http\Controllers;

use App\Models\ClinicalHistory;
use App\Models\ExamResult;
use App\Models\Student;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ClinicalHistoryController extends Controller
{
    private array $allowedRoles = [
        'administrador',
        'medicina general',
        'optometría',
        'optometria',
        'odontología',
        'odontologia',
        'psicología',
        'psicologia',
        'fonoaudiología',
        'fonoaudiologia',
        'audiometría',
        'audiometria',
    ];

    public function index(Request $request)
    {
        $this->ensureAccess();

        $search = trim((string) $request->input('search'));

        $students = Student::query()
            ->when($search, function ($query) use ($search) {
                $query->where('first_name', 'LIKE', "%{$search}%")
                    ->orWhere('last_name', 'LIKE', "%{$search}%")
                    ->orWhere('document_number', 'LIKE', "%{$search}%");
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('clinical_histories.index', compact('students', 'search'));
    }

    public function show(Student $student)
    {
        $this->ensureAccess();

        $isAdmin = $this->normalizedRoleName() === 'administrador';
        $entryAreaOptions = $this->entryAreaOptionsForCurrentUser();
        $canSelectEntryArea = count($entryAreaOptions) > 1;
        $defaultEntryArea = array_key_first($entryAreaOptions) ?? $this->currentAreaSlug();

        $student->load([
            'clinicalHistories' => function ($query) {
                $query->with('specialist.role')
                    ->latest('recorded_at')
                    ->latest('id');
            },
        ]);

        return view('clinical_histories.show', [
            'student' => $student,
            'isAdmin' => $isAdmin,
            'entryAreaOptions' => $entryAreaOptions,
            'canSelectEntryArea' => $canSelectEntryArea,
            'defaultEntryArea' => $defaultEntryArea,
            'availableAreas' => $this->availableAreas(),
            'currentAreaLabel' => $this->currentAreaLabel(),
            'defaultClinicalTitle' => $this->defaultClinicalTitle(),
            'defaultClinicalEntry' => $this->defaultClinicalEntry($student),
            'defaultClinicalTitlesByArea' => $this->defaultClinicalTitlesByArea(),
            'defaultClinicalEntriesByArea' => $this->defaultClinicalEntriesByArea($student),
        ]);
    }

    public function store(Request $request, Student $student)
    {
        $this->ensureAccess();

        $isAdmin = $this->normalizedRoleName() === 'administrador';
        $allowedAreaKeys = array_keys($this->entryAreaOptionsForCurrentUser());

        $validated = $request->validate([
            'area' => ['nullable', 'string', Rule::in($allowedAreaKeys)],
            'title' => 'nullable|string|max:150',
            'entry' => 'required|string|max:3000',
            'recorded_at' => 'nullable|date',
        ]);

        $selectedArea = $this->normalizeAreaKey($validated['area'] ?? $this->currentAreaSlug());
        $areaToSave = in_array($selectedArea, $allowedAreaKeys, true)
            ? $selectedArea
            : $this->currentAreaSlug();

        ClinicalHistory::create([
            'student_id' => $student->id,
            'user_id' => Auth::id(),
            'area' => $areaToSave,
            'title' => $validated['title'] ?? null,
            'entry' => $validated['entry'],
            'recorded_at' => $validated['recorded_at'] ?? now(),
        ]);

        return redirect()
            ->route('clinical_histories.show', $student)
            ->with('success', 'Entrada de historial clínico registrada correctamente.');
    }

    public function update(Request $request, Student $student, ClinicalHistory $clinical_history)
    {
        $this->ensureAccess();

        $isAdmin = $this->normalizedRoleName() === 'administrador';

        if ((int) $clinical_history->student_id !== (int) $student->id) {
            abort(404);
        }

        if (!$this->canEditEntry($clinical_history)) {
            abort(403, 'No tienes permiso para editar esta nota clínica.');
        }

        $validated = $request->validate([
            'area' => ['nullable', 'string', Rule::in(array_keys($this->availableAreas()))],
            'title' => 'nullable|string|max:150',
            'entry' => 'required|string|max:3000',
            'recorded_at' => 'nullable|date',
        ]);

        $selectedArea = $this->normalizeAreaKey($validated['area'] ?? $clinical_history->area);

        $clinical_history->update([
            'area' => $isAdmin ? $selectedArea : $clinical_history->area,
            'title' => $validated['title'] ?? null,
            'entry' => $validated['entry'],
            'recorded_at' => $validated['recorded_at'] ?? $clinical_history->recorded_at ?? now(),
        ]);

        return redirect()
            ->route('clinical_histories.show', $student)
            ->with('success', 'Entrada de historial clínico actualizada correctamente.');
    }

    public function downloadPdf(Student $student)
    {
        $this->ensurePdfAccess();

        $student->load([
            'clinicalHistories' => function ($query) {
                $query->with('specialist.role')
                    ->orderBy('recorded_at')
                    ->orderBy('id');
            },
        ]);

        $filename = 'historial_clinico_'
            . $student->document_number
            . '_'
            . now()->format('d-m-Y_H-i')
            . '.pdf';

        $graphics = ExamResult::query()
            ->whereHas('medicalExam', function ($query) use ($student) {
                $query->where('student_id', $student->id);
            })
            ->whereIn('area', ['audiometria', 'audiometría', 'odontologia', 'odontología'])
            ->with('specialist.role')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get()
            ->map(function (ExamResult $result) {
                $source = $this->resolveGraphicSourceForPdf($result);

                if (!$source) {
                    return null;
                }

                return [
                    'area' => $result->area,
                    'specialist' => $result->specialist?->name ?? 'No registrado',
                    'created_at' => optional($result->created_at)->format('d/m/Y H:i'),
                    'source' => $source,
                    'pta_od' => $result->pta_od,
                    'pta_oi' => $result->pta_oi,
                    'data' => $result->data ?? [],
                ];
            })
            ->filter()
            ->values();

        $entriesByArea = $student->clinicalHistories
            ->groupBy(fn (ClinicalHistory $entry) => $this->normalizeAreaKey($entry->area));

        $areasWithEntries = $entriesByArea->keys()->values();
        $areasWithGraphics = $graphics
            ->map(fn (array $graphic) => $this->normalizeAreaKey($graphic['area']))
            ->unique()
            ->values();

        $preferredAreaOrder = collect([
            'audiometria',
            'odontologia',
            'valoracion_medica',
            'optometria',
            'fonoaudiologia',
            'psicologia',
        ]);

        $orderedAreaKeys = $preferredAreaOrder
            ->filter(fn (string $areaKey) => $areasWithEntries->contains($areaKey) || $areasWithGraphics->contains($areaKey))
            ->values();

        $extraAreaKeys = $areasWithEntries
            ->merge($areasWithGraphics)
            ->unique()
            ->filter(fn (string $areaKey) => !$orderedAreaKeys->contains($areaKey))
            ->values();

        $allAreaKeys = $orderedAreaKeys->merge($extraAreaKeys)->values();

        $orderedSections = $allAreaKeys
            ->map(function (string $areaKey) use ($graphics, $entriesByArea) {
                return [
                    'area' => $areaKey,
                    'label' => $this->areaLabel($areaKey),
                    'title' => $this->areaSectionTitle($areaKey),
                    'graphic' => $graphics->first(fn (array $graphic) => $this->normalizeAreaKey($graphic['area']) === $areaKey),
                    'entries' => ($entriesByArea->get($areaKey) ?? collect())->values(),
                ];
            })
            ->values();

        $pdf = Pdf::loadView('clinical_histories.pdf', [
            'student' => $student,
            'entries' => $student->clinicalHistories,
            'graphics' => $graphics,
            'orderedSections' => $orderedSections,
            'generatedAt' => now()->format('d/m/Y H:i'),
        ])
            ->setPaper('letter', 'portrait')
            ->setOption('isRemoteEnabled', true)
            ->setOption('isHtml5ParserEnabled', true);

        return $pdf->download($filename);
    }

    private function ensureAccess(): void
    {
        $role = $this->normalizedRoleName();

        if (!$role || !in_array($role, $this->allowedRoles, true)) {
            abort(403, 'No tienes permiso para acceder al historial clínico.');
        }
    }

    private function normalizedRoleName(): ?string
    {
        $roleName = Auth::user()?->role?->name;

        if (!$roleName) {
            return null;
        }

        return mb_strtolower(trim($roleName));
    }

    private function currentAreaSlug(): string
    {
        $roleName = Auth::user()?->role?->name ?? 'especialista';

        return Str::slug($roleName, '_');
    }

    private function currentAreaLabel(): string
    {
        return Auth::user()?->role?->name ?? 'Especialista';
    }

    private function defaultClinicalEntry(Student $student): string
    {
        return $this->defaultClinicalEntryForArea($student, $this->currentAreaSlug());
    }

    private function defaultClinicalEntryForArea(Student $student, string $areaSlug): string
    {
        $medicalData = $this->getLatestMedicalAssessmentData($student->id);
        $optometryData = $this->getLatestOptometryAssessmentData($student->id);

        $peso = $medicalData['peso'] ?? '';
        $talla = $medicalData['talla'] ?? '';
        $imc = $medicalData['imc_calculado'] ?? $medicalData['imc'] ?? '';
        $percentil = $medicalData['imc_percentil'] ?? '';

        $odLejana = trim((string) ($optometryData['od_lejana'] ?? ''));
        $oiLejana = trim((string) ($optometryData['oi_lejana'] ?? ''));
        $odProxima = trim((string) ($optometryData['od_proxima'] ?? ''));
        $oiProxima = trim((string) ($optometryData['oi_proxima'] ?? ''));
        $vp = trim((string) (
            $optometryData['vp']
            ?? $optometryData['vision_proxima_binocular']
            ?? $optometryData['vision_proxima']
            ?? ''
        ));

        return match ($areaSlug) {
            'audiometria' => "A la valoración auditiva mediante audiometría comportamental de tonos puros realizada en la IE, NO se evidencian dificultades auditivas, presentando Normoacusia.\n\n"
                . "OBSERVACIONES:\n"
                . "- Durante la exploración física se evidencia:\n"
                . "  CAE: Permeables, sin presencia de cerumen impactado ni secreciones.\n"
                . "  MT: Integra, bien orientada, de color nacarado y sin evidencias de perforación, retracción o líquido en oído medio.\n"
                . "- Realizar Audiometría Clínica o Potenciales Evocados Auditivos con el fin de corroborar resultado obtenido.\n\n"
                . "RECOMENDACIONES:\n"
                . "- Mantener controles auditivos preventivos de manera periódica, especialmente en edad escolar.\n"
                . "- Evitar la exposición a ruidos intensos o prolongados (volumen elevado en dispositivos o ambientes ruidosos).\n"
                . "- Supervisar el uso de audífonos y evitar la introducción de objetos extraños en el conducto auditivo externo.\n"
                . "- Observar posibles signos de alerta como dificultad para seguir instrucciones o necesidad de repetición frecuente.\n"
                . "- Acudir a valoración por fonoaudiología u otorrinolaringología ante cualquier cambio en la respuesta auditiva o del lenguaje.",

                'medicina_general', 'valoracion_medica' => "El que suscribe legalmente autorizado para ejercer su profesión.\n\n"
                    . "CERTIFICA:\n"
                    . "- No existen síntomas o signos de enfermedad orgánica o infecciosa ni de ninguna otra enfermedad transmisible.\n"
                    . "- El/La paciente no padece de ninguna enfermedad crónica que lo/la limite físicamente.\n\n"
                    . "PARÁMETROS ANTROPOMÉTRICOS:\n"
                    . "Peso: " . ($peso !== '' ? $peso . " Kg" : "") . "\n"
                    . "Talla: " . ($talla !== '' ? $talla . " Cm" : "") . "\n"
                    . "IMC: {$imc}\n"
                    . "Percentil: {$percentil}\n\n"
                    . "IDX: 1. APS FISICAMENTE ESTABLE\n\n"
                    . "OBSERVACIONES/RECOMENDACIONES:\n"
                    . "- Mantener controles médicos periódicos de crecimiento y desarrollo, según esquema pediátrico.\n"
                    . "- Cumplir con el esquema de vacunación correspondiente a la edad.\n"
                    . "- Promover una alimentación balanceada, variada y acorde a la edad, rica en frutas, verduras, proteínas y adecuada hidratación.\n"
                    . "- Fomentar la actividad física diaria y el juego al aire libre, evitando el sedentarismo.\n"
                    . "- Establecer rutinas de sueño adecuadas, asegurando entre 10 y 12 horas de descanso nocturno.\n"
                    . "- Reforzar hábitos de higiene personal, como el lavado frecuente de manos y el cepillado dental después de cada comida.\n"
                    . "- Asistir a controles odontológicos al menos cada seis meses.\n"
                    . "- Limitar el tiempo de exposición a pantallas (televisión, tabletas, celulares).",

            'odontologia' => "Usuario que asiste a valoración Odontológica donde se realiza carta dental.\n"
                . "No se observan lesiones cariosas activas, procesos infecciosos ni alteraciones en tejidos blandos o duros. "
                . "La dentición temporal se encuentra acorde a la edad, con adecuado proceso de erupción.\n\n"
                . "Índice ceo-d: 0 (sin evidencia de dientes cariados, extraídos u obturados en dentición temporal).\n\n"
                . "De acuerdo con lo anterior, el(la) paciente se encuentra en condiciones de salud oral satisfactorias "
                . "al momento de la valoración y es apto(a) para su permanencia en el entorno escolar.\n\n"
                . "RECOMENDACIONES:\n"
                . "- Mantener hábitos adecuados de higiene oral mediante cepillado mínimo tres veces al día y supervisión de un adulto.\n"
                . "- Limitar el consumo de azúcares y alimentos cariogénicos; fomentar una alimentación balanceada.\n"
                . "- Asistir a controles odontológicos cada seis meses.",

            'optometria' => "Al realizar el examen del usuario en mención se encontró:\n\n"
                . "AGUDEZA VISUAL:\n"
                . "OD (Lejana): " . ($odLejana !== '' ? $odLejana : '_____') . " | OI (Lejana): " . ($oiLejana !== '' ? $oiLejana : '_____') . " | VP: " . ($vp !== '' ? $vp : '_____') . "\n"
                . "OD (Próxima): " . ($odProxima !== '' ? $odProxima : '_____') . " | OI (Próxima): " . ($oiProxima !== '' ? $oiProxima : '_____') . "\n"
                . "CSM (Capacidad Sensorial y Motora)\n"
                . "Examen realizado con opto-tipo E direccional y luz.\n\n"
                . "HALLAZGOS:\n"
                . "Al examen externo se encuentran corneas claras, medios transparentes y cámara anterior formada.\n"
                . "IDX: H527 (Trastorno de la refracción, no especificado).\n\n"
                . "OBSERVACIONES/RECOMENDACIONES:\n"
                . "- Se aconseja asistir por su EPS para controles anuales.\n"
                . "- Implementar hábitos de higiene visual: mantener adecuada iluminación y conservar una distancia prudente frente a pantallas (mínimo 40–50 cm).\n"
                . "- Aplicar la regla 20-20-20: cada 20 minutos descansar la vista mirando a lo lejos durante 20 segundos.\n"
                . "- Evitar el uso prolongado de dispositivos electrónicos sin pausas activas y fomentar actividades al aire libre.\n"
                . "- Consultar nuevamente si presenta visión borrosa, dolor de cabeza recurrente, fatiga visual o ardor ocular.\n"
                . "- El seguimiento oportuno permitirá mantener un adecuado desempeño en las actividades académicas y diarias.",

            'fonoaudiologia' => "Paciente valorado por Fonoaudiología. Se observan procesos comunicativos y de lenguaje acordes a la etapa del desarrollo, sin alteraciones evidentes durante la evaluación.\n"
                . "OBSERVACIONES/RECOMENDACIONES:\n"
                . "- Estimular hábitos de comunicación en el entorno familiar y escolar.\n"
                . "- Mantener seguimiento preventivo del desarrollo del lenguaje.\n"
                . "- Revalorar en caso de notar cambios en articulación, comprensión o fluidez verbal.",

            'psicologia' => "Motivo de consulta Entrevista psicológica para ingreso al colegio XXX\n\n"
                        . "Observaciones de la conducta\n"
                        . "Estudiante ingresa en compañía de Xxx en adecuadas condiciones de higiene y aseo, presenta coherencia ideo verbal, establece contacto visual adecuado y producción del pensamiento, con buen comportamiento, receptivo, es capaz de sostener el foco atencional de aquellos elementos del ambiente, tiene iniciativa y es propositivo en la comunicación, tiene buena valoración de sí misma, refiere relaciones interpersonales sanas.\n\n"
                        . "Composición familiar\n"
                        . "El/la menor reside actualmente con xxx Mantiene vinculo con su padre, con quien comparte tiempo de manera esporádica conservando una relación respetuosa y funcional. El grupo familiar presenta una dinámica adecuada, relaciones basadas en el respeto, la comunicación y el apoyo mutuo.\n"
                        . "La corresponsabilidad parental favorece un entorno estable y seguro, permitiendo al estudiante desarrollar vínculos afectivos sanos, normas claras y valores que contribuyen positivamente a su desarrollo integral. El comportamiento del estudiante dentro del contexto familiar se caracteriza por ser asertivo, respetuoso y acorde a las normas y rutinas establecidas.\n\n"
                        . "Dimensión académica (instituciones educativas previas, fortalezas, dificultades en asignaturas, etc.)\n"
                        . "El/la estudiante curso el grado xxx en el colegio xxx el cual aprobó con buen rendimiento académico, buen desempeño cognitivo, social y buena dinámica escolar. El motivo por el cual los acudientes deciden ingresarlo/a en el xxx es debido al deseo de continuar fortaleciendo sus habilidades sociales y cognitivas.\n\n"
                        . "Conclusiones\n"
                        . "- El/la estudiante cuenta con habilidades sociales, académicas y un núcleo familiar estable y positivo el cual favorece el logro exitoso de cada una de las competencias en el proceso educativo y acompañamiento significativo en el desarrollo personal. Por ende, es apto para el contexto educativo.\n"
                        . "- Se sugiere al acudiente e institución educativa estar atentos al proceso de adaptación escolar del estudiante, y la posibilidad de cambios emocionales y/o comportamentales en dicho proceso.",

            default => "Paciente evaluado por el área correspondiente. A la fecha no se evidencian hallazgos clínicos de alarma y el estado general se encuentra dentro de parámetros esperados.\n"
                . "OBSERVACIONES/RECOMENDACIONES:\n"
                . "- Continuar controles preventivos periódicos.\n"
                . "- Mantener medidas de autocuidado y hábitos saludables.\n"
                . "- Reconsultar ante cualquier síntoma nuevo o cambio clínico.",
        };
    }

    private function defaultClinicalTitle(): string
    {
        return $this->defaultClinicalTitleForArea($this->currentAreaSlug());
    }

    private function defaultClinicalTitleForArea(string $areaSlug): string
    {
        return match ($areaSlug) {
            'audiometria' => 'Audiometria',
            'valoracion_medica', 'medicina_general' => 'Valoracion Medica',
            'odontologia' => 'Odontologia',
            'optometria' => 'Optometria',
            'fonoaudiologia' => 'Fonoaudiologia',
            'psicologia' => 'Psicologia',
            default => 'Control Clinico',
        };
    }

    private function defaultClinicalEntriesByArea(Student $student): array
    {
        $entries = [];

        foreach (array_keys($this->availableAreas()) as $area) {
            $entries[$area] = $this->defaultClinicalEntryForArea($student, $area);
        }

        return $entries;
    }

    private function defaultClinicalTitlesByArea(): array
    {
        $titles = [];

        foreach (array_keys($this->availableAreas()) as $area) {
            $titles[$area] = $this->defaultClinicalTitleForArea($area);
        }

        return $titles;
    }

    private function availableAreas(): array
    {
        return [
            'valoracion_medica' => 'Valoración Médica',
            'odontologia' => 'Odontología',
            'optometria' => 'Optometría',
            'audiometria' => 'Audiometría',
            'fonoaudiologia' => 'Fonoaudiología',
            'psicologia' => 'Psicología',
        ];
    }

    private function entryAreaOptionsForCurrentUser(): array
    {
        $allAreas = $this->availableAreas();
        $role = $this->normalizedRoleName();
        $currentArea = $this->currentAreaSlug();

        if ($role === 'administrador') {
            return $allAreas;
        }

        if ($currentArea === 'audiometria') {
            return array_intersect_key($allAreas, array_flip(['audiometria', 'fonoaudiologia']));
        }

        if (array_key_exists($currentArea, $allAreas)) {
            return [$currentArea => $allAreas[$currentArea]];
        }

        return [];
    }

    private function getLatestMedicalAssessmentData(int $studentId): array
    {
        $result = ExamResult::query()
            ->whereHas('medicalExam', function ($query) use ($studentId) {
                $query->where('student_id', $studentId);
            })
            ->whereIn('area', ['valoracion_medica', 'medicina_general', 'medicina-general'])
            ->latest('id')
            ->first();

        return is_array($result?->data) ? $result->data : [];
    }

    private function getLatestOptometryAssessmentData(int $studentId): array
    {
        $result = ExamResult::query()
            ->whereHas('medicalExam', function ($query) use ($studentId) {
                $query->where('student_id', $studentId);
            })
            ->whereIn('area', ['optometria', 'optometría'])
            ->latest('id')
            ->first();

        return is_array($result?->data) ? $result->data : [];
    }

    private function canEditEntry(ClinicalHistory $entry): bool
    {
        $isAdmin = $this->normalizedRoleName() === 'administrador';

        if ($isAdmin) {
            return true;
        }

        return (int) $entry->user_id === (int) Auth::id();
    }

    private function ensurePdfAccess(): void
    {
        $role = $this->normalizedRoleName();

        if (!in_array($role, ['administrador', 'admisión', 'admision'], true)) {
            abort(403, 'Solo Admisión y Administrador pueden descargar este PDF.');
        }
    }

    private function resolveGraphicSourceForPdf(ExamResult $result): ?string
    {
        $candidate = $result->chart_path;

        if (!$candidate && is_array($result->data)) {
            $candidate = $result->data['audiogram_path']
                ?? $result->data['odontograma_path']
                ?? null;
        }

        if (!$candidate || !is_string($candidate)) {
            return null;
        }

        if (Str::startsWith($candidate, 'data:image')) {
            return $candidate;
        }

        $normalizedPath = ltrim(str_replace(['public/storage/', 'storage/'], '', $candidate), '/');

        // Mismo enfoque del reporte final: probar storage/app/public primero.
        $storageAppPath = storage_path('app/public/' . $normalizedPath);
        if (file_exists($storageAppPath)) {
            return $storageAppPath;
        }

        // Fallback a public/storage.
        $publicStoragePath = public_path('storage/' . $normalizedPath);
        if (file_exists($publicStoragePath)) {
            return $publicStoragePath;
        }

        // Último intento vía disco configurado (por si el path es distinto).
        if (!Storage::disk('public')->exists($normalizedPath)) {
            return null;
        }

        return public_path('storage/' . $normalizedPath);
    }

    private function normalizeAreaKey(?string $area): string
    {
        $slug = Str::slug((string) $area, '_');

        return match ($slug) {
            'valoracion_medica', 'valoracion-medica', 'medicina_general', 'medicina-general', 'medicina', 'medico', 'medica' => 'valoracion_medica',
            'optometria', 'optometria_' => 'optometria',
            'fonoaudiologia', 'fonoaudiologia_' => 'fonoaudiologia',
            'psicologia', 'psicologia_' => 'psicologia',
            'audiometria', 'audiometria_' => 'audiometria',
            'odontologia', 'odontologia_' => 'odontologia',
            default => $slug,
        };
    }

    private function areaLabel(string $areaKey): string
    {
        return match ($areaKey) {
            'valoracion_medica' => 'Valoración Médica',
            'optometria' => 'Optometría',
            'audiometria' => 'Audiometría',
            'odontologia' => 'Odontología',
            'fonoaudiologia' => 'Fonoaudiología',
            'psicologia' => 'Psicología',
            default => Str::title(str_replace('_', ' ', $areaKey)),
        };
    }

    private function areaSectionTitle(string $areaKey): string
    {
        return match ($areaKey) {
            'audiometria' => 'Tamiz Auditivo',
            'odontologia' => 'Tamiz Odontologico',
            default => $this->areaLabel($areaKey),
        };
    }
}
