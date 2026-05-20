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

    .doctor-absence-day-overview {
        display: none;
        margin-top: 18px;
        padding: 16px;
        border-radius: 16px;
        border: 1px solid rgba(23, 111, 190, 0.12);
        background: linear-gradient(180deg, rgba(247, 251, 255, 0.98) 0%, rgba(239, 246, 252, 0.98) 100%);
    }

    .doctor-absence-day-overview-title {
        margin-bottom: 12px;
        color: #16344d;
        font-size: 13px;
        font-weight: 800;
    }

    .doctor-absence-overview-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 12px;
    }

    .doctor-absence-overview-panel {
        min-height: 100%;
        padding: 12px;
        border-radius: 14px;
        border: 1px solid rgba(23, 111, 190, 0.1);
        background: rgba(255, 255, 255, 0.72);
    }

    .doctor-absence-overview-heading {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 10px;
        color: #35536e;
        font-size: 12px;
        font-weight: 800;
        text-transform: uppercase;
    }

    .doctor-absence-chip-list {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .doctor-absence-chip {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 7px 10px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 800;
        line-height: 1.2;
    }

    .doctor-absence-chip.available {
        background: rgba(40, 167, 69, 0.12);
        color: #1b6f35;
    }

    .doctor-absence-chip.occupied {
        background: rgba(220, 53, 69, 0.12);
        color: #a12838;
    }

    .doctor-absence-chip.absence {
        background: rgba(255, 193, 7, 0.2);
        color: #8a6100;
    }

    .doctor-absence-chip.interval {
        background: rgba(108, 117, 125, 0.16);
        color: #5b6570;
    }

    .doctor-absence-chip.neutral {
        background: rgba(108, 117, 125, 0.12);
        color: #4d5a66;
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

    html[data-theme="dark"] .doctor-absence-day-overview {
        background: linear-gradient(180deg, rgba(24, 43, 64, 0.96) 0%, rgba(21, 37, 55, 0.96) 100%);
        border-color: rgba(143, 197, 255, 0.16);
    }

    html[data-theme="dark"] .doctor-absence-day-overview-title,
    html[data-theme="dark"] .doctor-absence-overview-heading {
        color: #d3e4f3;
    }

    html[data-theme="dark"] .doctor-absence-overview-panel {
        background: rgba(255, 255, 255, 0.04);
        border-color: rgba(143, 197, 255, 0.12);
    }

    html[data-theme="dark"] .doctor-absence-chip.available {
        background: rgba(64, 201, 117, 0.16);
        color: #92e1ae;
    }

    html[data-theme="dark"] .doctor-absence-chip.occupied {
        background: rgba(255, 142, 156, 0.16);
        color: #ffb7c1;
    }

    html[data-theme="dark"] .doctor-absence-chip.absence {
        background: rgba(255, 193, 7, 0.16);
        color: #ffe09a;
    }

    html[data-theme="dark"] .doctor-absence-chip.interval {
        background: rgba(255, 255, 255, 0.1);
        color: #d3e4f3;
    }

    html[data-theme="dark"] .doctor-absence-chip.neutral {
        background: rgba(255, 255, 255, 0.08);
        color: #d3e4f3;
    }

    @media (max-width: 991.98px) {
        .doctor-absence-overview-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@php
    $appointmentsForPreview = ($appointments ?? collect())->values();
    $absencesForPreview = ($absences ?? collect())
        ->map(fn ($absence) => [
            'date' => optional($absence->data_ausencia)->format('Y-m-d'),
            'start_time' => substr((string) $absence->hora_inicial, 0, 5),
            'end_time' => substr((string) $absence->hora_final, 0, 5),
            'reason' => $absence->motivo,
            'observation' => $absence->observacao,
        ])->values();
    $selectedProfessional = $professional ?? null;
    $isProfessionalAbsenceContext = $isProfessionalAbsenceContext ?? true;
    $professionalOptions = $professionalOptions ?? collect();
@endphp
<section class="section">
    <div class="section-header">
        <h1>Ausências</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">Dashboard</a></div>
            <div class="breadcrumb-item">{{ $isProfessionalAbsenceContext ? 'Painel do Profissional' : 'Agendamentos' }}</div>
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

        @if(! $isProfessionalAbsenceContext)
            <div class="card mb-4">
                <div class="card-header">
                    <h4>Selecionar profissional</h4>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.doctor.absences') }}">
                        <div class="row align-items-end">
                            <div class="col-lg-5 col-md-8 col-12">
                                <div class="form-group mb-md-0">
                                    <label for="professional_id">Profissional</label>
                                    <select class="form-control" id="professional_id" name="professional_id" onchange="this.form.submit()">
                                        @forelse($professionalOptions as $option)
                                            <option value="{{ $option->id }}" @selected((int) ($selectedProfessional?->id ?? 0) === (int) $option->id)>{{ $option->nome }}</option>
                                        @empty
                                            <option value="">Nenhum profissional ativo</option>
                                        @endforelse
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-7 col-md-4 col-12">
                                <div class="doctor-absence-note h-100">
                                    {{ $selectedProfessional ? 'As ausencias abaixo pertencem ao profissional selecionado.' : 'Selecione um profissional para visualizar e registrar ausencias.' }}
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        @endif

        <div class="card mb-4">
            <div class="card-header">
                <h4>Registrar ausência</h4>
            </div>
            <div class="card-body">
                @if($selectedProfessional)
                <form action="{{ route('admin.doctor.absences.store') }}" method="POST">
                    @csrf
                    @if(! $isProfessionalAbsenceContext)
                        <input type="hidden" name="professional_id" value="{{ $selectedProfessional->id }}">
                    @endif
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="data_ausencia">Data *</label>
                                <input type="date" class="form-control @error('data_ausencia') is-invalid @enderror" id="data_ausencia" name="data_ausencia" value="{{ old('data_ausencia') }}" min="{{ ($minimumAbsenceDate ?? now())->format('Y-m-d') }}" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="doctor-absence-time-card" data-absence-time-card>
                                <label for="hora_inicial">Hora inicial *</label>
                                <select class="form-control @error('hora_inicial') is-invalid @enderror" id="hora_inicial" name="hora_inicial" required data-initial-value="{{ old('hora_inicial') }}">
                                    <option value="">Selecione uma data</option>
                                </select>
                                <span class="doctor-absence-time-help">Escolha o início exato do período que ficará bloqueado.</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="doctor-absence-time-card" data-absence-time-card>
                                <label for="hora_final">Hora final *</label>
                                <select class="form-control @error('hora_final') is-invalid @enderror" id="hora_final" name="hora_final" required data-initial-value="{{ old('hora_final') }}">
                                    <option value="">Selecione o inÃ­cio</option>
                                </select>
                                <span class="doctor-absence-time-help">O sistema compara este intervalo com os agendamentos do dia.</span>
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
                            <div id="absence-day-overview" class="doctor-absence-day-overview"></div>
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
                @endif
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
    var registeredAbsences = @json($absencesForPreview);
    var clinicHours = @json($clinicHours ?? null);
    var professionalSchedules = @json($professionalSchedules ?? []);
    var dateInput = document.getElementById('data_ausencia');
    var startInput = document.getElementById('hora_inicial');
    var endInput = document.getElementById('hora_final');
    var dayOverview = document.getElementById('absence-day-overview');
    var panel = document.querySelector('[data-absence-conflict-panel]');
    var list = document.querySelector('[data-absence-conflict-list]');
    var timeCards = Array.prototype.slice.call(document.querySelectorAll('[data-absence-time-card]'));
    var timeSlotStep = 5;
    var initialStartTime = startInput ? (startInput.getAttribute('data-initial-value') || '') : '';
    var initialEndTime = endInput ? (endInput.getAttribute('data-initial-value') || '') : '';

    if (!dateInput || !startInput || !endInput) {
        return;
    }

    function timeToMinutes(value) {
        if (!value || value.indexOf(':') === -1) {
            return null;
        }

        var parts = value.split(':');

        return (Number(parts[0]) * 60) + Number(parts[1]);
    }

    function timeFromMinutes(totalMinutes) {
        var hours = Math.floor(totalMinutes / 60);
        var minutes = totalMinutes % 60;

        return String(hours).padStart(2, '0') + ':' + String(minutes).padStart(2, '0');
    }

    function ceilToStep(totalMinutes) {
        return totalMinutes === null ? null : Math.ceil(totalMinutes / timeSlotStep) * timeSlotStep;
    }

    function floorToStep(totalMinutes) {
        return totalMinutes === null ? null : Math.floor(totalMinutes / timeSlotStep) * timeSlotStep;
    }

    function currentDateValue() {
        var now = new Date();
        var month = String(now.getMonth() + 1).padStart(2, '0');
        var day = String(now.getDate()).padStart(2, '0');

        return now.getFullYear() + '-' + month + '-' + day;
    }

    function currentTimeValue() {
        var now = new Date();

        return String(now.getHours()).padStart(2, '0') + ':' + String(now.getMinutes()).padStart(2, '0');
    }

    function selectedWeekday() {
        if (!dateInput || !dateInput.value) {
            return null;
        }

        var parts = dateInput.value.split('-');

        if (parts.length !== 3) {
            return null;
        }

        var date = new Date(Number(parts[0]), Number(parts[1]) - 1, Number(parts[2]));
        var weekday = date.getDay();

        return weekday === 0 ? 7 : weekday;
    }

    function clinicRestrictedInterval() {
        if (!clinicHours || !clinicHours.lunch_start_time || !clinicHours.lunch_end_time) {
            return null;
        }

        return {
            start: timeToMinutes(clinicHours.lunch_start_time),
            end: timeToMinutes(clinicHours.lunch_end_time)
        };
    }

    function splitByInterval(windows, interval) {
        if (!interval || interval.start === null || interval.end === null || interval.end <= interval.start) {
            return windows;
        }

        return (windows || []).reduce(function (result, windowRange) {
            if (interval.end <= windowRange.start || interval.start >= windowRange.end) {
                result.push(windowRange);
                return result;
            }

            if (interval.start > windowRange.start) {
                result.push({ start: windowRange.start, end: interval.start });
            }

            if (interval.end < windowRange.end) {
                result.push({ start: interval.end, end: windowRange.end });
            }

            return result;
        }, []);
    }

    function professionalScheduleWindowsForSelectedDate() {
        var weekday = selectedWeekday();

        if (weekday === null || !Array.isArray(professionalSchedules)) {
            return [];
        }

        var clinicOpen = clinicHours && clinicHours.opening_time ? timeToMinutes(clinicHours.opening_time) : 0;
        var clinicClose = clinicHours && clinicHours.closing_time ? timeToMinutes(clinicHours.closing_time) : (23 * 60) + 59;

        var windows = professionalSchedules
            .filter(function (schedule) {
                return Number(schedule.day_of_week) === Number(weekday);
            })
            .reduce(function (intervals, schedule) {
                var scheduleStart = timeToMinutes(schedule.start_time);
                var scheduleEnd = timeToMinutes(schedule.end_time);
                var start = Math.max(scheduleStart, clinicOpen);
                var end = Math.min(scheduleEnd, clinicClose);
                var breakStart = timeToMinutes(schedule.break_start_time);
                var breakEnd = timeToMinutes(schedule.break_end_time);

                if (scheduleStart === null || scheduleEnd === null || start === null || end === null || end <= start) {
                    return intervals;
                }

                if (breakStart !== null && breakEnd !== null && breakStart > start && breakEnd < end) {
                    intervals.push({ start: start, end: breakStart });
                    intervals.push({ start: breakEnd, end: end });
                    return intervals;
                }

                intervals.push({ start: start, end: end });
                return intervals;
            }, []);

        return windows.filter(function (windowRange) {
            return windowRange.end > windowRange.start;
        });
    }

    function availabilityWindowsForSelectedDate() {
        return splitByInterval(professionalScheduleWindowsForSelectedDate(), clinicRestrictedInterval()).filter(function (windowRange) {
            return windowRange.end > windowRange.start;
        });
    }

    function displayWindowsForSelectedDate() {
        var windows = professionalScheduleWindowsForSelectedDate().slice().sort(function (left, right) {
            return left.start - right.start;
        });
        var interval = clinicRestrictedInterval();

        if (!windows.length || !interval) {
            return windows;
        }

        return windows.reduce(function (merged, currentWindow) {
            if (!merged.length) {
                merged.push({ start: currentWindow.start, end: currentWindow.end });
                return merged;
            }

            var previousWindow = merged[merged.length - 1];
            var shouldBridgeInterval = previousWindow.end <= interval.start && currentWindow.start >= interval.end;

            if (currentWindow.start <= previousWindow.end || shouldBridgeInterval) {
                previousWindow.end = Math.max(previousWindow.end, currentWindow.end);
                return merged;
            }

            merged.push({ start: currentWindow.start, end: currentWindow.end });
            return merged;
        }, []);
    }

    function occupiedIntervalsForSelectedDate() {
        var selectedDate = dateInput ? dateInput.value : '';

        return (appointments || []).filter(function (appointment) {
            return selectedDate && String(appointment.date || '') === selectedDate;
        }).map(function (appointment) {
            return {
                start: timeToMinutes(appointment.start_time || ''),
                end: timeToMinutes(appointment.end_time || ''),
                start_time: appointment.start_time || '',
                end_time: appointment.end_time || '',
                patient_name: appointment.patient_name || 'Paciente',
                patient: appointment.patient_name || 'Paciente',
                service: appointment.service || 'Atendimento'
            };
        }).filter(function (item) {
            return item.start !== null && item.end !== null && item.end > item.start;
        });
    }

    function absenceIntervalsForSelectedDate() {
        var selectedDate = dateInput ? dateInput.value : '';

        return (registeredAbsences || []).filter(function (absence) {
            return selectedDate && String(absence.date || '') === selectedDate;
        }).map(function (absence) {
            return {
                start: timeToMinutes(absence.start_time || ''),
                end: timeToMinutes(absence.end_time || ''),
                start_time: absence.start_time || '',
                end_time: absence.end_time || '',
                reason: absence.reason || 'Ausencia programada',
                observation: absence.observation || ''
            };
        }).filter(function (item) {
            return item.start !== null && item.end !== null && item.end > item.start;
        });
    }

    function fillTimeSelect(selectElement, options, selectedValue, placeholder) {
        if (!selectElement) {
            return;
        }

        selectElement.innerHTML = '';

        var placeholderOption = document.createElement('option');
        placeholderOption.value = '';
        placeholderOption.textContent = placeholder || 'Selecione';
        selectElement.appendChild(placeholderOption);

        (options || []).forEach(function (time) {
            var option = document.createElement('option');

            if (typeof time === 'object' && time !== null) {
                option.value = time.value || '';
                option.textContent = time.label || time.value || '';
                option.disabled = !!time.disabled;

                if (time.color) {
                    option.style.color = time.color;
                }

                if (time.backgroundColor) {
                    option.style.backgroundColor = time.backgroundColor;
                }
            } else {
                option.value = time;
                option.textContent = time;
            }

            option.selected = String(selectedValue || '') === String(option.value);
            selectElement.appendChild(option);
        });

        if (selectedValue && !Array.from(selectElement.options).some(function (option) { return option.value === selectedValue; })) {
            selectElement.value = '';
        }
    }

    function overlapsRestrictedInterval(startMinutes, endMinutes) {
        var interval = clinicRestrictedInterval();

        if (!interval || startMinutes === null || endMinutes === null) {
            return false;
        }

        return rangesOverlap(startMinutes, endMinutes, interval.start, interval.end);
    }

    function startTimeOptions() {
        return displayWindowsForSelectedDate().reduce(function (times, windowRange) {
            var start = ceilToStep(windowRange.start);
            var end = floorToStep(windowRange.end - timeSlotStep);
            var occupied = occupiedIntervalsForSelectedDate();
            var absences = absenceIntervalsForSelectedDate();
            var isToday = dateInput && dateInput.value === currentDateValue();
            var nowMinutes = ceilToStep(timeToMinutes(currentTimeValue()));

            for (var minutes = start; minutes <= end; minutes += timeSlotStep) {
                var endMinutes = minutes + timeSlotStep;
                var intervalBlocked = overlapsRestrictedInterval(minutes, endMinutes);
                var occupiedMatch = occupied.find(function (appointment) {
                    return rangesOverlap(minutes, endMinutes, appointment.start, appointment.end);
                });
                var absenceMatch = absences.find(function (absence) {
                    return rangesOverlap(minutes, endMinutes, absence.start, absence.end);
                });
                var isPast = isToday && nowMinutes !== null && minutes < nowMinutes;
                var label = timeFromMinutes(minutes);

                if (intervalBlocked) {
                    label += ' - intervalo';
                } else if (occupiedMatch) {
                    label += ' - ocupado';
                } else if (absenceMatch) {
                    label += ' - ausencia';
                } else if (isPast) {
                    label += ' - horario ja passou';
                }

                times.push({
                    value: timeFromMinutes(minutes),
                    label: label,
                    disabled: intervalBlocked || !!occupiedMatch || !!absenceMatch || isPast,
                    color: intervalBlocked || isPast ? '#6c757d' : (occupiedMatch ? '#a12838' : (absenceMatch ? '#8a6100' : '')),
                    backgroundColor: intervalBlocked || isPast ? '#eef1f4' : (occupiedMatch ? 'rgba(220, 53, 69, 0.12)' : (absenceMatch ? 'rgba(255, 193, 7, 0.2)' : ''))
                });
            }

            return times;
        }, []);
    }

    function endTimeOptions() {
        var selectedStart = timeToMinutes(startInput ? startInput.value : '');

        if (selectedStart === null) {
            return [];
        }

        var activeWindow = displayWindowsForSelectedDate().find(function (windowRange) {
            return selectedStart >= windowRange.start && selectedStart < windowRange.end;
        });

        if (!activeWindow) {
            return [];
        }

        var times = [];
        var occupied = occupiedIntervalsForSelectedDate();
        var absences = absenceIntervalsForSelectedDate();
        var start = ceilToStep(selectedStart + timeSlotStep);
        var end = floorToStep(activeWindow.end);

        for (var minutes = start; minutes <= end; minutes += timeSlotStep) {
            var intervalBlocked = overlapsRestrictedInterval(selectedStart, minutes);
            var occupiedMatch = occupied.find(function (appointment) {
                return rangesOverlap(selectedStart, minutes, appointment.start, appointment.end);
            });
            var absenceMatch = absences.find(function (absence) {
                return rangesOverlap(selectedStart, minutes, absence.start, absence.end);
            });
            var label = timeFromMinutes(minutes);

            if (intervalBlocked) {
                label += ' - intervalo';
            } else if (occupiedMatch) {
                label += ' - ocupado';
            } else if (absenceMatch) {
                label += ' - ausencia';
            }

            times.push({
                value: timeFromMinutes(minutes),
                label: label,
                disabled: intervalBlocked || !!occupiedMatch || !!absenceMatch,
                color: intervalBlocked ? '#6c757d' : (occupiedMatch ? '#a12838' : (absenceMatch ? '#8a6100' : '')),
                backgroundColor: intervalBlocked ? '#eef1f4' : (occupiedMatch ? 'rgba(220, 53, 69, 0.12)' : (absenceMatch ? 'rgba(255, 193, 7, 0.2)' : ''))
            });
        }

        return times;
    }

    function renderDayOverview() {
        if (!dayOverview) {
            return;
        }

        if (!dateInput || !dateInput.value) {
            dayOverview.style.display = 'none';
            dayOverview.innerHTML = '';
            return;
        }

        var windows = availabilityWindowsForSelectedDate();
        var occupied = occupiedIntervalsForSelectedDate();
        var absences = absenceIntervalsForSelectedDate();
        var restrictedInterval = clinicRestrictedInterval();

        var availabilityHtml = windows.length
            ? windows.map(function (windowRange) {
                return '<span class="doctor-absence-chip available">Disponivel: ' + timeFromMinutes(windowRange.start) + ' as ' + timeFromMinutes(windowRange.end) + '</span>';
            }).join('')
            : '<span class="doctor-absence-chip neutral">Sem disponibilidade neste dia.</span>';

        var occupiedHtml = occupied.length
            ? occupied.map(function (interval) {
                return '<span class="doctor-absence-chip occupied">' + timeFromMinutes(interval.start) + ' as ' + timeFromMinutes(interval.end) + ' - ' + interval.patient + '</span>';
            }).join('')
            : '<span class="doctor-absence-chip neutral">Nenhum agendamento neste dia.</span>';

        var absenceHtml = absences.length
            ? absences.map(function (interval) {
                return '<span class="doctor-absence-chip absence">' + timeFromMinutes(interval.start) + ' as ' + timeFromMinutes(interval.end) + ' - ' + interval.reason + '</span>';
            }).join('')
            : '<span class="doctor-absence-chip neutral">Nenhuma ausencia cadastrada neste dia.</span>';

        var intervalHtml = restrictedInterval
            ? '<span class="doctor-absence-chip interval">Intervalo: ' + timeFromMinutes(restrictedInterval.start) + ' as ' + timeFromMinutes(restrictedInterval.end) + '</span>'
            : '<span class="doctor-absence-chip neutral">Sem intervalo configurado.</span>';

        dayOverview.style.display = 'block';
        dayOverview.innerHTML = '' +
            '<div class="doctor-absence-day-overview-title">Resumo da agenda do dia</div>' +
            '<div class="doctor-absence-overview-grid">' +
                '<div class="doctor-absence-overview-panel">' +
                    '<div class="doctor-absence-overview-heading"><i class="fas fa-clock"></i><span>Disponibilidade</span></div>' +
                    '<div class="doctor-absence-chip-list">' + availabilityHtml + '</div>' +
                '</div>' +
                '<div class="doctor-absence-overview-panel">' +
                    '<div class="doctor-absence-overview-heading"><i class="fas fa-user-check"></i><span>Agenda ocupada</span></div>' +
                    '<div class="doctor-absence-chip-list">' + occupiedHtml + '</div>' +
                '</div>' +
                '<div class="doctor-absence-overview-panel">' +
                    '<div class="doctor-absence-overview-heading"><i class="fas fa-user-clock"></i><span>Ausencias</span></div>' +
                    '<div class="doctor-absence-chip-list">' + absenceHtml + '</div>' +
                '</div>' +
                '<div class="doctor-absence-overview-panel">' +
                    '<div class="doctor-absence-overview-heading"><i class="fas fa-coffee"></i><span>Intervalo da Clinica</span></div>' +
                    '<div class="doctor-absence-chip-list">' + intervalHtml + '</div>' +
                '</div>' +
            '</div>';
    }

    function updateTimeOptions() {
        var previousStart = startInput ? (startInput.value || initialStartTime) : '';
        var starts = startTimeOptions();

        fillTimeSelect(startInput, starts, previousStart, dateInput && dateInput.value ? 'Selecione' : 'Selecione uma data');

        if (startInput && previousStart && startInput.value === previousStart) {
            initialStartTime = '';
        }

        var previousEnd = endInput ? (endInput.value || initialEndTime) : '';
        var ends = endTimeOptions();

        fillTimeSelect(endInput, ends, previousEnd, startInput && startInput.value ? 'Selecione' : 'Selecione o inicio');

        if (endInput && previousEnd && endInput.value === previousEnd) {
            initialEndTime = '';
        }

        renderDayOverview();
        renderConflicts();
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

        var conflicts = occupiedIntervalsForSelectedDate().filter(function (appointment) {
            return rangesOverlap(selectedStart, selectedEnd, appointment.start, appointment.end);
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

        field.addEventListener('input', updateTimeOptions);
        field.addEventListener('change', updateTimeOptions);
    });

    if (dateInput && dateInput.min && dateInput.value && dateInput.value < dateInput.min) {
        dateInput.value = dateInput.min;
    }

    updateTimeOptions();
});
</script>
@endsection
