@extends('layouts.app')

@section('page_title', 'Cadastrar Novo Paciente')

@section('content')
<div class="card-box">
    <div class="card-box-header">
        <h2 class="card-box-title">Ficha Cadastral do Paciente</h2>
        <a href="{{ route('pacientes.index') }}" class="btn-andrik-outline">Voltar à Lista</a>
    </div>

    <form action="{{ route('pacientes.store') }}" method="POST" novalidate data-validate="true">
        @csrf
        <div class="card-box-body">
            <h6 class="font-weight-bold text-dark border-bottom pb-2 mb-3">1. Identificação Civil</h6>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label class="small font-weight-bold">NOME COMPLETO</label>
                    <input type="text" name="nome" class="form-control @error('nome') is-invalid @enderror" value="{{ old('nome') }}" required minlength="3" maxlength="150" placeholder="Nome completo sem abreviações">
                    @error('nome') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>
                <div class="form-group col-md-3">
                    <label class="small font-weight-bold">CPF</label>
                    <input type="text" name="cpf" class="form-control @error('cpf') is-invalid @enderror" value="{{ old('cpf') }}" required maxlength="14" placeholder="000.000.000-00">
                    @error('cpf') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>
                <div class="form-group col-md-3">
                    <label class="small font-weight-bold">RG</label>
                    <input type="text" name="rg" class="form-control @error('rg') is-invalid @enderror" value="{{ old('rg') }}" maxlength="20" placeholder="Número do documento">
                    @error('rg') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-3">
                    <label class="small font-weight-bold">DATA DE NASCIMENTO</label>
                    <input type="date" name="data_nascimento" class="form-control @error('data_nascimento') is-invalid @enderror" value="{{ old('data_nascimento') }}" max="{{ date('Y-m-d', strtotime('-1 day')) }}" min="1900-01-01" required>
                    @error('data_nascimento') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>
                <div class="form-group col-md-3">
                    <label class="small font-weight-bold">SEXO</label>
                    <select name="sexo" class="form-control @error('sexo') is-invalid @enderror" required>
                        <option value="M" {{ old('sexo') === 'M' ? 'selected' : '' }}>Masculino</option>
                        <option value="F" {{ old('sexo') === 'F' ? 'selected' : '' }}>Feminino</option>
                        <option value="Outro" {{ old('sexo') === 'Outro' ? 'selected' : '' }}>Outro</option>
                    </select>
                    @error('sexo') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>
                <div class="form-group col-md-3">
                    <label class="small font-weight-bold">TIPO SANGUÍNEO</label>
                    <select name="tipo_sanguineo" class="form-control @error('tipo_sanguineo') is-invalid @enderror">
                        <option value="">Não informado</option>
                        @foreach(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $ts)
                            <option value="{{ $ts }}" {{ old('tipo_sanguineo') === $ts ? 'selected' : '' }}>{{ $ts }}</option>
                        @endforeach
                    </select>
                    @error('tipo_sanguineo') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>
                <div class="form-group col-md-3">
                    <label class="small font-weight-bold">CONVÊNIO / PLANO</label>
                    <select name="convenio" class="form-control select-convenio @error('convenio') is-invalid @enderror">
                        @foreach(\App\Paciente::listarConvenios() as $convItem)
                            <option value="{{ $convItem }}" {{ old('convenio', 'Particular') === $convItem ? 'selected' : '' }}>{{ $convItem }}</option>
                        @endforeach
                        <option value="__novo__">+ Cadastrar novo convênio...</option>
                    </select>
                    <div class="novo-convenio-box d-none mt-2">
                        <div class="d-flex">
                            <input type="text" class="form-control form-control-sm input-novo-convenio mr-1" maxlength="50" placeholder="Nome do novo convênio">
                            <button type="button" class="btn-andrik-primary btn-salvar-convenio px-2 py-1">Salvar</button>
                            <button type="button" class="btn-andrik-outline btn-cancelar-convenio px-2 py-1 ml-1">X</button>
                        </div>
                    </div>
                    @error('convenio') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>
            </div>

            <h6 class="font-weight-bold text-dark border-bottom pb-2 mb-3 mt-3">2. Contato e Endereço</h6>
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label class="small font-weight-bold">TELEFONE / CELULAR</label>
                    <input type="text" name="telefone" class="form-control @error('telefone') is-invalid @enderror" value="{{ old('telefone') }}" required maxlength="15" placeholder="(83) 98888-7777">
                    @error('telefone') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>
                <div class="form-group col-md-4">
                    <label class="small font-weight-bold">E-MAIL</label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" maxlength="255" placeholder="paciente@email.com">
                    @error('email') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>
                <div class="form-group col-md-4">
                    <label class="small font-weight-bold">NÚMERO DA CARTEIRINHA</label>
                    <input type="text" name="numero_carteirinha" class="form-control @error('numero_carteirinha') is-invalid @enderror" value="{{ old('numero_carteirinha') }}" maxlength="50" placeholder="Código do plano">
                    @error('numero_carteirinha') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-2">
                    <label class="small font-weight-bold">CEP</label>
                    <input type="text" name="cep" class="form-control @error('cep') is-invalid @enderror" value="{{ old('cep') }}" maxlength="9" placeholder="00000-000">
                    @error('cep') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>
                <div class="form-group col-md-6">
                    <label class="small font-weight-bold">LOGRADOURO / ENDEREÇO</label>
                    <input type="text" name="endereco" class="form-control @error('endereco') is-invalid @enderror" value="{{ old('endereco') }}" maxlength="255" placeholder="Rua, Avenida...">
                    @error('endereco') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>
                <div class="form-group col-md-2">
                    <label class="small font-weight-bold">NÚMERO</label>
                    <input type="text" name="numero" class="form-control @error('numero') is-invalid @enderror" value="{{ old('numero') }}" inputmode="numeric" maxlength="10" placeholder="Ex: 1558">
                    @error('numero') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>
                <div class="form-group col-md-2">
                    <label class="small font-weight-bold">BAIRRO</label>
                    <input type="text" name="bairro" class="form-control @error('bairro') is-invalid @enderror" value="{{ old('bairro') }}" maxlength="100">
                    @error('bairro') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="form-row">
                @php
                    $ufSelecionada = old('estado', 'PB');
                    $cidadeSelecionada = old('cidade', 'João Pessoa');
                    $cidadesUf = \App\Http\Controllers\PacienteController::listarCidadesUf($ufSelecionada, $cidadeSelecionada);
                @endphp
                <div class="form-group col-md-4">
                    <label class="small font-weight-bold">CIDADE</label>
                    <select name="cidade" class="form-control select-cidade @error('cidade') is-invalid @enderror" data-cidade-inicial="{{ $cidadeSelecionada }}">
                        <option value="">Selecione a cidade</option>
                        @foreach($cidadesUf as $cidItem)
                            <option value="{{ $cidItem }}" {{ mb_strtolower($cidadeSelecionada, 'UTF-8') === mb_strtolower($cidItem, 'UTF-8') ? 'selected' : '' }}>{{ $cidItem }}</option>
                        @endforeach
                    </select>
                    @error('cidade') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>
                <div class="form-group col-md-2">
                    <label class="small font-weight-bold">ESTADO</label>
                    <select name="estado" class="form-control select-estado @error('estado') is-invalid @enderror">
                        <option value="">Selecione</option>
                        @foreach(\App\Http\Controllers\PacienteController::ESTADOS_UF as $uf)
                            <option value="{{ $uf }}" {{ $ufSelecionada === $uf ? 'selected' : '' }}>{{ $uf }}</option>
                        @endforeach
                    </select>
                    @error('estado') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>
                <div class="form-group col-md-3">
                    <label class="small font-weight-bold">CONTATO DE EMERGÊNCIA</label>
                    <input type="text" name="contato_emergencia_nome" class="form-control @error('contato_emergencia_nome') is-invalid @enderror" value="{{ old('contato_emergencia_nome') }}" maxlength="100" placeholder="Nome do contato">
                    @error('contato_emergencia_nome') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>
                <div class="form-group col-md-3">
                    <label class="small font-weight-bold">TELEFONE DE EMERGÊNCIA</label>
                    <input type="text" name="contato_emergencia_telefone" class="form-control @error('contato_emergencia_telefone') is-invalid @enderror" value="{{ old('contato_emergencia_telefone') }}" maxlength="15" placeholder="(83) 98888-7777">
                    @error('contato_emergencia_telefone') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>
            </div>

            <h6 class="font-weight-bold text-dark border-bottom pb-2 mb-3 mt-3">3. Histórico Clínico Prévio e Alergias</h6>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label class="small font-weight-bold text-danger">ALERGIAS CONHECIDAS</label>
                    <textarea name="alergias" class="form-control border-danger" rows="2" placeholder="Dipirona, Penicilina, Iodo...">{{ old('alergias') }}</textarea>
                </div>
                <div class="form-group col-md-6">
                    <label class="small font-weight-bold">MEDICAMENTOS DE USO CONTÍNUO</label>
                    <textarea name="medicamentos_uso_continuo" class="form-control" rows="2">{{ old('medicamentos_uso_continuo') }}</textarea>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label class="small font-weight-bold">HISTÓRICO CIRÚRGICO / COMORBIDADES</label>
                    <textarea name="historico_cirurgico" class="form-control" rows="2">{{ old('historico_cirurgico') }}</textarea>
                </div>
                <div class="form-group col-md-6">
                    <label class="small font-weight-bold">OBSERVAÇÕES ADMINISTRATIVAS</label>
                    <textarea name="observacoes" class="form-control" rows="2">{{ old('observacoes') }}</textarea>
                </div>
            </div>
        </div>

        <div class="card-box-header bg-light justify-content-end">
            <a href="{{ route('pacientes.index') }}" class="btn-andrik-outline mr-2">Cancelar</a>
            <button type="submit" class="btn-andrik-primary">Salvar Cadastro</button>
        </div>
    </form>
</div>
@endsection
