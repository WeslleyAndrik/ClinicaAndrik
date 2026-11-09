@extends('layouts.app')

@section('page_title', 'Ficha do Paciente')

@section('content')
<div class="card-box">
    <div class="card-box-body">
        <div class="d-flex flex-wrap align-items-center justify-content-between">
            <div class="d-flex align-items-center mb-2 mb-md-0">
                <div class="user-avatar mr-3" style="width: 44px; height: 44px; font-size: 1.1rem;">
                    {{ strtoupper(substr($paciente->nome, 0, 1)) }}
                </div>
                <div>
                    <h4 class="font-weight-bold mb-1 text-dark">{{ $paciente->nome }}</h4>
                    <div class="text-muted small">
                        <span>CPF: <strong>{{ $paciente->cpf }}</strong></span> |
                        <span>Nascimento: {{ $paciente->data_nascimento->format('d/m/Y') }} ({{ $paciente->idade }} anos)</span> |
                        <span>Sexo: {{ $paciente->sexo === 'M' ? 'Masculino' : ($paciente->sexo === 'F' ? 'Feminino' : 'Outro') }}</span>
                    </div>
                </div>
            </div>
            <div>
                <a href="{{ route('consultas.create', ['paciente_id' => $paciente->id]) }}" class="btn-andrik-primary mr-1">Agendar Consulta</a>
                <a href="{{ route('pacientes.historico', $paciente->id) }}" class="btn-andrik-outline mr-1">Histórico Clínico</a>
                <a href="{{ route('pacientes.edit', $paciente->id) }}" class="btn-andrik-outline">Editar</a>
            </div>
        </div>

        @if($paciente->alergias)
            <div class="alert-allergy mt-3">
                <strong>ALERTA DE ALERGIAS:</strong> {{ $paciente->alergias }}
            </div>
        @endif
    </div>
</div>

<div class="row">
    <div class="col-md-5">
        <div class="card-box">
            <div class="card-box-header">
                <h2 class="card-box-title">Contato e Convênio</h2>
            </div>
            <table class="table-andrik">
                <tr>
                    <th style="width: 40%;">Telefone:</th>
                    <td><strong>{{ $paciente->telefone }}</strong></td>
                </tr>
                <tr>
                    <th>E-mail:</th>
                    <td>{{ $paciente->email ?? 'Não informado' }}</td>
                </tr>
                <tr>
                    <th>Convênio:</th>
                    <td>{{ $paciente->convenio ?? 'Particular' }}</td>
                </tr>
                @if($paciente->numero_carteirinha)
                    <tr>
                        <th>Carteirinha:</th>
                        <td><code>{{ $paciente->numero_carteirinha }}</code></td>
                    </tr>
                @endif
                <tr>
                    <th>Tipo Sanguíneo:</th>
                    <td>{{ $paciente->tipo_sanguineo ?: 'Não informado' }}</td>
                </tr>
                <tr>
                    <th>Endereço:</th>
                    <td>
                        {{ $paciente->endereco ?? '' }}{{ $paciente->numero ? ', ' . $paciente->numero : '' }}<br>
                        <small class="text-muted">{{ $paciente->bairro }} {{ $paciente->cidade ? '- ' . $paciente->cidade . '/' . $paciente->estado : '' }} {{ $paciente->cep ? '(' . $paciente->cep . ')' : '' }}</small>
                    </td>
                </tr>
                @if($paciente->contato_emergencia_nome)
                    <tr>
                        <th>Emergência:</th>
                        <td>
                            <strong>{{ $paciente->contato_emergencia_nome }}</strong><br>
                            <small class="text-muted">{{ $paciente->contato_emergencia_telefone }}</small>
                        </td>
                    </tr>
                @endif
            </table>
        </div>

        <div class="card-box">
            <div class="card-box-header">
                <h2 class="card-box-title">Medicamentos e Histórico</h2>
            </div>
            <div class="card-box-body">
                <div class="mb-3">
                    <label class="font-weight-bold small text-muted text-uppercase">Medicamentos de Uso Contínuo:</label>
                    <p class="mb-0 text-dark">{{ $paciente->medicamentos_uso_continuo ?: 'Nenhum medicamento informado.' }}</p>
                </div>
                <div class="mb-3">
                    <label class="font-weight-bold small text-muted text-uppercase">Histórico Cirúrgico / Comorbidades:</label>
                    <p class="mb-0 text-dark">{{ $paciente->historico_cirurgico ?: 'Nenhum histórico cirúrgico informado.' }}</p>
                </div>
                @if($paciente->observacoes)
                    <div>
                        <label class="font-weight-bold small text-muted text-uppercase">Observações Administrativas:</label>
                        <p class="mb-0 text-dark small">{{ $paciente->observacoes }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-7">
        <div class="card-box">
            <div class="card-box-header">
                <h2 class="card-box-title">Consultas Recentes</h2>
                <a href="{{ route('pacientes.historico', $paciente->id) }}" class="btn-andrik-outline">Linha do Tempo</a>
            </div>
            <div class="table-responsive">
                <table class="table-andrik">
                    <thead>
                        <tr>
                            <th>Data / Hora</th>
                            <th>Médico / Especialidade</th>
                            <th>Status</th>
                            <th style="text-align: right;">Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($paciente->consultas->take(5) as $c)
                            <tr>
                                <td>
                                    <strong>{{ $c->data_hora->format('d/m/Y') }}</strong>
                                    <small class="text-muted d-block">{{ $c->data_hora->format('H:i') }}</small>
                                </td>
                                <td>
                                    <div>{{ $c->medico->name }}</div>
                                    <small class="text-muted">{{ $c->especialidade }}</small>
                                </td>
                                <td>
                                    <span class="badge-status {{ $c->status }}">{{ $c->status_label }}</span>
                                </td>
                                <td style="text-align: right;">
                                    <a href="{{ route('consultas.show', $c->id) }}" class="btn-andrik-outline">Ver</a>
                                    @if($c->prontuario)
                                        <a href="{{ route('prontuarios.show', $c->prontuario->id) }}" class="btn-andrik-outline ml-1">Prontuário</a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-3">Nenhuma consulta registrada.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card-box">
            <div class="card-box-header">
                <h2 class="card-box-title">Prontuários Médicos</h2>
                @if(Auth::user()->isMedico() || Auth::user()->isAdmin())
                    <a href="{{ route('prontuarios.create', ['paciente_id' => $paciente->id]) }}" class="btn-andrik-success">
                        Novo Prontuário
                    </a>
                @endif
            </div>
            <div class="list-group list-group-flush">
                @forelse($paciente->prontuarios->take(4) as $pr)
                    <div class="list-group-item p-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <strong>Atendimento em {{ $pr->data_registro->format('d/m/Y H:i') }}</strong>
                            <small class="text-muted">{{ $pr->medico->name }} ({{ $pr->medico->crm }})</small>
                        </div>
                        <div class="mb-1 text-dark font-weight-bold">
                            Diagnóstico: {{ $pr->diagnostico }}
                            @if($pr->cid10)
                                <span class="badge badge-secondary ml-1">{{ $pr->cid10 }}</span>
                            @endif
                        </div>
                        <div class="small text-muted mb-2">
                            <strong>Queixa:</strong> {{ $pr->queixa_principal }}
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                PA: {{ $pr->pressao_arterial ?? 'N/I' }} | Peso: {{ $pr->peso ? $pr->peso . ' kg' : 'N/I' }}
                            </small>
                            <a href="{{ route('prontuarios.show', $pr->id) }}" class="btn-andrik-outline">
                                Abrir Prontuário
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-muted py-4">Nenhum prontuário registrado.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
