@extends('admin.layouts.master')
@section('content')
<section class="section">
    <div class="section-header">
        <h1>Detalhes do Paciente</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">Dashboard</a></div>
            <div class="breadcrumb-item"><a href="{{ route('admin.patients.index') }}">Pacientes</a></div>
            <div class="breadcrumb-item">Detalhes</div>
        </div>
    </div>

    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Informações do Paciente</h4>
                        <div class="card-header-action">
                            <a href="{{ route('admin.patients.edit', $patient) }}" class="btn btn-warning">Editar</a>
                            <a href="{{ route('admin.patients.index') }}" class="btn btn-secondary">Voltar</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-6 col-12">
                                <div class="border rounded p-4 mb-4 h-100 shadow-sm bg-white">
                                    <h5 class="mb-3">Dados Pessoais</h5>
                                    <p><strong>Nome:</strong> {{ $patient->nome }}</p>
                                    <p><strong>CPF:</strong> {{ $patient->cpf ?: '-' }}</p>
                                    <p><strong>Data de Nascimento:</strong> {{ $patient->data_nascimento ? $patient->data_nascimento->format('d/m/Y') : '-' }}</p>
                                    <p class="mb-0"><strong>Sexo:</strong> {{ $patient->sexo ?: '-' }}</p>
                                </div>
                            </div>
                            <div class="col-lg-6 col-12">
                                <div class="border rounded p-4 mb-4 h-100 shadow-sm bg-white">
                                    <h5 class="mb-3">Contato</h5>
                                    <p><strong>Celular:</strong> {{ $patient->telefone }}</p>
                                    <p class="mb-0"><strong>E-mail:</strong> {{ $patient->email }}</p>
                                </div>
                            </div>
                            <div class="col-lg-6 col-12">
                                <div class="border rounded p-4 mb-4 h-100 shadow-sm bg-white">
                                    <h5 class="mb-3">Endereço e Situação Cadastral</h5>
                                    <p><span class="badge badge-{{ $patient->cadastro_status_class }}">{{ $patient->cadastro_status_label }}</span></p>
                                    <p><strong>Pendências cadastrais:</strong> {{ empty($patient->cadastro_pendencias) ? 'Nenhuma' : implode(', ', $patient->cadastro_pendencias) }}</p>
                                    <p><strong>Endereço:</strong> {{ $patient->endereco ?: '-' }}</p>
                                    <p><strong>Número:</strong> {{ $patient->numero_endereco ?: '-' }}</p>
                                    <p><strong>CEP:</strong> {{ $patient->cep ?: '-' }}</p>
                                    <p><strong>Bairro:</strong> {{ $patient->bairro ?: '-' }}</p>
                                    <p><strong>Tipo de imóvel:</strong> {{ $patient->tipo_moradia ? ucfirst($patient->tipo_moradia) : '-' }}</p>
                                    <p class="mb-0"><strong>Complemento:</strong> {{ $patient->complemento ?: '-' }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="border rounded p-4 mt-4 shadow-sm bg-white">
                            <h5 class="mb-3">Linha do Tempo de Consultas</h5>
                            <div class="table-responsive">
                                <table class="table table-striped mb-0">
                                    <thead>
                                        <tr>
                                            <th>Data</th>
                                            <th>Profissional</th>
                                            <th>Serviço</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($history as $item)
                                            <tr>
                                                <td>{{ $item->data_agendamento->format('d/m/Y') }} às {{ $item->horario }}</td>
                                                <td>{{ $item->medico ?: 'Não informado' }}</td>
                                                <td>{{ $item->servico }}</td>
                                                <td>{{ ucfirst($item->status) }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center">Sem consultas registradas.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            @if(method_exists($history, 'hasPages') && $history->hasPages())
                                <div class="d-flex justify-content-center mt-4">
                                    {{ $history->links('vendor.pagination.patients-blocks') }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
