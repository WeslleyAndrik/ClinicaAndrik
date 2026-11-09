@extends('layouts.app')

@section('page_title', 'Editar Consulta')

@section('content')
<div class="card-box">
    <div class="card-box-header">
        <h2 class="card-box-title">Editando Consulta #{{ $consulta->id }} - {{ $consulta->paciente->nome }}</h2>
        <div>
            <a href="{{ route('consultas.show', $consulta->id) }}" class="btn-andrik-outline mr-1">Ver Detalhes</a>
            <a href="{{ route('consultas.index') }}" class="btn-andrik-outline">Voltar</a>
        </div>
    </div>

    <form action="{{ route('consultas.update', $consulta->id) }}" method="POST" novalidate data-validate="true">
        @csrf
        @method('PUT')
        <div class="card-box-body">
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label class="small font-weight-bold">PACIENTE</label>
                    <input type="text" class="form-control" value="{{ $consulta->paciente->nome }} (CPF: {{ $consulta->paciente->cpf }})" readonly disabled>
                </div>
                <div class="form-group col-md-6">
                    <label class="small font-weight-bold">MÉDICO RESPONSÁVEL</label>
                    <select name="medico_id" id="medico_id" class="form-control @error('medico_id') is-invalid @enderror" required>
                        @foreach($medicos as $m)
                            <option value="{{ $m->id }}" data-especialidade="{{ $m->especialidade }}" {{ old('medico_id', $consulta->medico_id) == $m->id ? 'selected' : '' }}>
                                {{ $m->name }} - {{ $m->especialidade }} ({{ $m->crm }})
                            </option>
                        @endforeach
                    </select>
                    @error('medico_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-3">
                    <label class="small font-weight-bold">ESPECIALIDADE</label>
                    <input type="text" name="especialidade" id="especialidade" class="form-control @error('especialidade') is-invalid @enderror" value="{{ old('especialidade', $consulta->especialidade) }}" required minlength="3" maxlength="100" readonly tabindex="-1" style="background-color: #f1f5f9; cursor: not-allowed;">
                    @error('especialidade') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>
                <div class="form-group col-md-3">
                    <label class="small font-weight-bold">TIPO DE ATENDIMENTO</label>
                    <select name="tipo_atendimento" class="form-control @error('tipo_atendimento') is-invalid @enderror" required>
                        <option value="primeira_consulta" {{ old('tipo_atendimento', $consulta->tipo_atendimento) === 'primeira_consulta' ? 'selected' : '' }}>Primeira Consulta</option>
                        <option value="retorno" {{ old('tipo_atendimento', $consulta->tipo_atendimento) === 'retorno' ? 'selected' : '' }}>Retorno</option>
                        <option value="rotina" {{ old('tipo_atendimento', $consulta->tipo_atendimento) === 'rotina' ? 'selected' : '' }}>Rotina / Acompanhamento</option>
                        <option value="urgencia" {{ old('tipo_atendimento', $consulta->tipo_atendimento) === 'urgencia' ? 'selected' : '' }}>Urgência / Encaixe</option>
                    </select>
                    @error('tipo_atendimento') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>
                <div class="form-group col-md-3">
                    <label class="small font-weight-bold">STATUS DA CONSULTA</label>
                    <select name="status" class="form-control @error('status') is-invalid @enderror" required>
                        <option value="agendada" {{ old('status', $consulta->status) === 'agendada' ? 'selected' : '' }}>Agendada</option>
                        <option value="confirmada" {{ old('status', $consulta->status) === 'confirmada' ? 'selected' : '' }}>Confirmada</option>
                        <option value="aguardando" {{ old('status', $consulta->status) === 'aguardando' ? 'selected' : '' }}>Aguardando na Recepção</option>
                        <option value="em_atendimento" {{ old('status', $consulta->status) === 'em_atendimento' ? 'selected' : '' }}>Em Atendimento</option>
                        <option value="finalizada" {{ old('status', $consulta->status) === 'finalizada' ? 'selected' : '' }}>Finalizada</option>
                        <option value="cancelada" {{ old('status', $consulta->status) === 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                    </select>
                    @error('status') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>
                <div class="form-group col-md-3">
                    <label class="small font-weight-bold">DURAÇÃO</label>
                    <select name="duracao_minutos" class="form-control @error('duracao_minutos') is-invalid @enderror" required>
                        <option value="15" {{ old('duracao_minutos', $consulta->duracao_minutos) == 15 ? 'selected' : '' }}>15 minutos</option>
                        <option value="30" {{ old('duracao_minutos', $consulta->duracao_minutos) == 30 ? 'selected' : '' }}>30 minutos</option>
                        <option value="45" {{ old('duracao_minutos', $consulta->duracao_minutos) == 45 ? 'selected' : '' }}>45 minutos</option>
                        <option value="60" {{ old('duracao_minutos', $consulta->duracao_minutos) == 60 ? 'selected' : '' }}>60 minutos</option>
                    </select>
                    @error('duracao_minutos') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-3">
                    <label class="small font-weight-bold">DATA DA CONSULTA</label>
                    <input type="date" name="data" class="form-control @error('data') is-invalid @enderror" value="{{ old('data', $consulta->data_hora->format('Y-m-d')) }}" required>
                    @error('data') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>
                <div class="form-group col-md-3">
                    <label class="small font-weight-bold">HORÁRIO</label>
                    <input type="time" name="hora" class="form-control @error('hora') is-invalid @enderror" value="{{ old('hora', $consulta->data_hora->format('H:i')) }}" required>
                    @error('hora') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>
                <div class="form-group col-md-3">
                    <label class="small font-weight-bold">CONVÊNIO</label>
                    <select name="convenio" id="convenio" class="form-control select-convenio @error('convenio') is-invalid @enderror" required>
                        @foreach(\App\Paciente::listarConvenios() as $convItem)
                            <option value="{{ $convItem }}" {{ old('convenio', $consulta->convenio ?: 'Particular') === $convItem ? 'selected' : '' }}>{{ $convItem }}</option>
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
                <div class="form-group col-md-3">
                    <label class="small font-weight-bold">VALOR</label>
                    <input type="number" step="0.01" min="0" max="99999.99" name="valor" class="form-control @error('valor') is-invalid @enderror" value="{{ old('valor', $consulta->valor) }}" placeholder="0.00">
                    @error('valor') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label class="small font-weight-bold">MOTIVO DO AGENDAMENTO</label>
                    <textarea name="motivo" class="form-control" rows="3">{{ old('motivo', $consulta->motivo) }}</textarea>
                </div>
                <div class="form-group col-md-6">
                    <label class="small font-weight-bold">OBSERVAÇÕES ADMINISTRATIVAS</label>
                    <textarea name="observacoes" class="form-control" rows="3">{{ old('observacoes', $consulta->observacoes) }}</textarea>
                </div>
            </div>
        </div>

        <div class="card-box-header bg-light justify-content-end">
            <a href="{{ route('consultas.show', $consulta->id) }}" class="btn-andrik-outline mr-2">Cancelar</a>
            <button type="submit" class="btn-andrik-primary">Salvar Alterações</button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    $('#medico_id').on('change', function() {
        var esp = $(this).find(':selected').data('especialidade');
        if (esp) $('#especialidade').val(esp).prop('readonly', true);
    });
</script>
@endpush
