@extends('layouts.app')

@section('page_title', 'Editar Prontuário Médico')

@section('content')
<div class="card-box">
    <div class="card-box-header">
        <h2 class="card-box-title">Editando Prontuário de: {{ $prontuario->paciente->nome }}</h2>
        <div>
            <a href="{{ route('prontuarios.show', $prontuario->id) }}" class="btn-andrik-outline mr-1">Ver Prontuário</a>
            <a href="{{ route('pacientes.historico', $prontuario->paciente_id) }}" class="btn-andrik-outline">Voltar</a>
        </div>
    </div>

    <form action="{{ route('prontuarios.update', $prontuario->id) }}" method="POST" novalidate data-validate="true">
        @csrf
        @method('PUT')
        <div class="card-box-body">
            <div class="row">
                <div class="col-lg-8">
                    <div class="card-box">
                        <div class="card-box-header">
                            <h2 class="card-box-title">1. Anamnese Clínica</h2>
                        </div>
                        <div class="card-box-body">
                            <div class="form-group">
                                <label class="small font-weight-bold">QUEIXA PRINCIPAL</label>
                                <textarea name="queixa_principal" class="form-control @error('queixa_principal') is-invalid @enderror" rows="3" required minlength="5">{{ old('queixa_principal', $prontuario->queixa_principal) }}</textarea>
                                @error('queixa_principal') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                            <div class="form-group mb-0">
                                <label class="small font-weight-bold">HISTÓRIA DA DOENÇA ATUAL</label>
                                <textarea name="historia_doenca_atual" class="form-control" rows="4">{{ old('historia_doenca_atual', $prontuario->historia_doenca_atual) }}</textarea>
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
                                    <label class="small font-weight-bold">DIAGNÓSTICO</label>
                                    <input type="text" name="diagnostico" class="form-control @error('diagnostico') is-invalid @enderror" value="{{ old('diagnostico', $prontuario->diagnostico) }}" required minlength="3">
                                    @error('diagnostico') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>
                                <div class="form-group col-md-4 mb-0">
                                    <label class="small font-weight-bold">CID-10</label>
                                    <input type="text" name="cid10" class="form-control @error('cid10') is-invalid @enderror" value="{{ old('cid10', $prontuario->cid10) }}" placeholder="Ex: I10, J03.9">
                                    @error('cid10') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-box">
                        <div class="card-box-header">
                            <h2 class="card-box-title">3. Prescrição Farmacológica</h2>
                        </div>
                        <div class="card-box-body">
                            <div class="form-group mb-0">
                                <textarea name="prescricao_medica" class="form-control" rows="5">{{ old('prescricao_medica', $prontuario->prescricao_medica) }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="card-box mb-0">
                        <div class="card-box-header">
                            <h2 class="card-box-title">4. Exames Solicitados e Conduta</h2>
                        </div>
                        <div class="card-box-body">
                            <div class="form-group">
                                <label class="small font-weight-bold">EXAMES SOLICITADOS</label>
                                <textarea name="exames_solicitados" class="form-control" rows="3">{{ old('exames_solicitados', $prontuario->exames_solicitados) }}</textarea>
                            </div>
                            <div class="form-group mb-0">
                                <label class="small font-weight-bold">CONDUTA E ORIENTAÇÕES</label>
                                <textarea name="conduta_plano" class="form-control" rows="3">{{ old('conduta_plano', $prontuario->conduta_plano) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card-box mb-0">
                        <div class="card-box-header">
                            <h2 class="card-box-title">Sinais Vitais</h2>
                        </div>
                        <div class="card-box-body">
                            <div class="form-group">
                                <label class="small font-weight-bold">PRESSÃO ARTERIAL</label>
                                <input type="text" name="pressao_arterial" class="form-control @error('pressao_arterial') is-invalid @enderror" value="{{ old('pressao_arterial', $prontuario->pressao_arterial) }}" placeholder="120/80">
                                @error('pressao_arterial') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                            <div class="form-group">
                                <label class="small font-weight-bold">FREQUÊNCIA CARDÍACA</label>
                                <input type="number" min="30" max="250" name="frequencia_cardiaca" class="form-control @error('frequencia_cardiaca') is-invalid @enderror" value="{{ old('frequencia_cardiaca', $prontuario->frequencia_cardiaca) }}">
                                @error('frequencia_cardiaca') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                            <div class="form-group">
                                <label class="small font-weight-bold">TEMPERATURA</label>
                                <input type="number" step="0.1" min="30" max="45" name="temperatura" class="form-control @error('temperatura') is-invalid @enderror" value="{{ old('temperatura', $prontuario->temperatura) }}">
                                @error('temperatura') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                            <div class="form-row">
                                <div class="form-group col-6">
                                    <label class="small font-weight-bold">PESO</label>
                                    <input type="number" step="0.1" min="0.5" max="400" name="peso" class="form-control @error('peso') is-invalid @enderror" value="{{ old('peso', $prontuario->peso) }}">
                                    @error('peso') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>
                                <div class="form-group col-6">
                                    <label class="small font-weight-bold">ALTURA</label>
                                    <input type="number" step="0.01" min="0.3" max="2.5" name="altura" class="form-control @error('altura') is-invalid @enderror" value="{{ old('altura', $prontuario->altura) }}">
                                    @error('altura') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="form-group mb-0">
                                <label class="small font-weight-bold">EXAME FÍSICO</label>
                                <textarea name="exame_fisico" class="form-control" rows="5">{{ old('exame_fisico', $prontuario->exame_fisico) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-box-header bg-light justify-content-end">
            <a href="{{ route('prontuarios.show', $prontuario->id) }}" class="btn-andrik-outline mr-2">Cancelar</a>
            <button type="submit" class="btn-andrik-primary">Salvar Alterações</button>
        </div>
    </form>
</div>
@endsection
