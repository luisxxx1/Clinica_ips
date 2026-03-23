<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Http\Requests\StoreStudentRequest;
use App\Services\StudentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StudentController extends Controller
{
    protected $studentService;

    public function __construct(StudentService $studentService)
    {
        $this->studentService = $studentService;
    }

    public function index()
    {
        $students = Student::latest()->paginate(10);
        return view('students.index', compact('students'));
    }

    public function create()
    {
        return view('students.create');
    }

    public function store(StoreStudentRequest $request)
    {
        try {
            // El servicio se encarga de la transacción DB (Estudiante + MedicalExam)
            // Asegúrate de que tu StudentService esté preparado para recibir:
            // guardian_name, guardian_lastname, guardian_document, guardian_age,
            // guardian_phone, guardian_relationship, guardian_address y guardian_email
            $this->studentService->registerWithMedicalCircuit($request->validated());

            return redirect()->route('students.index')
                ->with('success', 'Estudiante matriculado y circuito iniciado con éxito.');

        } catch (\Exception $e) {
            // Logueamos el error técnico para SnakeDev
            Log::error("Error en matrícula de estudiante: " . $e->getMessage());

            // Devolvemos el error real para que sepas si falta una columna en la DB
            return back()->withInput()->withErrors(['error' => 'Error en el proceso: ' . $e->getMessage()]);
        }
    }

    public function show(Student $student)
    {
        $student->load(['medicalExams' => function($query) {
            $query->latest();
        }, 'medicalExams.results.user']);

        return view('students.show', compact('student'));
    }

    public function edit(Student $student)
    {
        return view('students.edit', compact('student'));
    }

    public function update(StoreStudentRequest $request, Student $student)
    {
        try {
            $student->update($request->validated());

            return redirect()->route('students.index')
                ->with('success', "La ficha de {$student->first_name} ha sido actualizada.");
        } catch (\Exception $e) {
            Log::error("Error al actualizar estudiante ID {$student->id}: " . $e->getMessage());
            return back()->withInput()->withErrors(['error' => 'Error al actualizar los datos.']);
        }
    }

    public function search(Request $request)
    {
        $query = $request->input('query');

        if (empty($query)) {
            return response()->json([]);
        }

        $students = Student::where(function($q) use ($query) {
            $q->where('first_name', 'LIKE', "%{$query}%")
              ->orWhere('last_name', 'LIKE', "%{$query}%")
              ->orWhere('document_number', 'LIKE', "%{$query}%");
        })
        ->limit(10)
        ->get(['id', 'first_name', 'last_name', 'document_number']);

        return response()->json($students);
    }

    public function destroy(Student $student)
    {
        try {
            $student->delete();
            return redirect()->route('students.index')
                ->with('success', 'Registro movido a la papelera (Soft Delete).');
        } catch (\Exception $e) {
            Log::error("Error al eliminar estudiante: " . $e->getMessage());
            return back()->with('error', 'No se pudo eliminar el registro.');
        }
    }
}
