<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminController extends Controller
{
   //acessar o painel
   public function dashboard(){
       $totalAgendamentos = \App\Models\Agendamento::count();
       $agendamentosPendentes = \App\Models\Agendamento::where('status', 'pendente')->count();
       $agendamentosConfirmados = \App\Models\Agendamento::where('status', 'confirmado')->count();
       $totalPacientes = \App\Models\Patient::count();
       $proximosAgendamentos = \App\Models\Agendamento::where('data_agendamento', '>=', now()->toDateString())
           ->orderBy('data_agendamento')
           ->orderBy('horario')
           ->limit(5)
           ->get();

       return view('admin.dashboard', compact(
           'totalAgendamentos',
           'agendamentosPendentes',
           'agendamentosConfirmados',
           'totalPacientes',
           'proximosAgendamentos'
       ));
   }

   //fazer login
   public function login(){
     return view('admin.auth.login');
   }

   //recuperar a senha
   public function recuperarSenha(){
    return view('admin.auth.forgot-password');
   }
}
