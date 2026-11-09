@extends('layouts.app')

@section('page_title', 'Detalhes do Agendamento')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card-box">
            <div class="card-box-header">
                <div>
                    <strong class="text-uppercase">Consulta #{{ $consulta->id }}</strong> &mdash;
                    <span class="badge-status {{ $consulta->status }}">{{ $consulta->status_label }}</span>
                </div>
                <div>
                    <a href="{{ route('consultas.edit', $consulta->id) }}" class="btn-andrik-outline mr-1">Remarcar / Editar</a>
                    <a href="{{ route('consultas.index') }}" class="btn-andrik-outline">Voltar</a>
                </div>
            </div>

            <div class="card-box-body">
                <div class="p-3 bg-light border mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted small text-uppercase font-weight-bold">Paciente</span>
                            <h5 class="font-weight-bold mb-1">
                                <a href="{{ route('pacientes.show', $consulta->paciente_id) }}" class="text-dark">
                                    {{ $consulta->paciente->nome }}
                                </a>
                            </h5>
                            <div class="text-muted small">
                                CPF: {{ $consulta->paciente->cpf }} | Telefone: {{ $consulta->paciente->telefone }}
                            </div>
                        </div>
                        <div class="text-right">
                            <a href="{{ route('pacientes.historico', $consulta->paciente_id) }}" class="btn-andrik-outline">
                                Ver Histórico Clínico
                            </a>
                        </div>
                    </div>

                    @if($consulta->paciente->alergias)
                        <div class="alert-allergy mt-2 py-2">
                            <strong>Alergias:</strong> {{ $consulta->paciente->alergias }}
                        </div>
                    @endif
                </div>

                <div class="row mb-3">
                    <div class="col-md-6 mb-3">
                        <label class="small text-muted font-weight-bold text-uppercase">Médico Responsável:</label>
                        <div class="font-weight-bold text-dark">{{ $consulta->medico->name }}</div>
                        <div class="text-muted small">{{ $consulta->especialidade }} | {{ $consulta->medico->crm }}</div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="small text-muted font-weight-bold text-uppercase">Data e Horário:</label>
                        <div class="font-weight-bold text-dark">
                            {{ $consulta->data_hora->format('d/m/Y') }} às {{ $consulta->data_hora->format('H:i') }}
                        </div>
                        <div class="text-muted small">Duração prevista: {{ $consulta->duracao_minutos }} minutos</div>
                    </div>
                    <div class="col-md-6 mb-2">
                        <label class="small text-muted font-weight-bold text-uppercase">Tipo de Atendimento:</label>
                        <div><span class="badge badge-secondary">{{ $consulta->tipo_atendimento_label }}</span></div>
                    </div>
                    <div class="col-md-6 mb-2">
                        <label class="small text-muted font-weight-bold text-uppercase">Convênio / Valor:</label>
                        <div class="font-weight-bold text-dark">
                            {{ $consulta->convenio }} | R$ {{ number_format($consulta->valor, 2, ',', '.') }}
                        </div>
                    </div>
                </div>

                @if($consulta->motivo)
                    <div class="mb-3">
                        <label class="small text-muted font-weight-bold text-uppercase">Motivo do Agendamento:</label>
                        <p class="p-2 bg-light border text-dark mb-0">{{ $consulta->motivo }}</p>
                    </div>
                @endif

                @if($consulta->observacoes)
                    <div class="mb-2">
                        <label class="small text-muted font-weight-bold text-uppercase">Observações Administrativas:</label>
                        <p class="p-2 bg-light border text-dark small mb-0">{{ $consulta->observacoes }}</p>
                    </div>
                @endif
            </div>

            @if(Auth::user()->isMedico() || Auth::user()->isAdmin())
                <div class="card-box-header bg-light justify-content-between">
                    <div>
                        @if($consulta->prontuario)
                            <span class="text-success font-weight-bold small">
                                Prontuário registrado para esta consulta.
                            </span>
                        @endif
                    </div>
                    <div>
                        @if(!$consulta->prontuario && $consulta->status !== 'cancelada')
                            <a href="{{ route('prontuarios.create', ['paciente_id' => $consulta->paciente_id, 'consulta_id' => $consulta->id]) }}" class="btn-andrik-success">
                                Iniciar Atendimento Clínico
                            </a>
                        @elseif($consulta->prontuario)
                            <a href="{{ route('prontuarios.show', $consulta->prontuario->id) }}" class="btn-andrik-primary">
                                Visualizar Prontuário
                            </a>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>

    <div class="col-md-4">
        <div class="card-box">
            <div class="card-box-header">
                <h2 class="card-box-title">Fluxo de Atendimento</h2>
            </div>
            <div class="card-box-body">
                <p class="text-muted small">Atualize o status conforme o andamento na recepção:</p>

                <form action="{{ route('consultas.status', $consulta->id) }}" method="POST" class="mb-2">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="confirmada">
                    <button type="submit" class="btn-andrik-outline btn-block justify-content-start">
                        1. Confirmar Presença
                    </button>
                </form>

                <form action="{{ route('consultas.status', $consulta->id) }}" method="POST" class="mb-2">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="aguardando">
                    <button type="submit" class="btn-andrik-outline btn-block justify-content-start">
                        2. Aguardando na Recepção
                    </button>
                </form>

                <form action="{{ route('consultas.status', $consulta->id) }}" method="POST" class="mb-2">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="em_atendimento">
                    <button type="submit" class="btn-andrik-outline btn-block justify-content-start">
                        3. Chamar para Consultório
                    </button>
                </form>

                <form action="{{ route('consultas.status', $consulta->id) }}" method="POST" class="mb-2">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="finalizada">
                    <button type="submit" class="btn-andrik-outline btn-block justify-content-start">
                        4. Finalizar Consulta
                    </button>
                </form>

                <hr>

                <form action="{{ route('consultas.whatsapp', $consulta->id) }}" method="POST" class="mb-2">
                    @csrf
                    <button type="submit" class="btn-andrik-primary btn-block justify-content-center">
                        Disparar Confirmação via WhatsApp
                    </button>
                </form>

                <form action="{{ route('consultas.destroy', $consulta->id) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja cancelar esta consulta?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-block btn-outline-danger btn-sm">
                        Cancelar Agendamento
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
