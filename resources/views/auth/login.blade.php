@extends('layouts.auth')

@section('content')
<div class="login-card">
    <div class="login-header">
        <h4 class="font-weight-bold mb-1 text-white" style="text-transform: uppercase; letter-spacing: 0.04em; font-size: 1.15rem;">Clínica Andrik</h4>
        <div style="color: #cbd5e1; font-size: 0.8rem;">Sistema de Gestão Médica & Prontuário</div>
    </div>

    <div class="login-body">
        @if(session('info'))
            <div class="alert alert-info py-2" style="font-size: 0.82rem;">
                {{ session('info') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger py-2" style="font-size: 0.82rem;">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('login.post') }}" method="POST">
            @csrf
            <div class="form-group mb-3">
                <label for="email" class="font-weight-bold text-muted small">E-MAIL PROFISSIONAL</label>
                <input type="email" id="email" name="email" class="form-control" value="{{ old('email', 'admin@clinicaandrik.com.br') }}" required autofocus placeholder="nome@clinicaandrik.com.br">
            </div>

            <div class="form-group mb-3">
                <label for="password" class="font-weight-bold text-muted small">SENHA</label>
                <input type="password" id="password" name="password" class="form-control" value="password" required placeholder="Sua senha">
            </div>

            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="remember" name="remember" checked>
                    <label class="form-check-label small text-muted" for="remember">Manter sessão ativa</label>
                </div>
            </div>

            <button type="submit" class="btn-andrik-primary btn-block justify-content-center py-2">
                Acessar Sistema
            </button>
        </form>

        <hr class="my-3">

        <div class="p-2 bg-light border">
            <div class="font-weight-bold text-muted small mb-2">Credenciais de Demonstração:</div>
            <div class="d-flex flex-wrap">
                <button type="button" class="btn btn-sm btn-outline-secondary mr-1 mb-1 demo-btn" data-email="admin@clinicaandrik.com.br" data-pass="password">
                    Admin (Dr. Andrik)
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary mr-1 mb-1 demo-btn" data-email="pedro.miguel@clinicaandrik.com.br" data-pass="password">
                    Médico (Dr. Pedro Miguel)
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary mr-1 mb-1 demo-btn" data-email="matheus.brito@clinicaandrik.com.br" data-pass="password">
                    Recepção (Matheus Brito)
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $('.demo-btn').on('click', function() {
        $('#email').val($(this).data('email'));
        $('#password').val($(this).data('pass'));
    });
</script>
@endpush
