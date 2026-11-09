<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Clínica Andrik - Gestão Médica')</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/clinica.css') }}">
    @stack('styles')
</head>
<body>
<div class="app-wrapper">
    <aside class="app-sidebar no-print">
        <div>
            <a href="{{ route('dashboard') }}" class="sidebar-brand">
                <div class="brand-icon">+</div>
                <div class="brand-text-container">
                    <div class="brand-title">Clínica Andrik</div>
                    <span class="brand-tag">Saúde é a nossa missão!</span>
                </div>
            </a>

            <div class="sidebar-nav">
                <div class="menu-category">Geral</div>
                <ul class="menu-list">
                    <li>
                        <a href="{{ route('dashboard') }}" class="menu-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                            <span>Painel Clínico</span>
                        </a>
                    </li>
                </ul>

                <div class="menu-category">Atendimento</div>
                <ul class="menu-list">
                    <li>
                        <a href="{{ route('consultas.index') }}" class="menu-link {{ request()->routeIs('consultas.*') ? 'active' : '' }}">
                            <span>Agenda de Consultas</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('pacientes.index') }}" class="menu-link {{ request()->routeIs('pacientes.*') ? 'active' : '' }}">
                            <span>Pacientes</span>
                        </a>
                    </li>
                    @if(Auth::user()->isMedico() || Auth::user()->isAdmin())
                        <li>
                            <a href="{{ route('consultas.index', ['status' => 'aguardando']) }}" class="menu-link">
                                <span>Fila de Espera</span>
                            </a>
                        </li>
                    @endif
                </ul>

                <div class="menu-category">Gestão</div>
                <ul class="menu-list">
                    <li>
                        <a href="{{ route('relatorios.index') }}" class="menu-link {{ request()->routeIs('relatorios.*') ? 'active' : '' }}">
                            <span>Relatórios de Atendimento</span>
                        </a>
                    </li>
                    @if(Auth::user()->isAdmin())
                        <li>
                            <a href="{{ route('usuarios.index') }}" class="menu-link {{ request()->routeIs('usuarios.*') ? 'active' : '' }}">
                                <span>Corpo Clínico & Equipe</span>
                            </a>
                        </li>
                    @endif
                    <li>
                        <a href="{{ route('perfil') }}" class="menu-link {{ request()->routeIs('perfil') ? 'active' : '' }}">
                            <span>Meu Perfil</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="sidebar-footer">
            <div class="user-card">
                <div class="user-avatar-circle">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
                <div class="user-details">
                    <div class="user-name">{{ Auth::user()->name }}</div>
                    <div class="user-role">
                        @if(Auth::user()->isMedico())
                            {{ Auth::user()->crm ?? 'Médico' }}
                        @elseif(Auth::user()->isAdmin())
                            Direção Clínica
                        @else
                            Recepção
                        @endif
                    </div>
                </div>
                <form action="{{ route('logout') }}" method="POST" class="m-0 p-0">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-light py-1 px-2" style="font-size: 0.72rem; border-color: #475569; color: #cbd5e1;" title="Encerrar Sessão">
                        Sair
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <div class="app-main">
        <header class="app-topbar no-print">
            <div class="topbar-left">
                <button type="button" class="sidebar-toggle-btn" id="sidebarToggle" title="Alternar Menu">
                    &#9776;
                </button>
                <h1 class="page-title">@yield('page_title', 'Painel Geral')</h1>
            </div>

            <div class="topbar-actions">
                <div class="topbar-date">
                    {{ \Carbon\Carbon::now()->format('d/m/Y') }}
                </div>
                <a href="{{ route('consultas.create') }}" class="btn-andrik-primary">
                    <span class="btn-label">Agendar Consulta</span>
                </a>
                <a href="{{ route('pacientes.create') }}" class="btn-andrik-outline">
                    <span class="btn-label">Novo Paciente</span>
                </a>
            </div>
        </header>

        <main class="app-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show mb-3 py-2" role="alert">
                    <strong>Confirmação:</strong> {{ session('success') }}
                    <button type="button" class="close py-2" data-dismiss="alert">&times;</button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show mb-3 py-2" role="alert">
                    <strong>Atenção:</strong> {{ session('error') }}
                    <button type="button" class="close py-2" data-dismiss="alert">&times;</button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger mb-3 py-2" role="alert">
                    <strong>Corrija os campos destacados abaixo:</strong>
                    <ul class="mb-0 pl-3 mt-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>

<script src="{{ asset('js/jquery.min.js') }}"></script>
<script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
<script>
    function validarCpfDigitos(cpf) {
        cpf = (cpf || '').replace(/\D/g, '');
        if (cpf.length !== 11 || /^(\d)\1{10}$/.test(cpf)) return false;
        for (var t = 9; t < 11; t++) {
            var d = 0;
            for (var c = 0; c < t; c++) {
                d += parseInt(cpf.charAt(c), 10) * ((t + 1) - c);
            }
            d = ((10 * d) % 11) % 10;
            if (parseInt(cpf.charAt(t), 10) !== d) return false;
        }
        return true;
    }

    function definirErroCampo(el, mensagem) {
        if (!el || !el.parentElement) return;
        var parent = el.parentElement;
        var serverFeedbacks = parent.querySelectorAll('.invalid-feedback:not(.js-field-error)');
        serverFeedbacks.forEach(function(node) {
            node.style.display = 'none';
        });

        var box = parent.querySelector('.js-field-error');
        if (mensagem) {
            el.classList.add('is-invalid');
            el.setCustomValidity(mensagem);
            if (!box) {
                box = document.createElement('div');
                box.className = 'invalid-feedback d-block js-field-error';
                parent.appendChild(box);
            }
            box.textContent = mensagem;
            box.style.display = 'block';
        } else {
            el.classList.remove('is-invalid');
            el.setCustomValidity('');
            if (box) {
                box.textContent = '';
                box.style.display = 'none';
            }
        }
    }

    function validarCampoIndividual(el, isBlurOrSubmit) {
        if (!el || el.disabled || el.readOnly || el.type === 'hidden') return true;
        if (el.offsetParent === null && el.type !== 'hidden') return true;

        var name = el.name || '';
        var val = (el.value || '').trim();

        // 1. Número do Endereço (tratamento explícito para negativos, zero e caracteres)
        if (name === 'numero') {
            if (val === '') {
                definirErroCampo(el, '');
                return true;
            }
            if (/^-/.test(val)) {
                definirErroCampo(el, 'Não são permitidos números negativos neste campo.');
                return false;
            }
            if (/^0+$/.test(val)) {
                definirErroCampo(el, 'O número do endereço deve ser maior que zero.');
                return false;
            }
            if (!/^\d+$/.test(val)) {
                definirErroCampo(el, 'Digite apenas números inteiros positivos (ex: 1558).');
                return false;
            }
            definirErroCampo(el, '');
            return true;
        }

        // 2. CPF
        if (name === 'cpf') {
            if (/^-/.test(val)) {
                definirErroCampo(el, 'Não são permitidos números negativos no CPF.');
                return false;
            }
            if (/[a-zA-Z]/.test(val)) {
                definirErroCampo(el, 'O CPF deve conter apenas números.');
                return false;
            }
            var digitosCpf = val.replace(/\D/g, '');
            if (val === '') {
                if (isBlurOrSubmit && el.required) {
                    definirErroCampo(el, 'Informe o CPF do paciente.');
                    return false;
                }
                definirErroCampo(el, '');
                return true;
            }
            if ((isBlurOrSubmit || digitosCpf.length === 11) && !validarCpfDigitos(digitosCpf)) {
                definirErroCampo(el, 'CPF inválido. Verifique os 11 dígitos informados.');
                return false;
            }
            definirErroCampo(el, '');
            return true;
        }

        // 3. Telefone e Celular
        if (name === 'telefone' || name === 'contato_emergencia_telefone') {
            if (/^-/.test(val)) {
                definirErroCampo(el, 'Não são permitidos números negativos no telefone.');
                return false;
            }
            if (/[a-zA-Z]/.test(val)) {
                definirErroCampo(el, 'Digite apenas números com o código DDD.');
                return false;
            }
            var digitosTel = val.replace(/\D/g, '');
            if (val === '') {
                if (isBlurOrSubmit && el.required) {
                    definirErroCampo(el, 'Informe o telefone para contato.');
                    return false;
                }
                definirErroCampo(el, '');
                return true;
            }
            if (isBlurOrSubmit && (digitosTel.length < 10 || digitosTel.length > 11)) {
                definirErroCampo(el, 'Informe o telefone completo com DDD (10 ou 11 dígitos).');
                return false;
            }
            definirErroCampo(el, '');
            return true;
        }

        // 4. CEP, Cidade e Estado (UF) integrados com a base dos Correios
        if (name === 'cep') {
            if (/^-/.test(val)) {
                definirErroCampo(el, 'Não são permitidos números negativos no CEP.');
                return false;
            }
            if (/[a-zA-Z]/.test(val)) {
                definirErroCampo(el, 'O CEP deve conter apenas números.');
                return false;
            }
            var digitosCep = val.replace(/\D/g, '');
            if (val === '') {
                el.dataset.cepInvalido = '';
                if (el.form) {
                    delete el.form.dataset.correiosUf;
                    delete el.form.dataset.correiosCidade;
                }
                definirErroCampo(el, '');
                return true;
            }
            if (isBlurOrSubmit && digitosCep.length !== 8) {
                definirErroCampo(el, 'O CEP deve conter 8 dígitos numéricos (ex: 58000-000).');
                return false;
            }
            if (el.dataset.cepInvalido === '1') {
                definirErroCampo(el, el.dataset.cepMensagem || 'O CEP informado não existe na base dos Correios.');
                return false;
            }
            definirErroCampo(el, '');
            return true;
        }

        if (name === 'cidade') {
            if (val !== '' && el.form && el.form.dataset.correiosCidade) {
                var normInformada = val.normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase();
                var normCorreios = el.form.dataset.correiosCidade.normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase();
                if (normInformada !== normCorreios) {
                    definirErroCampo(el, 'A cidade informada diverge do CEP nos Correios (' + el.form.dataset.correiosCidade + ').');
                    return false;
                }
            }
            definirErroCampo(el, '');
            return true;
        }

        if (name === 'estado') {
            if (val !== '' && el.form && el.form.dataset.correiosUf) {
                if (val.toUpperCase() !== el.form.dataset.correiosUf.toUpperCase()) {
                    definirErroCampo(el, 'A UF selecionada diverge do CEP nos Correios (' + el.form.dataset.correiosUf + ').');
                    return false;
                }
            }
            definirErroCampo(el, '');
            return true;
        }

        // 5. RG e Número da Carteirinha
        if (name === 'rg' || name === 'numero_carteirinha') {
            if (val === '') {
                definirErroCampo(el, '');
                return true;
            }
            if (/^-/.test(val)) {
                definirErroCampo(el, 'Não são permitidos valores negativos neste campo.');
                return false;
            }
            if (!/^[0-9A-Za-z][0-9A-Za-z.\-\s\/]*$/.test(val)) {
                definirErroCampo(el, 'Informe apenas números e letras válidas.');
                return false;
            }
            if (name === 'rg' && isBlurOrSubmit && val.length < 4) {
                definirErroCampo(el, 'Informe um número de RG válido (mínimo 4 caracteres).');
                return false;
            }
            definirErroCampo(el, '');
            return true;
        }

        // 6. Campos de Nome
        if (name === 'nome' || name === 'name' || name === 'contato_emergencia_nome') {
            if (val === '') {
                if (isBlurOrSubmit && el.required) {
                    definirErroCampo(el, 'Informe o nome completo.');
                    return false;
                }
                definirErroCampo(el, '');
                return true;
            }
            if (/^-/.test(val) || /\d/.test(val)) {
                definirErroCampo(el, 'Este campo aceita apenas letras e espaços (números não são permitidos).');
                return false;
            }
            if (isBlurOrSubmit && val.length < 3) {
                definirErroCampo(el, 'Informe o nome com pelo menos 3 caracteres.');
                return false;
            }
            definirErroCampo(el, '');
            return true;
        }

        // 7. Campos Numéricos (Valor, Sinais Vitais: FC, Temperatura, Peso, Altura)
        if (el.type === 'number' || ['valor', 'frequencia_cardiaca', 'temperatura', 'peso', 'altura'].indexOf(name) !== -1) {
            if (el.validity && el.validity.badInput) {
                definirErroCampo(el, 'Valor inválido. Digite apenas números.');
                return false;
            }
            if (val === '') {
                if (isBlurOrSubmit && el.required) {
                    definirErroCampo(el, 'Preencha este campo numérico.');
                    return false;
                }
                definirErroCampo(el, '');
                return true;
            }
            var num = parseFloat(val);
            if (isNaN(num)) {
                definirErroCampo(el, 'Informe um valor numérico válido.');
                return false;
            }
            if (num < 0 || /^-/.test(val)) {
                definirErroCampo(el, 'Não são permitidos números negativos neste campo.');
                return false;
            }
            if (name === 'valor' && num > 99999.99) {
                definirErroCampo(el, 'O valor máximo permitido é R$ 99.999,99.');
                return false;
            }
            if (name === 'frequencia_cardiaca') {
                if (num === 0) {
                    definirErroCampo(el, 'A frequência cardíaca deve ser maior que zero.');
                    return false;
                }
                if (isBlurOrSubmit && (num < 30 || num > 250)) {
                    definirErroCampo(el, 'Informe uma frequência cardíaca entre 30 e 250 bpm.');
                    return false;
                }
            }
            if (name === 'temperatura') {
                if (num === 0) {
                    definirErroCampo(el, 'A temperatura deve ser maior que zero.');
                    return false;
                }
                if (isBlurOrSubmit && (num < 30 || num > 45)) {
                    definirErroCampo(el, 'Informe uma temperatura clínica entre 30,0 °C e 45,0 °C.');
                    return false;
                }
            }
            if (name === 'peso') {
                if (num === 0) {
                    definirErroCampo(el, 'O peso deve ser maior que zero.');
                    return false;
                }
                if (isBlurOrSubmit && (num < 0.5 || num > 400)) {
                    definirErroCampo(el, 'Informe um peso válido entre 0,5 kg e 400 kg.');
                    return false;
                }
            }
            if (name === 'altura') {
                if (num === 0) {
                    definirErroCampo(el, 'A altura deve ser maior que zero.');
                    return false;
                }
                if (isBlurOrSubmit && (num < 0.3 || num > 2.5)) {
                    definirErroCampo(el, 'Informe a altura em metros entre 0,30 e 2,50 (ex: 1.75).');
                    return false;
                }
            }
            definirErroCampo(el, '');
            return true;
        }

        // 8. Pressão Arterial
        if (name === 'pressao_arterial') {
            if (val === '') {
                definirErroCampo(el, '');
                return true;
            }
            if (/^-/.test(val) || /\/-/.test(val)) {
                definirErroCampo(el, 'A pressão arterial não pode conter números negativos.');
                return false;
            }
            if (isBlurOrSubmit && !/^\d{2,3}\/\d{2,3}(\s?mmHg)?$/i.test(val)) {
                definirErroCampo(el, 'Informe no formato sistólica/diastólica (ex: 120/80).');
                return false;
            }
            definirErroCampo(el, '');
            return true;
        }

        // 9. CID-10
        if (name === 'cid10') {
            if (val === '') {
                definirErroCampo(el, '');
                return true;
            }
            if (/^-/.test(val)) {
                definirErroCampo(el, 'Não são permitidos valores negativos no CID-10.');
                return false;
            }
            if (isBlurOrSubmit && !/^[A-Z]\d{2}(\.\d{1,2})?(\s.*)?$/i.test(val)) {
                definirErroCampo(el, 'Informe um código CID-10 válido (ex: I10 ou J03.9).');
                return false;
            }
            definirErroCampo(el, '');
            return true;
        }

        // 10. Datas
        if (el.type === 'date') {
            if (val === '') {
                if (isBlurOrSubmit && el.required) {
                    definirErroCampo(el, 'Selecione uma data válida.');
                    return false;
                }
                definirErroCampo(el, '');
                return true;
            }
            if (name === 'data_nascimento') {
                var hoje = new Date().toISOString().slice(0, 10);
                if (val >= hoje) {
                    definirErroCampo(el, 'A data de nascimento deve ser anterior à data de hoje.');
                    return false;
                }
                if (val < '1900-01-01') {
                    definirErroCampo(el, 'Informe uma data de nascimento posterior a 01/01/1900.');
                    return false;
                }
            }
            if (name === 'data' && el.min && val < el.min) {
                definirErroCampo(el, 'A data do agendamento não pode ser no passado.');
                return false;
            }
            definirErroCampo(el, '');
            return true;
        }

        // 11. E-mail
        if (el.type === 'email' && val !== '' && isBlurOrSubmit) {
            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val)) {
                definirErroCampo(el, 'Informe um endereço de e-mail válido (ex: nome@dominio.com).');
                return false;
            }
        }

        // 12. Campos obrigatórios gerais no blur/submit
        if (isBlurOrSubmit && el.required && val === '') {
            definirErroCampo(el, 'Preencha este campo para continuar.');
            return false;
        }

        definirErroCampo(el, '');
        return true;
    }

    document.addEventListener('DOMContentLoaded', function() {
        var toggleBtn = document.getElementById('sidebarToggle');
        var sidebar = document.querySelector('.app-sidebar');

        if (toggleBtn) {
            toggleBtn.addEventListener('click', function() {
                if (window.innerWidth <= 992) {
                    sidebar.classList.toggle('show-mobile');
                } else {
                    document.body.classList.toggle('sidebar-compact');
                }
            });
        }

        // Formatação inteligente de CPF (preserva sinal negativo ou letras para exibir o tratamento de erro)
        document.querySelectorAll('input[name="cpf"]').forEach(function(el) {
            el.addEventListener('input', function() {
                var raw = this.value;
                if (!/^-/.test(raw.trim()) && !/[a-zA-Z]/.test(raw)) {
                    var v = raw.replace(/\D/g, '').slice(0, 11);
                    if (v.length > 9) v = v.replace(/(\d{3})(\d{3})(\d{3})(\d{1,2})/, '$1.$2.$3-$4');
                    else if (v.length > 6) v = v.replace(/(\d{3})(\d{3})(\d{1,3})/, '$1.$2.$3');
                    else if (v.length > 3) v = v.replace(/(\d{3})(\d{1,3})/, '$1.$2');
                    this.value = v;
                }
                validarCampoIndividual(this, false);
            });
        });

        // Formatação inteligente de Telefone
        document.querySelectorAll('input[name="telefone"], input[name="contato_emergencia_telefone"]').forEach(function(el) {
            el.addEventListener('input', function() {
                var raw = this.value;
                if (!/^-/.test(raw.trim()) && !/[a-zA-Z]/.test(raw)) {
                    var v = raw.replace(/\D/g, '').slice(0, 11);
                    if (v.length > 10) v = v.replace(/^(\d{2})(\d{5})(\d{4}).*/, '($1) $2-$3');
                    else if (v.length > 6) v = v.replace(/^(\d{2})(\d{4})(\d{0,4}).*/, '($1) $2-$3');
                    else if (v.length > 2) v = v.replace(/^(\d{2})(\d{0,5})/, '($1) $2');
                    this.value = v;
                }
                validarCampoIndividual(this, false);
            });
        });

        // Listagem dinâmica de Cidades por UF e preenchimento automático via CEP (Correios / IBGE)
        var cidadesBasePorUf = @json(\App\Http\Controllers\PacienteController::CIDADES_POR_UF);
        var cacheCidadesIbge = {};

        function normalizarComparacao(str) {
            return (str || '').toString().trim().normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase();
        }

        function popularOpcoesCidade(selectCidade, listaCidades, cidadeAlvo) {
            if (!selectCidade) return;
            var alvo = (cidadeAlvo !== undefined && cidadeAlvo !== null) ? cidadeAlvo : selectCidade.value;
            var alvoNorm = normalizarComparacao(alvo);

            var opcoes = [];
            var vistos = {};

            if (alvo && alvo.trim() !== '') {
                opcoes.push(alvo.trim());
                vistos[alvoNorm] = true;
            }

            (listaCidades || []).forEach(function(nomeCid) {
                var n = normalizarComparacao(nomeCid);
                if (n && !vistos[n]) {
                    vistos[n] = true;
                    opcoes.push(nomeCid);
                } else if (n && vistos[n] && opcoes[0] && normalizarComparacao(opcoes[0]) === n) {
                    opcoes[0] = nomeCid;
                }
            });

            selectCidade.innerHTML = '';
            var optVazia = document.createElement('option');
            optVazia.value = '';
            optVazia.textContent = 'Selecione a cidade';
            selectCidade.appendChild(optVazia);

            var valorSelecionado = '';
            opcoes.forEach(function(cid) {
                var opt = document.createElement('option');
                opt.value = cid;
                opt.textContent = cid;
                if (alvoNorm && normalizarComparacao(cid) === alvoNorm) {
                    opt.selected = true;
                    valorSelecionado = cid;
                }
                selectCidade.appendChild(opt);
            });

            if (valorSelecionado) {
                selectCidade.value = valorSelecionado;
            }
        }

        function carregarCidadesNoSelect(selectCidade, uf, cidadeParaSelecionar) {
            if (!selectCidade) return;
            var sigla = (uf || '').toUpperCase().trim();
            var base = cidadesBasePorUf[sigla] ? cidadesBasePorUf[sigla].slice() : [];

            popularOpcoesCidade(selectCidade, cacheCidadesIbge[sigla] || base, cidadeParaSelecionar);

            if (sigla.length === 2 && !cacheCidadesIbge[sigla]) {
                fetch('https://servicodados.ibge.gov.br/api/v1/localidades/estados/' + sigla + '/municipios?orderBy=nome')
                    .then(function(r) { return r.ok ? r.json() : null; })
                    .then(function(lista) {
                        if (Array.isArray(lista) && lista.length > 0) {
                            cacheCidadesIbge[sigla] = lista.map(function(m) { return m.nome; });
                            var cidadeAtual = selectCidade.value || cidadeParaSelecionar || '';
                            popularOpcoesCidade(selectCidade, cacheCidadesIbge[sigla], cidadeAtual);
                        }
                    })
                    .catch(function() {});
            }
        }

        document.querySelectorAll('select[name="estado"]').forEach(function(selectEstado) {
            var form = selectEstado.form;
            var selectCidade = form ? form.querySelector('select[name="cidade"]') : null;

            if (selectCidade && selectEstado.value) {
                var cidInicial = selectCidade.getAttribute('data-cidade-inicial') || selectCidade.value || '';
                carregarCidadesNoSelect(selectCidade, selectEstado.value, cidInicial);
            }

            selectEstado.addEventListener('change', function() {
                if (!form) return;
                var ufNova = this.value;
                var cidadeVinculadaCep = (form.dataset.correiosUf && form.dataset.correiosUf.toUpperCase() === ufNova.toUpperCase())
                    ? (form.dataset.correiosCidade || '')
                    : '';
                if (selectCidade) {
                    carregarCidadesNoSelect(selectCidade, ufNova, cidadeVinculadaCep);
                    validarCampoIndividual(selectCidade, true);
                }
                validarCampoIndividual(this, true);
            });
        });

        // Formatação inteligente de CEP e preenchimento automático de Cidade, UF, Logradouro e Bairro nos Correios
        function aplicarResultadoCep(elCep, data) {
            var form = elCep.form;
            if (!data.offline && !data.encontrado) {
                elCep.dataset.cepInvalido = '1';
                elCep.dataset.cepMensagem = data.mensagem || 'O CEP informado não existe na base dos Correios.';
                if (form) {
                    delete form.dataset.correiosUf;
                    delete form.dataset.correiosCidade;
                }
                definirErroCampo(elCep, elCep.dataset.cepMensagem);
                return;
            }

            elCep.dataset.cepInvalido = '';
            elCep.dataset.cepMensagem = '';
            definirErroCampo(elCep, '');

            if (data.encontrado && form) {
                form.dataset.correiosUf = data.uf;
                form.dataset.correiosCidade = data.cidade;

                var selectEstado = form.querySelector('select[name="estado"], input[name="estado"]');
                var campoCidade = form.querySelector('select[name="cidade"], input[name="cidade"]');
                var inputEndereco = form.querySelector('input[name="endereco"]');
                var inputBairro = form.querySelector('input[name="bairro"]');

                if (selectEstado) {
                    selectEstado.value = data.uf;
                    validarCampoIndividual(selectEstado, true);
                }
                if (campoCidade) {
                    if (campoCidade.tagName === 'SELECT') {
                        carregarCidadesNoSelect(campoCidade, data.uf, data.cidade);
                    } else {
                        campoCidade.value = data.cidade;
                    }
                    validarCampoIndividual(campoCidade, true);
                }
                if (inputEndereco && data.endereco) {
                    inputEndereco.value = data.endereco;
                }
                if (inputBairro && data.bairro) {
                    inputBairro.value = data.bairro;
                }
            }
        }

        function consultarViaCepNavegador(elCep, digitos) {
            fetch('https://viacep.com.br/ws/' + digitos + '/json/')
                .then(function(r) { return r.ok ? r.json() : null; })
                .then(function(json) {
                    if (!json) return;
                    if (json.erro) {
                        aplicarResultadoCep(elCep, {
                            encontrado: false,
                            offline: false,
                            mensagem: 'O CEP informado não foi encontrado na base dos Correios.'
                        });
                    } else if (json.uf && json.localidade) {
                        aplicarResultadoCep(elCep, {
                            encontrado: true,
                            offline: false,
                            uf: json.uf.toUpperCase(),
                            cidade: json.localidade,
                            endereco: json.logradouro || '',
                            bairro: json.bairro || ''
                        });
                    }
                })
                .catch(function() {});
        }

        function consultarCepCorreios(elCep, forcar) {
            var digitos = (elCep.value || '').replace(/\D/g, '');
            if (digitos.length !== 8) return;
            if (!forcar && elCep.dataset.ultimoCepConsultado === digitos) return;
            elCep.dataset.ultimoCepConsultado = digitos;

            fetch('/cep/' + digitos, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(function(res) { return res.json(); })
            .then(function(data) {
                if (data && data.offline) {
                    consultarViaCepNavegador(elCep, digitos);
                    return;
                }
                aplicarResultadoCep(elCep, data);
            })
            .catch(function() {
                consultarViaCepNavegador(elCep, digitos);
            });
        }

        document.querySelectorAll('input[name="cep"]').forEach(function(el) {
            function tratarAlteracaoCep(forcarConsulta) {
                var raw = el.value || '';
                el.dataset.cepInvalido = '';
                if (!/^-/.test(raw.trim()) && !/[a-zA-Z]/.test(raw)) {
                    var v = raw.replace(/\D/g, '').slice(0, 8);
                    if (v.length < 8) {
                        el.dataset.ultimoCepConsultado = '';
                    }
                    if (v.length > 5) v = v.replace(/^(\d{5})(\d{1,3})/, '$1-$2');
                    el.value = v;
                    if (v.replace(/\D/g, '').length === 8) {
                        consultarCepCorreios(el, forcarConsulta);
                    }
                }
                validarCampoIndividual(el, false);
            }

            el.addEventListener('input', function() { tratarAlteracaoCep(false); });
            el.addEventListener('change', function() { tratarAlteracaoCep(true); });
            el.addEventListener('blur', function() {
                if ((this.value || '').replace(/\D/g, '').length === 8) {
                    consultarCepCorreios(this, false);
                }
            });

            // Caso o navegador preencha automaticamente (autofill) ou já exista um CEP no carregamento
            if ((el.value || '').replace(/\D/g, '').length === 8) {
                consultarCepCorreios(el, true);
            }
        });

        // Listagem de Convênios com possibilidade de novos cadastros em tempo real
        document.querySelectorAll('.select-convenio').forEach(function(selectEl) {
            var group = selectEl.closest('.form-group');
            if (!group) return;
            var box = group.querySelector('.novo-convenio-box');
            var inputNovo = group.querySelector('.input-novo-convenio');
            var btnSalvar = group.querySelector('.btn-salvar-convenio');
            var btnCancelar = group.querySelector('.btn-cancelar-convenio');

            function confirmarNovoConvenio() {
                var nome = (inputNovo.value || '').trim();
                if (nome.length < 2 || /^-/.test(nome)) {
                    definirErroCampo(selectEl, 'Informe um nome válido para o novo convênio (mínimo 2 letras).');
                    inputNovo.focus();
                    return false;
                }

                var optExistente = Array.from(selectEl.options).find(function(o) {
                    return o.value.toLowerCase() === nome.toLowerCase();
                });

                if (!optExistente) {
                    var novaOpt = document.createElement('option');
                    novaOpt.value = nome;
                    novaOpt.textContent = nome;
                    var optNovo = selectEl.querySelector('option[value="__novo__"]');
                    selectEl.insertBefore(novaOpt, optNovo);
                    selectEl.value = nome;
                } else {
                    selectEl.value = optExistente.value;
                }

                inputNovo.value = '';
                box.classList.add('d-none');
                definirErroCampo(selectEl, '');

                var csrfMeta = document.querySelector('meta[name="csrf-token"]');
                if (csrfMeta) {
                    fetch('/convenios', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfMeta.getAttribute('content')
                        },
                        body: JSON.stringify({ nome: nome })
                    }).catch(function() {});
                }
                return true;
            }

            selectEl.addEventListener('change', function() {
                if (this.value === '__novo__') {
                    box.classList.remove('d-none');
                    inputNovo.focus();
                } else {
                    box.classList.add('d-none');
                    definirErroCampo(selectEl, '');
                }
            });

            if (btnSalvar) {
                btnSalvar.addEventListener('click', confirmarNovoConvenio);
            }

            if (inputNovo) {
                inputNovo.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        confirmarNovoConvenio();
                    }
                });
            }

            if (btnCancelar) {
                btnCancelar.addEventListener('click', function() {
                    inputNovo.value = '';
                    box.classList.add('d-none');
                    selectEl.selectedIndex = 0;
                    definirErroCampo(selectEl, '');
                });
            }

            if (selectEl.form) {
                selectEl.form.addEventListener('submit', function(e) {
                    if (selectEl.value === '__novo__') {
                        if (!confirmarNovoConvenio()) {
                            e.preventDefault();
                        }
                    }
                });
            }
        });

        // Monitoramento em tempo real de todos os inputs, selects e textareas
        document.querySelectorAll('form input, form select, form textarea').forEach(function(el) {
            el.addEventListener('input', function() {
                validarCampoIndividual(this, false);
            });
            el.addEventListener('blur', function() {
                validarCampoIndividual(this, true);
            });
            el.addEventListener('change', function() {
                validarCampoIndividual(this, true);
            });
        });

        // Interceptação de envio de formulário para exibir tratamentos em todos os campos inválidos
        document.querySelectorAll('form[data-validate="true"]').forEach(function(form) {
            form.addEventListener('submit', function(e) {
                var primeiroInvalido = null;
                var elementos = form.querySelectorAll('input, select, textarea');
                elementos.forEach(function(el) {
                    var ok = validarCampoIndividual(el, true);
                    if (!ok && !primeiroInvalido) {
                        primeiroInvalido = el;
                    }
                });
                if (primeiroInvalido) {
                    e.preventDefault();
                    primeiroInvalido.focus();
                }
            });
        });
    });
</script>
@stack('scripts')
</body>
</html>
