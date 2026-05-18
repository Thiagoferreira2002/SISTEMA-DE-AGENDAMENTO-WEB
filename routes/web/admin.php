<?php

use App\Http\Controllers\Backend\AdminController;
use App\Http\Controllers\Backend\ClinicManagementController;
use App\Http\Controllers\Backend\DadosController;
use App\Http\Controllers\ProfileController;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;

/**
 * GRUPO DE ROTAS DO PAINEL DE CONTROLE DO ADMIN
 * CRIADOR DIA Y
 * AUTOR: MAYKON SILVEIRA
 * SITE: MAYKONSILVEIRA.COM.BR - MSFLIX.COM.BR - MSFLIX.DEV.BR
 * VERSÃO 1.0 LARAVEL 12
 */
Route::middleware('auth', 'admin')->group(function () {

//rota do painel de admin universal
Route::get('admin/dashboard', [AdminController::class, 'dashboard'])
->name('admin.dashboard');

Route::get('admin/notificacoes/abrir', [AdminController::class, 'markNotificationsRead'])
->name('admin.notifications.read');

Route::get('admin/minha-conta', [AdminController::class, 'editAccount'])
->name('admin.account.edit');

Route::get('admin/tutorial', [AdminController::class, 'tutorial'])
->name('admin.tutorial');

Route::put('admin/minha-conta', [AdminController::class, 'updateAccount'])
->name('admin.account.update');

/**
 * ******************************************************
 * ******************************************************
 * INICIO CONFIGURAÇÕES
 * Acessar as configurações do site
 * ******************************************************
 * ******************************************************
 * */

Route::get('admin/dados/index', [DadosController::class, 'index'])
->name('dados.index');

//atualiza as configurações do site
Route::put('admin/dados/update', [DadosController::class , 'update'])
->name('dados.update');

Route::get('admin/agendamentos/lista-espera', [ClinicManagementController::class, 'waitlist'])
->name('admin.agendamentos.waitlist');

Route::get('admin/agendamentos/bloqueios', [ClinicManagementController::class, 'scheduleBlocks'])
->name('admin.agendamentos.blocks');

Route::get('admin/agendamentos/confirmacoes', [ClinicManagementController::class, 'confirmations'])
->name('admin.agendamentos.confirmations');

Route::post('admin/agendamentos/{agendamento}/promover', [ClinicManagementController::class, 'promoteWaitlist'])
->name('admin.agendamentos.promote');

Route::post('admin/agendamentos/{agendamento}/confirmar', [ClinicManagementController::class, 'confirmAppointment'])
->name('admin.agendamentos.confirm');

Route::post('admin/agendamentos/{agendamento}/pendente', [ClinicManagementController::class, 'pendAppointment'])
->name('admin.agendamentos.pend');

Route::post('admin/agendamentos/{agendamento}/cancelar', [ClinicManagementController::class, 'cancelAppointment'])
->name('admin.agendamentos.cancel');

Route::post('admin/agendamentos/{agendamento}/cancelar-atendimento', [ClinicManagementController::class, 'cancelOperationalAppointment'])
->name('admin.agendamentos.cancel-operational');

Route::get('admin/patients/historico', [ClinicManagementController::class, 'patientHistory'])
->name('admin.patients.history');

Route::get('admin/agendamentos/finalizados', [ClinicManagementController::class, 'patientHistory'])
->name('admin.agendamentos.completed');

Route::get('admin/patients/documentos', [ClinicManagementController::class, 'patientDocuments'])
->name('admin.patients.documents');

Route::get('admin/doctor/fila-espera', [ClinicManagementController::class, 'doctorQueue'])
->name('admin.doctor.queue');

Route::get('admin/doctor/atendimentos-em-atraso', [ClinicManagementController::class, 'doctorPendingFinalization'])
->name('admin.doctor.pending-finalization');

Route::post('admin/doctor/fila-espera/{agendamento}/finalizar', [ClinicManagementController::class, 'finishAppointment'])
->name('admin.doctor.queue.finish');

Route::get('admin/doctor/prontuario', [ClinicManagementController::class, 'medicalRecords'])
->name('admin.doctor.records');

Route::get('admin/doctor/prescricoes', [ClinicManagementController::class, 'prescriptions'])
->name('admin.doctor.prescriptions');

Route::get('admin/doctor/laudos', [ClinicManagementController::class, 'reports'])
->name('admin.doctor.reports');

Route::get('admin/doctor/ausencias', [ClinicManagementController::class, 'doctorAbsences'])
->name('admin.doctor.absences');

Route::post('admin/doctor/ausencias', [ClinicManagementController::class, 'storeDoctorAbsence'])
->name('admin.doctor.absences.store');

Route::delete('admin/doctor/ausencias/{absence}', [ClinicManagementController::class, 'destroyDoctorAbsence'])
->name('admin.doctor.absences.destroy');

Route::get('admin/settings', [ClinicManagementController::class, 'settingsIndex'])
->name('admin.settings.index');

Route::get('admin/settings/horario-clinica', [ClinicManagementController::class, 'clinicHours'])
->name('admin.settings.clinic-hours');

Route::patch('admin/settings/horario-clinica', [ClinicManagementController::class, 'updateClinicHours'])
->name('admin.settings.clinic-hours.update');

Route::get('admin/settings/profissionais', [ClinicManagementController::class, 'professionals'])
->name('admin.settings.professionals');

Route::post('admin/settings/profissionais', [ClinicManagementController::class, 'storeProfessional'])
->name('admin.settings.professionals.store');

Route::put('admin/settings/profissionais/{professional}', [ClinicManagementController::class, 'updateProfessional'])
->name('admin.settings.professionals.update');

Route::delete('admin/settings/profissionais/{professional}', [ClinicManagementController::class, 'destroyProfessional'])
->name('admin.settings.professionals.destroy');

Route::get('admin/settings/procedimentos', [ClinicManagementController::class, 'procedures'])
->name('admin.settings.procedures');

Route::post('admin/settings/procedimentos', [ClinicManagementController::class, 'storeProcedure'])
->name('admin.settings.procedures.store');

Route::put('admin/settings/procedimentos/{procedure}', [ClinicManagementController::class, 'updateProcedure'])
->name('admin.settings.procedures.update');

Route::patch('admin/settings/procedimentos/{procedure}/status', [ClinicManagementController::class, 'toggleProcedureStatus'])
->name('admin.settings.procedures.status');

Route::delete('admin/settings/procedimentos/{procedure}', [ClinicManagementController::class, 'destroyProcedure'])
->name('admin.settings.procedures.destroy');

Route::get('admin/settings/usuarios-permissoes', [ClinicManagementController::class, 'usersPermissions'])
->name('admin.settings.users');

Route::get('admin/settings/logs-atividade', [ClinicManagementController::class, 'activityLogs'])
->name('admin.settings.activity-logs');

Route::post('admin/settings/usuarios-permissoes', [ClinicManagementController::class, 'storeUser'])
->name('admin.settings.users.store');

Route::put('admin/settings/usuarios-permissoes/{user}', [ClinicManagementController::class, 'updateUserPermissions'])
->name('admin.settings.users.update');

Route::patch('admin/settings/usuarios-permissoes/{user}/status', [ClinicManagementController::class, 'toggleUserStatus'])
->name('admin.settings.users.status');

Route::delete('admin/settings/usuarios-permissoes/{user}', [ClinicManagementController::class, 'destroyUser'])
->name('admin.settings.users.destroy');



});
