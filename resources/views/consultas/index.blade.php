@extends('layouts.app')

@section('page_title', 'Agenda de Consultas')

@section('content')
<div class="card-box">
    <div class="card-box-header">
        <h2 class="card-box-title">Agenda e Atendimentos Médicos</h2>
        <a href="{{ route('consultas.create') }}" class="btn-andrik-primary">Nova Consulta</a>
    </div>

    <div class="card-box-body bg-light border-bottom py-2">
        <form action="{{ route('consultas.index') }}" method="GET" class="form-row align-items-end">
            <div class="col-md-3 mb-2 mb-md-0">
                <label class="small font-weight-bold text-muted mb-1">DATA</label>
                <input type="date" name="data" class="form-control" value="{{ $dataFiltro }}">
            </div>
            <div class="col-md-3 mb-2 mb-md-0">
                <label class="small font-weight-bold text-muted mb-1">MÉDICO</label>
                <select name="medico_id" class="form-control">
                    <option value="">Todos os Médicos</option>
                    @foreach($medicos as $m)
                        <option value="{{ $m->id }}" {{ $medicoFiltro == $m->id ? 'selected' : '' }}>
                            {{ $m->name }} ({{ $m->especialidade }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 mb-2 mb-md-0">
                <label class="small font-weight-bold text-muted mb-1">STATUS</label>
                <select name="status" class="form-control">
                    <option value="">Todos os Status</option>
                    <option value="agendada" {{ $statusFiltro === 'agendada' ? 'selected' : '' }}>Agendada</option>
                    <option value="confirmada" {{ $statusFiltro === 'confirmada' ? 'selected' : '' }}>Confirmada</option>
                    <option value="aguardando" {{ $statusFiltro === 'aguardando' ? 'selected' : '' }}>Aguardando na Recepção</option>
                    <option value="em_atendimento" {{ $statusFiltro === 'em_atendimento' ? 'selected' : '' }}>Em Atendimento</option>
                    <option value="finalizada" {{ $statusFiltro === 'finalizada' ? 'selected' : '' }}>Finalizada</option>
                    <option value="cancelada" {{ $statusFiltro === 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                </select>
            </div>
            <div class="col-md-3 d-flex">
                <button type="submit" class="btn-andrik-primary mr-2 flex-grow-1 justify-content-center">Filtrar</button>
                @if($dataFiltro || $medicoFiltro || $statusFiltro)
                    <a href="{{ route('consultas.index') }}" class="btn-andrik-outline">Limpar</a>
                @endif
            </div>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table-andrik">
            <thead>
                <tr>
                    <th style="width: 140px;">Data / Horário</th>
                    <th>Paciente</th>
                    <th>Médico / Especialidade</th>
                    <th>Tipo</th>
                    <th>Convênio</th>
                    <th>Status</th>
                    <th style="text-align: right;">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($consultas as $c)
                    <tr>
                        <td>
                            <strong>{{ $c->data_hora->format('d/m/Y') }}</strong>
                            <small class="text-muted d-block">{{ $c->data_hora->format('H:i') }} ({{ $c->duracao_minutos }} min)</small>
                        </td>
                        <td>
                            <div style="font-weight: 600;">
                                <a href="{{ route('pacientes.show', $c->paciente_id) }}" style="color: #0f172a;">
                                    {{ $c->paciente->nome }}
                                </a>
                            </div>
                            <small class="text-muted">{{ $c->paciente->telefone }}</small>
                            @if($c->paciente->alergias)
                                <span class="badge badge-danger ml-1" title="Alergias: {{ $c->paciente->alergias }}">Alergia</span>
                            @endif
                        </td>
                        <td>
                            <div>{{ $c->medico->name }}</div>
                            <small class="text-muted">{{ $c->especialidade }}</small>
                        </td>
                        <td>{{ $c->tipo_atendimento_label }}</td>
                        <td>{{ $c->convenio }}</td>
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
                            @endif
                            <a href="{{ route('consultas.show', $c->id) }}" class="btn-andrik-outline ml-1">Detalhes</a>
                            <a href="{{ route('consultas.edit', $c->id) }}" class="btn-andrik-outline ml-1">Editar</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            Nenhuma consulta encontrada para os critérios selecionados.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($consultas->hasPages())
        <div class="card-box-body bg-white border-top py-2">
            {{ $consultas->links() }}
        </div>
    @endif
</div>
@endsection
