<?php


use App\Http\Controllers\Backend\ClienteController;
use App\Http\Controllers\ProfileController;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;



/**
 * GRUPO DE ROTAS DO PAINEL DE CONTROLE DO CLIENTE
 * CRIADOR 11/4
 * AUTOR: Thiago Cruz
 * SITE: MSFLIX.COM.BR
 * VERSÃO 1.0 LARAVEL 12
 */

Route::middleware('auth')->group(function () {

//rota do painel dashboard universal (admin e user)
Route::get('cliente/dashboard', [ClienteController::class, 'dashboard'])
->name('cliente.dashboard');

});
