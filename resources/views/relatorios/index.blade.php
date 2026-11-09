@extends('layouts.app')

@section('page_title', 'Relatórios de Atendimento')

@section('content')
<div class="reports-page">
    <div class="print-header d-none d-print-block mb-3">
        <div style="border-bottom: 2px solid #0f172a; padding-bottom: 10px;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <div>
                    <h2 style="font-size: 16pt; font-weight: 700; margin: 0;">CLÍNICA ANDRIK</h2>
                    <div style="font-size: 9pt; color: #475569; margin-top: 2px;">
                        CNPJ: 45.128.930/0001-82 | Av. Rio Grande do Sul, 1558 - Estados, João Pessoa - PB | Tel: (83) 3224-5500
                    </div>
                </div>
                <div style="text-align: right; font-size: 8.5pt; color: #475569;">
                    <div><strong>Relatório de Atendimentos</strong></div>
                    <div>Emissão: {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}</div>
                    <div>Operador: {{ Auth::user()->name }}</div>
                </div>
            </div>
            <div style="background: #f8fafc; border: 1px solid #cbd5e1; padding: 6px 12px; margin-top: 10px; font-size: 9pt;">
                <strong>Parâmetros:</strong>
                Período: <strong>{{ \Carbon\Carbon::parse($dataInicio)->format('d/m/Y') }}</strong> até <strong>{{ \Carbon\Carbon::parse($dataFim)->format('d/m/Y') }}</strong> |
                Médico: <strong>{{ $medicoSelecionado ? $medicoSelecionado->name . ' (' . $medicoSelecionado->crm . ')' : 'Todos' }}</strong> |
                Paciente: <strong>{{ $pacienteSelecionado ? $pacienteSelecionado->nome . ' (' . $pacienteSelecionado->cpf . ')' : 'Todos' }}</strong> |
                Status: <strong>{{ $status ? ucfirst(str_replace('_', ' ', $status)) : 'Todos' }}</strong>
            </div>
        </div>
    </div>

    <div class="card-box no-print">
        <div class="card-box-header">
            <h2 class="card-box-title">Filtros do Relatório de Atendimentos</h2>
            <button type="button" onclick="window.print()" class="btn-andrik-outline">
                Imprimir / PDF
            </button>
        </div>

        <div class="card-box-body">
            <form action="{{ route('relatorios.index') }}" method="GET">
                <div class="form-row">
                    <div class="form-group col-lg-3 col-md-6">
                        <label for="medico_id" class="small font-weight-bold text-muted">MÉDICO RESPONSÁVEL</label>
                        <select name="medico_id" id="medico_id" class="form-control">
                            <option value="">Todos os Médicos</option>
                            @foreach($medicos as $m)
                                <option value="{{ $m->id }}" {{ $medicoId == $m->id ? 'selected' : '' }}>
                                    {{ $m->name }} ({{ $m->crm ?? 'N/D' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group col-lg-3 col-md-6">
                        <label for="paciente_id" class="small font-weight-bold text-muted">PACIENTE ESPECÍFICO</label>
                        <select name="paciente_id" id="paciente_id" class="form-control">
                            <option value="">Todos os Pacientes</option>
                            @foreach($pacientes as $p)
                                <option value="{{ $p->id }}" {{ $pacienteId == $p->id ? 'selected' : '' }}>
                                    {{ $p->nome }} (CPF: {{ $p->cpf }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group col-lg-2 col-md-3 col-6">
                        <label for="data_inicio" class="small font-weight-bold text-muted">DATA INICIAL</label>
                        <input type="date" name="data_inicio" id="data_inicio" class="form-control" value="{{ $dataInicio }}">
                    </div>

                    <div class="form-group col-lg-2 col-md-3 col-6">
                        <label for="data_fim" class="small font-weight-bold text-muted">DATA FINAL</label>
                        <input type="date" name="data_fim" id="data_fim" class="form-control" value="{{ $dataFim }}">
                    </div>

                    <div class="form-group col-lg-2 col-md-3 col-6">
                        <label for="status" class="small font-weight-bold text-muted">STATUS</label>
                        <select name="status" id="status" class="form-control">
                            <option value="">Todos</option>
                            <option value="finalizada" {{ $status == 'finalizada' ? 'selected' : '' }}>Finalizada</option>
                            <option value="em_atendimento" {{ $status == 'em_atendimento' ? 'selected' : '' }}>Em Atendimento</option>
                            <option value="aguardando" {{ $status == 'aguardando' ? 'selected' : '' }}>Aguardando</option>
                            <option value="confirmada" {{ $status == 'confirmada' ? 'selected' : '' }}>Confirmada</option>
                            <option value="agendada" {{ $status == 'agendada' ? 'selected' : '' }}>Agendada</option>
                            <option value="cancelada" {{ $status == 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                        </select>
                    </div>

                    <div class="form-group col-lg-3 col-md-3 col-6 mb-0">
                        <label for="convenio" class="small font-weight-bold text-muted">CONVÊNIO</label>
                        <select name="convenio" id="convenio" class="form-control">
                            <option value="">Todos</option>
                            @foreach($convenios as $conv)
                                <option value="{{ $conv }}" {{ $convenio == $conv ? 'selected' : '' }}>{{ $conv }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group col-lg-4 col-md-6 mb-0 d-flex align-items-end">
                        <button type="submit" class="btn-andrik-primary mr-2 flex-grow-1 justify-content-center">
                            Aplicar Filtros
                        </button>
                        <a href="{{ route('relatorios.index') }}" class="btn-andrik-outline">
                            Limpar
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if($medicoSelecionado)
        <div class="alert-medico-focus mb-3">
            <div class="d-flex align-items-center justify-content-between flex-wrap">
                <div>
                    <div class="medico-focus-badge">Médico em Foco</div>
                    <h4 class="medico-focus-name mb-0">{{ $medicoSelecionado->name }}</h4>
                    <div class="medico-focus-sub">
                        CRM: <strong>{{ $medicoSelecionado->crm ?? 'Não informado' }}</strong> |
                        Especialidade: <strong>{{ $medicoSelecionado->especialidade ?? 'Clínica Médica' }}</strong> |
                        E-mail: {{ $medicoSelecionado->email }}
                    </div>
                </div>
                <div>
                    <span class="badge-status finalizada">{{ $consultas->count() }} Atendimentos no período</span>
                </div>
            </div>
        </div>
    @elseif($pacienteSelecionado)
        <div class="alert-paciente-focus mb-3">
            <div class="d-flex align-items-center justify-content-between flex-wrap">
                <div>
                    <div class="paciente-focus-badge">Paciente / Cliente em Foco</div>
                    <h4 class="paciente-focus-name mb-0">{{ $pacienteSelecionado->nome }}</h4>
                    <div class="paciente-focus-sub">
                        CPF: <strong>{{ $pacienteSelecionado->cpf }}</strong> |
                        Idade: <strong>{{ $pacienteSelecionado->idade ? $pacienteSelecionado->idade . ' anos' : 'N/D' }}</strong> |
                        Convênio: <strong>{{ $pacienteSelecionado->convenio ?? 'Particular' }}</strong>
                    </div>
                </div>
                <div>
                    <a href="{{ route('pacientes.historico', $pacienteSelecionado->id) }}" class="btn-andrik-outline mr-1">Linha do Tempo</a>
                    <a href="{{ route('pacientes.show', $pacienteSelecionado->id) }}" class="btn-andrik-primary">Ficha Cadastral</a>
                </div>
            </div>
        </div>
    @endif

    <div class="metrics-grid">
        <div class="metric-card">
            <div>
                <div class="metric-number">{{ $totalAtendimentos }}</div>
                <div class="metric-title">Total de Atendimentos</div>
            </div>
        </div>

        <div class="metric-card" style="border-left-color: #15803d;">
            <div>
                <div class="metric-number">{{ $totalFinalizadas }}</div>
                <div class="metric-title">Realizadas ({{ $taxaConclusao }}%)</div>
            </div>
        </div>

        <div class="metric-card" style="border-left-color: #b45309;">
            <div>
                <div class="metric-number">{{ $totalAgendadas }}</div>
                <div class="metric-title">Em Espera / Agendadas</div>
            </div>
        </div>

        <div class="metric-card" style="border-left-color: #0284c7;">
            <div>
                <div class="metric-number">R$ {{ number_format($valorTotal, 2, ',', '.') }}</div>
                <div class="metric-title">Volume Faturado</div>
            </div>
        </div>
    </div>

    <div class="card-box">
        <div class="card-box-header">
            <h2 class="card-box-title">Registros de Atendimento ({{ $consultas->count() }})</h2>
        </div>

        <div class="table-responsive">
            <table class="table-andrik">
                <thead>
                    <tr>
                        <th style="width: 135px;">Data / Horário</th>
                        <th>Paciente</th>
                        <th>Médico Responsável</th>
                        <th>Convênio / Valor</th>
                        <th>Diagnóstico / CID-10</th>
                        <th>Status</th>
                        <th style="text-align: right;" class="no-print">Ações</th>
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
                                <small class="text-muted">CPF: {{ $c->paciente->cpf }}</small>
                            </td>
                            <td>
                                <div>{{ $c->medico->name }}</div>
                                <small class="text-muted">{{ $c->medico->crm ?? 'N/D' }} - {{ $c->medico->especialidade ?? 'Clínico' }}</small>
                            </td>
                            <td>
                                <div>{{ $c->convenio ?? 'Particular' }}</div>
                                <small class="text-success font-weight-bold">R$ {{ number_format($c->valor, 2, ',', '.') }}</small>
                            </td>
                            <td>
                                @if($c->prontuario)
                                    <div>{{ $c->prontuario->diagnostico ?: 'Registrado' }}</div>
                                    @if($c->prontuario->cid10)
                                        <span class="badge badge-secondary">CID-10: {{ $c->prontuario->cid10 }}</span>
                                    @endif
                                @else
                                    <span class="text-muted small">Sem prontuário</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge-status {{ $c->status }}">
                                    {{ $c->status_label }}
                                </span>
                            </td>
                            <td style="text-align: right;" class="no-print">
                                <a href="{{ route('consultas.show', $c->id) }}" class="btn-andrik-outline">Detalhes</a>
                                @if($c->prontuario)
                                    <a href="{{ route('prontuarios.show', $c->prontuario->id) }}" class="btn-andrik-outline ml-1">Prontuário</a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                Nenhum atendimento encontrado para os filtros selecionados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
