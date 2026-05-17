@extends('admin.layouts.master')
@section('content')
<style>
    .professional-modal {
        z-index: 10060;
    }

    .schedule-row-feedback {
        display: block;
        margin-top: -2px;
        font-size: 12px;
        line-height: 1.45;
    }

    .professionals-table {
        border-collapse: separate;
        border-spacing: 0 14px;
    }

    .professionals-table thead th {
        border-bottom: 0;
    }

    .professionals-table tbody tr {
        background: #ffffff;
        box-shadow: 0 8px 20px rgba(15, 23, 42, 0.05);
    }

    html[data-theme="dark"] .professionals-table tbody tr {
        background: rgba(19, 33, 49, 0.98);
        box-shadow: 0 16px 28px rgba(2, 8, 15, 0.28);
    }

    .professionals-table tbody td {
        vertical-align: middle;
        padding-top: 18px;
        padding-bottom: 18px;
        border-top: 1px solid #eef2f7;
        border-bottom: 1px solid #eef2f7;
    }

    html[data-theme="dark"] .professionals-table tbody td {
        border-top-color: rgba(143, 197, 255, 0.12);
        border-bottom-color: rgba(143, 197, 255, 0.12);
    }

    .professionals-table tbody td:first-child {
        border-left: 1px solid #eef2f7;
        border-top-left-radius: 14px;
        border-bottom-left-radius: 14px;
        padding-left: 22px;
    }

    html[data-theme="dark"] .professionals-table tbody td:first-child,
    html[data-theme="dark"] .professionals-table tbody td:last-child {
        border-left-color: rgba(143, 197, 255, 0.12);
        border-right-color: rgba(143, 197, 255, 0.12);
    }

    .professionals-table tbody td:last-child {
        border-right: 1px solid #eef2f7;
        border-top-right-radius: 14px;
        border-bottom-right-radius: 14px;
        padding-right: 22px;
    }

    .professional-edit-dialog {
        width: 96vw;
        max-width: 1400px;
        margin: 1rem auto;
    }

    .professional-edit-dialog .modal-content {
        min-height: 88vh;
    }

    .professional-edit-dialog .modal-body {
        max-height: calc(88vh - 130px);
        overflow-y: auto;
        overflow-x: hidden;
    }

    .professional-registry-cell {
        min-width: 220px;
    }

    .professional-identity-cell,
    .professional-linked-user-cell,
    .professional-specialty-cell,
    .professional-subspecialty-cell {
        min-width: 170px;
    }

    .professional-primary-text {
        font-size: 14px;
        font-weight: 700;
        color: #16344d;
    }

    .professional-identity-cell {
        min-width: 220px;
    }

    .professional-identity-content {
        display: flex;
        align-items: center;
        gap: 12px;
        text-align: left;
    }

    .professional-identity-meta {
        min-width: 0;
    }

    .professional-secondary-text {
        margin-top: 4px;
        font-size: 12px;
        color: #6b88a3;
    }

    .professional-subspecialty-cell .professional-tags-list {
        gap: 6px;
    }

    .professional-subspecialty-empty {
        display: inline-flex;
        align-items: center;
        padding: 7px 10px;
        border-radius: 999px;
        background: #f4f7fb;
        color: #6b88a3;
        font-size: 12px;
        font-weight: 600;
    }

    .professional-schedule-mode-options {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 12px;
        margin-bottom: 14px;
    }

    .professional-schedule-mode-option {
        position: relative;
    }

    .professional-schedule-mode-option input {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }

    .professional-schedule-mode-card {
        display: block;
        padding: 14px 16px;
        border: 1px solid #d7e5f3;
        border-radius: 14px;
        background: #f8fbff;
        cursor: pointer;
        transition: border-color 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
    }

    .professional-schedule-mode-option input:checked + .professional-schedule-mode-card {
        border-color: #2f79c7;
        background: #eef6ff;
        box-shadow: 0 0 0 3px rgba(47, 121, 199, 0.12);
    }

    .professional-schedule-mode-title {
        display: block;
        font-size: 13px;
        font-weight: 700;
        color: #16344d;
    }

    .professional-schedule-mode-description {
        display: block;
        margin-top: 4px;
        font-size: 12px;
        color: #6b88a3;
        line-height: 1.45;
    }

    .professional-schedule-summary {
        display: none;
        margin-bottom: 12px;
        padding: 12px 14px;
        border-radius: 12px;
        background: #f4f8fc;
        color: #31506b;
        font-size: 12px;
        font-weight: 600;
    }

    .professional-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
        flex: 0 0 auto;
        border: 2px solid rgba(23, 111, 190, 0.12);
    }

    .professional-avatar-large {
        width: 132px;
        height: 132px;
        border-radius: 28px;
        object-fit: cover;
        display: block;
        margin: 0 auto 16px;
        border: 4px solid rgba(47, 121, 199, 0.12);
        box-shadow: 0 16px 30px rgba(15, 23, 42, 0.1);
    }

    .professional-view-photo-card {
        text-align: center;
    }

    .professional-availability-trigger {
        min-width: 0;
        width: auto;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .professional-period-grid {
        display: grid;
        grid-template-columns: 1.2fr repeat(4, minmax(120px, 1fr));
        gap: 12px;
        align-items: end;
    }

    .schedule-row .remove-schedule-row {
        margin-top: 10px;
    }

    .professional-period-hint {
        margin-top: 12px;
    }

    .professional-availability-summary {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 14px;
    }

    .professional-availability-card {
        border: 1px solid #e5edf5;
        border-radius: 16px;
        padding: 16px;
        background: #fbfdff;
    }

    .professional-availability-card-title {
        font-size: 13px;
        font-weight: 700;
        color: #16344d;
        margin-bottom: 12px;
    }

    .professional-availability-block + .professional-availability-block {
        margin-top: 10px;
        padding-top: 10px;
        border-top: 1px solid #edf3f8;
    }

    .professional-availability-label {
        display: block;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        color: #6b88a3;
        margin-bottom: 4px;
    }

    .professional-availability-value {
        font-size: 14px;
        font-weight: 700;
        color: #16344d;
    }

    .professional-modal + .modal-backdrop,
    .modal-backdrop.show {
        z-index: 10050;
    }

    html[data-theme="dark"] .professional-modal .bg-white,
    html[data-theme="dark"] .professional-modal .badge-light,
    html[data-theme="dark"] .professionals-table .badge-light {
        background: rgba(22, 40, 59, 0.94) !important;
        color: var(--text-primary) !important;
        border-color: rgba(143, 197, 255, 0.16) !important;
    }

    .professional-actions {
        display: inline-flex;
        flex-wrap: nowrap;
        align-items: center;
        gap: 6px;
        justify-content: flex-start;
        width: auto;
        max-width: 100%;
        white-space: nowrap;
    }

    .professional-actions form {
        margin: 0;
    }

    .professional-actions .btn,
    .settings-actions .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: auto;
        min-width: 0;
        max-width: 100%;
        min-height: 28px;
        padding: 4px 6px;
        font-size: 10px;
        line-height: 1.1;
        border-radius: 10px;
        white-space: nowrap;
    }

    .professional-subspecialty-cell,
    .professional-availability-cell {
        text-align: center;
    }

    .professional-actions-cell {
        min-width: 190px;
        white-space: nowrap;
    }

    .professionals-table {
        min-width: 1280px;
    }

    .professional-tags-list {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .professional-tag-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 10px;
        border-radius: 999px;
        background: #eef6ff;
        border: 1px solid #cfe1f5;
        color: #164569;
        font-size: 12px;
        font-weight: 700;
        line-height: 1;
    }

    .professional-tag-pill button {
        border: 0;
        background: transparent;
        color: inherit;
        padding: 0;
        line-height: 1;
        font-size: 14px;
    }

    .professional-color-label {
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .professional-color-help {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(180deg, #fff5d8 0%, #ffe7a6 100%);
        color: #8c6300;
        border: 1px solid rgba(173, 121, 0, 0.2);
        font-size: 12px;
        box-shadow: 0 8px 16px rgba(173, 121, 0, 0.12);
        cursor: help;
    }

    .professional-color-tools {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 8px;
        margin-top: 10px;
    }

    .professional-color-random {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 6px 12px;
        border-radius: 999px;
        background: linear-gradient(180deg, #f9fbff 0%, #e8f1ff 100%);
        border: 1px solid rgba(23, 111, 190, 0.18);
        color: #225d98;
        font-weight: 600;
        box-shadow: 0 10px 18px rgba(23, 111, 190, 0.12);
    }

    .professional-color-random:hover,
    .professional-color-random:focus {
        background: linear-gradient(180deg, #ffffff 0%, #dcecff 100%);
        color: #17497b;
    }

    html[data-theme="dark"] .professional-color-help {
        background: linear-gradient(180deg, rgba(112, 88, 24, 0.92) 0%, rgba(87, 66, 17, 0.96) 100%);
        border-color: rgba(255, 223, 129, 0.2);
        color: #fff1c3;
    }

    html[data-theme="dark"] .professional-color-random {
        background: linear-gradient(180deg, rgba(33, 58, 84, 0.96) 0%, rgba(24, 45, 66, 0.98) 100%);
        border-color: rgba(143, 197, 255, 0.18);
        color: #d8ebff;
        box-shadow: 0 12px 22px rgba(2, 8, 15, 0.24);
    }

    html[data-theme="dark"] .professional-color-random:hover,
    html[data-theme="dark"] .professional-color-random:focus {
        background: linear-gradient(180deg, rgba(42, 71, 102, 0.96) 0%, rgba(28, 51, 74, 0.98) 100%);
        color: #f1f7ff;
    }

    html[data-theme="dark"] .professional-tag-pill {
        background: rgba(31, 61, 92, 0.9);
        border-color: rgba(143, 197, 255, 0.22);
        color: #e7f3ff;
    }

    html[data-theme="dark"] .professional-primary-text {
        color: #eef5fc;
    }

    html[data-theme="dark"] .professional-secondary-text {
        color: #a7c1d9;
    }

    html[data-theme="dark"] .professional-subspecialty-empty {
        background: rgba(31, 61, 92, 0.86);
        color: #c8def2;
    }

    html[data-theme="dark"] .professional-schedule-mode-card {
        border-color: rgba(143, 197, 255, 0.16);
        background: rgba(20, 35, 52, 0.92);
    }

    html[data-theme="dark"] .professional-schedule-mode-option input:checked + .professional-schedule-mode-card {
        border-color: rgba(143, 197, 255, 0.55);
        background: rgba(31, 61, 92, 0.82);
        box-shadow: 0 0 0 3px rgba(143, 197, 255, 0.12);
    }

    html[data-theme="dark"] .professional-schedule-mode-title {
        color: #eef5fc;
    }

    html[data-theme="dark"] .professional-schedule-mode-description,
    html[data-theme="dark"] .professional-schedule-summary {
        color: #b5cde3;
    }

    html[data-theme="dark"] .professional-schedule-summary {
        background: rgba(31, 61, 92, 0.68);
    }

    html[data-theme="dark"] .professional-availability-card {
        border-color: rgba(143, 197, 255, 0.12);
        background: rgba(20, 35, 52, 0.92);
    }

    html[data-theme="dark"] .professional-availability-card-title,
    html[data-theme="dark"] .professional-availability-value {
        color: #eef5fc;
    }

    html[data-theme="dark"] .professional-availability-label {
        color: #a7c1d9;
    }

    html[data-theme="dark"] .professional-availability-block + .professional-availability-block {
        border-top-color: rgba(143, 197, 255, 0.1);
    }

    html[data-theme="dark"] .professional-avatar,
    html[data-theme="dark"] .professional-avatar-large {
        border-color: rgba(143, 197, 255, 0.16);
        box-shadow: 0 16px 26px rgba(2, 8, 15, 0.26);
    }

    @media (max-width: 767.98px) {
        .professional-edit-dialog {
            width: calc(100vw - 20px);
            max-width: calc(100vw - 20px);
            margin: 10px auto;
        }

        .professional-edit-dialog .modal-content {
            min-height: auto;
        }

        .professional-edit-dialog .modal-body {
            max-height: none;
        }

        .professional-registry-cell {
            min-width: 0;
        }

        .professional-actions {
            display: inline-flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: flex-start;
            gap: 8px;
            width: auto;
            max-width: 100%;
        }

        .professional-actions > *,
        .professional-actions form,
        .professional-actions .btn {
            width: auto !important;
            min-width: 0;
            max-width: 100%;
            flex: 0 0 auto;
        }

        .professional-actions .btn {
            min-height: 26px !important;
            padding: 3px 8px !important;
            font-size: 9.5px !important;
            line-height: 1 !important;
            border-radius: 9px !important;
            white-space: nowrap;
        }

        .professional-subspecialty-cell,
        .professional-availability-cell {
            text-align: left;
        }

        .professional-availability-cell::before {
            text-align: center;
        }

        .professional-subspecialty-cell .text-center,
        .professional-availability-cell .text-center {
            text-align: left !important;
        }

        .professional-subspecialty-cell .btn,
        .professional-availability-cell .btn {
            width: auto !important;
            min-width: 0;
            max-width: 100%;
        }

        .action-button-cell .professional-actions {
            display: inline-flex !important;
            width: auto !important;
            max-width: 100%;
        }

        .professional-actions-cell {
            min-width: 220px !important;
            white-space: normal !important;
        }

        .professional-availability-cell {
            text-align: center;
        }

        .professional-availability-cell .professional-availability-trigger {
            margin-left: auto;
            margin-right: auto;
        }

        .professional-period-grid {
            grid-template-columns: 1fr;
        }
    }
    .settings-toolbar {
        gap: 12px;
    }

    .settings-toolbar-form {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 8px;
    }

    .settings-toolbar-search {
        min-width: 260px;
    }

    @media (max-width: 767.98px) {
        .settings-toolbar,
        .settings-toolbar-form {
            width: 100%;
            align-items: stretch !important;
        }

        .settings-toolbar-form > *,
        .settings-toolbar-form .btn,
        .settings-toolbar-search {
            width: 100%;
            min-width: 0;
        }

        .professional-form > .btn.btn-primary {
            width: 100%;
        }
    }
</style>
@php
    $buildProfessionalScheduleLabels = function ($schedule) use ($weekDays) {
        $dayLabel = $weekDays[$schedule['day_of_week']] ?? $schedule['day_of_week'];

        if (!empty($schedule['break_start_time']) && !empty($schedule['break_end_time'])
            && $schedule['start_time'] < $schedule['break_start_time']
            && $schedule['break_end_time'] < $schedule['end_time']) {
            return [
                $dayLabel . ' ' . $schedule['start_time'] . ' às ' . $schedule['break_start_time'],
                $dayLabel . ' ' . $schedule['break_end_time'] . ' às ' . $schedule['end_time'],
            ];
        }

        return [
            $dayLabel . ' ' . $schedule['start_time'] . ' às ' . $schedule['end_time'],
        ];
    };

    $isClinicHoursSchedule = function ($schedules) use ($clinicHoursWindow) {
        if ($schedules->count() !== 5) {
            return false;
        }

        $openingTime = $clinicHoursWindow['opening_time'] ?? null;
        $closingTime = $clinicHoursWindow['closing_time'] ?? null;

        if (!$openingTime || !$closingTime) {
            return false;
        }

        $sortedSchedules = $schedules->sortBy('day_of_week')->values();

        foreach ([1, 2, 3, 4, 5] as $index => $dayOfWeek) {
            $schedule = $sortedSchedules[$index] ?? null;

            if (!$schedule || (int) $schedule->day_of_week !== $dayOfWeek) {
                return false;
            }

            if (substr((string) $schedule->start_time, 0, 5) !== $openingTime || substr((string) $schedule->end_time, 0, 5) !== $closingTime) {
                return false;
            }
        }

        return true;
    };

    $professionalPhotoUrl = function ($professional) {
        return $professional->user?->profile_photo_url ?: asset('backend/assets/img/avatar/avatar-1.png');
    };

    $buildSpecificScheduleRows = function ($days = [], $morningStarts = [], $morningEnds = [], $afternoonStarts = [], $afternoonEnds = [], $schedules = null) use ($clinicHoursWindow) {
        $rows = [];
        $lunchStartTime = $clinicHoursWindow['lunch_start_time'] ?? null;
        $lunchEndTime = $clinicHoursWindow['lunch_end_time'] ?? null;

        if ($schedules instanceof \Illuminate\Support\Collection && $schedules->isNotEmpty()) {
            foreach ($schedules->sortBy('day_of_week')->groupBy('day_of_week') as $dayOfWeek => $daySchedules) {
                $row = [
                    'day_of_week' => (string) $dayOfWeek,
                    'morning_start_time' => '',
                    'morning_end_time' => '',
                    'afternoon_start_time' => '',
                    'afternoon_end_time' => '',
                ];

                foreach ($daySchedules->sortBy('start_time') as $schedule) {
                    $startTime = substr((string) $schedule->start_time, 0, 5);
                    $endTime = substr((string) $schedule->end_time, 0, 5);
                    $breakStartTime = $schedule->break_start_time ? substr((string) $schedule->break_start_time, 0, 5) : null;
                    $breakEndTime = $schedule->break_end_time ? substr((string) $schedule->break_end_time, 0, 5) : null;

                    if ($breakStartTime && $breakEndTime && $startTime < $breakStartTime && $breakEndTime < $endTime) {
                        $row['morning_start_time'] = $startTime;
                        $row['morning_end_time'] = $breakStartTime;
                        $row['afternoon_start_time'] = $breakEndTime;
                        $row['afternoon_end_time'] = $endTime;
                        continue;
                    }

                    if ($lunchStartTime && $endTime <= $lunchStartTime) {
                        $row['morning_start_time'] = $startTime;
                        $row['morning_end_time'] = $endTime;
                        continue;
                    }

                    if ($lunchEndTime && $startTime >= $lunchEndTime) {
                        $row['afternoon_start_time'] = $startTime;
                        $row['afternoon_end_time'] = $endTime;
                        continue;
                    }

                    if ($row['morning_start_time'] === '') {
                        $row['morning_start_time'] = $startTime;
                        $row['morning_end_time'] = $endTime;
                    } else {
                        $row['afternoon_start_time'] = $startTime;
                        $row['afternoon_end_time'] = $endTime;
                    }
                }

                $rows[] = $row;
            }

            return count($rows) ? $rows : [[
                'day_of_week' => '',
                'morning_start_time' => '',
                'morning_end_time' => '',
                'afternoon_start_time' => '',
                'afternoon_end_time' => '',
            ]];
        }

        $rowsCount = max(count($days), count($morningStarts), count($morningEnds), count($afternoonStarts), count($afternoonEnds), 1);

        for ($index = 0; $index < $rowsCount; $index++) {
            $rows[] = [
                'day_of_week' => (string) ($days[$index] ?? ''),
                'morning_start_time' => (string) ($morningStarts[$index] ?? ''),
                'morning_end_time' => (string) ($morningEnds[$index] ?? ''),
                'afternoon_start_time' => (string) ($afternoonStarts[$index] ?? ''),
                'afternoon_end_time' => (string) ($afternoonEnds[$index] ?? ''),
            ];
        }

        return $rows;
    };
@endphp
<section class="section">
    <div class="section-header">
        <h1>Profissionais de Saúde</h1>
    </div>

    <div class="section-body">
        @if($errors->any())
            <div class="alert alert-danger">
                <strong>Não foi possível salvar o profissional.</strong>
                <ul class="mb-0 mt-2 pl-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if(session('warning'))
            <div class="alert alert-warning">{{ session('warning') }}</div>
        @endif
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(!empty($setupWarning))
            <div class="alert alert-warning">{{ $setupWarning }}</div>
        @endif

        <div class="card mb-4">
            <div class="card-header"><h4>Novo profissional</h4></div>
            <div class="card-body">
                <form action="{{ route('admin.settings.professionals.store') }}" method="POST" class="professional-form" data-draft-form="true" data-draft-key="admin.settings.professionals.create">
                    @csrf
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Usuário profissional vinculado *</label>
                                <select class="form-control professional-user-select @error('user_id') is-invalid @enderror" id="professional-user-id" name="user_id" required {{ $availableUsers->isEmpty() ? 'disabled' : '' }}>
                                    <option value="">Selecione um profissional</option>
                                    @foreach($availableUsers as $user)
                                        <option value="{{ $user->id }}" data-name="{{ trim(($user->nome ?? '') . ' ' . ($user->sobrenome ?? '')) }}" data-cpf="{{ $formatCpf($user->cpf) }}" {{ (string) old('user_id') === (string) $user->id ? 'selected' : '' }}>
                                            {{ trim(($user->nome ?? '') . ' ' . ($user->sobrenome ?? '')) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('user_id')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Nome social *</label>
                                <input type="text" class="form-control professional-name-input" id="professional-name" name="nome" value="{{ old('nome') }}" autocomplete="off" required>
                                @error('nome')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-4"><div class="form-group"><label>Especialidade principal *</label><input type="text" class="form-control" name="especialidade_principal" value="{{ old('especialidade_principal') }}" required></div></div>
                        <div class="col-md-4">
                            <div class="form-group professional-tags-field" data-tags-field data-tags-name="subespecialidades">
                                <label>Subespecialidades</label>
                                <input type="text" class="form-control professional-tags-input" placeholder="Digite e pressione Enter para adicionar">
                                <div class="professional-tags-list mt-2" data-tags-list>
                                    @foreach((array) old('subespecialidades', []) as $subspecialty)
                                        @if(trim((string) $subspecialty) !== '')
                                            <span class="professional-tag-pill" data-tag-value="{{ trim((string) $subspecialty) }}">
                                                <span>{{ trim((string) $subspecialty) }}</span>
                                                <button type="button" data-remove-tag aria-label="Remover subespecialidade">&times;</button>
                                            </span>
                                        @endif
                                    @endforeach
                                </div>
                                <div data-tags-hidden-inputs>
                                    @foreach((array) old('subespecialidades', []) as $subspecialty)
                                        @if(trim((string) $subspecialty) !== '')
                                            <input type="hidden" name="subespecialidades[]" value="{{ trim((string) $subspecialty) }}">
                                        @endif
                                    @endforeach
                                </div>
                                @error('subespecialidades')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                                @error('subespecialidades.*')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>CPF</label>
                                <input type="text" class="form-control professional-cpf-input" id="professional-cpf" value="{{ old('user_id') ? $formatCpf(optional($availableUsers->firstWhere('id', (int) old('user_id')))->cpf) : '' }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Conselho de saúde *</label>
                                <select class="form-control professional-council-select @error('registro_tipo') is-invalid @enderror" id="professional-council" name="registro_tipo" required>
                                    <option value="">Selecione o conselho</option>
                                    @foreach($professionalCouncils as $sigla => $council)
                                        <option value="{{ $sigla }}" data-category="{{ $council['category'] }}" data-profession="{{ $council['profession'] }}" {{ old('registro_tipo', 'CRM') === $sigla ? 'selected' : '' }}>
                                            {{ $sigla }} - {{ $council['name'] }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('registro_tipo')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Número do registro no conselho *</label>
                                <input type="text" class="form-control" name="registro_numero" value="{{ old('registro_numero') }}" placeholder="Ex.: 12345" maxlength="20" inputmode="numeric" pattern="[0-9]*" required>
                                <small class="text-muted">Informe apenas números, com no máximo 20 dígitos.</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>RQE</label>
                                <input type="text" class="form-control" name="rqe" value="{{ old('rqe') }}" placeholder="Ex.: 12345" maxlength="20" inputmode="numeric" pattern="[0-9]*">
                                <small class="text-muted">Campo opcional. Informe apenas números, com no máximo 20 dígitos.</small>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="professional-color-label">Cor da agenda * <span class="professional-color-help" title="Escolha uma cor exclusiva para destacar este profissional na agenda. O botão abaixo sugere automaticamente uma opção ainda livre."><i class="fas fa-palette"></i></span></label>
                                <input type="color" class="form-control professional-color-input" name="agenda_color" value="{{ old('agenda_color', '#0d6efd') }}" required>
                                <div class="professional-color-tools">
                                    <button type="button" class="btn btn-sm professional-color-random" data-random-color><i class="fas fa-lightbulb"></i><span>Sugerir cor única</span></button>
                                </div>
                                <small class="text-muted d-block mt-2 professional-color-feedback">Cada profissional precisa usar uma cor exclusiva na agenda.</small>
                            </div>
                        </div>
                    </div>

                    <div class="border rounded p-3 mt-2">
                        <h6 class="mb-3">Vínculo de agenda</h6>
                        <p class="text-muted mb-2">Você pode aplicar a janela padrão da clínica ou deixar o profissional com horários específicos. Dias já escolhidos não poderão ser selecionados novamente.</p>
                        <p class="text-muted mb-3">Horário da clínica: {{ $clinicHoursWindow['opening_time'] ?? '--:--' }} às {{ $clinicHoursWindow['closing_time'] ?? '--:--' }}@if(!empty($clinicHoursWindow['lunch_start_time']) && !empty($clinicHoursWindow['lunch_end_time'])) com intervalo das {{ $clinicHoursWindow['lunch_start_time'] }} às {{ $clinicHoursWindow['lunch_end_time'] }}@endif.</p>
                        <div class="professional-schedule-mode-options">
                            <div class="professional-schedule-mode-option">
                                <input type="radio" id="schedule-mode-clinic-create" name="schedule_mode" value="clinic_hours" {{ old('schedule_mode') === 'clinic_hours' ? 'checked' : '' }}>
                                <label class="professional-schedule-mode-card" for="schedule-mode-clinic-create">
                                    <span class="professional-schedule-mode-title">Segunda a Sexta no horário da clínica</span>
                                    <span class="professional-schedule-mode-description">Preenche automaticamente a agenda com a abertura e o fechamento da clínica.</span>
                                </label>
                            </div>
                            <div class="professional-schedule-mode-option">
                                <input type="radio" id="schedule-mode-specific-create" name="schedule_mode" value="specific_hours" {{ old('schedule_mode', 'specific_hours') === 'specific_hours' ? 'checked' : '' }}>
                                <label class="professional-schedule-mode-card" for="schedule-mode-specific-create">
                                    <span class="professional-schedule-mode-title">Horários específicos do profissional</span>
                                    <span class="professional-schedule-mode-description">Permite definir apenas horários válidos dentro da clínica, sem deixar selecionar o intervalo.</span>
                                </label>
                            </div>
                        </div>
                        <div class="professional-schedule-summary" data-schedule-summary></div>
                        @php
                            $createSpecificScheduleRows = $buildSpecificScheduleRows(
                                old('schedule_day_of_week', []),
                                old('schedule_morning_start_time', []),
                                old('schedule_morning_end_time', []),
                                old('schedule_afternoon_start_time', []),
                                old('schedule_afternoon_end_time', []),
                            );
                        @endphp
                        <div data-schedule-specific-settings>
                        <div class="schedule-rows-container" id="schedule-rows-container">
                        @foreach($createSpecificScheduleRows as $i => $scheduleRow)
                            <div class="row schedule-row align-items-end" data-schedule-row>
                                <div class="col-md-12">
                                    <div class="professional-period-grid">
                                        <div class="form-group mb-0">
                                            <label>Dia da semana</label>
                                            <select class="form-control schedule-day-select" name="schedule_day_of_week[]">
                                                <option value="">Selecione</option>
                                                @foreach($weekDays as $number => $label)
                                                    <option value="{{ $number }}" {{ (string) $scheduleRow['day_of_week'] === (string) $number ? 'selected' : '' }}>{{ $label }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group mb-0"><label>Início da manhã</label><select class="form-control schedule-time-select" name="schedule_morning_start_time[]" data-period="morning" data-selected-value="{{ $scheduleRow['morning_start_time'] }}"><option value="">Selecione</option></select></div>
                                        <div class="form-group mb-0"><label>Fim da manhã</label><select class="form-control schedule-time-select" name="schedule_morning_end_time[]" data-period="morning" data-selected-value="{{ $scheduleRow['morning_end_time'] }}"><option value="">Selecione</option></select></div>
                                        <div class="form-group mb-0"><label>Início da tarde</label><select class="form-control schedule-time-select" name="schedule_afternoon_start_time[]" data-period="afternoon" data-selected-value="{{ $scheduleRow['afternoon_start_time'] }}"><option value="">Selecione</option></select></div>
                                        <div class="form-group mb-0"><label>Fim da tarde</label><select class="form-control schedule-time-select" name="schedule_afternoon_end_time[]" data-period="afternoon" data-selected-value="{{ $scheduleRow['afternoon_end_time'] }}"><option value="">Selecione</option></select></div>
                                    </div>
                                </div>
                                <div class="col-md-12 mb-2 text-right">
                                    <button type="button" class="btn btn-outline-danger btn-sm remove-schedule-row" {{ $i === 0 && count($createSpecificScheduleRows) === 1 ? 'style=display:none;' : '' }}>Remover</button>
                                </div>
                                <div class="col-md-12">
                                    <small class="schedule-row-feedback text-muted" data-schedule-feedback></small>
                                </div>
                            </div>
                        @endforeach
                        </div>
                        <small class="text-muted d-block professional-period-hint">No horário específico você pode definir manhã e tarde separadamente para cada dia.</small>
                        <button type="button" class="btn btn-outline-primary btn-sm mt-2 add-schedule-row" id="add-schedule-row">Adicionar mais um</button>
                        </div>
                        @error('schedule_day_of_week')<div class="text-danger small mt-2">{{ $message }}</div>@enderror
                        @error('schedule_mode')<div class="text-danger small mt-2">{{ $message }}</div>@enderror
                    </div>

                    <button type="submit" class="btn btn-primary mt-3" {{ $availableUsers->isEmpty() ? 'disabled' : '' }}>Cadastrar profissional</button>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap settings-toolbar">
                <h4 class="mb-0">Equipe cadastrada</h4>
                <form action="{{ route('admin.settings.professionals') }}" method="GET" class="settings-toolbar-form">
                    <input type="text" class="form-control" name="professional_user_search" value="{{ $professionalUserSearch ?? '' }}" placeholder="Pesquisar usuário vinculado" style="min-width: 260px;">
                    <button type="submit" class="btn btn-primary btn-sm">Pesquisar</button>
                    @if(!empty($professionalUserSearch))
                        <a href="{{ route('admin.settings.professionals') }}" class="btn btn-light btn-sm">Limpar</a>
                    @endif
                </form>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped professionals-table table-mobile-cards">
                        <thead>
                            <tr>
                                <th>Profissional</th>
                                <th>Usuário vinculado</th>
                                <th>Especialidade</th>
                                <th>Subespecialidades</th>
                                <th>RQE / Registro</th>
                                <th>Cor da agenda</th>
                                <th>Disponibilidade</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($professionals as $professional)
                                @php
                                    $professionalScheduleRowsCount = max($professional->schedules->count(), 1);
                                    $professionalLabel = $professionalCouncils[$professional->registro_tipo]['profession'] ?? ($professional->registro_tipo ?: 'Profissional');
                                @endphp
                                <tr>
                                    <td class="professional-identity-cell table-mobile-full" data-label="Profissional">
                                        <div class="professional-identity-content">
                                            <img src="{{ $professionalPhotoUrl($professional) }}" alt="Foto de {{ $professional->nome }}" class="professional-avatar">
                                            <div class="professional-identity-meta">
                                                <div class="professional-primary-text">{{ $professional->nome }}</div>
                                                <div class="professional-secondary-text">{{ $professionalLabel }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="professional-linked-user-cell" data-label="Usuário vinculado">
                                        @if($professional->user)
                                            <div class="professional-primary-text">{{ trim(($professional->user->nome ?? '') . ' ' . ($professional->user->sobrenome ?? '')) }}</div>
                                        @else
                                            <span class="text-muted">Usuário não vinculado</span>
                                        @endif
                                    </td>
                                    <td class="professional-specialty-cell" data-label="Especialidade">
                                        <div class="professional-primary-text">{{ $professional->especialidade_principal }}</div>
                                    </td>
                                    <td class="professional-subspecialty-cell" data-label="Subespecialidades">
                                        @if(!empty($professional->subespecialidades))
                                            <div class="text-center">
                                                <button type="button" class="btn btn-sm btn-outline-primary" data-toggle="modal" data-target="#subspecialties-professional-modal-{{ $professional->id }}">Exibir subespecialidades</button>
                                            </div>
                                        @else
                                            <span class="professional-subspecialty-empty">Sem subespecialidades</span>
                                        @endif
                                    </td>
                                    <td class="professional-registry-cell" data-label="RQE / Registro">
                                        <div class="professional-primary-text">{{ $professional->registro_completo }}</div>
                                        @if($professional->rqe)
                                            <div class="professional-secondary-text">RQE {{ $professional->rqe }}</div>
                                        @endif
                                    </td>
                                    <td data-label="Cor da agenda">
                                        <span class="badge" style="background: {{ $professional->agenda_color }}; color: #fff;">{{ $professional->agenda_color }}</span>
                                    </td>
                                    <td class="professional-availability-cell" data-label="Disponibilidade">
                                        @if(($professional->display_schedules ?? collect())->isNotEmpty())
                                            <button type="button" class="btn btn-sm btn-outline-primary professional-availability-trigger" data-toggle="modal" data-target="#availability-professional-modal-{{ $professional->id }}">Exibir horários</button>
                                        @else
                                            <span class="text-muted">Sem agenda definida</span>
                                        @endif
                                    </td>
                                    <td class="table-mobile-full action-button-cell professional-actions-cell" data-label="Ações">
                                        <div class="professional-actions">
                                            <button type="button" class="btn btn-sm btn-secondary" data-toggle="modal" data-target="#view-professional-modal-{{ $professional->id }}">Ver</button>
                                            <button type="button" class="btn btn-sm btn-info" data-toggle="modal" data-target="#edit-professional-modal-{{ $professional->id }}">Editar</button>
                                            <form action="{{ route('admin.settings.professionals.destroy', $professional) }}" method="POST" class="d-inline" onsubmit="return confirm('Deseja realmente excluir este profissional?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">Excluir</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="8" class="text-center text-muted">Nenhum profissional cadastrado.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        @foreach($professionals as $professional)
            @php
                $professionalScheduleRowsCount = max($professional->schedules->count(), 1);
                $editScheduleMode = old('schedule_mode', $isClinicHoursSchedule($professional->schedules) ? 'clinic_hours' : 'specific_hours');
                $editSpecificScheduleRows = $buildSpecificScheduleRows([], [], [], [], [], $professional->schedules);
                $professionalLabel = $professionalCouncils[$professional->registro_tipo]['profession'] ?? ($professional->registro_tipo ?: 'Profissional');
            @endphp
            <div class="modal fade professional-modal" id="view-professional-modal-{{ $professional->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Detalhes do profissional</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-4 mb-3"><div class="border rounded p-3 h-100 bg-white professional-view-photo-card"><div class="text-muted small text-uppercase">Foto do profissional</div><img src="{{ $professionalPhotoUrl($professional) }}" alt="Foto de {{ $professional->nome }}" class="professional-avatar-large"><div class="font-weight-bold mt-1">{{ $professional->nome }}</div></div></div>
                                <div class="col-md-8 mb-3"><div class="border rounded p-3 h-100 bg-white"><div class="text-muted small text-uppercase">Usuário vinculado</div><div class="font-weight-bold mt-1">{{ $professional->user ? trim(($professional->user->nome ?? '') . ' ' . ($professional->user->sobrenome ?? '')) : 'Usuário não vinculado' }}</div><div class="text-muted small text-uppercase mt-3">Nome social</div><div class="font-weight-bold mt-1">{{ $professional->nome }}</div></div></div>
                                <div class="col-md-4 mb-3"><div class="border rounded p-3 h-100 bg-white"><div class="text-muted small text-uppercase">Especialidade</div><div class="mt-1">{{ $professional->especialidade_principal }}</div></div></div>
                                <div class="col-md-4 mb-3"><div class="border rounded p-3 h-100 bg-white"><div class="text-muted small text-uppercase">Subespecialidades</div><div class="mt-2">@forelse(($professional->subespecialidades ?? []) as $subspecialty)<span class="professional-tag-pill mr-1 mb-1"><span>{{ $subspecialty }}</span></span>@empty<span class="text-muted">Não informado</span>@endforelse</div></div></div>
                                <div class="col-md-4 mb-3"><div class="border rounded p-3 h-100 bg-white"><div class="text-muted small text-uppercase">CPF</div><div class="mt-1">{{ $formatCpf($professional->cpf) ?: 'Não informado' }}</div></div></div>
                                <div class="col-md-4 mb-3"><div class="border rounded p-3 h-100 bg-white"><div class="text-muted small text-uppercase">Registro</div><div class="mt-1">{{ $professional->registro_completo }}</div></div></div>
                                <div class="col-md-4 mb-3"><div class="border rounded p-3 h-100 bg-white"><div class="text-muted small text-uppercase">RQE</div><div class="mt-1">{{ $professional->rqe ?: 'Não informado' }}</div></div></div>
                                <div class="col-md-12 mb-3"><div class="border rounded p-3 h-100 bg-white"><div class="text-muted small text-uppercase">Disponibilidade</div><div class="mt-2">@forelse(($professional->display_schedules ?? collect()) as $schedule)@foreach($buildProfessionalScheduleLabels($schedule) as $scheduleLabel)<span class="badge badge-light border mr-1 mb-1">{{ $scheduleLabel }}</span>@endforeach@empty<span class="text-muted">Sem agenda definida</span>@endforelse</div></div></div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade professional-modal" id="availability-professional-modal-{{ $professional->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Horários de {{ $professional->nome }}</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="d-flex align-items-center mb-3" style="gap: 14px;">
                                <img src="{{ $professionalPhotoUrl($professional) }}" alt="Foto de {{ $professional->nome }}" class="professional-avatar" style="margin: 0;">
                                <div>
                                    <div class="professional-primary-text">{{ $professional->nome }}</div>
                                    <div class="professional-secondary-text">{{ $professionalLabel }}</div>
                                </div>
                            </div>
                            @php
                                $availabilityRows = $buildSpecificScheduleRows([], [], [], [], [], $professional->schedules);
                            @endphp
                            <div class="professional-availability-summary">
                                @forelse($availabilityRows as $availabilityRow)
                                    <div class="professional-availability-card">
                                        <div class="professional-availability-card-title">{{ $weekDays[(int) ($availabilityRow['day_of_week'] ?? 0)] ?? 'Dia não definido' }}</div>
                                        <div class="professional-availability-block">
                                            <span class="professional-availability-label">Manhã</span>
                                            <div class="professional-availability-value">{{ ($availabilityRow['morning_start_time'] && $availabilityRow['morning_end_time']) ? $availabilityRow['morning_start_time'] . ' às ' . $availabilityRow['morning_end_time'] : 'Não definido' }}</div>
                                        </div>
                                        <div class="professional-availability-block">
                                            <span class="professional-availability-label">Tarde</span>
                                            <div class="professional-availability-value">{{ ($availabilityRow['afternoon_start_time'] && $availabilityRow['afternoon_end_time']) ? $availabilityRow['afternoon_start_time'] . ' às ' . $availabilityRow['afternoon_end_time'] : 'Não definido' }}</div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-muted">Sem agenda definida</div>
                                @endforelse
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                        </div>
                    </div>
                </div>
            </div>

            @if(!empty($professional->subespecialidades))
                <div class="modal fade professional-modal" id="subspecialties-professional-modal-{{ $professional->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Subespecialidades de {{ $professional->nome }}</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="d-flex align-items-center mb-3" style="gap: 14px;">
                                    <img src="{{ $professionalPhotoUrl($professional) }}" alt="Foto de {{ $professional->nome }}" class="professional-avatar" style="margin: 0;">
                                    <div>
                                        <div class="professional-primary-text">{{ $professional->nome }}</div>
                                        <div class="professional-secondary-text">{{ $professionalLabel }}</div>
                                    </div>
                                </div>
                                <div class="border rounded p-3 bg-white">
                                    <div class="professional-tags-list">
                                        @foreach($professional->subespecialidades as $subspecialty)
                                            <span class="professional-tag-pill"><span>{{ $subspecialty }}</span></span>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="modal fade professional-modal" id="edit-professional-modal-{{ $professional->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-xl professional-edit-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Editar profissional</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form action="{{ route('admin.settings.professionals.update', $professional) }}" method="POST" class="professional-form" data-professional-id="{{ $professional->id }}">
                            @csrf
                            @method('PUT')
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Usuário profissional vinculado *</label>
                                            <select class="form-control professional-user-select" name="user_id" required>
                                                <option value="">Selecione um profissional</option>
                                                @foreach($availableUsers as $user)
                                                    <option value="{{ $user->id }}" data-name="{{ trim(($user->nome ?? '') . ' ' . ($user->sobrenome ?? '')) }}" data-cpf="{{ $formatCpf($user->cpf) }}" {{ (int) $professional->user_id === (int) $user->id ? 'selected' : '' }}>
                                                        {{ trim(($user->nome ?? '') . ' ' . ($user->sobrenome ?? '')) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Nome social *</label>
                                            <input type="text" class="form-control professional-name-input" name="nome" value="{{ $professional->nome }}" autocomplete="off" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4"><div class="form-group"><label>Especialidade principal *</label><input type="text" class="form-control" name="especialidade_principal" value="{{ $professional->especialidade_principal }}" required></div></div>
                                    <div class="col-md-4">
                                        <div class="form-group professional-tags-field" data-tags-field data-tags-name="subespecialidades">
                                            <label>Subespecialidades</label>
                                            <input type="text" class="form-control professional-tags-input" placeholder="Digite e pressione Enter para adicionar">
                                            <small class="text-muted d-block mt-2">Use tags para detalhar o foco de atendimento do profissional.</small>
                                            <div class="professional-tags-list mt-2" data-tags-list>
                                                @foreach(($professional->subespecialidades ?? []) as $subspecialty)
                                                    @if(trim((string) $subspecialty) !== '')
                                                        <span class="professional-tag-pill" data-tag-value="{{ trim((string) $subspecialty) }}">
                                                            <span>{{ trim((string) $subspecialty) }}</span>
                                                            <button type="button" data-remove-tag aria-label="Remover subespecialidade">&times;</button>
                                                        </span>
                                                    @endif
                                                @endforeach
                                            </div>
                                            <div data-tags-hidden-inputs>
                                                @foreach(($professional->subespecialidades ?? []) as $subspecialty)
                                                    @if(trim((string) $subspecialty) !== '')
                                                        <input type="hidden" name="subespecialidades[]" value="{{ trim((string) $subspecialty) }}">
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>CPF</label>
                                            <input type="text" class="form-control professional-cpf-input" value="{{ $formatCpf($professional->cpf) }}" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Conselho de saúde *</label>
                                            <select class="form-control professional-council-select" name="registro_tipo" required>
                                                <option value="">Selecione o conselho</option>
                                                @foreach($professionalCouncils as $sigla => $council)
                                                    <option value="{{ $sigla }}" data-category="{{ $council['category'] }}" data-profession="{{ $council['profession'] }}" {{ $professional->registro_tipo === $sigla ? 'selected' : '' }}>
                                                        {{ $sigla }} - {{ $council['name'] }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <small class="text-muted professional-council-category"></small>
                                        </div>
                                    </div>
                                    <div class="col-md-4"><div class="form-group"><label>Número do registro no conselho *</label><input type="text" class="form-control" name="registro_numero" value="{{ $professional->registro_numero }}" maxlength="20" inputmode="numeric" pattern="[0-9]*" required><small class="text-muted">Informe apenas números, com no máximo 20 dígitos.</small></div></div>
                                    <div class="col-md-4"><div class="form-group"><label>RQE</label><input type="text" class="form-control" name="rqe" value="{{ $professional->rqe }}" placeholder="Ex.: 12345" maxlength="20" inputmode="numeric" pattern="[0-9]*"><small class="text-muted">Campo opcional. Informe apenas números, com no máximo 20 dígitos.</small></div></div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="professional-color-label">Cor da agenda * <span class="professional-color-help" title="Escolha uma cor exclusiva para destacar este profissional na agenda. O botão abaixo sugere automaticamente uma opção ainda livre."><i class="fas fa-palette"></i></span></label>
                                            <input type="color" class="form-control professional-color-input" name="agenda_color" value="{{ $professional->agenda_color }}" required>
                                            <div class="professional-color-tools">
                                                <button type="button" class="btn btn-sm professional-color-random" data-random-color><i class="fas fa-lightbulb"></i><span>Sugerir cor única</span></button>
                                            </div>
                                            <small class="text-muted d-block mt-2 professional-color-feedback">Cada profissional precisa usar uma cor exclusiva na agenda.</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="border rounded p-3 mt-2">
                                    <h6 class="mb-3">Vínculo de agenda</h6>
                                    <p class="text-muted mb-3">Selecione o modo da agenda. Nos horários específicos, só serão mostradas opções válidas dentro da clínica e fora do intervalo.</p>
                                    <div class="professional-schedule-mode-options">
                                        <div class="professional-schedule-mode-option">
                                            <input type="radio" id="schedule-mode-clinic-{{ $professional->id }}" name="schedule_mode" value="clinic_hours" {{ $editScheduleMode === 'clinic_hours' ? 'checked' : '' }}>
                                            <label class="professional-schedule-mode-card" for="schedule-mode-clinic-{{ $professional->id }}">
                                                <span class="professional-schedule-mode-title">Segunda a Sexta no horário da clínica</span>
                                                <span class="professional-schedule-mode-description">Aplica automaticamente a abertura e o fechamento da clínica para os dias úteis.</span>
                                            </label>
                                        </div>
                                        <div class="professional-schedule-mode-option">
                                            <input type="radio" id="schedule-mode-specific-{{ $professional->id }}" name="schedule_mode" value="specific_hours" {{ $editScheduleMode === 'specific_hours' ? 'checked' : '' }}>
                                            <label class="professional-schedule-mode-card" for="schedule-mode-specific-{{ $professional->id }}">
                                                <span class="professional-schedule-mode-title">Horários específicos do profissional</span>
                                                <span class="professional-schedule-mode-description">Permite escolher apenas horários válidos, sem deixar selecionar o intervalo da clínica.</span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="professional-schedule-summary" data-schedule-summary></div>
                                    <div data-schedule-specific-settings>
                                    <div class="schedule-rows-container">
                                        @foreach($editSpecificScheduleRows as $i => $scheduleRow)
                                            <div class="row schedule-row align-items-end" data-schedule-row>
                                                <div class="col-md-12">
                                                    <div class="professional-period-grid">
                                                        <div class="form-group mb-0">
                                                            <label>Dia da semana</label>
                                                            <select class="form-control schedule-day-select" name="schedule_day_of_week[]">
                                                                <option value="">Selecione</option>
                                                                @foreach($weekDays as $number => $label)
                                                                    <option value="{{ $number }}" {{ (string) $scheduleRow['day_of_week'] === (string) $number ? 'selected' : '' }}>{{ $label }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="form-group mb-0"><label>Início da manhã</label><select class="form-control schedule-time-select" name="schedule_morning_start_time[]" data-period="morning" data-selected-value="{{ $scheduleRow['morning_start_time'] }}"><option value="">Selecione</option></select></div>
                                                        <div class="form-group mb-0"><label>Fim da manhã</label><select class="form-control schedule-time-select" name="schedule_morning_end_time[]" data-period="morning" data-selected-value="{{ $scheduleRow['morning_end_time'] }}"><option value="">Selecione</option></select></div>
                                                        <div class="form-group mb-0"><label>Início da tarde</label><select class="form-control schedule-time-select" name="schedule_afternoon_start_time[]" data-period="afternoon" data-selected-value="{{ $scheduleRow['afternoon_start_time'] }}"><option value="">Selecione</option></select></div>
                                                        <div class="form-group mb-0"><label>Fim da tarde</label><select class="form-control schedule-time-select" name="schedule_afternoon_end_time[]" data-period="afternoon" data-selected-value="{{ $scheduleRow['afternoon_end_time'] }}"><option value="">Selecione</option></select></div>
                                                    </div>
                                                </div>
                                                <div class="col-md-12 mb-2 text-right">
                                                    <button type="button" class="btn btn-outline-danger btn-sm remove-schedule-row" {{ $i === 0 && count($editSpecificScheduleRows) === 1 ? 'style=display:none;' : '' }}>Remover</button>
                                                </div>
                                                <div class="col-md-12">
                                                    <small class="schedule-row-feedback text-muted" data-schedule-feedback></small>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <small class="text-muted d-block professional-period-hint">No horário específico você pode definir manhã e tarde separadamente para cada dia.</small>
                                    <button type="button" class="btn btn-outline-primary btn-sm mt-2 add-schedule-row">Adicionar mais um</button>
                                    </div>
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
@php
    $professionalColors = $professionals->map(function ($professional) {
        return [
            'id' => (string) $professional->id,
            'color' => mb_strtolower((string) $professional->agenda_color),
            'name' => $professional->nome,
        ];
    })->values();
@endphp
<script>
    $(function () {
        var clinicHoursWindow = @json($clinicHoursWindow ?? null);
        var professionalColors = @json($professionalColors);

        function toMinutes(timeValue) {
            if (!timeValue || timeValue.indexOf(':') === -1) {
                return null;
            }

            var parts = timeValue.split(':');
            return (parseInt(parts[0], 10) * 60) + parseInt(parts[1], 10);
        }

        function toTimeString(totalMinutes) {
            var hours = Math.floor(totalMinutes / 60);
            var minutes = totalMinutes % 60;

            return String(hours).padStart(2, '0') + ':' + String(minutes).padStart(2, '0');
        }

        function buildValidScheduleTimes() {
            var openingInMinutes = toMinutes(clinicHoursWindow && clinicHoursWindow.opening_time ? clinicHoursWindow.opening_time : '');
            var closingInMinutes = toMinutes(clinicHoursWindow && clinicHoursWindow.closing_time ? clinicHoursWindow.closing_time : '');
            var lunchStartInMinutes = toMinutes(clinicHoursWindow && clinicHoursWindow.lunch_start_time ? clinicHoursWindow.lunch_start_time : '');
            var lunchEndInMinutes = toMinutes(clinicHoursWindow && clinicHoursWindow.lunch_end_time ? clinicHoursWindow.lunch_end_time : '');
            var timeOptions = [];

            if (openingInMinutes === null || closingInMinutes === null) {
                return timeOptions;
            }

            for (var minutes = openingInMinutes; minutes <= closingInMinutes; minutes += 5) {
                if (lunchStartInMinutes !== null && lunchEndInMinutes !== null && minutes > lunchStartInMinutes && minutes < lunchEndInMinutes) {
                    continue;
                }

                timeOptions.push(toTimeString(minutes));
            }

            return timeOptions;
        }

        var validScheduleTimes = buildValidScheduleTimes();

        function buildPeriodTimeOptions(period) {
            var lunchStartTime = clinicHoursWindow && clinicHoursWindow.lunch_start_time ? clinicHoursWindow.lunch_start_time : '';
            var lunchEndTime = clinicHoursWindow && clinicHoursWindow.lunch_end_time ? clinicHoursWindow.lunch_end_time : '';

            if (period === 'morning' && lunchStartTime) {
                return validScheduleTimes.filter(function (timeValue) {
                    return timeValue <= lunchStartTime;
                });
            }

            if (period === 'afternoon' && lunchEndTime) {
                return validScheduleTimes.filter(function (timeValue) {
                    return timeValue >= lunchEndTime;
                });
            }

            return validScheduleTimes;
        }

        function populateScheduleTimeSelect(select, selectedValue) {
            if (!select.length) {
                return;
            }

            var currentValue = selectedValue || select.val() || select.data('selectedValue') || '';
            var options = ['<option value="">Selecione</option>'];
            var period = String(select.data('period') || 'all');

            buildPeriodTimeOptions(period).forEach(function (timeValue) {
                options.push('<option value="' + timeValue + '">' + timeValue + '</option>');
            });

            select.html(options.join(''));

            if (currentValue && select.find('option[value="' + currentValue + '"]').length) {
                select.val(currentValue);
            } else {
                select.val('');
            }
        }

        function populateScheduleTimeSelects(form) {
            form.find('.schedule-time-select').each(function () {
                populateScheduleTimeSelect($(this));
            });
        }

        function persistManualScheduleState(form) {
            if ((form.find('[name="schedule_mode"]:checked').val() || 'specific_hours') !== 'specific_hours') {
                return;
            }

            form.data('manualScheduleMarkup', form.find('.schedule-rows-container').html());
        }

        function restoreManualScheduleState(form) {
            var manualMarkup = form.data('manualScheduleMarkup');

            if (!manualMarkup) {
                return;
            }

            form.find('.schedule-rows-container').html(manualMarkup);
        }

        function syncScheduleMode(form) {
            var mode = form.find('[name="schedule_mode"]:checked').val() || 'specific_hours';
            var specificSettings = form.find('[data-schedule-specific-settings]');
            var summary = form.find('[data-schedule-summary]');

            if (mode === 'clinic_hours') {
                persistManualScheduleState(form);
                specificSettings.hide();
                summary.text('Este profissional ficará disponível de segunda a sexta em toda a janela da clínica.').show();
            } else {
                if (specificSettings.is(':hidden')) {
                    restoreManualScheduleState(form);
                }

                specificSettings.show();
                summary.text('Defina somente os horários específicos em que o profissional pode atender.').show();
            }

            populateScheduleTimeSelects(form);
            syncScheduleDayOptions(form);
            applyScheduleTimeConstraints(form);
        }

        function syncProfessionalFields(form) {
            var userSelect = form.find('.professional-user-select');
            var nameInput = form.find('.professional-name-input');
            var cpfInput = form.find('.professional-cpf-input');
            var selectedUser = userSelect.find('option:selected');
            var professionalName = $.trim(String(selectedUser.data('name') || ''));
            var currentValue = $.trim(String(nameInput.val() || ''));
            var lastAutoValue = $.trim(String(nameInput.data('lastAutoValue') || ''));
            var manualName = Boolean(nameInput.data('manualName'));

            if (!manualName && (currentValue === '' || currentValue === lastAutoValue)) {
                nameInput.val(professionalName);
                nameInput.data('lastAutoValue', professionalName);
            }

            cpfInput.val(selectedUser.data('cpf') || '');
        }

        function initializeProfessionalNameState(form) {
            var userSelect = form.find('.professional-user-select');
            var nameInput = form.find('.professional-name-input');

            if (!nameInput.length) {
                return;
            }

            var selectedUser = userSelect.find('option:selected');
            var linkedUserName = $.trim(String(selectedUser.data('name') || ''));
            var currentValue = $.trim(String(nameInput.val() || ''));
            var isManual = currentValue !== '' && linkedUserName !== '' && currentValue !== linkedUserName;

            nameInput.data('lastAutoValue', linkedUserName);
            nameInput.data('manualName', isManual);

            if (currentValue === '' && linkedUserName !== '') {
                nameInput.val(linkedUserName);
                nameInput.data('lastAutoValue', linkedUserName);
                nameInput.data('manualName', false);
            }
        }

        function syncCouncilCategory(form) {
            var councilSelect = form.find('.professional-council-select');
            var councilCategory = form.find('.professional-council-category');
            var selectedCouncil = councilSelect.find('option:selected');
            councilCategory.text(selectedCouncil.data('category') || '');
            syncProfessionalFields(form);
        }

        function normalizeTagValue(value) {
            return $.trim(String(value || '').replace(/\s+/g, ' '));
        }

        function appendTag(container, rawValue) {
            var value = normalizeTagValue(rawValue);
            var list = container.find('[data-tags-list]');
            var hiddenInputs = container.find('[data-tags-hidden-inputs]');
            var inputName = container.data('tagsName') || 'subespecialidades';
            var alreadyExists = hiddenInputs.find('input').filter(function () {
                return normalizeTagValue($(this).val()).toLowerCase() === value.toLowerCase();
            }).length > 0;

            if (!value || alreadyExists) {
                return;
            }

            hiddenInputs.append($('<input>', {
                type: 'hidden',
                name: inputName + '[]',
                value: value
            }));

            list.append(
                $('<span>', {
                    'class': 'professional-tag-pill',
                    'data-tag-value': value
                }).append($('<span>').text(value)).append(
                    $('<button>', {
                        type: 'button',
                        'data-remove-tag': 'true',
                        'aria-label': 'Remover subespecialidade'
                    }).html('&times;')
                )
            );
        }

        function initializeTagsField(container) {
            var input = container.find('.professional-tags-input');

            container.find('[data-tags-hidden-inputs] input').each(function () {
                var value = normalizeTagValue($(this).val());
                var existsInList = container.find('[data-tags-list] [data-tag-value]').filter(function () {
                    return normalizeTagValue($(this).data('tagValue')).toLowerCase() === value.toLowerCase();
                }).length > 0;

                if (!existsInList && value) {
                    container.find('[data-tags-list]').append(
                        $('<span>', {
                            'class': 'professional-tag-pill',
                            'data-tag-value': value
                        }).append($('<span>').text(value)).append(
                            $('<button>', {
                                type: 'button',
                                'data-remove-tag': 'true',
                                'aria-label': 'Remover subespecialidade'
                            }).html('&times;')
                        )
                    );
                }
            });

            input.off('keydown.professionalTags blur.professionalTags');

            input.on('keydown.professionalTags', function (event) {
                if (event.key === 'Enter' || event.key === ',') {
                    event.preventDefault();
                    appendTag(container, $(this).val());
                    $(this).val('');
                }
            });

            input.on('blur.professionalTags', function () {
                appendTag(container, $(this).val());
                $(this).val('');
            });
        }

        function getReservedDays(form) {
            var reserved = [];

            form.find('.schedule-day-select').each(function () {
                var value = $(this).val();

                if (!value) {
                    return;
                }

                reserved.push(String(value));
            });

            return reserved;
        }

        function syncScheduleDayOptions(form) {
            form.find('.schedule-day-select').each(function () {
                var currentSelect = $(this);
                var currentValue = String(currentSelect.val() || '');
                var reservedDays = getReservedDays(form).filter(function (value) {
                    return value !== currentValue;
                });

                currentSelect.find('option').each(function () {
                    var option = $(this);
                    var optionValue = String(option.val() || '');

                    if (!optionValue) {
                        option.prop('disabled', false);
                        return;
                    }

                    option.prop('disabled', reservedDays.indexOf(optionValue) !== -1);
                });
            });

            form.find('.remove-schedule-row').toggle(form.find('.schedule-row').length > 1);
        }

        function syncColorFeedback(form) {
            var colorInput = form.find('.professional-color-input').first();
            var feedback = form.find('.professional-color-feedback').first();

            if (!colorInput.length || !feedback.length) {
                return;
            }

            var selectedColor = String(colorInput.val() || '').toLowerCase();
            var currentProfessionalId = String(form.data('professional-id') || '');
            var duplicatedColor = professionalColors.find(function (item) {
                return item.color === selectedColor && item.id !== currentProfessionalId;
            });

            colorInput[0].setCustomValidity('');
            feedback.removeClass('text-danger text-success').addClass('text-muted');

            if (!selectedColor) {
                feedback.text('Cada profissional precisa usar uma cor exclusiva na agenda.');
                return;
            }

            if (duplicatedColor) {
                colorInput[0].setCustomValidity('Esta cor de agenda já está em uso por outro profissional.');
                feedback
                    .text('A cor ' + selectedColor + ' já está sendo usada por ' + duplicatedColor.name + '.')
                    .removeClass('text-muted text-success')
                    .addClass('text-danger');
                return;
            }

            feedback
                .text('Cor disponível para uso na agenda.')
                .removeClass('text-muted text-danger')
                .addClass('text-success');
        }

        function generateUniqueColor(form) {
            var colorInput = form.find('.professional-color-input').first();
            var currentProfessionalId = String(form.data('professional-id') || '');
            var attempts = 0;
            var generatedColor = null;

            while (attempts < 40) {
                attempts += 1;

                var candidate = '#' + Math.floor(Math.random() * 16777215).toString(16).padStart(6, '0');
                var duplicatedColor = professionalColors.find(function (item) {
                    return item.color === candidate.toLowerCase() && item.id !== currentProfessionalId;
                });

                if (!duplicatedColor) {
                    generatedColor = candidate;
                    break;
                }
            }

            if (!generatedColor) {
                generatedColor = '#0d6efd';
            }

            colorInput.val(generatedColor);
            syncColorFeedback(form);
        }

        function applyScheduleTimeConstraints(form) {
            var lunchStartTime = clinicHoursWindow && clinicHoursWindow.lunch_start_time ? clinicHoursWindow.lunch_start_time : '';
            var lunchEndTime = clinicHoursWindow && clinicHoursWindow.lunch_end_time ? clinicHoursWindow.lunch_end_time : '';

            form.find('.schedule-time-select').each(function () {
                this.setCustomValidity('');
            });

            form.find('.schedule-row').each(function () {
                var row = $(this);
                var morningStartInput = row.find('[name="schedule_morning_start_time[]"]');
                var morningEndInput = row.find('[name="schedule_morning_end_time[]"]');
                var afternoonStartInput = row.find('[name="schedule_afternoon_start_time[]"]');
                var afternoonEndInput = row.find('[name="schedule_afternoon_end_time[]"]');
                var feedback = row.find('[data-schedule-feedback]');
                var morningStartValue = morningStartInput.val() || '';
                var morningEndValue = morningEndInput.val() || '';
                var afternoonStartValue = afternoonStartInput.val() || '';
                var afternoonEndValue = afternoonEndInput.val() || '';

                feedback.text('').removeClass('text-info text-warning').addClass('text-muted');

                if ((morningStartValue && !morningEndValue) || (!morningStartValue && morningEndValue)) {
                    var morningMessage = 'Preencha o início e o fim da manhã no mesmo dia.';
                    morningStartInput[0].setCustomValidity(morningMessage);
                    morningEndInput[0].setCustomValidity(morningMessage);
                    feedback.text(morningMessage);
                }

                if ((afternoonStartValue && !afternoonEndValue) || (!afternoonStartValue && afternoonEndValue)) {
                    var afternoonMessage = 'Preencha o início e o fim da tarde no mesmo dia.';
                    afternoonStartInput[0].setCustomValidity(afternoonMessage);
                    afternoonEndInput[0].setCustomValidity(afternoonMessage);
                    feedback.text(afternoonMessage);
                }

                if (morningStartValue && morningEndValue && morningStartValue >= morningEndValue) {
                    var morningOrderMessage = 'O fim da manhã deve ser maior que o início da manhã.';
                    morningStartInput[0].setCustomValidity(morningOrderMessage);
                    morningEndInput[0].setCustomValidity(morningOrderMessage);
                    feedback.text(morningOrderMessage);
                }

                if (afternoonStartValue && afternoonEndValue && afternoonStartValue >= afternoonEndValue) {
                    var afternoonOrderMessage = 'O fim da tarde deve ser maior que o início da tarde.';
                    afternoonStartInput[0].setCustomValidity(afternoonOrderMessage);
                    afternoonEndInput[0].setCustomValidity(afternoonOrderMessage);
                    feedback.text(afternoonOrderMessage);
                }

                if (morningEndValue && afternoonStartValue && morningEndValue > afternoonStartValue) {
                    var overlapMessage = 'O horário da manhã deve terminar antes do início da tarde.';
                    morningEndInput[0].setCustomValidity(overlapMessage);
                    afternoonStartInput[0].setCustomValidity(overlapMessage);
                    feedback.text(overlapMessage);
                }

                if (lunchStartTime && lunchEndTime) {
                    if (morningEndValue && morningEndValue > lunchStartTime) {
                        var morningLimitMessage = 'O período da manhã deve terminar antes do intervalo da clínica.';
                        morningEndInput[0].setCustomValidity(morningLimitMessage);
                        feedback.text(morningLimitMessage);
                    }

                    if (afternoonStartValue && afternoonStartValue < lunchEndTime) {
                        var afternoonLimitMessage = 'O período da tarde deve começar depois do intervalo da clínica.';
                        afternoonStartInput[0].setCustomValidity(afternoonLimitMessage);
                        feedback.text(afternoonLimitMessage);
                    }

                    if (morningStartValue && morningEndValue && afternoonStartValue && afternoonEndValue) {
                        feedback
                            .text('Os períodos da manhã e da tarde respeitam o intervalo da clínica entre ' + lunchStartTime + ' e ' + lunchEndTime + '.')
                            .removeClass('text-muted text-warning')
                            .addClass('text-info');
                    }
                }
            });
        }

        function buildScheduleRow() {
            return [
                '<div class="row schedule-row align-items-end" data-schedule-row>',
                '    <div class="col-md-12">',
                '        <div class="professional-period-grid">',
                '            <div class="form-group mb-0">',
                '                <label>Dia da semana</label>',
                '                <select class="form-control schedule-day-select" name="schedule_day_of_week[]">',
                '                    <option value="">Selecione</option>',
                '                    @foreach($weekDays as $number => $label)',
                '                        <option value="{{ $number }}">{{ $label }}</option>',
                '                    @endforeach',
                '                </select>',
                '            </div>',
                '            <div class="form-group mb-0"><label>Início da manhã</label><select class="form-control schedule-time-select" name="schedule_morning_start_time[]" data-period="morning"><option value="">Selecione</option></select></div>',
                '            <div class="form-group mb-0"><label>Fim da manhã</label><select class="form-control schedule-time-select" name="schedule_morning_end_time[]" data-period="morning"><option value="">Selecione</option></select></div>',
                '            <div class="form-group mb-0"><label>Início da tarde</label><select class="form-control schedule-time-select" name="schedule_afternoon_start_time[]" data-period="afternoon"><option value="">Selecione</option></select></div>',
                '            <div class="form-group mb-0"><label>Fim da tarde</label><select class="form-control schedule-time-select" name="schedule_afternoon_end_time[]" data-period="afternoon"><option value="">Selecione</option></select></div>',
                '        </div>',
                '    </div>',
                '    <div class="col-md-12 mb-2 text-right">',
                '        <button type="button" class="btn btn-outline-danger btn-sm remove-schedule-row">Remover</button>',
                '    </div>',
                '    <div class="col-md-12">',
                '        <small class="schedule-row-feedback text-muted" data-schedule-feedback></small>',
                '    </div>',
                '</div>'
            ].join('');
        }

        $('.professional-modal').each(function () {
            var modal = $(this);

            if (!modal.parent().is('body')) {
                modal.appendTo('body');
            }
        });

        $('.professional-form').each(function () {
            var form = $(this);

            initializeProfessionalNameState(form);
            syncCouncilCategory(form);
            populateScheduleTimeSelects(form);
            syncScheduleDayOptions(form);
            applyScheduleTimeConstraints(form);
            syncColorFeedback(form);
            syncScheduleMode(form);
            form.find('[data-tags-field]').each(function () {
                initializeTagsField($(this));
            });
        });

        var createForm = $('form.professional-form').first();
        syncCouncilCategory(createForm);

        $(document).on('change', '.professional-user-select', function () {
            syncProfessionalFields($(this).closest('form'));
        });

        $(document).on('input', '.professional-name-input', function () {
            var input = $(this);
            var currentValue = $.trim(String(input.val() || ''));
            var lastAutoValue = $.trim(String(input.data('lastAutoValue') || ''));

            input.data('manualName', currentValue !== '' && currentValue !== lastAutoValue);
        });

        $(document).on('click', '[data-remove-tag]', function () {
            var tag = $(this).closest('.professional-tag-pill');
            var container = $(this).closest('[data-tags-field]');
            var hiddenInputs = container.find('[data-tags-hidden-inputs] input');
            var tagValue = normalizeTagValue(tag.data('tagValue'));

            hiddenInputs.filter(function () {
                return normalizeTagValue($(this).val()).toLowerCase() === tagValue.toLowerCase();
            }).first().remove();

            tag.remove();
        });

        $(document).on('change', '.professional-council-select', function () {
            syncCouncilCategory($(this).closest('form'));
        });

        $(document).on('input change', '.professional-color-input', function () {
            syncColorFeedback($(this).closest('form'));
        });

        $(document).on('click', '[data-random-color]', function () {
            generateUniqueColor($(this).closest('form'));
        });

        $(document).on('change', '[name="schedule_mode"]', function () {
            syncScheduleMode($(this).closest('form'));
        });

        $(document).on('change', '.schedule-day-select', function () {
            var form = $(this).closest('form');
            persistManualScheduleState(form);
            syncScheduleDayOptions(form);
        });

        $(document).on('change', '.schedule-time-select', function () {
            var form = $(this).closest('form');
            persistManualScheduleState(form);
            applyScheduleTimeConstraints(form);
        });

        $(document).on('submit', '.professional-form', function () {
            syncScheduleMode($(this));
            applyScheduleTimeConstraints($(this));
            syncColorFeedback($(this));
        });

        $(document).on('click', '.remove-schedule-row', function () {
            var form = $(this).closest('form');
            $(this).closest('.schedule-row').remove();
            persistManualScheduleState(form);
            syncScheduleDayOptions(form);
            applyScheduleTimeConstraints(form);
        });

        $(document).on('click', '.add-schedule-row', function () {
            var form = $(this).closest('form');
            form.find('.schedule-rows-container').append(buildScheduleRow());
            populateScheduleTimeSelects(form);
            persistManualScheduleState(form);
            syncScheduleDayOptions(form);
            applyScheduleTimeConstraints(form);
        });

        populateScheduleTimeSelects(createForm);
        syncScheduleMode(createForm);
        syncColorFeedback(createForm);
    });
</script>
@endpush
@endsection
