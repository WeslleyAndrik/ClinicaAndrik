@extends('layouts.app')

@section('page_title', 'Meu Perfil de Acesso')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card-box">
            <div class="card-box-header">
                <h2 class="card-box-title">Meus Dados Cadastrais</h2>
            </div>

            <form action="{{ route('perfil.update') }}" method="POST" novalidate data-validate="true">
                @csrf
                @method('PUT')
                <div class="card-box-body">
                    <div class="form-group">
                        <label class="small font-weight-bold">NOME COMPLETO</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required minlength="3">
                        @error('name') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group">
                        <label class="small font-weight-bold">E-MAIL</label>
                        <input type="email" class="form-control" value="{{ $user->email }}" readonly disabled>
                        <small class="text-muted">O e-mail de login só pode ser alterado pela Direção Clínica.</small>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label class="small font-weight-bold">PERFIL DE ACESSO</label>
                            <input type="text" class="form-control" value="{{ strtoupper($user->role) }}" readonly disabled>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="small font-weight-bold">TELEFONE DE CONTATO</label>
                            <input type="text" name="telefone" class="form-control @error('telefone') is-invalid @enderror" value="{{ old('telefone', $user->telefone) }}" placeholder="(83) 98888-7777">
                            @error('telefone') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    @if($user->crm)
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label class="small font-weight-bold">CRM</label>
                                <input type="text" class="form-control" value="{{ $user->crm }}" readonly disabled>
                            </div>
                            <div class="form-group col-md-6">
                                <label class="small font-weight-bold">ESPECIALIDADE</label>
                                <input type="text" class="form-control" value="{{ $user->especialidade }}" readonly disabled>
                            </div>
                        </div>
                    @endif

                    <hr class="my-3">
                    <h6 class="font-weight-bold text-dark mb-3">Alterar Senha de Acesso</h6>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label class="small font-weight-bold">NOVA SENHA</label>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" minlength="6" placeholder="Deixe em branco para não alterar">
                            @error('password') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-group col-md-6">
                            <label class="small font-weight-bold">CONFIRMAR NOVA SENHA</label>
                            <input type="password" name="password_confirmation" class="form-control" minlength="6" placeholder="Repita a nova senha">
                        </div>
                    </div>
                </div>

                <div class="card-box-header bg-light justify-content-end">
                    <button type="submit" class="btn-andrik-primary">
                        Salvar Meu Perfil
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
