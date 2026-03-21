<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\MedicalExamController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdminSettingsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', fn() => redirect()->route('login'));

// Dashboard unificado
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    /* PERFIL DEL USUARIO */
    Route::controller(ProfileController::class)->group(function () {
        Route::get('/profile', 'edit')->name('profile.edit');
        Route::patch('/profile', 'update')->name('profile.update');
        Route::delete('/profile', 'destroy')->name('profile.destroy');
    });

    /* ADMINISTRACIÓN Y AJUSTES */
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

    /* GESTIÓN DE ESTUDIANTES */
    Route::get('/students/search', [StudentController::class, 'search'])->name('students.search');
    Route::resource('students', StudentController::class);

    /* CIRCUITO MÉDICO (Evaluaciones y Reportes) */
    
    // IMPORTANTE: Las rutas estáticas (/history) van ANTES que las dinámicas (/{medical_exam})
    Route::prefix('medical-exams')->name('medical_exams.')->controller(MedicalExamController::class)->group(function () {
        Route::get('/history', 'history')->name('history'); // Si estuviera abajo, Laravel creería que "history" es un ID
        Route::get('/{medical_exam}/evaluate', 'evaluate')->name('evaluate');
        Route::post('/{medical_exam}/result', 'storeResult')->name('store_result');
        Route::get('/{medical_exam}/report', 'report')->name('report'); // Simplifiqué el nombre del método a 'report'
        Route::patch('/{medical_exam}/finish', 'finish')->name('finish');
    });

    // Resource para rutas estándar: index, create, store, show, edit, update, destroy
    Route::resource('medical-exams', MedicalExamController::class)
        ->parameters(['medical-exams' => 'medical_exam'])
        ->names('medical_exams');
});

require __DIR__.'/auth.php';