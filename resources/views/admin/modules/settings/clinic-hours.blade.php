@extends('admin.layouts.master')

@section('content')
<style>
    .section-body > .card,
    .section-body > .row > .col-12 > .card {
        border: 1px solid #d2dbe6 !important;
        box-shadow: inset 0 0 0 1px #d2dbe6;
    }

    html[data-theme="dark"] .section-body > .card,
    html[data-theme="dark"] .section-body > .row > .col-12 > .card {
        border-color: #000000 !important;
        box-shadow: inset 0 0 0 1px #000000;
    }
</style>
<section class="section">
    <div class="section-header">
        <h1>Horário da Clínica</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">Dashboard</a></div>
            <div class="breadcrumb-item"><a href="{{ route('admin.settings.index') }}">Cadastros Base</a></div>
            <div class="breadcrumb-item">Horário da Clínica</div>
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

        <div class="card">
            <div class="card-header"><h4>Horário de funcionamento</h4></div>
            <div class="card-body">
                <p class="text-muted">Defina a janela em que a clínica aceita agendamentos e, se quiser, configure também o intervalo da clínica. O sistema bloqueará horários fora desse intervalo e durante a pausa configurada.</p>

                <form action="{{ route('admin.settings.clinic-hours.update') }}" method="POST" data-draft-form="true" data-draft-key="admin.settings.clinic-hours.update">
                    @csrf
                    @method('PATCH')

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="opening_time">Horário de abertura *</label>
                                <input type="time" step="900" class="form-control @error('opening_time') is-invalid @enderror" id="opening_time" name="opening_time" value="{{ old('opening_time', $clinicHours ? substr((string) $clinicHours->opening_time, 0, 5) : '07:00') }}" required>
                                @error('opening_time')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="closing_time">Horário de término *</label>
                                <input type="time" step="900" class="form-control @error('closing_time') is-invalid @enderror" id="closing_time" name="closing_time" value="{{ old('closing_time', $clinicHours ? substr((string) $clinicHours->closing_time, 0, 5) : '19:00') }}" required>
                                @error('closing_time')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="lunch_start_time">Início do intervalo</label>
                                <div class="input-group">
                                    <input type="time" step="900" class="form-control @error('lunch_start_time') is-invalid @enderror" id="lunch_start_time" name="lunch_start_time" value="{{ old('lunch_start_time', $clinicHours && $clinicHours->lunch_start_time ? substr((string) $clinicHours->lunch_start_time, 0, 5) : '') }}">
                                    <div class="input-group-append">
                                        <span class="input-group-text">Intervalo da clínica</span>
                                    </div>
                                </div>
                                @error('lunch_start_time')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="lunch_end_time">Término do intervalo</label>
                                <div class="input-group">
                                    <input type="time" step="900" class="form-control @error('lunch_end_time') is-invalid @enderror" id="lunch_end_time" name="lunch_end_time" value="{{ old('lunch_end_time', $clinicHours && $clinicHours->lunch_end_time ? substr((string) $clinicHours->lunch_end_time, 0, 5) : '') }}">
                                    <div class="input-group-append">
                                        <span class="input-group-text">Intervalo da clínica</span>
                                    </div>
                                </div>
                                @error('lunch_end_time')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Salvar horário</button>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection
