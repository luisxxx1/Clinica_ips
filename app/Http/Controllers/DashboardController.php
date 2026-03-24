<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\MedicalExam;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $isAdminOrAdmision = $user && in_array($user->role->name, ['Administrador', 'Admisión']);

        // ✅ Si es usuario médico, redirigir directamente a su bandeja de pacientes
        if (!$isAdminOrAdmision) {
            return redirect()->route('medical_exams.index');
        }

        $totalPacientes = Student::count();

        try {
            // Conteos para las Cards del Dashboard
            $totalCertificados = MedicalExam::where('status', 'completado')->count();
            $pendientes = MedicalExam::where('status', 'en_proceso')->count();

            // ESTA ES LA CONEXIÓN CLAVE:
            // Obtenemos los exámenes que ya pasaron por los 6 médicos
            // Cargamos 'student' y 'results' para que Admisión vea todo de una vez
            $examenesListos = MedicalExam::with(['student', 'results.specialist'])
                ->where('status', 'completado')
                ->latest()
                ->take(10) // Mostramos los últimos 10 terminados
                ->get();

            // Datos del Circuito Médico para Administrador y Admisión
            $circuitoMedico = [
                'pendiente' => MedicalExam::where('status', 'pendiente')->count(),
                'en_proceso' => MedicalExam::where('status', 'en_proceso')->count(),
                'completado' => MedicalExam::where('status', 'completado')->count(),
                'total' => MedicalExam::count()
            ];

        } catch (\Exception $e) {
            $totalCertificados = 0;
            $pendientes = 0;
            $examenesListos = collect();
            $circuitoMedico = ['pendiente' => 0, 'en_proceso' => 0, 'completado' => 0, 'total' => 0];
        }

        return view('dashboard', compact(
            'totalPacientes',
            'totalCertificados',
            'pendientes',
            'examenesListos',
            'isAdminOrAdmision',
            'circuitoMedico'
        ));
    }

    public function followup()
    {
        $user = Auth::user();
        $isAdminOrAdmision = $user && in_array($user->role->name, ['Administrador', 'Admisión']);

        if (!$isAdminOrAdmision) {
            return redirect()->route('medical_exams.index');
        }

        [$circuitoMedico, $faltantesPorArea, $faltantesPorEstudiante] = $this->buildFollowupData();

        return view('followup', compact(
            'isAdminOrAdmision',
            'circuitoMedico',
            'faltantesPorArea',
            'faltantesPorEstudiante'
        ));
    }

    private function buildFollowupData(): array
    {
        $circuitoMedico = [
            'pendiente' => MedicalExam::where('status', 'pendiente')->count(),
            'en_proceso' => MedicalExam::where('status', 'en_proceso')->count(),
            'completado' => MedicalExam::where('status', 'completado')->count(),
            'total' => MedicalExam::count(),
        ];

        $nombresArea = [
            'valoracion_medica' => 'Medicina General',
            'odontologia' => 'Odontologia',
            'optometria' => 'Optometria',
            'audiometria' => 'Audiometria',
            'fonoaudiologia' => 'Fonoaudiologia',
            'psicologia' => 'Psicologia',
        ];

        $examenesActivos = MedicalExam::with(['student', 'results'])
            ->whereIn('status', ['pendiente', 'en_proceso'])
            ->whereHas('student')
            ->latest()
            ->get();

        $faltantesPorArea = collect($nombresArea)->mapWithKeys(fn ($label) => [$label => 0])->toArray();

        $faltantesPorEstudiante = $examenesActivos->map(function ($exam) use (&$faltantesPorArea, $nombresArea) {
            $requeridas = collect($exam->requested_areas ?? [])->map(fn ($a) => Str::slug($a, '_'));
            $completadas = $exam->results->pluck('area')->map(fn ($a) => Str::slug($a, '_'));

            $faltantesSlugs = $requeridas->diff($completadas)->values();
            $faltantesNombres = $faltantesSlugs
                ->map(fn ($slug) => $nombresArea[$slug] ?? Str::headline(str_replace('_', ' ', $slug)))
                ->values();

            foreach ($faltantesNombres as $areaNombre) {
                $faltantesPorArea[$areaNombre] = ($faltantesPorArea[$areaNombre] ?? 0) + 1;
            }

            return [
                'exam_id' => $exam->id,
                'student_id' => $exam->student->id,
                'estudiante' => $exam->student->full_name,
                'estado' => $exam->status,
                'faltantes' => $faltantesNombres,
                'faltantes_count' => $faltantesNombres->count(),
            ];
        })->filter(fn ($row) => $row['faltantes']->isNotEmpty())
            ->take(20)
            ->values();

        return [$circuitoMedico, $faltantesPorArea, $faltantesPorEstudiante];
    }
}
