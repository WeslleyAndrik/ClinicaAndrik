@extends('layouts.app')

@section('page_title', 'Prontuário Médico')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3 no-print">
    <div>
        <a href="{{ route('pacientes.historico', $prontuario->paciente_id) }}" class="btn-andrik-outline mr-1">
            Linha do Tempo
        </a>
        <a href="{{ route('pacientes.show', $prontuario->paciente_id) }}" class="btn-andrik-outline">
            Ficha Cadastral
        </a>
    </div>
    <div>
        @if(Auth::user()->isAdmin() || Auth::id() === $prontuario->medico_id)
            <a href="{{ route('prontuarios.edit', $prontuario->id) }}" class="btn-andrik-outline mr-1">
                Editar Prontuário
            </a>
        @endif
        <button onclick="window.print()" class="btn-andrik-primary">
            Imprimir Prontuário / Receituário
        </button>
    </div>
</div>

<div class="card-box p-4">
    <div class="print-header mb-4 text-center">
        <h3 class="font-weight-bold mb-1">CLÍNICA ANDRIK</h3>
        <div class="small text-muted">
            Av. Rio Grande do Sul, 1558 - Estados, João Pessoa - PB, 58030-021 | Tel: (83) 3224-5500 | CNPJ: 45.128.930/0001-82
        </div>
        <div class="small text-muted font-weight-bold">
            Direção Técnica: Dr. Andrik Cardoso - CRM/PB 8412
        </div>
        <hr class="my-3">
    </div>

    <div class="p-3 bg-light border mb-4">
        <div class="row">
            <div class="col-md-7">
                <span class="text-muted small text-uppercase font-weight-bold">Paciente:</span>
                <h5 class="font-weight-bold text-dark mb-1">{{ $prontuario->paciente->nome }}</h5>
                <div class="small text-muted">
                    CPF: <strong>{{ $prontuario->paciente->cpf }}</strong> |
                    Nascimento: {{ $prontuario->paciente->data_nascimento->format('d/m/Y') }} ({{ $prontuario->paciente->idade }} anos) |
                    Sexo: {{ $prontuario->paciente->sexo === 'M' ? 'Masculino' : ($prontuario->paciente->sexo === 'F' ? 'Feminino' : 'Outro') }}
                </div>
            </div>
            <div class="col-md-5 text-md-right mt-2 mt-md-0">
                <div class="small text-muted">Data do Atendimento:</div>
                <div class="font-weight-bold text-dark">
                    {{ $prontuario->data_registro->format('d/m/Y \à\s H:i') }}
                </div>
                <div class="small text-muted">
                    Médico: <strong>{{ $prontuario->medico->name }}</strong> ({{ $prontuario->medico->crm }})
                </div>
            </div>
        </div>

        @if($prontuario->paciente->alergias)
            <div class="alert-allergy mt-3 py-2">
                <strong>ALERGIAS REGISTRADAS:</strong> {{ $prontuario->paciente->alergias }}
            </div>
        @endif
    </div>

    <div class="mb-4">
        <h6 class="font-weight-bold text-dark border-bottom pb-2 mb-3">Sinais Vitais e Medidas Antropométricas</h6>
        <div class="row">
            <div class="col-md-2 col-4 mb-2">
                <div class="p-2 border text-center bg-light">
                    <small class="text-muted d-block">Pressão (PA)</small>
                    <strong>{{ $prontuario->pressao_arterial ?: 'Não aferida' }}</strong>
                </div>
            </div>
            <div class="col-md-2 col-4 mb-2">
                <div class="p-2 border text-center bg-light">
                    <small class="text-muted d-block">Freq. Cardíaca</small>
                    <strong>{{ $prontuario->frequencia_cardiaca ? $prontuario->frequencia_cardiaca . ' bpm' : '--' }}</strong>
                </div>
            </div>
            <div class="col-md-2 col-4 mb-2">
                <div class="p-2 border text-center bg-light">
                    <small class="text-muted d-block">Temperatura</small>
                    <strong>{{ $prontuario->temperatura ? $prontuario->temperatura . ' °C' : '--' }}</strong>
                </div>
            </div>
            <div class="col-md-2 col-4 mb-2">
                <div class="p-2 border text-center bg-light">
                    <small class="text-muted d-block">Peso</small>
                    <strong>{{ $prontuario->peso ? $prontuario->peso . ' kg' : '--' }}</strong>
                </div>
            </div>
            <div class="col-md-2 col-4 mb-2">
                <div class="p-2 border text-center bg-light">
                    <small class="text-muted d-block">Altura</small>
                    <strong>{{ $prontuario->altura ? $prontuario->altura . ' m' : '--' }}</strong>
                </div>
            </div>
            <div class="col-md-2 col-4 mb-2">
                <div class="p-2 border text-center bg-light">
                    <small class="text-muted d-block">IMC</small>
                    <strong>{{ $prontuario->imc ? $prontuario->imc . ' (' . $prontuario->imc_classificacao . ')' : '--' }}</strong>
                </div>
            </div>
        </div>
    </div>

    <div class="mb-4">
        <h6 class="font-weight-bold text-dark border-bottom pb-2 mb-3">Anamnese Clínica</h6>
        <div class="mb-3">
            <strong class="text-muted small text-uppercase">Queixa Principal:</strong>
            <p class="mb-1 text-dark">{{ $prontuario->queixa_principal }}</p>
        </div>
        @if($prontuario->historia_doenca_atual)
            <div class="mb-3">
                <strong class="text-muted small text-uppercase">História da Doença Atual (HDA):</strong>
                <p class="mb-1 text-dark">{{ $prontuario->historia_doenca_atual }}</p>
            </div>
        @endif
        @if($prontuario->exame_fisico)
            <div class="mb-3">
                <strong class="text-muted small text-uppercase">Exame Físico:</strong>
                <p class="mb-1 text-dark">{{ $prontuario->exame_fisico }}</p>
            </div>
        @endif
    </div>

    <div class="mb-4 p-3 bg-light border-left border-primary">
        <strong class="text-muted small text-uppercase d-block mb-1">Hipótese Diagnóstica / Impressão Clínica:</strong>
        <span class="font-weight-bold text-dark" style="font-size: 1.05rem;">
            {{ $prontuario->diagnostico }}
        </span>
        @if($prontuario->cid10)
            <span class="badge badge-secondary ml-2 px-2 py-1">
                CID-10: {{ $prontuario->cid10 }}
            </span>
        @endif
    </div>

    @if($prontuario->prescricao_medica)
        <div class="mb-4 p-3 border bg-white">
            <h6 class="font-weight-bold text-dark border-bottom pb-2 mb-2">Prescrição Farmacológica (Receituário)</h6>
            <pre class="p-2 mb-0 text-dark" style="font-family: inherit; white-space: pre-wrap; line-height: 1.5;">{{ $prontuario->prescricao_medica }}</pre>
        </div>
    @endif

    @if($prontuario->exames_solicitados)
        <div class="mb-4 p-3 border bg-white">
            <h6 class="font-weight-bold text-dark border-bottom pb-2 mb-2">Exames Complementares Solicitados</h6>
            <pre class="p-2 mb-0 text-dark" style="font-family: inherit; white-space: pre-wrap; line-height: 1.5;">{{ $prontuario->exames_solicitados }}</pre>
        </div>
    @endif

    @if($prontuario->conduta_plano)
        <div class="mb-4">
            <h6 class="font-weight-bold text-dark border-bottom pb-2 mb-2">Conduta Médica e Orientações</h6>
            <p class="mb-0 text-dark">{{ $prontuario->conduta_plano }}</p>
        </div>
    @endif

    <div class="mt-5 pt-4 text-center" style="max-width: 380px; margin-left: auto; margin-right: auto;">
        <div class="border-top border-dark pt-2">
            <div class="font-weight-bold text-dark">{{ $prontuario->medico->name }}</div>
            <div class="text-muted small">{{ $prontuario->medico->especialidade }}</div>
            <div class="text-muted small">{{ $prontuario->medico->crm }}</div>
        </div>
    </div>
</div>
@endsection
