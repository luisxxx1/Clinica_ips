<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\MedicalExam;

class DashboardController extends Controller
{
    public function index()
    {
        $totalPacientes = Student::count();

        try {
            // Conteos para las Cards del Dashboard
            $totalCertificados = MedicalExam::where('status', 'completado')->count();
            $pendientes = MedicalExam::where('status', 'en_proceso')->count();

            // ESTA ES LA CONEXIÓN CLAVE:
            // Obtenemos los exámenes que ya pasaron por los 6 médicos
            // Cargamos 'student' y 'results' para que Admisión vea todo de una vez
            $examenesListos = MedicalExam::with(['student', 'results.specialist'])
                ->where('status', 'completado')
                ->latest()
                ->take(10) // Mostramos los últimos 10 terminados
                ->get();

        } catch (\Exception $e) {
            $totalCertificados = 0;
            $pendientes = 0;
            $examenesListos = collect(); // Colección vacía para que no de error en la vista
        }

        return view('dashboard', compact(
            'totalPacientes',
            'totalCertificados',
            'pendientes',
            'examenesListos'
        ));
    }
}
