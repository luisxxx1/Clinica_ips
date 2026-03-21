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
            // Usamos only() para asegurarnos de que solo entren datos de la tabla students
            $student = Student::create($data);

            // 2. Normalizar las áreas solicitadas
            // Transformamos "Medicina General" en "medicina_general" para la lógica de la BD
            $requestedAreas = $data['requested_areas'] ?? [];
            
            $normalizedAreas = collect($requestedAreas)
                ->map(fn($area) => Str::slug($area, '_'))
                ->filter() // Eliminamos valores vacíos si los hay
                ->values()
                ->toArray();

            // 3. Crear el Examen Médico (Circuito Inicial)
            $student->medicalExams()->create([
                'user_id'         => Auth::id() ?? 1, // Fallback al ID 1 (Admin) si no hay sesión
                'requested_areas' => $normalizedAreas,
                'status'          => 'pendiente',
                'observations'    => $data['observations'] ?? 'Inicio de proceso de ingreso.',
            ]);

            // Cargamos la relación para devolver el objeto completo
            return $student->load('medicalExams');
        });
    }
}