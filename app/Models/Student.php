<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use HasFactory, SoftDeletes;

    protected $casts = [
        'birth_date' => 'date',
    ];

    /**
     * Atributos asignables masivamente.
     */
    protected $fillable = [
        // Datos del Estudiante
        'document_type',
        'document_number',
        'first_name',
        'last_name',
        'birth_date',
        'age',
        'gender',
        'previous_school',
        'grade',

        // Datos del Acudiente
        'guardian_name',
        'guardian_lastname',
        'guardian_document',
        'guardian_age',
        'guardian_phone',
        'guardian_address',
        'guardian_relationship',
        'guardian_email',
    ];

    /* |--------------------------------------------------------------------------
    | Relaciones Eloquent
    |-------------------------------------------------------------------------- */

    /**
     * Historial completo de procesos médicos.
     */
    public function medicalExams(): HasMany
    {
        return $this->hasMany(MedicalExam::class);
    }

    /**
     * Entradas del historial clinico por especialistas.
     */
    public function clinicalHistories(): HasMany
    {
        return $this->hasMany(ClinicalHistory::class);
    }

    /**
     * El examen que está actualmente en proceso (no completado).
     */
    public function currentExam(): HasOne
    {
        return $this->hasOne(MedicalExam::class)
            ->where('status', '!=', 'completado')
            ->latestOfMany();
    }

    /* |--------------------------------------------------------------------------
    | Accessors & Mutators (Sintaxis Moderna)
    |-------------------------------------------------------------------------- */

    /**
     * Nombre completo del estudiante.
     * Uso: $student->full_name
     */
    protected function fullName(): Attribute
    {
        return Attribute::make(
            get: fn () => "{$this->first_name} {$this->last_name}",
        );
    }

    /**
     * Nombre completo del acudiente.
     * Uso: $student->guardian_full_name
     */
    protected function guardianFullName(): Attribute
    {
        return Attribute::make(
            get: fn () => "{$this->guardian_name} {$this->guardian_lastname}",
        );
    }

    /**
     * Documento formateado con mayúsculas.
     * Uso: $student->full_document
     */
    protected function fullDocument(): Attribute
    {
        return Attribute::make(
            get: fn () => strtoupper("{$this->document_type} - {$this->document_number}"),
        );
    }

    /**
     * Mutator para asegurar que los nombres siempre se guarden con la primera letra en mayúscula.
     */
    protected function firstName(): Attribute
    {
        return Attribute::make(
            set: fn (string $value) => mb_convert_case(trim($value), MB_CASE_TITLE, "UTF-8"),
        );
    }
}
