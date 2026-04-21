@extends('admin.layouts.master')
@section('content')
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

                        <form method="GET" action="{{ route('admin.patients.index') }}" class="mb-4">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="q">Busca global</label>
                                        <input type="text" class="form-control" id="q" name="q" value="{{ request('q') }}" placeholder="Digite o nome ou CPF do paciente">
                                    </div>
                                    <div class="form-group mb-0">
                                        <button type="submit" class="btn btn-primary px-4">Filtrar</button>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="status">Situação cadastral</label>
                                        <select class="form-control" id="status" name="status">
                                            <option value="">Todos</option>
                                            <option value="completo" {{ request('status') === 'completo' ? 'selected' : '' }}>Completo</option>
                                            <option value="incompleto" {{ request('status') === 'incompleto' ? 'selected' : '' }}>Incompleto</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-5">
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
                                    <tr>
                                        <td>{{ $patient->nome }}</td>
                                        <td>{{ $patient->cpf ?: '-' }}</td>
                                        <td>{{ $patient->email }}</td>
                                        <td>{{ $patient->telefone }}</td>
                                        <td class="text-center align-middle">{{ $patient->data_nascimento ? $patient->data_nascimento->format('d/m/Y') : '-' }}</td>
                                        <td class="text-center align-middle">
                                            <span class="badge badge-{{ $patient->cadastro_status_class }}">{{ $patient->cadastro_status_label }}</span>
                                        </td>
                                        <td class="text-center align-middle">
                                            <a href="{{ route('admin.agendamentos.create', ['patient_id' => $patient->id, 'return_to' => url()->full()]) }}" class="btn btn-sm btn-success">Agendar</a>
                                            <a href="{{ route('admin.patients.show', $patient) }}" class="btn btn-sm btn-info">Detalhes</a>
                                            <a href="{{ route('admin.patients.edit', $patient) }}" class="btn btn-sm btn-warning">Editar</a>
                                            <form action="{{ route('admin.patients.destroy', $patient) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Excluir paciente?')">Excluir</button>
                                            </form>
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
