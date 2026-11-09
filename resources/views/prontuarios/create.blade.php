@extends('layouts.app')

@section('page_title', 'Registro de Atendimento Médico e Prontuário')

@section('content')
<div class="card-box">
    <div class="card-box-body">
        <div class="d-flex flex-wrap justify-content-between align-items-center">
            <div>
                <h5 class="font-weight-bold mb-1 text-dark">{{ $paciente->nome }}</h5>
                <div class="text-muted small">
                    <span>CPF: <strong>{{ $paciente->cpf }}</strong></span> |
                    <span>Nascimento: {{ $paciente->data_nascimento->format('d/m/Y') }} ({{ $paciente->idade }} anos)</span> |
                    <span>Tipo Sanguíneo: <strong>{{ $paciente->tipo_sanguineo ?? 'Não informado' }}</strong></span>
                </div>
            </div>
            <div>
                <a href="{{ route('pacientes.historico', $paciente->id) }}" class="btn-andrik-outline mr-1" target="_blank">
                    Histórico Prévio
                </a>
                <a href="{{ route('consultas.index') }}" class="btn-andrik-outline">
                    Cancelar
                </a>
            </div>
        </div>

        @if($paciente->alergias)
            <div class="alert-allergy mt-3 py-2">
                <strong>ATENÇÃO MÉDICA - ALERGIAS:</strong> {{ $paciente->alergias }}
            </div>
        @endif

        @if($paciente->medicamentos_uso_continuo)
            <div class="mt-2 small text-muted">
                <strong>Uso Contínuo Declarado:</strong> {{ $paciente->medicamentos_uso_continuo }}
            </div>
        @endif
    </div>
</div>

<form action="{{ route('prontuarios.store') }}" method="POST" novalidate data-validate="true">
    @csrf
    <input type="hidden" name="paciente_id" value="{{ $paciente->id }}">
    @if($consulta)
        <input type="hidden" name="consulta_id" value="{{ $consulta->id }}">
    @endif

    <div class="row">
        <div class="col-lg-8">
            <div class="card-box">
                <div class="card-box-header">
                    <h2 class="card-box-title">1. Anamnese Clínica</h2>
                </div>
                <div class="card-box-body">
                    <div class="form-group">
                        <label class="small font-weight-bold">QUEIXA PRINCIPAL</label>
                        <textarea name="queixa_principal" class="form-control @error('queixa_principal') is-invalid @enderror" rows="3" required minlength="5">{{ old('queixa_principal', $consulta ? $consulta->motivo : '') }}</textarea>
                        @error('queixa_principal') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group mb-0">
                        <label class="small font-weight-bold">HISTÓRIA DA DOENÇA ATUAL</label>
                        <textarea name="historia_doenca_atual" class="form-control" rows="4">{{ old('historia_doenca_atual') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="card-box">
                <div class="card-box-header">
                    <h2 class="card-box-title">2. Hipótese Diagnóstica e CID-10</h2>
                </div>
                <div class="card-box-body">
                    <div class="form-row">
                        <div class="form-group col-md-8 mb-0">
                            <label class="small font-weight-bold">DIAGNÓSTICO / IMPRESSÃO CLÍNICA</label>
                            <input type="text" name="diagnostico" class="form-control @error('diagnostico') is-invalid @enderror" value="{{ old('diagnostico') }}" required minlength="3">
                            @error('diagnostico') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-group col-md-4 mb-0">
                            <label class="small font-weight-bold">CID-10</label>
                            <input type="text" name="cid10" class="form-control @error('cid10') is-invalid @enderror" value="{{ old('cid10') }}" placeholder="Ex: I10, J03.9">
                            @error('cid10') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-box">
                <div class="card-box-header">
                    <h2 class="card-box-title">3. Prescrição Farmacológica (Receituário)</h2>
                </div>
                <div class="card-box-body">
                    <div class="form-group mb-0">
                        <label class="small font-weight-bold text-muted">MEDICAMENTOS, POSOLOGIA E DURAÇÃO DO TRATAMENTO</label>
                        <textarea name="prescricao_medica" class="form-control" rows="5">{{ old('prescricao_medica') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="card-box">
                <div class="card-box-header">
                    <h2 class="card-box-title">4. Exames Complementares e Conduta</h2>
                </div>
                <div class="card-box-body">
                    <div class="form-group">
                        <label class="small font-weight-bold">SOLICITAÇÃO DE EXAMES</label>
                        <textarea name="exames_solicitados" class="form-control" rows="3">{{ old('exames_solicitados') }}</textarea>
                    </div>

                    <div class="form-group mb-0">
                        <label class="small font-weight-bold">CONDUTA CLÍNICA E RETORNO</label>
                        <textarea name="conduta_plano" class="form-control" rows="3">{{ old('conduta_plano') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card-box">
                <div class="card-box-header">
                    <h2 class="card-box-title">Sinais Vitais</h2>
                </div>
                <div class="card-box-body">
                    <div class="form-group">
                        <label class="small font-weight-bold">PRESSÃO ARTERIAL</label>
                        <input type="text" name="pressao_arterial" class="form-control @error('pressao_arterial') is-invalid @enderror" value="{{ old('pressao_arterial', '120/80') }}" placeholder="120/80">
                        @error('pressao_arterial') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group">
                        <label class="small font-weight-bold">FREQUÊNCIA CARDÍACA</label>
                        <input type="number" min="30" max="250" name="frequencia_cardiaca" class="form-control @error('frequencia_cardiaca') is-invalid @enderror" value="{{ old('frequencia_cardiaca', '75') }}" placeholder="Ex: 75">
                        @error('frequencia_cardiaca') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group">
                        <label class="small font-weight-bold">TEMPERATURA</label>
                        <input type="number" step="0.1" min="30" max="45" name="temperatura" class="form-control @error('temperatura') is-invalid @enderror" value="{{ old('temperatura', '36.5') }}" placeholder="Ex: 36.5">
                        @error('temperatura') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-row">
                        <div class="form-group col-6">
                            <label class="small font-weight-bold">PESO</label>
                            <input type="number" step="0.1" min="0.5" max="400" id="peso" name="peso" class="form-control @error('peso') is-invalid @enderror" value="{{ old('peso') }}" placeholder="70.5">
                            @error('peso') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-group col-6">
                            <label class="small font-weight-bold">ALTURA</label>
                            <input type="number" step="0.01" min="0.3" max="2.5" id="altura" name="altura" class="form-control @error('altura') is-invalid @enderror" value="{{ old('altura') }}" placeholder="1.75">
                            @error('altura') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="p-2 bg-light border text-center mb-3">
                        <small class="text-muted d-block font-weight-bold">ÍNDICE DE MASSA CORPORAL (IMC)</small>
                        <span id="imc-display" class="font-weight-bold text-dark" style="font-size: 1.15rem;">--</span>
                        <small id="imc-desc" class="d-block text-muted">Informe peso e altura</small>
                    </div>

                    <div class="form-group mb-0">
                        <label class="small font-weight-bold">EXAME FÍSICO DIRIGIDO</label>
                        <textarea name="exame_fisico" class="form-control" rows="5">{{ old('exame_fisico') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="card-box">
                <div class="card-box-body">
                    <button type="submit" class="btn-andrik-success btn-block justify-content-center py-2 mb-2">
                        Salvar e Finalizar Atendimento
                    </button>
                    <a href="{{ route('consultas.index') }}" class="btn-andrik-outline btn-block justify-content-center">
                        Cancelar
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
    function calcularIMC() {
        var peso = parseFloat($('#peso').val());
        var altura = parseFloat($('#altura').val());
        if (peso > 0 && altura > 0) {
            var imc = (peso / (altura * altura)).toFixed(1);
            $('#imc-display').text(imc);
            var desc = 'Normal';
            if (imc < 18.5) desc = 'Abaixo do peso';
            else if (imc < 25) desc = 'Peso normal';
            else if (imc < 30) desc = 'Sobrepeso';
            else if (imc < 35) desc = 'Obesidade Grau I';
            else if (imc < 40) desc = 'Obesidade Grau II';
            else desc = 'Obesidade Grau III';
            $('#imc-desc').text(desc);
        } else {
            $('#imc-display').text('--');
            $('#imc-desc').text('Informe peso e altura');
        }
    }
    $('#peso, #altura').on('input change blur', calcularIMC);
    calcularIMC();
</script>
@endpush
