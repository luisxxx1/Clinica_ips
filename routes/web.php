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

    /* --- ADMINISTRACIÓN --- */
    Route::prefix('admin')->name('admin.')->middleware('role:Administrador')->group(function () {
        Route::controller(AdminSettingsController::class)->group(function () {
            Route::get('/settings', 'index')->name('settings');
            Route::post('/settings/update-branding', 'updateBranding')->name('update_branding');
            Route::patch('/settings/user/{user}', 'updateUser')->name('user.update');
            Route::post('/reset-access', 'resetAccess')->name('reset_access');
            Route::get('/roles/colors', 'editRoleColors')->name('role_colors');
            Route::delete('/users/{user}/permissions', 'revokePermissions')->name('users.revoke');
        });

        // ✅ Dashboard admin: ve todos los exámenes completados con sus 6 valoraciones
        Route::get('/medical-exams/dashboard', [MedicalExamController::class, 'dashboard'])
            ->name('medical_exams.dashboard');
    });

    /* --- GESTIÓN DE ESTUDIANTES --- */
    Route::middleware('role:Administrador,Admisión')->group(function () {
        Route::get('/students/search', [StudentController::class, 'search'])->name('students.search');
        Route::resource('students', StudentController::class);
    });

    /* --- CIRCUITO MÉDICO --- */

    // 1. Rutas estáticas (deben ir ANTES de las rutas con {medical_exam})
    Route::get('medical-exams/history', [MedicalExamController::class, 'history'])
        ->name('medical_exams.history');

    // 2. Rutas con parámetros dinámicos
    Route::controller(MedicalExamController::class)
        ->prefix('medical-exams')
        ->name('medical_exams.')
        ->group(function () {
            Route::get('/{medical_exam}/evaluate', 'evaluate')->name('evaluate');
            Route::post('/{medical_exam}/evaluate', 'storeEvaluation')->name('store_evaluation');
            Route::get('/{medical_exam}/report', 'report')->name('report');
            // ❌ ELIMINADA: Route::patch('/{medical_exam}/finish', 'finish')
            //    El método finish() no existe. El cierre del circuito
            //    ocurre automáticamente en storeEvaluation() cuando
            //    las 6 áreas han evaluado al estudiante.
        });

    // 3. Recurso principal
    Route::resource('medical-exams', MedicalExamController::class)
        ->parameters(['medical-exams' => 'medical_exam'])
        ->names('medical_exams')
        ->except(['create']);
});

require __DIR__.'/auth.php';
