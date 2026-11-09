<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acesso ao Sistema - Clínica Andrik</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/clinica.css') }}">
    <style>
        body {
            background-color: #111827;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }
        .login-card {
            background: #ffffff;
            width: 100%;
            max-width: 420px;
            border: 1px solid #374151;
        }
        .login-header {
            background: #1e3a8a;
            color: #ffffff;
            padding: 22px 24px;
            border-bottom: 1px solid #172554;
        }
        .login-body {
            padding: 24px;
        }
    </style>
</head>
<body>
    @yield('content')
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    @stack('scripts')
</body>
</html>
