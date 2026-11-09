@extends('layouts.app')

@section('page_title', 'Histórico Clínico do Paciente')

@section('content')
<div class="card-box">
    <div class="card-box-body">
        <div class="d-flex flex-wrap align-items-center justify-content-between">
            <div>
                <span class="text-muted small text-uppercase font-weight-bold">Linha do Tempo Clínica</span>
                <h4 class="font-weight-bold mb-1 text-dark">{{ $paciente->nome }}</h4>
                <div class="text-muted small">
                    <span>CPF: <strong>{{ $paciente->cpf }}</strong></span> |
                    <span>Nascimento: {{ $paciente->data_nascimento->format('d/m/Y') }} ({{ $paciente->idade }} anos)</span> |
                    <span>Convênio: <strong>{{ $paciente->convenio ?? 'Particular' }}</strong></span>
                </div>
            </div>
            <div>
                <a href="{{ route('consultas.create', ['paciente_id' => $paciente->id]) }}" class="btn-andrik-primary mr-1">Agendar Consulta</a>
                <a href="{{ route('pacientes.show', $paciente->id) }}" class="btn-andrik-outline">Ficha Cadastral</a>
            </div>
        </div>

        @if($paciente->alergias)
            <div class="alert-allergy mt-3">
                <strong>ALERTA DE ALERGIAS:</strong> {{ $paciente->alergias }}
            </div>
        @endif
    </div>
</div>

<div class="card-box no-print">
    <div class="card-box-body py-2">
        <form action="{{ route('pacientes.historico', $paciente->id) }}" method="GET" class="form-row align-items-end">
            <div class="col-md-4 mb-2 mb-md-0">
                <label class="small font-weight-bold text-muted mb-1">PERÍODO</label>
                <select name="periodo" class="form-control" onchange="this.form.submit()">
                    <option value="todos" {{ $periodo === 'todos' ? 'selected' : '' }}>Todo o Histórico</option>
                    <option value="30d" {{ $periodo === '30d' ? 'selected' : '' }}>Últimos 30 dias</option>
                    <option value="6m" {{ $periodo === '6m' ? 'selected' : '' }}>Últimos 6 meses</option>
                    <option value="1a" {{ $periodo === '1a' ? 'selected' : '' }}>Último 1 ano</option>
                </select>
            </div>
            <div class="col-md-5 mb-2 mb-md-0">
                <label class="small font-weight-bold text-muted mb-1">MÉDICO RESPONSÁVEL</label>
                <select name="medico_id" class="form-control" onchange="this.form.submit()">
                    <option value="">Todos os Médicos</option>
                    @foreach($medicosAtendimento as $med)
                        <option value="{{ $med->id }}" {{ $medicoFiltro == $med->id ? 'selected' : '' }}>
                            {{ $med->name }} ({{ $med->especialidade }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-flex">
                <button type="submit" class="btn-andrik-primary mr-2 flex-grow-1 justify-content-center">Filtrar</button>
                @if($periodo !== 'todos' || $medicoFiltro)
                    <a href="{{ route('pacientes.historico', $paciente->id) }}" class="btn-andrik-outline">Limpar</a>
                @endif
            </div>
        </form>
    </div>
</div>

@if($consultas->isEmpty() && $prontuarios->isEmpty())
    <div class="card-box text-center py-4">
        <div class="text-muted">Nenhum registro clínico ou consulta encontrada para este paciente.</div>
    </div>
@else
    <ul class="timeline">
        @foreach($consultas as $consulta)
            <li class="timeline-item">
                <div class="timeline-point"></div>
                <div class="timeline-content">
                    <div class="d-flex flex-wrap justify-content-between align-items-start mb-2">
                        <div>
                            <div class="mb-1">
                                <span class="badge badge-light border text-dark mr-1 font-weight-bold">
                                    {{ $consulta->data_hora->format('d/m/Y H:i') }}
                                </span>
                                <span class="badge-status {{ $consulta->status }} mr-1">
                                    {{ $consulta->status_label }}
                                </span>
                                <span class="badge badge-secondary">
                                    {{ $consulta->tipo_atendimento_label }}
                                </span>
                            </div>
                            <h6 class="font-weight-bold text-dark mb-0">
                                {{ $consulta->medico->name }}
                                <small class="text-muted font-weight-normal">({{ $consulta->especialidade }} - {{ $consulta->medico->crm }})</small>
                            </h6>
                        </div>
                        <div class="text-right">
                            <span class="text-muted small">Convênio: <strong>{{ $consulta->convenio }}</strong></span>
                            @if($consulta->valor > 0)
                                <div class="text-muted small">R$ {{ number_format($consulta->valor, 2, ',', '.') }}</div>
                            @endif
                        </div>
                    </div>

                    @if($consulta->motivo)
                        <div class="mb-2 p-2 bg-light border small">
                            <strong>Motivo do Agendamento:</strong> {{ $consulta->motivo }}
                        </div>
                    @endif

                    @if($consulta->prontuario)
                        @php $pr = $consulta->prontuario; @endphp
                        <div class="border-top pt-2 mt-2">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <strong class="text-uppercase small text-success">Prontuário Registrado</strong>
                                <a href="{{ route('prontuarios.show', $pr->id) }}" class="btn-andrik-outline">Abrir Prontuário</a>
                            </div>

                            <div class="d-flex flex-wrap mb-2 bg-light p-2 border small">
                                @if($pr->pressao_arterial)
                                    <div class="mr-3"><strong>PA:</strong> {{ $pr->pressao_arterial }}</div>
                                @endif
                                @if($pr->frequencia_cardiaca)
                                    <div class="mr-3"><strong>FC:</strong> {{ $pr->frequencia_cardiaca }} bpm</div>
                                @endif
                                @if($pr->temperatura)
                                    <div class="mr-3"><strong>Temp:</strong> {{ $pr->temperatura }} °C</div>
                                @endif
                                @if($pr->peso)
                                    <div class="mr-3"><strong>Peso:</strong> {{ $pr->peso }} kg</div>
                                @endif
                                @if($pr->altura)
                                    <div class="mr-3"><strong>Altura:</strong> {{ $pr->altura }} m</div>
                                @endif
                                @if($pr->imc)
                                    <div><strong>IMC:</strong> {{ $pr->imc }} ({{ $pr->imc_classificacao }})</div>
                                @endif
                            </div>

                            <div class="mb-2 small">
                                <strong>Queixa Principal:</strong> {{ $pr->queixa_principal }}
                            </div>

                            <div class="mb-2 p-2 bg-light border-left border-primary small">
                                <strong>Hipótese Diagnóstica:</strong> {{ $pr->diagnostico }}
                                @if($pr->cid10)
                                    <span class="badge badge-secondary ml-1">{{ $pr->cid10 }}</span>
                                @endif
                            </div>

                            @if($pr->prescricao_medica)
                                <div class="mb-1 small">
                                    <strong>Prescrição:</strong>
                                    <pre class="bg-light p-2 border mb-0 text-dark" style="font-family: inherit; white-space: pre-wrap;">{{ $pr->prescricao_medica }}</pre>
                                </div>
                            @endif
                        </div>
                    @else
                        @if(Auth::user()->isMedico() || Auth::user()->isAdmin())
                            <div class="border-top pt-2 mt-2 text-right">
                                <a href="{{ route('prontuarios.create', ['paciente_id' => $paciente->id, 'consulta_id' => $consulta->id]) }}" class="btn-andrik-success">
                                    Registrar Prontuário
                                </a>
                            </div>
                        @endif
                    @endif
                </div>
            </li>
        @endforeach
    </ul>
@endif
@endsection
