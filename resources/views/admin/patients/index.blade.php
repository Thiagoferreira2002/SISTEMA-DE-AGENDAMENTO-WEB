@extends('admin.layouts.master')
@section('content')
<style>
    .patients-summary-card {
        width: fit-content;
        min-width: 190px;
        max-width: 100%;
    }

    .patients-summary-card .card-icon {
        margin: 14px 14px 0;
    }

    .patients-summary-card .card-wrap {
        padding: 14px 14px 16px;
    }

    .patients-summary-card .card-header h4 {
        font-size: 11px;
        line-height: 1.25;
        white-space: normal;
        margin-bottom: 0;
    }

    .patients-actions {
        display: inline-flex;
        flex-wrap: nowrap;
        align-items: center;
        gap: 6px;
        white-space: nowrap;
    }

    .patients-actions form {
        margin: 0;
    }

    .patients-actions .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .patient-name-cell {
        display: flex;
        align-items: center;
        gap: 12px;
        min-width: 220px;
    }

    .patient-name-cell img {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
        flex: 0 0 auto;
        border: 2px solid rgba(23, 111, 190, 0.12);
    }

    @media (max-width: 767.98px) {
        .patients-actions {
            flex-wrap: wrap;
            justify-content: center;
        }

        .patients-actions > *,
        .patients-actions form,
        .patients-actions .btn {
            width: 100%;
        }

        .patients-table-actions {
            min-width: 220px !important;
            white-space: normal !important;
        }
    }
</style>
<section class="section">
    <div class="section-header">
        <h1>Pacientes</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">Dashboard</a></div>
            <div class="breadcrumb-item">Pacientes</div>
        </div>
    </div>

    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Lista de Pacientes</h4>
                        <div class="card-header-action">
                            <a href="{{ route('admin.patients.create') }}" class="btn btn-primary">Novo Paciente</a>
                        </div>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        <div class="row mb-4">
                            <div class="col-xl-auto col-lg-auto col-md-5 col-12">
                                <div class="card card-statistic-1 mb-0 patients-summary-card">
                                    <div class="card-icon bg-primary"><i class="fas fa-users"></i></div>
                                    <div class="card-wrap">
                                        <div class="card-header"><h4>Total de Pacientes</h4></div>
                                        <div class="card-body">{{ method_exists($patients, 'total') ? $patients->total() : $patients->count() }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <form method="GET" action="{{ route('admin.patients.index') }}" class="mb-4">
                            <div class="row">
                                <div class="col-lg-4 col-md-6">
                                    <div class="form-group">
                                        <label for="q">Busca global</label>
                                        <input type="text" class="form-control" id="q" name="q" value="{{ request('q') }}" placeholder="Digite o nome ou CPF do paciente">
                                    </div>
                                    <div class="d-flex flex-wrap align-items-center" style="gap: 8px;">
                                        <button type="submit" class="btn btn-primary px-4">Filtrar</button>
                                        <a href="{{ route('admin.patients.index') }}" class="btn btn-light">Limpar</a>
                                    </div>
                                </div>
                                <div class="col-xl-2 col-lg-2 col-md-3">
                                    <div class="form-group">
                                        <label for="status">Situação cadastral</label>
                                        <select class="form-control" id="status" name="status">
                                            <option value="">Todos</option>
                                            <option value="completo" {{ request('status') === 'completo' ? 'selected' : '' }}>Completo</option>
                                            <option value="incompleto" {{ request('status') === 'incompleto' ? 'selected' : '' }}>Incompleto</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-3 d-none d-lg-block">
                                </div>
                            </div>
                        </form>

                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Nome</th>
                                        <th>CPF</th>
                                        <th>Email</th>
                                        <th>Celular</th>
                                        <th class="text-center">Data de Nascimento</th>
                                        <th class="text-center">Situação cadastral</th>
                                        <th class="text-center">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($patients as $patient)
                                    @php
                                        $canManagePatient = ! auth()->user()?->isClinicManager();
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="patient-name-cell">
                                                <img src="{{ $patient->foto_url }}" alt="Foto de {{ $patient->nome }}">
                                                <span>{{ $patient->nome }}</span>
                                            </div>
                                        </td>
                                        <td>{{ $patient->cpf ?: '-' }}</td>
                                        <td>{{ $patient->email }}</td>
                                        <td>{{ $patient->telefone }}</td>
                                        <td class="text-center align-middle">{{ $patient->data_nascimento ? $patient->data_nascimento->format('d/m/Y') : '-' }}</td>
                                        <td class="text-center align-middle">
                                            <span class="badge badge-{{ $patient->cadastro_status_class }}">{{ $patient->cadastro_status_label }}</span>
                                        </td>
                                        <td class="text-center align-middle patients-table-actions action-button-cell" style="white-space: nowrap; min-width: 320px;">
                                            <div class="patients-actions action-button-group">
                                                <a href="{{ route('admin.patients.show', $patient) }}" class="btn btn-sm btn-secondary">Detalhes</a>
                                                @if($canManagePatient)
                                                    <a href="{{ route('admin.agendamentos.create', ['patient_id' => $patient->id, 'return_to' => url()->full()]) }}" class="btn btn-sm btn-success">Agendar</a>
                                                    <a href="{{ route('admin.patients.edit', $patient) }}" class="btn btn-sm btn-info">Editar</a>
                                                    <form action="{{ route('admin.patients.destroy', $patient) }}" method="POST" class="mb-0">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Excluir paciente?')">Excluir</button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="text-center">Nenhum paciente encontrado</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if($patients->hasPages())
                            <div class="d-flex justify-content-center mt-4">
                                {{ $patients->links('vendor.pagination.patients-blocks') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
