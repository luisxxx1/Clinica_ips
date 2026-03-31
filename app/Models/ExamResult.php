<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ExamResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'medical_exam_id',
        'user_id',
        'area',
        'data',
        'notes',
        'chart_path', // Ruta de la imagen del audiograma
        'pta_od',     // Promedio Tonos Audibles Oído Derecho
        'pta_oi',     // Promedio Tonos Audibles Oído Izquierdo
    ];

    protected $casts = [
        'data'            => 'array',
        'medical_exam_id' => 'integer',
        'user_id'         => 'integer',
        'pta_od'          => 'float',
        'pta_oi'          => 'float',
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
     * Normalización del área para lógica interna (slug).
     */
    protected function area(): Attribute
    {
        return Attribute::make(
            get: fn($value) => Str::slug($value ?? '', '_'),
            set: fn($value) => Str::slug($value ?? '', '_'),
        );
    }

    /**
     * Accessor de PRESENTACIÓN para las vistas.
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
     * Genera la URL completa para la imagen del gráfico de audiometría.
     * Úsalo en Blade: <img src="{{ $result->chart_url }}">
     */
    protected function chartUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->chart_path ? Storage::url($this->chart_path) : null,
        );
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
     */
    public function isArea(string $areaName): bool
    {
        $rawArea = $this->getAttributes()['area'] ?? '';
        return $rawArea === Str::slug($areaName, '_');
    }
}
