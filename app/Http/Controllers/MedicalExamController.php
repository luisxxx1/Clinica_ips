<?php

namespace App\Http\Controllers;

use App\Models\MedicalExam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MedicalExamController extends Controller
{
    public function index()
    {
        $userArea = Auth::user()->role->name ?? 'Personal Médico';
        $userAreaSlug = Str::slug($userArea, '_');

        // Filtramos exámenes pendientes que requieran el área del usuario logueado
        $pendingExams = MedicalExam::with('student')
            ->where('status', '!=', 'completado')
            ->whereJsonContains('requested_areas', $userAreaSlug) 
            ->latest()
            ->get();

        $exams = MedicalExam::with('student')->latest()->paginate(15);

        return view('medical_exams.index', compact('exams', 'pendingExams', 'userArea'));
    }

    public function evaluate(MedicalExam $medical_exam)
    {
        $userArea = Str::slug(Auth::user()->role->name, '_');
        $requested = collect($medical_exam->requested_areas)->map(fn($area) => Str::slug($area, '_'));

        if (!$requested->contains($userArea)) {
            return redirect()->route('medical_exams.index')
                ->with('error', "Tu área no está asignada para evaluar a este estudiante.");
        }

        return view('medical_exams.evaluate', compact('medical_exam', 'userArea'));
    }

    public function storeResult(Request $request, MedicalExam $medical_exam)
    {
        // Validamos que al menos las notas (conclusión) estén presentes
        $request->validate([
            'notes' => 'required|string|min:5',
        ]);

        $userArea = Str::slug(Auth::user()->role->name, '_');

        return DB::transaction(function () use ($medical_exam, $userArea, $request) {
            
            /**
             * NORMALIZACIÓN DE DATOS:
             * En los formularios usamos name="result[campo]". 
             * Capturamos todo ese array 'result' directamente para el JSON.
             */
            $data = $request->input('result', []);

            // Guardar o actualizar el resultado de esta área específica
            $medical_exam->results()->updateOrCreate(
                ['area' => $userArea],
                [
                    'user_id' => Auth::id(),
                    'data'    => $data, // Se guarda el JSON con lo que venga del form (medicina, psico, etc)
                    'notes'   => $request->notes,
                ]
            );

            // Lógica de avance del circuito médico
            $requestedAreas = collect($medical_exam->requested_areas)->map(fn($a) => Str::slug($a, '_'));
            $completedAreas = $medical_exam->results()->pluck('area');

            // Verificamos si todas las áreas requeridas ya tienen un resultado
            $isFullyCompleted = $requestedAreas->every(fn($area) => $completedAreas->contains($area));

            if ($isFullyCompleted) {
                $medical_exam->update(['status' => 'completado']);
                $msg = "¡Circuito médico finalizado! Todas las áreas han evaluado al estudiante.";
            } else {
                $medical_exam->update(['status' => 'en_proceso']);
                $msg = "Valoración de " . Auth::user()->role->name . " guardada correctamente.";
            }

            return redirect()->route('medical_exams.index')->with('success', $msg);
        });
    }

    public function history()
    {
        $exams = MedicalExam::with(['student', 'results.user'])
            ->where('status', 'completado')
            ->latest()
            ->paginate(20);

        return view('medical_exams.history', compact('exams'));
    }
}