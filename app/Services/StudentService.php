<?php

namespace App\Services;

use App\Models\Student;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class StudentService
{
    /**
     * Registra un estudiante y crea su circuito médico en una sola transacción.
     */
    public function registerWithMedicalCircuit(array $data)
    {
        return DB::transaction(function () use ($data) {
            // 1. Crear el Estudiante
            // Filtramos el array para que solo pasen los datos que existen en la tabla students
            // (Nombres, apellidos, documento, datos del acudiente, etc.)
            $studentData = collect($data)->except(['requested_areas', 'observations'])->toArray();
            $student = Student::create($studentData);

            // 2. Normalizar las áreas solicitadas
            // Transformamos los nombres a slugs (ej: "Valoración Médica" -> "valoracion_medica")
            $requestedAreas = $data['requested_areas'] ?? [
                'valoracion_medica',
                'odontologia',
                'optometria',
                'fonoaudiologia',
                'audiometria',
                'psicologia'
            ];

            $normalizedAreas = collect($requestedAreas)
                ->map(fn($area) => Str::slug($area, '_'))
                ->filter()
                ->unique()
                ->values()
                ->toArray();

            // 3. Crear el Examen Médico (Circuito Inicial)
            // Vinculamos el user_id del administrador que está logueado
            // Estado inicial: 'pendiente' → cambiará a 'en_proceso' cuando un médico lo active
            $student->medicalExams()->create([
                'user_id'         => Auth::id(),
                'requested_areas' => $normalizedAreas,
                'status'          => 'pendiente',
                'observations'    => $data['observations'] ?? 'Inicio de proceso de ingreso.',
            ]);

            // Cargamos la relación para devolver el objeto completo
            return $student->load('medicalExams');
        });
    }
}
