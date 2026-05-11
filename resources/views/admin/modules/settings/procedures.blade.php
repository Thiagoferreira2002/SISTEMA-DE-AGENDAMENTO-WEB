@extends('admin.layouts.master')
@section('content')
<style>
    .procedure-edit-modal {
        z-index: 10060;
    }

    .settings-actions {
        display: inline-flex;
        flex-wrap: nowrap;
        align-items: center;
        gap: 6px;
        white-space: nowrap;
    }

    .settings-actions form {
        margin: 0;
    }

    .procedure-edit-modal + .modal-backdrop,
    .modal-backdrop.show {
        z-index: 10050;
    }

    .procedure-edit-modal .modal-dialog,
    .procedure-edit-modal .modal-content {
        pointer-events: auto;
    }

    @media (max-width: 767.98px) {
        .settings-actions {
            flex-wrap: wrap;
            justify-content: center;
        }

        .settings-actions > *,
        .settings-actions form,
        .settings-actions .btn {
            width: 100%;
        }

        .procedure-edit-modal .modal-dialog {
            margin: 10px;
            max-width: calc(100vw - 20px);
        }
    }
</style>
<section class="section">
    <div class="section-header">
        <h1>Procedimentos</h1>
    </div>

    <div class="section-body">
        @if(session('warning'))
            <div class="alert alert-warning">{{ session('warning') }}</div>
        @endif
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(!empty($setupWarning))
            <div class="alert alert-warning">{{ $setupWarning }}</div>
        @endif

        @php
            $durationOptions = range(15, 180, 15);
            $formatDuration = function ($minutes) {
                $minutes = (int) $minutes;

                if ($minutes < 60) {
                    return $minutes . ' min';
                }

                if ($minutes % 60 === 0) {
                    return ($minutes / 60) . 'h';
                }

                return floor($minutes / 60) . 'h ' . ($minutes % 60) . 'min';
            };
        @endphp

        <div class="card mb-4">
            <div class="card-header"><h4>Novo procedimento</h4></div>
            <div class="card-body">
                <form action="{{ route('admin.settings.procedures.store') }}" method="POST" data-draft-form="true" data-draft-key="admin.settings.procedures.create">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 col-lg-4"><div class="form-group"><label>Profissional *</label><select class="form-control @error('professional_id') is-invalid @enderror" name="professional_id" required><option value="">Selecione</option>@foreach($professionalOptions as $professionalOption)<option value="{{ $professionalOption->id }}" {{ (string) old('professional_id', $selectedProfessionalId) === (string) $professionalOption->id ? 'selected' : '' }}>{{ $professionalOption->nome }}{{ $professionalOption->especialidade_principal ? ' - ' . $professionalOption->especialidade_principal : '' }}</option>@endforeach</select>@error('professional_id')<div class="text-danger small mt-1">{{ $message }}</div>@enderror</div></div>
                        <div class="col-md-4 col-lg-3"><div class="form-group"><label>Nome *</label><input type="text" class="form-control @error('nome') is-invalid @enderror" name="nome" value="{{ old('nome') }}" required>@error('nome')<div class="text-danger small mt-1">{{ $message }}</div>@enderror</div></div>
                        <div class="col-md-2 col-lg-2"><div class="form-group"><label>Duração *</label><select class="form-control @error('duracao_minutos') is-invalid @enderror" name="duracao_minutos" required><option value="">Selecione</option>@foreach($durationOptions as $durationOption)<option value="{{ $durationOption }}" {{ (string) old('duracao_minutos') === (string) $durationOption ? 'selected' : '' }}>{{ $durationOption < 60 ? $durationOption . ' min' : ($durationOption % 60 === 0 ? ($durationOption / 60) . 'h' : floor($durationOption / 60) . 'h ' . ($durationOption % 60) . 'min') }}</option>@endforeach</select>@error('duracao_minutos')<div class="text-danger small mt-1">{{ $message }}</div>@enderror</div></div>
                    </div>
                    <button type="submit" class="btn btn-primary">Cadastrar procedimento</button>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex flex-wrap justify-content-between align-items-center" style="gap: 12px;">
                <h4 class="mb-0">Serviços e duração padrão</h4>
                <form action="{{ route('admin.settings.procedures') }}" method="GET" class="d-flex flex-wrap align-items-end" style="gap: 12px;">
                    <div>
                        <label class="mb-1">Profissional</label>
                        <select class="form-control" name="professional_filter" onchange="this.form.submit()">
                            <option value="">Todos os profissionais</option>
                            @foreach($professionalOptions as $professionalOption)
                                <option value="{{ $professionalOption->id }}" {{ (string) $selectedProfessionalId === (string) $professionalOption->id ? 'selected' : '' }}>{{ $professionalOption->nome }}</option>
                            @endforeach
                        </select>
                    </div>
                    @if($selectedProfessionalId)
                        <div>
                            <a href="{{ route('admin.settings.procedures') }}" class="btn btn-light border">Ver todos</a>
                        </div>
                    @endif
                </form>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Profissional</th>
                                <th>Serviço</th>
                                <th>Duração</th>
                                <th>Status</th>
                                <th>Ação</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($procedures as $procedure)
                                <tr>
                                    <td>{{ $procedure->professional?->nome ?? '-' }}</td>
                                    <td>
                                        <div class="font-weight-600">{{ $procedure->nome }}</div>
                                    </td>
                                    <td>{{ $formatDuration($procedure->duracao_minutos) }}</td>
                                    <td><span class="badge badge-{{ $procedure->ativo ? 'success' : 'danger' }}">{{ $procedure->ativo ? 'Ativo' : 'Inativo' }}</span></td>
                                    <td>
                                        <div class="settings-actions">
                                            <button type="button" class="btn btn-sm btn-info" data-toggle="modal" data-target="#edit-procedure-modal-{{ $procedure->id }}">Editar</button>
                                            <form action="{{ route('admin.settings.procedures.status', $procedure) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-sm btn-{{ $procedure->ativo ? 'warning' : 'success' }}">{{ $procedure->ativo ? 'Inativar' : 'Ativar' }}</button>
                                            </form>
                                            <form action="{{ route('admin.settings.procedures.destroy', $procedure) }}" method="POST" class="d-inline" onsubmit="return confirm('Deseja realmente excluir este serviço?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">Excluir</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center text-muted">Nenhum procedimento cadastrado.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if(method_exists($procedures, 'hasPages') && $procedures->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $procedures->links('vendor.pagination.patients-blocks') }}
                    </div>
                @endif
            </div>
        </div>

        @foreach($procedures as $procedure)
            <div class="modal fade procedure-edit-modal" id="edit-procedure-modal-{{ $procedure->id }}" tabindex="-1" role="dialog" aria-labelledby="editProcedureModalLabel{{ $procedure->id }}" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editProcedureModalLabel{{ $procedure->id }}">Editar serviço</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form action="{{ route('admin.settings.procedures.update', $procedure) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="modal-body">
                                <div class="form-group">
                                    <label>Profissional *</label>
                                    <select class="form-control" name="professional_id" required>
                                        <option value="">Selecione</option>
                                        @foreach($professionalOptions as $professionalOption)
                                            <option value="{{ $professionalOption->id }}" {{ (string) old('professional_id', $procedure->professional_id) === (string) $professionalOption->id ? 'selected' : '' }}>{{ $professionalOption->nome }}{{ $professionalOption->especialidade_principal ? ' - ' . $professionalOption->especialidade_principal : '' }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Nome *</label>
                                    <input type="text" class="form-control" name="nome" value="{{ old('nome', $procedure->nome) }}" required>
                                </div>
                                <div class="form-group mb-0">
                                    <label>Duração *</label>
                                    <select class="form-control" name="duracao_minutos" required>
                                        @foreach($durationOptions as $durationOption)
                                            <option value="{{ $durationOption }}" {{ (string) old('duracao_minutos', $procedure->duracao_minutos) === (string) $durationOption ? 'selected' : '' }}>{{ $formatDuration($durationOption) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-primary">Salvar alterações</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</section>

@push('scripts')
<script>
    $(function () {
        $('.procedure-edit-modal').each(function () {
            var modal = $(this);

            if (!modal.parent().is('body')) {
                modal.appendTo('body');
            }
        });
    });
</script>
@endpush
@endsection
