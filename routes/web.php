<?php

use App\Http\Controllers\{ProfileController, StudentController, MedicalExamController, DashboardController, AdminSettingsController};
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => redirect()->route('login'));

Route::middleware(['auth', 'verified'])->group(function () {
    
    // Dashboard Principal
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    /* --- PERFIL DEL USUARIO --- */
    Route::controller(ProfileController::class)->group(function () {
        Route::get('/profile', 'edit')->name('profile.edit');
        Route::patch('/profile', 'update')->name('profile.update');
        Route::delete('/profile', 'destroy')->name('profile.destroy');
    });

    /* --- ADMINISTRACIÓN (Cali - SnakeDEV) --- */
    Route::prefix('admin')->name('admin.')->middleware('role:Administrador')->group(function () {
        Route::controller(AdminSettingsController::class)->group(function () {
            Route::get('/settings', 'index')->name('settings');
            Route::post('/settings/update-branding', 'updateBranding')->name('update_branding');
            Route::patch('/settings/user/{user}', 'updateUser')->name('user.update');
            Route::post('/reset-access', 'resetAccess')->name('reset_access');
            Route::get('/roles/colors', 'editRoleColors')->name('role_colors');
            Route::delete('/users/{user}/permissions', 'revokePermissions')->name('users.revoke');
        });
    });

    /* --- GESTIÓN DE ESTUDIANTES --- */
    Route::middleware('role:Administrador,Admisión')->group(function () {
        Route::get('/students/search', [StudentController::class, 'search'])->name('students.search');
        Route::resource('students', StudentController::class);
    });

    /* --- CIRCUITO MÉDICO (SNAKEDEV HEALTH ENGINE) --- */
    
    // 1. Rutas Estáticas (Deben definirse primero para evitar colisión con {medical_exam})
    Route::get('medical-exams/history', [MedicalExamController::class, 'history'])->name('medical_exams.history');

    // 2. Rutas con Parámetros Dinámicos
    Route::controller(MedicalExamController::class)->prefix('medical-exams')->name('medical_exams.')->group(function () {
        Route::get('/{medical_exam}/evaluate', 'evaluate')->name('evaluate');
        Route::post('/{medical_exam}/evaluate', 'storeEvaluation')->name('store_evaluation');
        Route::get('/{medical_exam}/report', 'report')->name('report'); // Ruta para generar el PDF
        Route::patch('/{medical_exam}/finish', 'finish')->name('finish');
    });

    // 3. Recurso Principal (Manejamos solo los métodos estándar restantes)
    Route::resource('medical-exams', MedicalExamController::class)
        ->parameters(['medical-exams' => 'medical_exam'])
        ->names('medical_exams')
        ->except(['create']); // "create" no se usa porque se genera desde el perfil del estudiante

});

require __DIR__.'/auth.php';