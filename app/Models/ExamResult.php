<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

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
        'data'            => 'array',
        'medical_exam_id' => 'integer',
        'user_id'         => 'integer',
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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /* |--------------------------------------------------------------------------
    | Accessors & Mutators
    |-------------------------------------------------------------------------- */

    /**
     * CORRECCIÓN CRÍTICA: separamos el accessor de presentación del valor real.
     *
     * ANTES: el accessor devolvía "Valoracion medica" (con mayúscula y espacio)
     * y el controlador comparaba ese valor contra el slug "valoracion_medica"
     * → nunca coincidían → el circuito nunca se marcaba como completado.
     *
     * AHORA:
     * - get  → devuelve el slug limpio (ej: "valoracion_medica") para comparaciones
     * - set  → guarda el slug limpio en BD
     * - Para mostrar en vistas usa el accessor 'areaLabel' (ver abajo)
     */
    protected function area(): Attribute
    {
        return Attribute::make(
            get: fn($value) => Str::slug($value ?? '', '_'),  // ← slug limpio para lógica
            set: fn($value) => Str::slug($value ?? '', '_'),  // ← slug limpio en BD
        );
    }

    /**
     * Accessor de PRESENTACIÓN para las vistas.
     * Úsalo en Blade así: {{ $result->area_label }}
     * Ejemplos: "valoracion_medica" → "Valoración médica"
     */
    public function getAreaLabelAttribute(): string
    {
        $labels = [
            'valoracion_medica' => 'Valoración médica',
            'odontologia'       => 'Odontología',
            'optometria'        => 'Optometría',
            'audiometria'       => 'Audiometría',
            'fonoaudiologia'    => 'Fonoaudiología',
            'psicologia'        => 'Psicología',
        ];

        $slug = $this->getAttributes()['area'] ?? '';
        return $labels[$slug] ?? ucfirst(str_replace('_', ' ', $slug));
    }

    /**
     * IMC desde el JSON de data (navegación segura).
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
     * Color del badge de estado IMC para Tailwind.
     */
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

    /**
     * Compara el área contra un slug dado.
     * Usa el valor RAW de BD (no el accessor) para comparación exacta.
     */
    public function isArea(string $areaName): bool
    {
        $rawArea = $this->getAttributes()['area'] ?? '';
        return $rawArea === Str::slug($areaName, '_');
    }
}
