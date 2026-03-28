<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

class StoreStudentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Se deja en true para permitir la validación.
        // La seguridad por roles se maneja usualmente en el Controller o Middleware.
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        // Obtenemos el ID del estudiante si estamos en una ruta de actualización (PATCH/PUT)
        // Esto es necesario para que la regla 'unique' no falle al editar el mismo registro.
        $studentId = $this->route('student') ? $this->route('student')->id : null;

        return [
            // Datos del Estudiante
            'document_type'   => ['required', Rule::in(['TI', 'CC', 'RC', 'CE'])], // Añadí CE (Cédula Extranjería)
            'document_number' => [
                'required',
                'string',
                'max:20',
                // Corregido: Si es un objeto, extraemos el ID. Si es creación, ignoramos null.
                Rule::unique('students', 'document_number')->ignore($studentId),
            ],
            'first_name'      => 'required|string|max:100',
            'last_name'       => 'required|string|max:100',
            'birth_date'      => 'required|date|before_or_equal:today',
            'age'             => 'nullable|integer|min:3|max:25', // Se calcula automáticamente desde fecha de nacimiento
            'gender'          => ['required', Rule::in(['Masculino', 'Femenino', 'Otro'])],
            'previous_school' => 'nullable|string|max:255', // Cambiado a nullable por si es su primer colegio
            'grade'           => 'required|string|max:50',

            // Datos Acudiente
            'guardian_name'         => 'required|string|max:100',
            'guardian_lastname'     => 'required|string|max:100',
            'guardian_document'     => 'required|string|max:20',
            'guardian_age'          => 'required|integer|min:18', // Validación de mayoría de edad
            'guardian_phone'        => 'required|string|max:20',
            'guardian_address'      => 'required|string|max:255',
            'guardian_relationship' => 'required|string|max:50',
            'guardian_email'        => 'required|email|max:100',

            // Circuito Médico
            'requested_areas'       => 'required|array|min:1',
            'requested_areas.*'     => 'string',
        ];
    }

    /**
     * Personalizar los nombres de los atributos para los mensajes de error.
     */
    public function attributes(): array
    {
        return [
            'document_type' => 'tipo de documento',
            'document_number' => 'número de documento',
            'first_name' => 'nombres',
            'last_name' => 'apellidos',
            'birth_date' => 'fecha de nacimiento',
            'requested_areas' => 'áreas de valoración',
            'guardian_email' => 'correo del acudiente',
        ];
    }

    protected function prepareForValidation(): void
    {
        $birthDate = $this->input('birth_date');

        if (!$birthDate) {
            return;
        }

        try {
            $calculatedAge = Carbon::parse($birthDate)->age;
            $this->merge(['age' => $calculatedAge]);
        } catch (\Throwable $e) {
            // Si la fecha es inválida, dejamos que la regla de birth_date responda.
        }
    }
}
