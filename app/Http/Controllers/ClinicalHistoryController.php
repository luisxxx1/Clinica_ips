<?php

namespace App\Http\Controllers;

use App\Models\ClinicalHistory;
use App\Models\Student;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

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

        $student->load([
            'clinicalHistories' => function ($query) {
                $query->with('specialist.role')
                    ->latest('recorded_at')
                    ->latest('id');
            },
        ]);

        return view('clinical_histories.show', [
            'student' => $student,
            'currentAreaLabel' => $this->currentAreaLabel(),
            'defaultClinicalTitle' => $this->defaultClinicalTitle(),
            'defaultClinicalEntry' => $this->defaultClinicalEntry(),
        ]);
    }

    public function store(Request $request, Student $student)
    {
        $this->ensureAccess();

        $validated = $request->validate([
            'title' => 'nullable|string|max:150',
            'entry' => 'required|string|max:3000',
            'recorded_at' => 'nullable|date',
        ]);

        ClinicalHistory::create([
            'student_id' => $student->id,
            'user_id' => Auth::id(),
            'area' => $this->currentAreaSlug(),
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

        if ((int) $clinical_history->student_id !== (int) $student->id) {
            abort(404);
        }

        if (!$this->canEditEntry($clinical_history)) {
            abort(403, 'No tienes permiso para editar esta nota clínica.');
        }

        $validated = $request->validate([
            'title' => 'nullable|string|max:150',
            'entry' => 'required|string|max:3000',
            'recorded_at' => 'nullable|date',
        ]);

        $clinical_history->update([
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

        $pdf = Pdf::loadView('clinical_histories.pdf', [
            'student' => $student,
            'entries' => $student->clinicalHistories,
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

    private function defaultClinicalEntry(): string
    {
        return match ($this->currentAreaSlug()) {
            'audiometria' => "A la valoración auditiva mediante audiometría comportamental de tonos puros realizada en la IPS NO se evidencian dificultades auditivas presentando Normoacusia.\n"
                . "OBSERVACIONES/RECOMENDACIONES:\n"
                . "- Durante la exploración física se evidencia conducto auditivo normal sin lesión, cera fisiológica normal.\n"
                . "- Mantener controles auditivos preventivos de manera periódica, especialmente en edad escolar.\n"
                . "- Evitar la exposición a ruidos intensos o prolongados (volumen elevado en televisión, dispositivos electrónicos o ambientes ruidosos).\n"
                . "- Evitar la introducción de objetos extraños en el conducto auditivo externo.\n"
                . "- Observar posibles signos de alerta como dificultad para seguir instrucciones, aumento del volumen al escuchar, o necesidad de repetición frecuente.",

            'valoracion_medica', 'medicina_general' => "Paciente valorado en Medicina General. Estado clínico general dentro de parámetros esperados para la edad, sin hallazgos de alarma al momento de la consulta.\n"
                . "OBSERVACIONES/RECOMENDACIONES:\n"
                . "- Mantener hábitos de vida saludable (alimentación balanceada, hidratación y actividad física).\n"
                . "- Continuar controles médicos preventivos periódicos.\n"
                . "- Acudir nuevamente si aparecen síntomas nuevos o cambios clínicos relevantes.",

            'odontologia' => "Paciente valorado en Odontología. No se evidencian alteraciones odontológicas significativas al momento del examen clínico, con estado oral general dentro de la normalidad.\n"
                . "OBSERVACIONES/RECOMENDACIONES:\n"
                . "- Mantener higiene oral diaria (cepillado después de cada comida y uso de seda dental).\n"
                . "- Control odontológico preventivo cada 6 meses.\n"
                . "- Reducir consumo frecuente de azúcares para prevención de caries.",

            'optometria' => "Paciente valorado en Optometría. Agudeza visual y valoración ocular sin hallazgos significativos, compatible con estado visual funcional dentro de parámetros normales.\n"
                . "OBSERVACIONES/RECOMENDACIONES:\n"
                . "- Mantener controles visuales periódicos según edad escolar.\n"
                . "- Promover higiene visual (descansos en pantallas, buena iluminación y distancia adecuada de lectura).\n"
                . "- Consultar nuevamente ante síntomas como visión borrosa, cefalea o fatiga visual.",

            'fonoaudiologia' => "Paciente valorado por Fonoaudiología. Se observan procesos comunicativos y de lenguaje acordes a la etapa del desarrollo, sin alteraciones evidentes durante la evaluación.\n"
                . "OBSERVACIONES/RECOMENDACIONES:\n"
                . "- Estimular hábitos de comunicación en el entorno familiar y escolar.\n"
                . "- Mantener seguimiento preventivo del desarrollo del lenguaje.\n"
                . "- Revalorar en caso de notar cambios en articulación, comprensión o fluidez verbal.",

            'psicologia' => "Paciente valorado por Psicología. Durante la consulta se evidencia adaptación emocional y conductual adecuada para su contexto escolar, sin signos de riesgo psicológico inmediato.\n"
                . "OBSERVACIONES/RECOMENDACIONES:\n"
                . "- Fortalecer rutinas de sueño, estudio y recreación saludable.\n"
                . "- Mantener acompañamiento familiar y comunicación asertiva en casa.\n"
                . "- Solicitar nueva valoración si se identifican cambios marcados en estado de ánimo o conducta.",

            default => "Paciente evaluado por el área correspondiente. A la fecha no se evidencian hallazgos clínicos de alarma y el estado general se encuentra dentro de parámetros esperados.\n"
                . "OBSERVACIONES/RECOMENDACIONES:\n"
                . "- Continuar controles preventivos periódicos.\n"
                . "- Mantener medidas de autocuidado y hábitos saludables.\n"
                . "- Reconsultar ante cualquier síntoma nuevo o cambio clínico.",
        };
    }

    private function defaultClinicalTitle(): string
    {
        return match ($this->currentAreaSlug()) {
            'audiometria' => 'Evolucion normal - Audiometria',
            'valoracion_medica', 'medicina_general' => 'Evolucion normal - Medicina General',
            'odontologia' => 'Evolucion normal - Odontologia',
            'optometria' => 'Evolucion normal - Optometria',
            'fonoaudiologia' => 'Evolucion normal - Fonoaudiologia',
            'psicologia' => 'Evolucion normal - Psicologia',
            default => 'Evolucion clinica - Control',
        };
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
}
