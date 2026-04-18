<?php

use App\Http\Controllers\Backend\AgendamentoController;
use Illuminate\Support\Facades\Route;

/**
 * GRUPO DE ROTAS DE AGENDAMENTOS
 */

Route::middleware('auth', 'admin')->group(function () {

    Route::get('admin/agendamentos/calendario/eventos', [AgendamentoController::class, 'calendarEvents'])
        ->name('admin.agendamentos.calendar.events');

    Route::get('admin/agendamentos/calendario', [AgendamentoController::class, 'calendar'])
        ->name('admin.agendamentos.calendar');

    // CRUD de agendamentos
    Route::resource('admin/agendamentos', AgendamentoController::class)->except(['show'])->names([
        'index' => 'admin.agendamentos.index',
        'create' => 'admin.agendamentos.create',
        'store' => 'admin.agendamentos.store',
        'edit' => 'admin.agendamentos.edit',
        'update' => 'admin.agendamentos.update',
        'destroy' => 'admin.agendamentos.destroy',
    ]);

    // Rota para visualizar agendamento
    Route::get('admin/agendamentos/{agendamento}', [AgendamentoController::class, 'show'])->name('admin.agendamentos.show');
});
