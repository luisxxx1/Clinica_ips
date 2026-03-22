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

    /**
     * Relación con el profesional (Especialista).
     */
    public function specialist(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Alias para evitar RelationNotFoundException (Imagen 878587)
     */
    public function user(): BelongsTo
    {
        // Retornamos directamente la relación para que funcione el eager loading
        return $this->belongsTo(User::class, 'user_id');
    }

    /* |--------------------------------------------------------------------------
    | Accessors & Mutators (Laravel 9+)
    |-------------------------------------------------------------------------- */

    protected function area(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => ucfirst(str_replace('_', ' ', $value ?? '')),
            set: fn ($value) => strtolower(trim($value ?? '')),
        );
    }

    protected function imc(): Attribute
    {
        return Attribute::make(
            get: function () {
                // Navegación segura en el array JSON
                return $this->data['biometria']['imc'] 
                    ?? $this->data['antropometria']['imc'] 
                    ?? $this->data['imc'] 
                    ?? 'N/A';
            },
        );
    }

    protected function statusColor(): Attribute
    {
        return Attribute::make(
            get: function () {
                $status = $this->data['biometria']['imc_status'] 
                    ?? $this->data['antropometria']['imc_status'] 
                    ?? $this->data['imc_status'] 
                    ?? 'Desconocido';

                return match (trim($status)) {
                    'Normal'    => 'text-green-700 bg-green-100 border-green-200',
                    'Sobrepeso' => 'text-yellow-700 bg-yellow-100 border-yellow-200',
                    'Obesidad'  => 'text-red-700 bg-red-100 border-red-200',
                    'Bajo Peso' => 'text-orange-700 bg-orange-100 border-orange-200',
                    default     => 'text-slate-500 bg-slate-100 border-slate-200',
                };
            },
        );
    }

    /* |--------------------------------------------------------------------------
    | Métodos de Utilidad
    |-------------------------------------------------------------------------- */

    public function isArea(string $areaName): bool
    {
        // Comparamos contra el valor bruto de la base de datos (slug)
        $rawArea = $this->getAttributes()['area'] ?? '';
        return $rawArea === strtolower(trim($areaName));
    }
}