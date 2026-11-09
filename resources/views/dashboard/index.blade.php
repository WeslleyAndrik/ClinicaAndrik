@extends('layouts.app')

@section('page_title', 'Painel Clínico Geral')

@section('content')
<div class="metrics-grid">
    <div class="metric-card">
        <div>
            <div class="metric-number">{{ $totalPacientes }}</div>
            <div class="metric-title">Pacientes Cadastrados</div>
        </div>
    </div>

    <div class="metric-card" style="border-left-color: #0284c7;">
        <div>
            <div class="metric-number">{{ $consultasHojeCount }}</div>
            <div class="metric-title">Consultas Hoje</div>
        </div>
    </div>

    <div class="metric-card" style="border-left-color: #b45309;">
        <div>
            <div class="metric-number">{{ $consultasAguardandoCount }}</div>
            <div class="metric-title">Em Espera / Sala</div>
        </div>
    </div>

    <div class="metric-card" style="border-left-color: #15803d;">
        <div>
            <div class="metric-number">{{ $prontuariosCount }}</div>
            <div class="metric-title">Prontuários Registrados</div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card-box">
            <div class="card-box-header">
                <h2 class="card-box-title">Consultas Programadas para Hoje</h2>
                <a href="{{ route('consultas.index', ['data' => date('Y-m-d')]) }}" class="btn-andrik-outline">
                    Ver Agenda Completa
                </a>
            </div>

            <div class="table-responsive">
                <table class="table-andrik">
                    <thead>
                        <tr>
                            <th style="width: 85px;">Horário</th>
                            <th>Paciente</th>
                            <th>Médico / Especialidade</th>
                            <th>Status</th>
                            <th style="text-align: right;">Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($consultasHoje as $c)
                            <tr>
                                <td>
                                    <strong>{{ $c->data_hora->format('H:i') }}</strong>
                                </td>
                                <td>
                                    <div style="font-weight: 600;">
                                        <a href="{{ route('pacientes.show', $c->paciente_id) }}" style="color: #0f172a;">
                                            {{ $c->paciente->nome }}
                                        </a>
                                    </div>
                                    <small class="text-muted">
                                        {{ $c->paciente->idade ? $c->paciente->idade . ' anos - ' : '' }}{{ $c->convenio }}
                                    </small>
                                </td>
                                <td>
                                    <div>{{ $c->medico->name }}</div>
                                    <small class="text-muted">{{ $c->especialidade }}</small>
                                </td>
                                <td>
                                    <span class="badge-status {{ $c->status }}">{{ $c->status_label }}</span>
                                </td>
                                <td style="text-align: right;">
                                    @if(Auth::user()->isMedico() || Auth::user()->isAdmin())
                                        @if($c->status !== 'finalizada' && $c->status !== 'cancelada')
                                            <a href="{{ route('prontuarios.create', ['paciente_id' => $c->paciente_id, 'consulta_id' => $c->id]) }}" class="btn-andrik-success">
                                                Atender
                                            </a>
                                        @elseif($c->prontuario)
                                            <a href="{{ route('prontuarios.show', $c->prontuario->id) }}" class="btn-andrik-outline">
                                                Prontuário
                                            </a>
                                        @endif
                                    @else
                                        <a href="{{ route('consultas.show', $c->id) }}" class="btn-andrik-outline">
                                            Detalhes
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    Nenhuma consulta agendada para hoje.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card-box">
            <div class="card-box-header">
                <h2 class="card-box-title">Ações Rápidas</h2>
            </div>
            <div class="card-box-body">
                <a href="{{ route('consultas.create') }}" class="btn-andrik-primary btn-block justify-content-center mb-2">
                    Novo Agendamento
                </a>
                <a href="{{ route('pacientes.create') }}" class="btn-andrik-outline btn-block justify-content-center mb-2">
                    Cadastrar Paciente
                </a>
                <a href="{{ route('pacientes.index') }}" class="btn-andrik-outline btn-block justify-content-center">
                    Consultar Pacientes
                </a>
            </div>
        </div>

        <div class="card-box">
            <div class="card-box-header">
                <h2 class="card-box-title">Próximos Atendimentos</h2>
            </div>
            <ul class="list-group list-group-flush">
                @forelse($proximasConsultas as $pc)
                    <li class="list-group-item d-flex justify-content-between align-items-center py-2 px-3">
                        <div>
                            <div class="font-weight-bold text-dark">{{ $pc->paciente->nome }}</div>
                            <small class="text-muted">{{ $pc->data_hora->format('d/m H:i') }} - {{ $pc->medico->name }}</small>
                        </div>
                        <span class="badge-status {{ $pc->status }}">{{ $pc->status_label }}</span>
                    </li>
                @empty
                    <li class="list-group-item text-center text-muted py-3">
                        Sem agendamentos futuros.
                    </li>
                @endforelse
            </ul>
        </div>
    </div>
</div>

<div class="card-box">
    <div class="card-box-header">
        <h2 class="card-box-title">Últimos Prontuários Registrados</h2>
    </div>
    <div class="table-responsive">
        <table class="table-andrik">
            <thead>
                <tr>
                    <th style="width: 140px;">Data / Hora</th>
                    <th>Paciente</th>
                    <th>Médico Responsável</th>
                    <th>Diagnóstico / CID-10</th>
                    <th>Sinais Vitais</th>
                    <th style="text-align: right;">Ação</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ultimosProntuarios as $pr)
                    <tr>
                        <td>{{ $pr->data_registro->format('d/m/Y H:i') }}</td>
                        <td>
                            <strong>{{ $pr->paciente->nome }}</strong>
                            @if($pr->paciente->tipo_sanguineo)
                                <span class="badge badge-light border ml-1">{{ $pr->paciente->tipo_sanguineo }}</span>
                            @endif
                        </td>
                        <td>
                            <div>{{ $pr->medico->name }}</div>
                            <small class="text-muted">{{ $pr->medico->crm }}</small>
                        </td>
                        <td>
                            <strong>{{ $pr->diagnostico }}</strong>
                            @if($pr->cid10)
                                <span class="badge badge-secondary ml-1">{{ $pr->cid10 }}</span>
                            @endif
                        </td>
                        <td>
                            <small>PA: <strong>{{ $pr->pressao_arterial ?? 'N/I' }}</strong></small>
                            @if($pr->imc)
                                <span class="badge badge-light border ml-1">IMC: {{ $pr->imc }}</span>
                            @endif
                        </td>
                        <td style="text-align: right;">
                            <a href="{{ route('prontuarios.show', $pr->id) }}" class="btn-andrik-outline">Visualizar</a>
                            <a href="{{ route('pacientes.historico', $pr->paciente_id) }}" class="btn-andrik-outline ml-1">Histórico</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            Nenhum prontuário registrado até o momento.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
