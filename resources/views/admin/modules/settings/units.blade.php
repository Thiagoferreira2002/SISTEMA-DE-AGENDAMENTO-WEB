@extends('admin.layouts.master')
@section('content')
<section class="section">
    <div class="section-header">
        <h1>Unidades e Salas</h1>
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

        <div class="row">
            <div class="col-lg-7">
                <div class="card mb-4">
                    <div class="card-header"><h4>Nova unidade</h4></div>
                    <div class="card-body">
                        <form action="{{ route('admin.settings.units.store') }}" method="POST" data-draft-form="true" data-draft-key="admin.settings.units.create">
                            @csrf
                            <div class="row">
                                <div class="col-md-4"><div class="form-group"><label>Nome *</label><input type="text" class="form-control" name="nome" required></div></div>
                                <div class="col-md-5"><div class="form-group"><label>Endereço</label><input type="text" class="form-control" name="endereco"></div></div>
                                <div class="col-md-3"><div class="form-group"><label>Telefone</label><input type="text" class="form-control" name="telefone"></div></div>
                                <div class="col-md-6"><div class="form-group mb-0"><label>E-mail</label><input type="email" class="form-control" name="email"></div></div>
                            </div>
                            <button type="submit" class="btn btn-primary mt-3">Cadastrar unidade</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card mb-4">
                    <div class="card-header"><h4>Nova sala</h4></div>
                    <div class="card-body">
                        <form action="{{ route('admin.settings.rooms.store') }}" method="POST" data-draft-form="true" data-draft-key="admin.settings.rooms.create">
                            @csrf
                            <div class="form-group">
                                <label>Unidade *</label>
                                <select class="form-control" name="unit_id" required>
                                    <option value="">Selecione</option>
                                    @foreach($units as $unit)
                                        <option value="{{ $unit->id }}">{{ $unit->nome }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group"><label>Nome da sala *</label><input type="text" class="form-control" name="nome" required></div>
                            <button type="submit" class="btn btn-primary">Cadastrar sala</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h4>Unidades cadastradas</h4></div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Unidade</th>
                                <th>Contato</th>
                                <th>Salas</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($units as $unit)
                                <tr>
                                    <td>
                                        <div>{{ $unit->nome }}</div>
                                        <small class="text-muted">{{ $unit->endereco ?: 'Endereço não informado' }}</small>
                                    </td>
                                    <td>
                                        <div>{{ $unit->telefone ?: 'Telefone não informado' }}</div>
                                        <small class="text-muted">{{ $unit->email ?: 'E-mail não informado' }}</small>
                                    </td>
                                    <td>
                                        @forelse($unit->rooms as $room)
                                            <span class="badge badge-light border mr-1 mb-1">{{ $room->nome }}</span>
                                        @empty
                                            <span class="text-muted">Sem salas cadastradas</span>
                                        @endforelse
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-center text-muted">Nenhuma unidade cadastrada.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
