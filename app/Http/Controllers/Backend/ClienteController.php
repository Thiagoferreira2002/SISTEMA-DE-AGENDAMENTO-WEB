<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Agendamento;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClienteController extends Controller
{
    public function dashboard()
    {
        $totalAgendamentos = Agendamento::count();
        $agendamentosPendentes = Agendamento::where('status', 'pendente')->count();
        $agendamentosConfirmados = Agendamento::where('status', 'confirmado')->count();
        $totalPacientes = Patient::count();
        $proximosAgendamentos = Agendamento::where('data_agendamento', '>=', now()->toDateString())
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
}
