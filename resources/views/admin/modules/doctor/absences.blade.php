@extends('admin.layouts.master')
@section('content')
<style>
    .section-body > .card,
    .section-body > .row > .col-12 > .card,
    .section-body > .row > .col-lg-4 > .card {
        border: 1px solid #d2dbe6 !important;
        box-shadow: inset 0 0 0 1px #d2dbe6;
    }

    html[data-theme="dark"] .section-body > .card,
    html[data-theme="dark"] .section-body > .row > .col-12 > .card,
    html[data-theme="dark"] .section-body > .row > .col-lg-4 > .card {
        border-color: #000000 !important;
        box-shadow: inset 0 0 0 1px #000000;
    }

    .doctor-absence-summary-card {
        width: fit-content;
        min-width: 210px;
        max-width: 100%;
    }

    .doctor-absence-summary-card .card-icon {
        margin: 14px 14px 0;
    }

    .doctor-absence-summary-card .card-wrap {
        padding: 14px 14px 16px;
    }

    .doctor-absence-summary-card .card-header h4 {
        font-size: 11px;
        line-height: 1.25;
        white-space: normal;
        margin-bottom: 0;
    }

    .doctor-absence-note {
        padding: 14px 16px;
        border-radius: 16px;
        border: 1px solid rgba(23, 111, 190, 0.12);
        background: linear-gradient(180deg, rgba(244, 249, 255, 0.98) 0%, rgba(235, 243, 252, 0.98) 100%);
        color: #35536e;
    }

    .doctor-absence-table {
        min-width: 860px;
    }

    .doctor-absence-time-card {
        padding: 14px 14px 12px;
        border-radius: 16px;
        border: 1px solid rgba(23, 111, 190, 0.12);
        background: linear-gradient(180deg, rgba(247, 251, 255, 0.98) 0%, rgba(239, 246, 252, 0.98) 100%);
        height: 100%;
    }

    .doctor-absence-time-card.is-conflicted {
        border-color: rgba(220, 53, 69, 0.32);
        box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.08);
    }

    .doctor-absence-time-card label {
        display: block;
        margin-bottom: 8px;
        font-size: 12px;
        font-weight: 800;
        color: #35536e;
    }

    .doctor-absence-time-card .form-control {
        min-height: 48px;
        font-size: 16px;
        font-weight: 700;
    }

    .doctor-absence-time-help {
        display: block;
        margin-top: 8px;
        font-size: 11px;
        color: #6a879f;
        line-height: 1.4;
    }

    .doctor-absence-conflict-panel {
        display: none;
        margin-top: 18px;
        padding: 14px 16px;
        border-radius: 16px;
        border: 1px solid rgba(220, 53, 69, 0.18);
        background: linear-gradient(180deg, rgba(255, 245, 246, 0.98) 0%, rgba(255, 238, 240, 0.98) 100%);
    }

    .doctor-absence-conflict-panel.is-visible {
        display: block;
    }

    .doctor-absence-conflict-title {
        margin: 0 0 8px;
        font-size: 13px;
        font-weight: 800;
        color: #a12838;
    }

    .doctor-absence-conflict-list {
        display: grid;
        gap: 8px;
        margin: 0;
        padding: 0;
        list-style: none;
    }

    .doctor-absence-conflict-item {
        padding: 10px 12px;
        border-radius: 12px;
        background: rgba(255, 255, 255, 0.72);
        border: 1px solid rgba(220, 53, 69, 0.1);
    }

    .doctor-absence-conflict-time {
        display: inline-block;
        font-size: 12px;
        font-weight: 800;
        color: #a12838;
    }

    .doctor-absence-conflict-copy {
        display: block;
        margin-top: 4px;
        color: #5b3340;
        font-size: 12px;
        line-height: 1.45;
    }

    .doctor-absence-reason {
        font-weight: 700;
        color: #16344d;
    }

    .doctor-absence-time {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 120px;
        padding: 7px 12px;
        border-radius: 999px;
        background: rgba(23, 111, 190, 0.08);
        color: #0f5aa6;
        font-weight: 700;
    }

    html[data-theme="dark"] .doctor-absence-note {
        background: linear-gradient(180deg, rgba(24, 43, 64, 0.96) 0%, rgba(21, 37, 55, 0.96) 100%);
        border-color: rgba(143, 197, 255, 0.16);
        color: #d3e4f3;
    }

    html[data-theme="dark"] .doctor-absence-reason {
        color: #eef5fc;
    }

    html[data-theme="dark"] .doctor-absence-time {
        background: rgba(143, 197, 255, 0.12);
        color: #d8ebff;
    }

    html[data-theme="dark"] .doctor-absence-time-card {
        background: linear-gradient(180deg, rgba(24, 43, 64, 0.96) 0%, rgba(21, 37, 55, 0.96) 100%);
        border-color: rgba(143, 197, 255, 0.16);
    }

    html[data-theme="dark"] .doctor-absence-time-card label {
        color: #d3e4f3;
    }

    html[data-theme="dark"] .doctor-absence-time-card.is-conflicted {
        border-color: rgba(255, 142, 156, 0.34);
        box-shadow: 0 0 0 3px rgba(255, 142, 156, 0.08);
    }

    html[data-theme="dark"] .doctor-absence-time-help {
        color: #9db8cf;
    }

    html[data-theme="dark"] .doctor-absence-conflict-panel {
        background: linear-gradient(180deg, rgba(72, 28, 36, 0.92) 0%, rgba(58, 23, 30, 0.92) 100%);
        border-color: rgba(255, 142, 156, 0.18);
    }

    html[data-theme="dark"] .doctor-absence-conflict-title,
    html[data-theme="dark"] .doctor-absence-conflict-time {
        color: #ffb7c1;
    }

    html[data-theme="dark"] .doctor-absence-conflict-copy {
        color: #f2d6db;
    }

    html[data-theme="dark"] .doctor-absence-conflict-item {
        background: rgba(255, 255, 255, 0.04);
        border-color: rgba(255, 142, 156, 0.1);
    }
</style>
@php
    $appointmentsForPreview = ($appointments ?? collect())->values();
@endphp
<section class="section">
    <div class="section-header">
        <h1>Ausências</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">Dashboard</a></div>
            <div class="breadcrumb-item">Painel do Profissional</div>
            <div class="breadcrumb-item">Ausências</div>
        </div>
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
        @if($errors->any())
            <div class="alert alert-danger">
                <strong>Não foi possível registrar a ausência.</strong>
                <ul class="mb-0 mt-2 pl-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="row mb-4">
            <div class="col-xl-auto col-lg-auto col-md-5 col-12">
                <div class="card card-statistic-1 mb-0 doctor-absence-summary-card">
                    <div class="card-icon bg-warning"><i class="fas fa-user-clock"></i></div>
                    <div class="card-wrap">
                        <div class="card-header"><h4>Ausências programadas</h4></div>
                        <div class="card-body">{{ ($absences ?? collect())->count() }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h4>Registrar ausência</h4>
            </div>
            <div class="card-body">
                <div class="doctor-absence-note mb-4">
                    Use este bloco quando surgir um imprevisto em um horário específico. A ausência só é salva se não houver agendamentos ativos nesse mesmo período.
                </div>

                <form action="{{ route('admin.doctor.absences.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="data_ausencia">Data *</label>
                                <input type="date" class="form-control @error('data_ausencia') is-invalid @enderror" id="data_ausencia" name="data_ausencia" value="{{ old('data_ausencia') }}" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="doctor-absence-time-card" data-absence-time-card>
                                <label for="hora_inicial">Hora inicial *</label>
                                <input type="time" class="form-control @error('hora_inicial') is-invalid @enderror" id="hora_inicial" name="hora_inicial" value="{{ old('hora_inicial') }}" required>
                                <span class="doctor-absence-time-help">Escolha o início exato do período que ficará bloqueado.</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="doctor-absence-time-card" data-absence-time-card>
                                <label for="hora_final">Hora final *</label>
                                <input type="time" class="form-control @error('hora_final') is-invalid @enderror" id="hora_final" name="hora_final" value="{{ old('hora_final') }}" required>
                                <span class="doctor-absence-time-help">O sistema compara este intervalo com os agendamentos do dia.</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="doctor-absence-note h-100 mb-3 mb-md-0">
                                <strong>Validação imediata</strong><br>
                                Ao informar data e horário, a tela já mostra se existe conflito com paciente agendado.
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="motivo">Motivo *</label>
                                <input type="text" class="form-control @error('motivo') is-invalid @enderror" id="motivo" name="motivo" value="{{ old('motivo') }}" placeholder="Ex.: emergência, reunião externa, imprevisto pessoal" required>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group mb-0">
                                <label for="observacao">Observação</label>
                                <textarea class="form-control @error('observacao') is-invalid @enderror" id="observacao" name="observacao" rows="3" placeholder="Se necessário, detalhe o motivo para a recepção entender o bloqueio.">{{ old('observacao') }}</textarea>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="doctor-absence-conflict-panel" data-absence-conflict-panel>
                                <p class="doctor-absence-conflict-title">Já existem agendamentos neste período</p>
                                <ul class="doctor-absence-conflict-list" data-absence-conflict-list></ul>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary">Salvar ausência</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h4>Ausências cadastradas</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-mobile-cards doctor-absence-table">
                        <thead>
                            <tr>
                                <th>Data</th>
                                <th>Período</th>
                                <th>Motivo</th>
                                <th>Observação</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse(($absences ?? collect()) as $absence)
                                <tr>
                                    <td data-label="Data">{{ $absence->data_ausencia?->format('d/m/Y') ?: '-' }}</td>
                                    <td data-label="Período">
                                        <span class="doctor-absence-time">
                                            {{ substr((string) $absence->hora_inicial, 0, 5) }} às {{ substr((string) $absence->hora_final, 0, 5) }}
                                        </span>
                                    </td>
                                    <td data-label="Motivo"><span class="doctor-absence-reason">{{ $absence->motivo }}</span></td>
                                    <td class="table-mobile-full" data-label="Observação">{{ $absence->observacao ?: '-' }}</td>
                                    <td class="table-mobile-full action-button-cell" data-label="Ações">
                                        <form action="{{ route('admin.doctor.absences.destroy', $absence) }}" method="POST" class="d-inline" onsubmit="return confirm('Deseja realmente remover esta ausência?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">Remover</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">Nenhuma ausência cadastrada.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var appointments = @json($appointmentsForPreview);
    var dateInput = document.getElementById('data_ausencia');
    var startInput = document.getElementById('hora_inicial');
    var endInput = document.getElementById('hora_final');
    var panel = document.querySelector('[data-absence-conflict-panel]');
    var list = document.querySelector('[data-absence-conflict-list]');
    var timeCards = Array.prototype.slice.call(document.querySelectorAll('[data-absence-time-card]'));

    function timeToMinutes(value) {
        if (!value || value.indexOf(':') === -1) {
            return null;
        }

        var parts = value.split(':');

        return (Number(parts[0]) * 60) + Number(parts[1]);
    }

    function rangesOverlap(startA, endA, startB, endB) {
        return startA < endB && endA > startB;
    }

    function clearPreview() {
        if (list) {
            list.innerHTML = '';
        }

        if (panel) {
            panel.classList.remove('is-visible');
        }

        timeCards.forEach(function (card) {
            card.classList.remove('is-conflicted');
        });
    }

    function renderConflicts() {
        clearPreview();

        var selectedDate = dateInput ? dateInput.value : '';
        var selectedStart = timeToMinutes(startInput ? startInput.value : '');
        var selectedEnd = timeToMinutes(endInput ? endInput.value : '');

        if (!selectedDate || selectedStart === null || selectedEnd === null || selectedEnd <= selectedStart) {
            return;
        }

        var conflicts = (appointments || []).filter(function (appointment) {
            if (String(appointment.date || '') !== selectedDate) {
                return false;
            }

            var appointmentStart = timeToMinutes(appointment.start_time || '');
            var appointmentEnd = timeToMinutes(appointment.end_time || '');

            if (appointmentStart === null || appointmentEnd === null) {
                return false;
            }

            return rangesOverlap(selectedStart, selectedEnd, appointmentStart, appointmentEnd);
        });

        if (!conflicts.length || !panel || !list) {
            return;
        }

        conflicts.forEach(function (appointment) {
            var item = document.createElement('li');
            item.className = 'doctor-absence-conflict-item';
            item.innerHTML =
                '<span class="doctor-absence-conflict-time">' + (appointment.start_time || '--:--') + ' às ' + (appointment.end_time || '--:--') + '</span>' +
                '<span class="doctor-absence-conflict-copy">' + (appointment.patient_name || 'Paciente') + ' • ' + (appointment.service || 'Atendimento') + '</span>';
            list.appendChild(item);
        });

        panel.classList.add('is-visible');

        timeCards.forEach(function (card) {
            card.classList.add('is-conflicted');
        });
    }

    [dateInput, startInput, endInput].forEach(function (field) {
        if (!field) {
            return;
        }

        field.addEventListener('input', renderConflicts);
        field.addEventListener('change', renderConflicts);
    });

    renderConflicts();
});
</script>
@endsection
