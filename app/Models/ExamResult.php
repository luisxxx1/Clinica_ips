<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ExamResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'medical_exam_id',
        'user_id',
        'area',
        'data',
        'notes',
    ];

    protected $casts = [
        'data' => 'array',
        'medical_exam_id' => 'integer',
        'user_id' => 'integer',
    ];

    /* |--------------------------------------------------------------------------
    | Relaciones Eloquent
    |-------------------------------------------------------------------------- */

    public function medicalExam(): BelongsTo
    {
        return $this->belongsTo(MedicalExam::class);
    }

    public function specialist(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /* |--------------------------------------------------------------------------
    | Accessors & Mutators
    |-------------------------------------------------------------------------- */

    /**
     * Normalización del nombre del área.
     */
    protected function area(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => ucfirst(str_replace('_', ' ', $value)), // Ej: medicina_general -> Medicina general
            set: fn (string $value) => strtolower(trim($value)),
        );
    }

    /**
     * Accessor virtual para el IMC.
     * Busca en diferentes niveles del JSON para mayor compatibilidad.
     */
    protected function imc(): Attribute
    {
        return Attribute::make(
            get: function () {
                return $this->data['biometria']['imc'] 
                    ?? $this->data['antropometria']['imc'] 
                    ?? $this->data['imc'] 
                    ?? 'N/A';
            },
        );
    }

    /**
     * Accessor para el color del estado del IMC (Tailwind Classes)
     */
    protected function statusColor(): Attribute
    {
        return Attribute::make(
            get: function () {
                // Buscamos el estado en el JSON
                $status = $this->data['biometria']['imc_status'] 
                    ?? $this->data['antropometria']['imc_status'] 
                    ?? $this->data['imc_status'] 
                    ?? 'Desconocido';

                return match (trim($status)) {
                    'Normal'      => 'text-green-700 bg-green-100 border-green-200',
                    'Sobrepeso'   => 'text-yellow-700 bg-yellow-100 border-yellow-200',
                    'Obesidad'    => 'text-red-700 bg-red-100 border-red-200',
                    'Bajo Peso'   => 'text-orange-700 bg-orange-100 border-orange-200',
                    default       => 'text-slate-500 bg-slate-100 border-slate-200',
                };
            },
        );
    }

    /* |--------------------------------------------------------------------------
    | Métodos de Utilidad
    |-------------------------------------------------------------------------- */

    /**
     * Verifica si el resultado pertenece a un área específica.
     */
    public function isArea(string $areaName): bool
    {
        // Usamos getAttributes() para obtener el valor real de la base de datos (slug)
        // y evitar interferencia con el ucfirst del Accessor.
        return $this->getAttributes()['area'] === strtolower($areaName);
    }
}