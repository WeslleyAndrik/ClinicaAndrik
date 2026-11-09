@extends('layouts.app')

@section('page_title', 'Agendar Nova Consulta')

@section('content')
<div class="card-box">
    <div class="card-box-header">
        <h2 class="card-box-title">Formulário de Agendamento</h2>
        <a href="{{ route('consultas.index') }}" class="btn-andrik-outline">Voltar à Agenda</a>
    </div>

    <form action="{{ route('consultas.store') }}" method="POST" novalidate data-validate="true">
        @csrf
        <div class="card-box-body">
            <h6 class="font-weight-bold text-dark border-bottom pb-2 mb-3">1. Seleção do Paciente</h6>
            <div class="form-row">
                <div class="form-group col-md-8">
                    <label class="small font-weight-bold">PACIENTE</label>
                    <select name="paciente_id" id="paciente_id" class="form-control @error('paciente_id') is-invalid @enderror" required>
                        <option value="">Selecione um paciente cadastrado...</option>
                        @foreach($pacientes as $pac)
                            <option value="{{ $pac->id }}"
                                    data-convenio="{{ $pac->convenio }}"
                                    {{ (old('paciente_id', $pacienteSelecionado ? $pacienteSelecionado->id : '') == $pac->id) ? 'selected' : '' }}>
                                {{ $pac->nome }} - CPF: {{ $pac->cpf }} ({{ $pac->convenio ?? 'Particular' }})
                            </option>
                        @endforeach
                    </select>
                    @error('paciente_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-4 d-flex align-items-end mb-3">
                    <a href="{{ route('pacientes.create') }}" class="btn-andrik-outline btn-block justify-content-center">
                        Cadastrar Novo Paciente
                    </a>
                </div>
            </div>

            <h6 class="font-weight-bold text-dark border-bottom pb-2 mb-3 mt-3">2. Profissional e Especialidade</h6>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label class="small font-weight-bold">MÉDICO RESPONSÁVEL</label>
                    <select name="medico_id" id="medico_id" class="form-control @error('medico_id') is-invalid @enderror" required>
                        <option value="">Selecione o profissional médico...</option>
                        @foreach($medicos as $m)
                            <option value="{{ $m->id }}"
                                    data-especialidade="{{ $m->especialidade }}"
                                    {{ old('medico_id') == $m->id ? 'selected' : '' }}>
                                {{ $m->name }} - {{ $m->especialidade }} ({{ $m->crm }})
                            </option>
                        @endforeach
                    </select>
                    @error('medico_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>
                <div class="form-group col-md-3">
                    <label class="small font-weight-bold">ESPECIALIDADE</label>
                    <input type="text" name="especialidade" id="especialidade" class="form-control @error('especialidade') is-invalid @enderror" value="{{ old('especialidade') }}" required minlength="3" maxlength="100" readonly tabindex="-1" style="background-color: #f1f5f9; cursor: not-allowed;" placeholder="Selecione o médico">
                    @error('especialidade') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>
                <div class="form-group col-md-3">
                    <label class="small font-weight-bold">TIPO DE ATENDIMENTO</label>
                    <select name="tipo_atendimento" class="form-control @error('tipo_atendimento') is-invalid @enderror" required>
                        <option value="primeira_consulta" {{ old('tipo_atendimento') === 'primeira_consulta' ? 'selected' : '' }}>Primeira Consulta</option>
                        <option value="retorno" {{ old('tipo_atendimento') === 'retorno' ? 'selected' : '' }}>Retorno</option>
                        <option value="rotina" {{ old('tipo_atendimento', 'rotina') === 'rotina' ? 'selected' : '' }}>Rotina / Acompanhamento</option>
                        <option value="urgencia" {{ old('tipo_atendimento') === 'urgencia' ? 'selected' : '' }}>Urgência / Encaixe</option>
                    </select>
                    @error('tipo_atendimento') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>
            </div>

            <h6 class="font-weight-bold text-dark border-bottom pb-2 mb-3 mt-3">3. Horário e Pagamento</h6>
            <div class="form-row">
                <div class="form-group col-md-3">
                    <label class="small font-weight-bold">DATA DA CONSULTA</label>
                    <input type="date" name="data" class="form-control @error('data') is-invalid @enderror" value="{{ old('data', date('Y-m-d')) }}" min="{{ date('Y-m-d') }}" required>
                    @error('data') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>
                <div class="form-group col-md-3">
                    <label class="small font-weight-bold">HORÁRIO</label>
                    <input type="time" name="hora" class="form-control @error('hora') is-invalid @enderror" value="{{ old('hora', '09:00') }}" required>
                    @error('hora') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>
                <div class="form-group col-md-2">
                    <label class="small font-weight-bold">DURAÇÃO</label>
                    <select name="duracao_minutos" class="form-control @error('duracao_minutos') is-invalid @enderror" required>
                        <option value="15" {{ old('duracao_minutos') == 15 ? 'selected' : '' }}>15 minutos</option>
                        <option value="30" {{ old('duracao_minutos', 30) == 30 ? 'selected' : '' }}>30 minutos</option>
                        <option value="45" {{ old('duracao_minutos') == 45 ? 'selected' : '' }}>45 minutos</option>
                        <option value="60" {{ old('duracao_minutos') == 60 ? 'selected' : '' }}>60 minutos</option>
                    </select>
                    @error('duracao_minutos') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>
                <div class="form-group col-md-2">
                    <label class="small font-weight-bold">CONVÊNIO</label>
                    <select name="convenio" id="convenio" class="form-control select-convenio @error('convenio') is-invalid @enderror" required>
                        @foreach(\App\Paciente::listarConvenios() as $convItem)
                            <option value="{{ $convItem }}" {{ old('convenio', $pacienteSelecionado && $pacienteSelecionado->convenio ? $pacienteSelecionado->convenio : 'Particular') === $convItem ? 'selected' : '' }}>{{ $convItem }}</option>
                        @endforeach
                        <option value="__novo__">+ Cadastrar novo convênio...</option>
                    </select>
                    <div class="novo-convenio-box d-none mt-2">
                        <div class="d-flex">
                            <input type="text" class="form-control form-control-sm input-novo-convenio mr-1" maxlength="50" placeholder="Novo convênio">
                            <button type="button" class="btn-andrik-primary btn-salvar-convenio px-2 py-1">Salvar</button>
                            <button type="button" class="btn-andrik-outline btn-cancelar-convenio px-2 py-1 ml-1">X</button>
                        </div>
                    </div>
                    @error('convenio') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>
                <div class="form-group col-md-2">
                    <label class="small font-weight-bold">VALOR</label>
                    <input type="number" step="0.01" min="0" max="99999.99" name="valor" class="form-control @error('valor') is-invalid @enderror" value="{{ old('valor', '250.00') }}" placeholder="0.00">
                    @error('valor') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label class="small font-weight-bold">MOTIVO PRINCIPAL RELATADO PELO PACIENTE</label>
                    <textarea name="motivo" class="form-control" rows="3">{{ old('motivo') }}</textarea>
                </div>
                <div class="form-group col-md-6">
                    <label class="small font-weight-bold">OBSERVAÇÕES ADMINISTRATIVAS</label>
                    <textarea name="observacoes" class="form-control" rows="3">{{ old('observacoes') }}</textarea>
                </div>
            </div>
        </div>

        <div class="card-box-header bg-light justify-content-end">
            <a href="{{ route('consultas.index') }}" class="btn-andrik-outline mr-2">Cancelar</a>
            <button type="submit" class="btn-andrik-primary">Confirmar Agendamento</button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    function atualizarEspecialidadeMedico() {
        var esp = $('#medico_id').find(':selected').data('especialidade');
        $('#especialidade').val(esp || '').prop('readonly', true);
        if (typeof definirErroCampo === 'function' && esp) {
            definirErroCampo(document.getElementById('especialidade'), '');
        }
    }
    $('#medico_id').on('change', atualizarEspecialidadeMedico);
    if ($('#medico_id').val()) {
        atualizarEspecialidadeMedico();
    }
    $('#paciente_id').on('change', function() {
        var conv = $(this).find(':selected').data('convenio');
        if (conv) $('#convenio').val(conv);
    });
</script>
@endpush
