@extends('admin.layouts.master')
@section('content')
 <section class="section">
            <div class="section-header">
              <h1>Painel de Controle</h1>
            </div>
            <div class="row">
              <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                  <div class="card-icon bg-primary">
                    <i class="far fa-calendar"></i>
                  </div>
                  <div class="card-wrap">
                    <div class="card-header">
                      <h4>Total Agendamentos</h4>
                    </div>
                    <div class="card-body">
                      {{ $totalAgendamentos }}
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                  <div class="card-icon bg-warning">
                    <i class="far fa-clock"></i>
                  </div>
                  <div class="card-wrap">
                    <div class="card-header">
                      <h4>Agendamentos Pendentes</h4>
                    </div>
                    <div class="card-body">
                       {{ $agendamentosPendentes }}
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                  <div class="card-icon bg-success">
                    <i class="far fa-check-circle"></i>
                  </div>
                  <div class="card-wrap">
                    <div class="card-header">
                      <h4>Agendamentos Confirmados</h4>
                    </div>
                    <div class="card-body">
                        {{ $agendamentosConfirmados }}
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                <div class="card card-statistic-1">
                  <div class="card-icon bg-info">
                    <i class="far fa-user"></i>
                  </div>
                  <div class="card-wrap">
                    <div class="card-header">
                      <h4>Total Pacientes</h4>
                    </div>
                    <div class="card-body">
                      {{ $totalPacientes }}
                    </div>
                  </div>
                </div>
              </div>
            </div>
          <!-- FIM CONTADORES  -->


          <!-- INICIO PRÓXIMOS AGENDAMENTOS   -->
          <div class="row">
            <div class="col-lg-12 col-md-12 col-12 col-sm-12">
              <div class="card">
                <div class="card-header">
                  <h4>Próximos Agendamentos</h4>
                  <div class="card-header-action">
                    <a href="{{ route('admin.agendamentos.index') }}" class="btn btn-primary">Ver Todos</a>
                  </div>
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table table-striped">
                      <thead>
                        <tr>
                          <th>Nome</th>
                          <th>Serviço</th>
                          <th>Data</th>
                          <th>Horário</th>
                          <th>Status</th>
                        </tr>
                      </thead>
                      <tbody>
                        @forelse($proximosAgendamentos as $agendamento)
                        <tr>
                          <td>{{ $agendamento->nome }}</td>
                          <td>{{ $agendamento->servico }}</td>
                          <td>{{ $agendamento->data_agendamento->format('d/m/Y') }}</td>
                          <td>{{ $agendamento->horario }}</td>
                          <td>
                            @if($agendamento->status == 'confirmado')
                              <span class="badge badge-success">Confirmado</span>
                            @elseif($agendamento->status == 'pendente')
                              <span class="badge badge-warning">Pendente</span>
                            @elseif($agendamento->status == 'cancelado')
                              <span class="badge badge-danger">Cancelado</span>
                            @endif
                          </td>
                        </tr>
                        @empty
                        <tr>
                          <td colspan="5" class="text-center">Nenhum agendamento próximo</td>
                        </tr>
                        @endforelse
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- FIM PRÓXIMOS AGENDAMENTOS   --->



</section>
@endsection
