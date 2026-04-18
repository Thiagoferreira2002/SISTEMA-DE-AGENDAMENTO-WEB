@extends('admin.layouts.master')
@section('content')
<style>
    .professional-modal {
        z-index: 10060;
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

    .professional-modal + .modal-backdrop,
    .modal-backdrop.show {
        z-index: 10050;
    }
</style>
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
                                <label>Nome do profissional</label>
                                <input type="text" class="form-control professional-name-input" id="professional-name" name="nome" value="{{ old('nome') }}" readonly>
                                <small class="text-muted">O nome é preenchido automaticamente com base no usuário profissional vinculado e no conselho selecionado.</small>
                            </div>
                        </div>
                        <div class="col-md-4"><div class="form-group"><label>Especialidade principal *</label><input type="text" class="form-control" name="especialidade_principal" value="{{ old('especialidade_principal') }}" required></div></div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>CPF</label>
                                <input type="text" class="form-control professional-cpf-input" id="professional-cpf" value="{{ old('user_id') ? $formatCpf(optional($availableUsers->firstWhere('id', (int) old('user_id')))->cpf) : '' }}" readonly>
                                <small class="text-muted">O CPF é preenchido automaticamente com base no usuário vinculado.</small>
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
                                <small class="text-muted professional-council-category" id="professional-council-category"></small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Número do registro no conselho *</label>
                                <input type="text" class="form-control" name="registro_numero" value="{{ old('registro_numero') }}" placeholder="Ex.: 12345" required>
                                <small class="text-muted">Informe o número que o profissional possui no conselho de saúde selecionado acima.</small>
                            </div>
                        </div>
                        <div class="col-md-2"><div class="form-group"><label>Cor da agenda *</label><input type="color" class="form-control" name="agenda_color" value="{{ old('agenda_color', '#0d6efd') }}" required></div></div>
                    </div>

                    <div class="border rounded p-3 mt-2">
                        <h6 class="mb-3">Vínculo de agenda</h6>
                        <p class="text-muted mb-3">Você pode escolher Segunda a Sexta de uma vez ou cadastrar os dias manualmente. Dias já escolhidos não poderão ser selecionados novamente.</p>
                        @php
                            $scheduleRowsCount = max(
                                count(old('schedule_day_of_week', [])),
                                count(old('schedule_start_time', [])),
                                count(old('schedule_break_start_time', [])),
                                count(old('schedule_break_end_time', [])),
                                count(old('schedule_end_time', [])),
                                1
                            );
                        @endphp
                        <div class="schedule-rows-container" id="schedule-rows-container">
                        @for($i = 0; $i < $scheduleRowsCount; $i++)
                            <div class="row schedule-row align-items-end" data-schedule-row>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Dia da semana</label>
                                        <select class="form-control schedule-day-select" name="schedule_day_of_week[]">
                                            <option value="">Selecione</option>
                                            <option value="weekdays" {{ old('schedule_day_of_week.' . $i) === 'weekdays' ? 'selected' : '' }}>Segunda a Sexta</option>
                                            @foreach($weekDays as $number => $label)
                                                <option value="{{ $number }}" {{ (string) old('schedule_day_of_week.' . $i) === (string) $number ? 'selected' : '' }}>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2"><div class="form-group"><label>Início</label><input type="time" class="form-control" name="schedule_start_time[]" value="{{ old('schedule_start_time.' . $i) }}"></div></div>
                                <div class="col-md-2"><div class="form-group"><label>Início do descanso</label><input type="time" class="form-control" name="schedule_break_start_time[]" value="{{ old('schedule_break_start_time.' . $i) }}"></div></div>
                                <div class="col-md-2"><div class="form-group"><label>Fim do descanso</label><input type="time" class="form-control" name="schedule_break_end_time[]" value="{{ old('schedule_break_end_time.' . $i) }}"></div></div>
                                <div class="col-md-2"><div class="form-group"><label>Fim</label><input type="time" class="form-control" name="schedule_end_time[]" value="{{ old('schedule_end_time.' . $i) }}"></div></div>
                                <div class="col-md-12 mb-2 text-right">
                                    <button type="button" class="btn btn-outline-danger btn-sm remove-schedule-row" {{ $i === 0 && $scheduleRowsCount === 1 ? 'style=display:none;' : '' }}>Remover</button>
                                </div>
                            </div>
                        @endfor
                        </div>
                        <button type="button" class="btn btn-outline-primary btn-sm mt-2 add-schedule-row" id="add-schedule-row">Adicionar mais um</button>
                        @error('schedule_day_of_week')<div class="text-danger small mt-2">{{ $message }}</div>@enderror
                        @error('schedule_break_start_time')<div class="text-danger small mt-2">{{ $message }}</div>@enderror
                    </div>

                    <button type="submit" class="btn btn-primary mt-3" {{ $availableUsers->isEmpty() ? 'disabled' : '' }}>Cadastrar profissional</button>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap" style="gap: 12px;">
                <h4 class="mb-0">Equipe cadastrada</h4>
                <form action="{{ route('admin.settings.professionals') }}" method="GET" class="d-flex flex-wrap align-items-center" style="gap: 8px;">
                    <input type="text" class="form-control" name="professional_user_search" value="{{ $professionalUserSearch ?? '' }}" placeholder="Pesquisar usuário vinculado" style="min-width: 260px;">
                    <button type="submit" class="btn btn-primary btn-sm">Pesquisar</button>
                    @if(!empty($professionalUserSearch))
                        <a href="{{ route('admin.settings.professionals') }}" class="btn btn-light btn-sm">Limpar</a>
                    @endif
                </form>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Profissão</th>
                                <th>Usuário vinculado</th>
                                <th>Especialidade</th>
                                <th>CPF / Registro</th>
                                <th>Cor</th>
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
                                    <td>{{ $professionalLabel }}</td>
                                    <td>
                                        @if($professional->user)
                                            <div>{{ trim(($professional->user->nome ?? '') . ' ' . ($professional->user->sobrenome ?? '')) }}</div>
                                        @else
                                            <span class="text-muted">Usuário não vinculado</span>
                                        @endif
                                    </td>
                                    <td>{{ $professional->especialidade_principal }}</td>
                                    <td class="professional-registry-cell">
                                        <div>{{ $formatCpf($professional->cpf) ?: 'CPF não informado' }}</div>
                                        <small class="text-muted">{{ $professional->registro_completo }}</small>
                                    </td>
                                    <td><span class="badge" style="background: {{ $professional->agenda_color }}; color: #fff;">{{ $professional->agenda_color }}</span></td>
                                    <td>
                                        @forelse(($professional->display_schedules ?? collect()) as $schedule)
                                            <span class="badge badge-light border mr-1 mb-1">{{ $weekDays[$schedule['day_of_week']] ?? $schedule['day_of_week'] }} {{ $schedule['start_time'] }} às {{ $schedule['end_time'] }}{{ $schedule['break_start_time'] && $schedule['break_end_time'] ? ' • descanso ' . $schedule['break_start_time'] . ' às ' . $schedule['break_end_time'] : '' }}</span>
                                        @empty
                                            <span class="text-muted">Sem agenda definida</span>
                                        @endforelse
                                    </td>
                                    <td>
                                        <div class="d-flex flex-wrap" style="gap: 6px;">
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
                                <tr><td colspan="7" class="text-center text-muted">Nenhum profissional cadastrado.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        @foreach($professionals as $professional)
            @php
                $professionalScheduleRowsCount = max($professional->schedules->count(), 1);
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
                                <div class="col-md-6 mb-3"><div class="border rounded p-3 h-100 bg-white"><div class="text-muted small text-uppercase">Nome</div><div class="font-weight-bold mt-1">{{ $professional->nome }}</div></div></div>
                                <div class="col-md-6 mb-3"><div class="border rounded p-3 h-100 bg-white"><div class="text-muted small text-uppercase">Usuário vinculado</div><div class="font-weight-bold mt-1">{{ $professional->user ? trim(($professional->user->nome ?? '') . ' ' . ($professional->user->sobrenome ?? '')) : 'Usuário não vinculado' }}</div></div></div>
                                <div class="col-md-4 mb-3"><div class="border rounded p-3 h-100 bg-white"><div class="text-muted small text-uppercase">Especialidade</div><div class="mt-1">{{ $professional->especialidade_principal }}</div></div></div>
                                <div class="col-md-4 mb-3"><div class="border rounded p-3 h-100 bg-white"><div class="text-muted small text-uppercase">CPF</div><div class="mt-1">{{ $formatCpf($professional->cpf) ?: 'Não informado' }}</div></div></div>
                                <div class="col-md-4 mb-3"><div class="border rounded p-3 h-100 bg-white"><div class="text-muted small text-uppercase">Registro</div><div class="mt-1">{{ $professional->registro_completo }}</div></div></div>
                                <div class="col-md-12 mb-3"><div class="border rounded p-3 h-100 bg-white"><div class="text-muted small text-uppercase">Disponibilidade</div><div class="mt-2">@forelse($professional->schedules as $schedule)<span class="badge badge-light border mr-1 mb-1">{{ $weekDays[$schedule->day_of_week] ?? $schedule->day_of_week }} {{ substr($schedule->start_time, 0, 5) }} às {{ substr($schedule->end_time, 0, 5) }}{{ $schedule->break_start_time && $schedule->break_end_time ? ' • descanso ' . substr($schedule->break_start_time, 0, 5) . ' às ' . substr($schedule->break_end_time, 0, 5) : '' }}</span>@empty<span class="text-muted">Sem agenda definida</span>@endforelse</div></div></div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade professional-modal" id="edit-professional-modal-{{ $professional->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-xl professional-edit-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Editar profissional</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form action="{{ route('admin.settings.professionals.update', $professional) }}" method="POST" class="professional-form">
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
                                            <label>Nome do profissional</label>
                                            <input type="text" class="form-control professional-name-input" value="{{ $professional->nome }}" readonly>
                                            <small class="text-muted">O nome é exibido automaticamente conforme o usuário e o conselho.</small>
                                        </div>
                                    </div>
                                    <div class="col-md-4"><div class="form-group"><label>Especialidade principal *</label><input type="text" class="form-control" name="especialidade_principal" value="{{ $professional->especialidade_principal }}" required></div></div>
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
                                    <div class="col-md-4"><div class="form-group"><label>Número do registro no conselho *</label><input type="text" class="form-control" name="registro_numero" value="{{ $professional->registro_numero }}" required></div></div>
                                    <div class="col-md-2"><div class="form-group"><label>Cor da agenda *</label><input type="color" class="form-control" name="agenda_color" value="{{ $professional->agenda_color }}" required></div></div>
                                </div>

                                <div class="border rounded p-3 mt-2">
                                    <h6 class="mb-3">Vínculo de agenda</h6>
                                    <div class="schedule-rows-container">
                                        @for($i = 0; $i < $professionalScheduleRowsCount; $i++)
                                            @php
                                                $schedule = $professional->schedules[$i] ?? null;
                                            @endphp
                                            <div class="row schedule-row align-items-end" data-schedule-row>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>Dia da semana</label>
                                                        <select class="form-control schedule-day-select" name="schedule_day_of_week[]">
                                                            <option value="">Selecione</option>
                                                            <option value="weekdays">Segunda a Sexta</option>
                                                            @foreach($weekDays as $number => $label)
                                                                <option value="{{ $number }}" {{ (int) ($schedule->day_of_week ?? 0) === (int) $number ? 'selected' : '' }}>{{ $label }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-2"><div class="form-group"><label>Início</label><input type="time" class="form-control" name="schedule_start_time[]" value="{{ $schedule ? substr($schedule->start_time, 0, 5) : '' }}"></div></div>
                                                <div class="col-md-2"><div class="form-group"><label>Início do descanso</label><input type="time" class="form-control" name="schedule_break_start_time[]" value="{{ $schedule && $schedule->break_start_time ? substr($schedule->break_start_time, 0, 5) : '' }}"></div></div>
                                                <div class="col-md-2"><div class="form-group"><label>Fim do descanso</label><input type="time" class="form-control" name="schedule_break_end_time[]" value="{{ $schedule && $schedule->break_end_time ? substr($schedule->break_end_time, 0, 5) : '' }}"></div></div>
                                                <div class="col-md-2"><div class="form-group"><label>Fim</label><input type="time" class="form-control" name="schedule_end_time[]" value="{{ $schedule ? substr($schedule->end_time, 0, 5) : '' }}"></div></div>
                                                <div class="col-md-12 mb-2 text-right">
                                                    <button type="button" class="btn btn-outline-danger btn-sm remove-schedule-row" {{ $i === 0 && $professionalScheduleRowsCount === 1 ? 'style=display:none;' : '' }}>Remover</button>
                                                </div>
                                            </div>
                                        @endfor
                                    </div>
                                    <button type="button" class="btn btn-outline-primary btn-sm mt-2 add-schedule-row">Adicionar mais um</button>
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
        function syncProfessionalFields(form) {
            var userSelect = form.find('.professional-user-select');
            var nameInput = form.find('.professional-name-input');
            var cpfInput = form.find('.professional-cpf-input');
            var councilSelect = form.find('.professional-council-select');
            var selectedUser = userSelect.find('option:selected');
            var selectedCouncil = councilSelect.find('option:selected');
            var professionalName = selectedUser.data('name') || '';
            var profession = selectedCouncil.data('profession') || '';

            nameInput.val(professionalName && profession ? professionalName + ' - ' + profession : professionalName);
            cpfInput.val(selectedUser.data('cpf') || '');
        }

        function syncCouncilCategory(form) {
            var councilSelect = form.find('.professional-council-select');
            var councilCategory = form.find('.professional-council-category');
            var selectedCouncil = councilSelect.find('option:selected');
            councilCategory.text(selectedCouncil.data('category') || '');
            syncProfessionalFields(form);
        }

        function getReservedDays(form) {
            var reserved = [];

            form.find('.schedule-day-select').each(function () {
                var value = $(this).val();

                if (!value) {
                    return;
                }

                if (value === 'weekdays') {
                    reserved = reserved.concat(['1', '2', '3', '4', '5']);
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
                    if (currentValue === 'weekdays' && ['1', '2', '3', '4', '5'].indexOf(value) !== -1) {
                        return false;
                    }

                    return value !== currentValue;
                });

                currentSelect.find('option').each(function () {
                    var option = $(this);
                    var optionValue = String(option.val() || '');

                    if (!optionValue) {
                        option.prop('disabled', false);
                        return;
                    }

                    if (optionValue === 'weekdays') {
                        var weekdaysTaken = reservedDays.some(function (value) {
                            return ['1', '2', '3', '4', '5'].indexOf(value) !== -1;
                        });

                        option.prop('disabled', weekdaysTaken);
                        return;
                    }

                    option.prop('disabled', reservedDays.indexOf(optionValue) !== -1);
                });
            });

            form.find('.remove-schedule-row').toggle(form.find('.schedule-row').length > 1);
        }

        function buildScheduleRow() {
            return [
                '<div class="row schedule-row align-items-end" data-schedule-row>',
                '    <div class="col-md-4">',
                '        <div class="form-group">',
                '            <label>Dia da semana</label>',
                '            <select class="form-control schedule-day-select" name="schedule_day_of_week[]">',
                '                <option value="">Selecione</option>',
                '                <option value="weekdays">Segunda a Sexta</option>',
                '                @foreach($weekDays as $number => $label)',
                '                    <option value="{{ $number }}">{{ $label }}</option>',
                '                @endforeach',
                '            </select>',
                '        </div>',
                '    </div>',
                '    <div class="col-md-2"><div class="form-group"><label>Início</label><input type="time" class="form-control" name="schedule_start_time[]"></div></div>',
                '    <div class="col-md-2"><div class="form-group"><label>Início do descanso</label><input type="time" class="form-control" name="schedule_break_start_time[]"></div></div>',
                '    <div class="col-md-2"><div class="form-group"><label>Fim do descanso</label><input type="time" class="form-control" name="schedule_break_end_time[]"></div></div>',
                '    <div class="col-md-2"><div class="form-group"><label>Fim</label><input type="time" class="form-control" name="schedule_end_time[]"></div></div>',
                '    <div class="col-md-12 mb-2 text-right">',
                '        <button type="button" class="btn btn-outline-danger btn-sm remove-schedule-row">Remover</button>',
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

            syncCouncilCategory(form);
            syncScheduleDayOptions(form);
        });

        var createForm = $('form.professional-form').first();
        syncCouncilCategory(createForm);

        $(document).on('change', '.professional-user-select', function () {
            syncProfessionalFields($(this).closest('form'));
        });

        $(document).on('change', '.professional-council-select', function () {
            syncCouncilCategory($(this).closest('form'));
        });

        $(document).on('change', '.schedule-day-select', function () {
            syncScheduleDayOptions($(this).closest('form'));
        });

        $(document).on('click', '.remove-schedule-row', function () {
            var form = $(this).closest('form');
            $(this).closest('.schedule-row').remove();
            syncScheduleDayOptions(form);
        });

        $(document).on('click', '.add-schedule-row', function () {
            var form = $(this).closest('form');
            form.find('.schedule-rows-container').append(buildScheduleRow());
            syncScheduleDayOptions(form);
        });

        syncScheduleDayOptions(createForm);
    });
</script>
@endpush
@endsection
