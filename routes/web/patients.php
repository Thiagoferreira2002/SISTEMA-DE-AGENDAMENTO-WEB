<?php

use App\Http\Controllers\Backend\PatientController;
use Illuminate\Support\Facades\Route;

/**
 * GRUPO DE ROTAS DE PACIENTES
 */

Route::middleware('auth', 'admin')->group(function () {

    Route::get('admin/patients/logs', [PatientController::class, 'logs'])->name('admin.patients.logs');
    Route::get('admin/patients/duplicate-check', [PatientController::class, 'duplicateCheck'])->name('admin.patients.duplicate-check');

    // CRUD de pacientes
    Route::resource('admin/patients', PatientController::class)->names([
        'index' => 'admin.patients.index',
        'create' => 'admin.patients.create',
        'store' => 'admin.patients.store',
        'show' => 'admin.patients.show',
        'edit' => 'admin.patients.edit',
        'update' => 'admin.patients.update',
        'destroy' => 'admin.patients.destroy',
    ]);
});
