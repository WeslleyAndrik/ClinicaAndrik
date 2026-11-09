<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Sistema de Clínica Médica (Laravel 7)
|--------------------------------------------------------------------------
*/

// Redirecionamento da raiz
Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

// Autenticação
Route::get('login', 'AuthController@showLoginForm')->name('login');
Route::post('login', 'AuthController@login')->name('login.post');
Route::post('logout', 'AuthController@logout')->name('logout');

// Rotas Autenticadas
Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/dashboard', 'DashboardController@index')->name('dashboard');

    // Perfil do Usuário
    Route::get('/perfil', 'AuthController@profile')->name('perfil');
    Route::put('/perfil', 'AuthController@updateProfile')->name('perfil.update');

    // Gestão de Pacientes (Clientes), Consulta de CEP nos Correios e Cadastro de Convênios
    Route::get('/cep/{cep}', 'PacienteController@consultarCep')->name('cep.consultar');
    Route::post('/convenios', 'PacienteController@salvarConvenio')->name('convenios.store');
    Route::resource('pacientes', 'PacienteController');

    // Histórico Clínico do Paciente
    Route::get('/pacientes/{id}/historico', 'HistoricoController@paciente')->name('pacientes.historico');

    // Agendamento e Gestão de Consultas
    Route::resource('consultas', 'ConsultaController');
    Route::patch('/consultas/{id}/status', 'ConsultaController@updateStatus')->name('consultas.status');
    Route::post('/consultas/{id}/whatsapp', 'ConsultaController@enviarWhatsapp')->name('consultas.whatsapp');

    // Prontuário Médico
    Route::resource('prontuarios', 'ProntuarioController')->except(['index', 'destroy']);

    // Relatórios de Atendimento e Estatísticas
    Route::get('/relatorios', 'RelatorioController@index')->name('relatorios.index');

    // Gestão de Profissionais e Usuários (Apenas Administrador)
    Route::middleware(['role:admin'])->group(function () {
        Route::resource('usuarios', 'UsuarioController');
    });
});
