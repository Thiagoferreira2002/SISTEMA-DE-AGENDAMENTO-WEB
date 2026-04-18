@extends('admin.layouts.master')
@section('content')
<section class="section">
    <div class="section-header">
        <h1>Arquivos e Documentos</h1>
    </div>

    <div class="section-body">
        <div class="row">
            <div class="col-lg-5 col-12">
                <div class="card">
                    <div class="card-header"><h4>Tipos de documento</h4></div>
                    <div class="card-body">
                        <ul class="list-group">
                            @foreach($documentTypes as $type)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    {{ $type }}
                                    <span class="badge badge-primary badge-pill">Upload</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-lg-7 col-12">
                <div class="card">
                    <div class="card-header"><h4>Pacientes aptos para anexos</h4></div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Paciente</th>
                                        <th>Email</th>
                                        <th>Telefone</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($patients as $patient)
                                        <tr>
                                            <td>{{ $patient->nome }}</td>
                                            <td>{{ $patient->email }}</td>
                                            <td>{{ $patient->telefone }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center">Nenhum paciente cadastrado.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
