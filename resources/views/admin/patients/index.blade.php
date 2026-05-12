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

    .patients-mobile-label {
        display: none;
    }

    .patients-drag-scroll {
        cursor: grab;
    }

    .patients-drag-scroll.is-dragging {
        cursor: grabbing;
        user-select: none;
    }

    @media (max-width: 767.98px) {
        .patients-actions {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            justify-content: stretch;
            align-items: stretch;
            gap: 8px;
            width: 100%;
            max-width: 100%;
            margin: 0 auto;
            box-sizing: border-box;
        }

        .patients-actions > *,
        .patients-actions form,
        .patients-actions .btn {
            width: 100%;
            min-width: 0;
            max-width: 100%;
            box-sizing: border-box;
        }

        .patients-actions > *:first-child:nth-last-child(1),
        .patients-actions form:first-child:nth-last-child(1) {
            grid-column: 1 / -1;
        }

        .patients-table-actions {
            min-width: 0 !important;
            width: 100% !important;
            white-space: normal !important;
        }

        .patient-name-cell {
            min-width: 0;
            align-items: center;
            gap: 10px;
            width: 100%;
        }

        .patient-name-cell span {
            word-break: break-word;
            font-size: 15px;
            font-weight: 600;
            line-height: 1.35;
        }

        .patients-mobile-label {
            display: block;
            margin-bottom: 6px;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: var(--text-muted);
        }

        .table-responsive {
            overflow: visible;
        }

        .table-responsive .table {
            border: 0;
        }

        .table-responsive .table thead {
            display: none;
        }

        .table-responsive .table tbody {
            display: grid;
            gap: 14px;
        }

        .table-responsive .table tbody tr {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            border: 1px solid var(--border-soft);
            border-radius: 16px;
            background: var(--surface-primary);
            box-shadow: var(--shadow-soft);
            overflow: visible;
        }

        .table-responsive .table tbody td {
            display: flex;
            flex-direction: column;
            justify-content: center;
            width: 100%;
            min-width: 0;
            padding: 14px 16px !important;
            border: 0;
            text-align: left !important;
            white-space: normal !important;
            word-break: break-word;
            overflow-wrap: anywhere;
        }

        .table-responsive .table tbody tr > td:first-child {
            padding-top: 12px !important;
            padding-bottom: 12px !important;
        }

        .table-responsive .table tbody td {
            border-top: 1px solid var(--border-soft);
        }

        .table-responsive .table tbody tr > td:nth-child(1),
        .table-responsive .table tbody tr > td:nth-child(6),
        .table-responsive .table tbody tr > td:last-child,
        .table-responsive .table tbody tr > td[colspan] {
            grid-column: 1 / -1;
        }

        .table-responsive .table tbody tr > td:nth-child(odd):not(:first-child):not(:nth-child(6)):not(:last-child):not([colspan]) {
            border-right: 1px solid var(--border-soft);
        }

        .table-responsive .table tbody tr > td:nth-child(6) {
            padding-top: 16px !important;
            padding-bottom: 18px !important;
        }

        .table-responsive .table tbody tr > td:not(:last-child) {
            min-height: 74px;
        }

        .table-responsive .table tbody td .badge {
            align-self: flex-start;
        }

        .table-responsive .table tbody td[colspan] {
            text-align: center !important;
        }

        .patients-table-actions {
            min-width: 0 !important;
            width: 100% !important;
            padding-top: 20px !important;
            padding-bottom: 28px !important;
            background: color-mix(in srgb, var(--surface-primary) 88%, var(--primary) 12%);
            align-items: stretch;
            row-gap: 0;
            min-height: 132px;
            overflow: visible;
        }

        .patients-table-actions .btn {
            min-height: 28px;
            padding: 4px 6px;
            font-size: 10px;
            line-height: 1.1;
            border-radius: 10px;
        }

        .patients-actions form {
            display: block;
            width: 100%;
        }

        .patients-table-actions .patients-mobile-label {
            align-self: flex-start;
            width: 100%;
            margin-bottom: 14px;
            line-height: 1.2;
        }

        .patient-name-cell img {
            width: 42px;
            height: 42px;
        }

        @media (max-width: 420px) {
            .patients-actions {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 6px;
            }

            .table-responsive .table tbody tr > td:not(:last-child) {
                min-height: 0;
            }

            .patients-table-actions {
                padding-left: 12px !important;
                padding-right: 12px !important;
                padding-bottom: 32px !important;
                min-height: 140px;
            }

            .patients-table-actions .btn {
                min-height: 26px;
                padding: 4px 5px;
                font-size: 9.5px;
            }
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

                        <div class="table-responsive patients-drag-scroll" data-drag-scroll>
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
                                            <span class="patients-mobile-label">Nome</span>
                                            <div class="patient-name-cell">
                                                <img src="{{ $patient->foto_url }}" alt="Foto de {{ $patient->nome }}">
                                                <span>{{ $patient->nome }}</span>
                                            </div>
                                        </td>
                                        <td><span class="patients-mobile-label">CPF</span>{{ $patient->cpf ?: '-' }}</td>
                                        <td><span class="patients-mobile-label">Email</span>{{ $patient->email }}</td>
                                        <td><span class="patients-mobile-label">Celular</span>{{ $patient->telefone }}</td>
                                        <td class="text-center align-middle"><span class="patients-mobile-label">Data de Nascimento</span>{{ $patient->data_nascimento ? $patient->data_nascimento->format('d/m/Y') : '-' }}</td>
                                        <td class="text-center align-middle">
                                            <span class="patients-mobile-label">Situação cadastral</span>
                                            <span class="badge badge-{{ $patient->cadastro_status_class }}">{{ $patient->cadastro_status_label }}</span>
                                        </td>
                                        <td class="text-center align-middle patients-table-actions action-button-cell">
                                            <span class="patients-mobile-label">Ações</span>
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
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('[data-drag-scroll]').forEach(function (container) {
            let isDragging = false;
            let startY = 0;
            let startX = 0;
            let startScrollTop = 0;
            let startScrollLeft = 0;
            let moved = false;

            const ignoreSelector = 'a, button, input, select, textarea, label, form, .page-link';

            container.addEventListener('mousedown', function (event) {
                if (event.button !== 0 || event.target.closest(ignoreSelector)) {
                    return;
                }

                isDragging = true;
                moved = false;
                startY = event.clientY;
                startX = event.clientX;
                startScrollTop = window.scrollY;
                startScrollLeft = window.scrollX;
                container.classList.add('is-dragging');
            });

            window.addEventListener('mousemove', function (event) {
                if (!isDragging) {
                    return;
                }

                const deltaY = event.clientY - startY;
                const deltaX = event.clientX - startX;

                if (!moved && (Math.abs(deltaY) > 4 || Math.abs(deltaX) > 4)) {
                    moved = true;
                }

                if (moved) {
                    event.preventDefault();
                    window.scrollTo({
                        top: startScrollTop - deltaY,
                        left: startScrollLeft - deltaX,
                        behavior: 'auto'
                    });
                }
            }, { passive: false });

            window.addEventListener('mouseup', function () {
                if (!isDragging) {
                    return;
                }

                isDragging = false;
                container.classList.remove('is-dragging');
            });

            container.addEventListener('mouseleave', function () {
                if (!isDragging) {
                    return;
                }

                isDragging = false;
                container.classList.remove('is-dragging');
            });
        });
    });
</script>
@endsection
