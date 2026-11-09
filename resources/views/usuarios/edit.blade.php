@extends('layouts.app')

@section('page_title', 'Editar Profissional')

@section('content')
<div class="card-box">
    <div class="card-box-header">
        <h2 class="card-box-title">Editando: {{ $usuario->name }}</h2>
        <a href="{{ route('usuarios.index') }}" class="btn-andrik-outline">Voltar à Lista</a>
    </div>

    <form action="{{ route('usuarios.update', $usuario->id) }}" method="POST" novalidate data-validate="true">
        @csrf
        @method('PUT')
        <div class="card-box-body">
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label class="small font-weight-bold text-muted">NOME COMPLETO</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $usuario->name) }}" required minlength="3" maxlength="150">
                    @error('name') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>
                <div class="form-group col-md-6">
                    <label class="small font-weight-bold text-muted">E-MAIL PROFISSIONAL</label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $usuario->email) }}" required maxlength="255">
                    @error('email') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-4">
                    <label class="small font-weight-bold text-muted">PERFIL DE ACESSO</label>
                    <select name="role" id="role_select" class="form-control @error('role') is-invalid @enderror" required>
                        <option value="medico" {{ old('role', $usuario->role) === 'medico' ? 'selected' : '' }}>Médico</option>
                        <option value="recepcao" {{ old('role', $usuario->role) === 'recepcao' ? 'selected' : '' }}>Recepção / Atendente</option>
                        <option value="admin" {{ old('role', $usuario->role) === 'admin' ? 'selected' : '' }}>Administrador / Direção Clínica</option>
                    </select>
                    @error('role') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>
                <div class="form-group col-md-4">
                    <label class="small font-weight-bold text-muted">NOVA SENHA</label>
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" minlength="6" placeholder="Deixe em branco para manter">
                    @error('password') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>
                <div class="form-group col-md-4">
                    <label class="small font-weight-bold text-muted">TELEFONE</label>
                    <input type="text" name="telefone" class="form-control @error('telefone') is-invalid @enderror" value="{{ old('telefone', $usuario->telefone) }}" maxlength="15">
                    @error('telefone') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="form-row" id="medico_fields">
                <div class="form-group col-md-6">
                    <label class="small font-weight-bold text-muted">CRM / UF</label>
                    <input type="text" name="crm" id="crm" class="form-control @error('crm') is-invalid @enderror" value="{{ old('crm', $usuario->crm) }}" maxlength="30">
                    @error('crm') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>
                <div class="form-group col-md-6">
                    <label class="small font-weight-bold text-muted">ESPECIALIDADE PRINCIPAL</label>
                    <input type="text" name="especialidade" id="especialidade" class="form-control @error('especialidade') is-invalid @enderror" value="{{ old('especialidade', $usuario->especialidade) }}" maxlength="100">
                    @error('especialidade') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="form-group mb-0">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="ativoSwitch" name="ativo" value="1" {{ old('ativo', $usuario->ativo) ? 'checked' : '' }}>
                    <label class="form-check-label font-weight-bold" for="ativoSwitch">Usuário Ativo no Sistema</label>
                </div>
            </div>
        </div>

        <div class="card-box-header bg-light justify-content-end">
            <a href="{{ route('usuarios.index') }}" class="btn-andrik-outline mr-2">Cancelar</a>
            <button type="submit" class="btn-andrik-primary">Salvar Alterações</button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    function toggleMedicoFields() {
        var role = $('#role_select').val();
        if (role === 'medico') {
            $('#medico_fields').show();
            padronizarPrefixoMedico();
        } else {
            $('#medico_fields').hide();
        }
    }
    function padronizarPrefixoMedico() {
        var role = $('#role_select').val();
        var inputNome = $('input[name="name"]');
        var val = $.trim(inputNome.val());
        if (role === 'medico' && val.length > 0) {
            var semPrefixo = val.replace(/^(dr\.?|dra\.?)\s+/i, '');
            if (semPrefixo.length > 0) {
                inputNome.val('Dr. ' + semPrefixo);
            }
        }
    }
    $('input[name="name"]').on('blur', padronizarPrefixoMedico);
    $('#role_select').on('change', toggleMedicoFields);
    toggleMedicoFields();
</script>
@endpush
