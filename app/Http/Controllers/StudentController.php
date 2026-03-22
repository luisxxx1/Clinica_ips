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
        // Usamos simplePaginate o paginate para mejorar el rendimiento si la tabla crece
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
            $this->studentService->registerWithMedicalCircuit($request->validated());

            return redirect()->route('students.index')
                ->with('success', 'Estudiante matriculado y circuito iniciado con éxito.');

        } catch (\Exception $e) {
            Log::error("Error en matrícula de estudiante: " . $e->getMessage());
            
            return back()->withInput()->withErrors(['error' => 'Error real: ' . $e->getMessage()]);
        }
    }

    public function show(Student $student)
    {
        // Cargamos relaciones y ordenamos los exámenes por fecha
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
            // Actualizamos los datos validados
            $student->update($request->validated());

            return redirect()->route('students.index')
                ->with('success', "La ficha de {$student->first_name} ha sido actualizada.");
        } catch (\Exception $e) {
            Log::error("Error al actualizar estudiante ID {$student->id}: " . $e->getMessage());
            return back()->withInput()->withErrors(['error' => 'Error al actualizar los datos.']);
        }
    }

    /**
     * Buscador dinámico optimizado.
     */
    public function search(Request $request)
    {
        $query = $request->get('query');

        // Si no hay consulta, devolvemos vacío para evitar cargar toda la DB
        if (empty($query)) {
            return response()->json([]);
        }

        $students = Student::where(function($q) use ($query) {
            $q->where('first_name', 'LIKE', "%{$query}%")
              ->orWhere('last_name', 'LIKE', "%{$query}%")
              ->orWhere('document_number', 'LIKE', "%{$query}%");
        })
        ->limit(10)
        ->get(['id', 'first_name', 'last_name', 'document_number']); // Solo traemos campos necesarios

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