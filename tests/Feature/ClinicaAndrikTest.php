<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use App\User;
use App\Paciente;
use App\Consulta;
use App\Prontuario;

class ClinicaAndrikTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate');
    }

    public function test_usuarios_criacao_e_papeis_corretos()
    {
        $admin = User::create([
            'name' => 'Diretor Andrik',
            'email' => 'admin@clinicaandrik.com.br',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'crm' => 'CRM/PB 8412',
            'especialidade' => 'Direção Clínica',
            'ativo' => true,
        ]);

        $medico = User::create([
            'name' => 'Carlos Mendes',
            'email' => 'carlos@clinicaandrik.com.br',
            'password' => Hash::make('password'),
            'role' => 'medico',
            'crm' => 'CRM/SP 148920',
            'especialidade' => 'Cardiologia',
            'ativo' => true,
        ]);

        $recepcao = User::create([
            'name' => 'Juliana Recepção',
            'email' => 'recepcao@clinicaandrik.com.br',
            'password' => Hash::make('password'),
            'role' => 'recepcao',
            'ativo' => true,
        ]);

        $this->assertTrue($admin->isAdmin());
        $this->assertFalse($admin->isMedico());
        $this->assertTrue($medico->isMedico());
        $this->assertEquals('CRM/SP 148920', $medico->crm);
        $this->assertTrue($recepcao->isRecepcao());

        $medicosAtivos = User::medicos()->get();
        $this->assertCount(1, $medicosAtivos);
        $this->assertEquals('Dr. Carlos Mendes', $medicosAtivos->first()->name);
    }

    public function test_autenticacao_login_e_logout()
    {
        $user = User::create([
            'name' => 'Dr. Roberto',
            'email' => 'dr.roberto@clinicaandrik.com.br',
            'password' => Hash::make('secret123'),
            'role' => 'medico',
            'crm' => 'CRM/SP 55555',
            'especialidade' => 'Clínica Médica',
            'ativo' => true,
        ]);

        $response = $this->post('/login', [
            'email' => 'dr.roberto@clinicaandrik.com.br',
            'password' => 'secret123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);

        $responseLogout = $this->post('/logout');
        $responseLogout->assertRedirect('/login');
        $this->assertGuest();

        $responseInvalido = $this->post('/login', [
            'email' => 'dr.roberto@clinicaandrik.com.br',
            'password' => 'senha_errada',
        ]);
        $responseInvalido->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_autenticacao_bloqueio_usuario_inativo()
    {
        User::create([
            'name' => 'Médico Inativo',
            'email' => 'inativo@clinicaandrik.com.br',
            'password' => Hash::make('password'),
            'role' => 'medico',
            'ativo' => false,
        ]);

        $response = $this->post('/login', [
            'email' => 'inativo@clinicaandrik.com.br',
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_controle_de_acesso_por_perfil()
    {
        $recepcao = User::create([
            'name' => 'Atendente Luiza',
            'email' => 'luiza@clinicaandrik.com.br',
            'password' => Hash::make('password'),
            'role' => 'recepcao',
            'ativo' => true,
        ]);

        $response = $this->actingAs($recepcao)->get('/usuarios');
        $response->assertRedirect('/dashboard');
        $response->assertSessionHas('error');

        $admin = User::create([
            'name' => 'Diretor Andrik',
            'email' => 'diretor@clinicaandrik.com.br',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'ativo' => true,
        ]);

        $responseAdmin = $this->actingAs($admin)->get('/usuarios');
        $responseAdmin->assertStatus(200);
    }

    public function test_clientes_cadastro_idade_e_busca()
    {
        $admin = User::create([
            'name' => 'Admin Andrik',
            'email' => 'adm@clinicaandrik.com.br',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'ativo' => true,
        ]);

        Http::fake([
            '*viacep.com.br/ws/58030021/json/*' => Http::response([
                'cep' => '58030-021',
                'logradouro' => 'Avenida Rio Grande do Sul',
                'bairro' => 'Estados',
                'localidade' => 'João Pessoa',
                'uf' => 'PB',
            ], 200),
        ]);

        $dadosPaciente = [
            'nome' => 'Marcos Vinicius de Souza',
            'cpf' => '284.918.372-56',
            'rg' => '41.829.301-8',
            'data_nascimento' => '1990-05-20',
            'sexo' => 'M',
            'telefone' => '(11) 98122-3344',
            'email' => 'marcos@email.com',
            'tipo_sanguineo' => 'O+',
            'alergias' => 'Dipirona e Frutos do mar',
            'medicamentos_uso_continuo' => 'Losartana 50mg',
            'convenio' => 'Unimed',
            'cep' => '58030-021',
            'endereco' => 'Av. Rio Grande do Sul',
            'numero' => '1558',
            'bairro' => 'Estados',
            'cidade' => 'João Pessoa',
            'estado' => 'PB',
        ];

        $this->actingAs($admin)->post('/pacientes', $dadosPaciente);
        $this->assertDatabaseHas('pacientes', ['cpf' => '284.918.372-56', 'numero' => '1558']);

        $paciente = Paciente::where('cpf', '284.918.372-56')->first();
        $this->assertNotNull($paciente);
        $this->assertGreaterThan(30, $paciente->idade);

        $buscaNome = Paciente::buscar('Marcos')->get();
        $this->assertCount(1, $buscaNome);

        $buscaCpf = Paciente::buscar('28491837256')->get();
        $this->assertCount(1, $buscaCpf);
    }

    public function test_validacoes_de_campos_cpf_numero_endereco_cep_e_uf()
    {
        $admin = User::create([
            'name' => 'Admin Andrik',
            'email' => 'adm.val@clinicaandrik.com.br',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'ativo' => true,
        ]);

        // 1. CPF inválido (dígitos verificadores incorretos ou repetidos)
        $resCpfInvalido = $this->actingAs($admin)->post('/pacientes', [
            'nome' => 'Paciente Teste',
            'cpf' => '111.111.111-11',
            'data_nascimento' => '1990-01-10',
            'sexo' => 'M',
            'telefone' => '(83) 98888-7777',
        ]);
        $resCpfInvalido->assertSessionHasErrors('cpf');

        // 2. Número de endereço com letras, zero ou negativo e valores negativos em outros campos
        $resNumeroInvalido = $this->actingAs($admin)->post('/pacientes', [
            'nome' => 'Paciente Teste',
            'cpf' => '284.918.372-56',
            'data_nascimento' => '1990-01-10',
            'sexo' => 'M',
            'telefone' => '(83) 98888-7777',
            'numero' => '123AB',
            'cep' => '123',
            'estado' => 'XX',
        ]);
        $resNumeroInvalido->assertSessionHasErrors(['numero', 'cep', 'estado']);

        $resNegativos = $this->actingAs($admin)->post('/pacientes', [
            'nome' => 'Paciente Negativo',
            'cpf' => '-28491837256',
            'rg' => '-41829301',
            'numero_carteirinha' => '-998877',
            'data_nascimento' => '1990-01-10',
            'sexo' => 'M',
            'telefone' => '(83) 98888-7777',
            'numero' => '-1558',
            'cep' => '-58030000',
        ]);
        $resNegativos->assertSessionHasErrors(['cpf', 'rg', 'numero_carteirinha', 'numero', 'cep']);

        // 3. Validação na API dos Correios: CEP inexistente e divergência de UF / Cidade
        Http::fake([
            '*viacep.com.br/ws/99999999/json/*' => Http::response(['erro' => true], 200),
            '*viacep.com.br/ws/58030021/json/*' => Http::response([
                'cep' => '58030-021',
                'logradouro' => 'Avenida Rio Grande do Sul',
                'bairro' => 'Estados',
                'localidade' => 'João Pessoa',
                'uf' => 'PB',
            ], 200),
        ]);

        $resCepInexistente = $this->actingAs($admin)->post('/pacientes', [
            'nome' => 'Paciente CEP Invalido',
            'cpf' => '284.918.372-56',
            'data_nascimento' => '1990-01-10',
            'sexo' => 'M',
            'telefone' => '(83) 98888-7777',
            'cep' => '99999-999',
        ]);
        $resCepInexistente->assertSessionHasErrors('cep');

        $resDivergenciaUfCidade = $this->actingAs($admin)->post('/pacientes', [
            'nome' => 'Paciente UF Divergente',
            'cpf' => '284.918.372-56',
            'data_nascimento' => '1990-01-10',
            'sexo' => 'M',
            'telefone' => '(83) 98888-7777',
            'cep' => '58030-021',
            'cidade' => 'São Paulo',
            'estado' => 'SP',
        ]);
        $resDivergenciaUfCidade->assertSessionHasErrors(['cidade', 'estado']);

        $resConsultaJson = $this->actingAs($admin)->getJson('/cep/58030021');
        $resConsultaJson->assertStatus(200)->assertJson([
            'encontrado' => true,
            'cidade' => 'João Pessoa',
            'uf' => 'PB',
        ]);
    }

    public function test_especifico_1_agendamento_e_conflito_de_horarios()
    {
        $admin = User::create([
            'name' => 'Admin Andrik',
            'email' => 'admin2@clinicaandrik.com.br',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'ativo' => true,
        ]);

        $medico = User::create([
            'name' => 'Dr. André Vinicius',
            'email' => 'dr.andre@clinicaandrik.com.br',
            'password' => Hash::make('password'),
            'role' => 'medico',
            'crm' => 'CRM/SP 129481',
            'especialidade' => 'Ortopedia',
            'ativo' => true,
        ]);

        $paciente1 = Paciente::create([
            'nome' => 'Larissa Guimarães',
            'cpf' => '441.928.375-01',
            'data_nascimento' => '1998-09-20',
            'sexo' => 'F',
            'telefone' => '(11) 98877-6655',
            'convenio' => 'Particular',
        ]);

        $paciente2 = Paciente::create([
            'nome' => 'Fernando Henrique Lima',
            'cpf' => '312.456.789-73',
            'data_nascimento' => '1976-08-12',
            'sexo' => 'M',
            'telefone' => '(11) 97654-3210',
            'convenio' => 'Amil',
        ]);

        $dataConsulta = Carbon::tomorrow()->format('Y-m-d');

        Http::fake([
            '*message/sendText/*' => Http::response(['status' => 'PENDING'], 201),
        ]);

        config([
            'services.evolution.url' => 'http://localhost:8081',
            'services.evolution.apikey' => 'chave_teste_evolution',
            'services.evolution.instance' => 'clinica_andrik',
        ]);

        $dadosConsulta1 = [
            'paciente_id' => $paciente1->id,
            'medico_id' => $medico->id,
            'data' => $dataConsulta,
            'hora' => '14:00',
            'duracao_minutos' => 30,
            'especialidade' => 'Ortopedia',
            'tipo_atendimento' => 'primeira_consulta',
            'convenio' => 'Particular',
            'valor' => 300.00,
            'motivo' => 'Dor no joelho direito após trauma esportivo.',
        ];

        $this->actingAs($admin)->post('/consultas', $dadosConsulta1);
        $this->assertDatabaseHas('consultas', [
            'paciente_id' => $paciente1->id,
            'medico_id' => $medico->id,
            'status' => 'agendada',
        ]);

        Http::assertSent(function ($request) {
            return strpos($request->url(), 'http://localhost:8081/message/sendText/clinica_andrik') !== false
                && $request->hasHeader('apikey', 'chave_teste_evolution')
                && $request['number'] === '5511988776655'
                && strpos($request['text'], 'Larissa Guimarães') !== false;
        });

        $dadosConsultaConflito = [
            'paciente_id' => $paciente2->id,
            'medico_id' => $medico->id,
            'data' => $dataConsulta,
            'hora' => '14:00',
            'duracao_minutos' => 30,
            'especialidade' => 'Ortopedia',
            'tipo_atendimento' => 'rotina',
            'convenio' => 'Amil',
            'valor' => 250.00,
        ];

        $responseConflito = $this->actingAs($admin)->post('/consultas', $dadosConsultaConflito);
        $responseConflito->assertSessionHasErrors('hora');
        $this->assertEquals(1, Consulta::where('medico_id', $medico->id)->count());

        $consulta = Consulta::where('paciente_id', $paciente1->id)->first();
        $this->actingAs($admin)->patch("/consultas/{$consulta->id}/status", ['status' => 'aguardando']);
        $this->assertEquals('aguardando', $consulta->fresh()->status);
    }

    public function test_especifico_2_prontuario_medico_imc_e_finalizacao()
    {
        $medico = User::create([
            'name' => 'Dra. Mariana Alencar',
            'email' => 'dra.mariana@clinicaandrik.com.br',
            'password' => Hash::make('password'),
            'role' => 'medico',
            'crm' => 'CRM/SP 173402',
            'especialidade' => 'Dermatologia',
            'ativo' => true,
        ]);

        $paciente = Paciente::create([
            'nome' => 'Beatriz Helena de Castro',
            'cpf' => '392.104.856-70',
            'data_nascimento' => '1992-11-28',
            'sexo' => 'F',
            'telefone' => '(11) 97412-8822',
            'convenio' => 'Bradesco Saúde',
        ]);

        $consulta = Consulta::create([
            'paciente_id' => $paciente->id,
            'medico_id' => $medico->id,
            'data_hora' => Carbon::now(),
            'duracao_minutos' => 30,
            'especialidade' => 'Dermatologia',
            'tipo_atendimento' => 'primeira_consulta',
            'status' => 'em_atendimento',
            'convenio' => 'Bradesco Saúde',
            'motivo' => 'Lesões eritematosas em mãos',
        ]);

        $dadosProntuario = [
            'paciente_id' => $paciente->id,
            'consulta_id' => $consulta->id,
            'queixa_principal' => 'Eritema e prurido intenso no dorso das mãos há 3 semanas.',
            'historia_doenca_atual' => 'Quadro iniciou após lavagem frequente e uso de álcool gel.',
            'pressao_arterial' => '120/80',
            'frequencia_cardiaca' => 70,
            'temperatura' => 36.5,
            'peso' => 60.00,
            'altura' => 1.65,
            'exame_fisico' => 'Placas eritematosas descamativas sem secreção ativa.',
            'diagnostico' => 'Dermatite de Contato por Irritante Primário',
            'cid10' => 'L24',
            'prescricao_medica' => "1. Desonida creme 0,05% - aplicar 2x/dia por 7 dias.\n2. Hidratante neutro.",
            'exames_solicitados' => 'Nenhum exame invasivo no momento.',
            'conduta_plano' => 'Uso de luvas de proteção em afazeres domésticos. Retorno em 20 dias.',
        ];

        $this->actingAs($medico)->post('/prontuarios', $dadosProntuario);

        $this->assertDatabaseHas('prontuarios', [
            'paciente_id' => $paciente->id,
            'diagnostico' => 'Dermatite de Contato por Irritante Primário',
            'cid10' => 'L24',
        ]);

        $prontuario = Prontuario::where('paciente_id', $paciente->id)->first();
        $this->assertEquals(22.0, $prontuario->imc);
        $this->assertEquals('Peso normal', $prontuario->imc_classificacao);
        $this->assertEquals('finalizada', $consulta->fresh()->status);
    }

    public function test_especifico_3_historico_consultas_e_timeline()
    {
        $medico = User::create([
            'name' => 'Dr. Carlos Mendes',
            'email' => 'dr.carlos3@clinicaandrik.com.br',
            'password' => Hash::make('password'),
            'role' => 'medico',
            'crm' => 'CRM/SP 148920',
            'especialidade' => 'Cardiologia',
            'ativo' => true,
        ]);

        $paciente = Paciente::create([
            'nome' => 'Antônio Carlos Moreira',
            'cpf' => '109.837.261-16',
            'data_nascimento' => '1960-03-04',
            'sexo' => 'M',
            'telefone' => '(11) 99341-5520',
            'convenio' => 'SulAmérica',
        ]);

        $data1 = Carbon::now()->subMonths(3);
        $consulta1 = Consulta::create([
            'paciente_id' => $paciente->id,
            'medico_id' => $medico->id,
            'data_hora' => $data1,
            'duracao_minutos' => 30,
            'especialidade' => 'Cardiologia',
            'tipo_atendimento' => 'primeira_consulta',
            'status' => 'finalizada',
            'motivo' => 'Check-up inicial de pressão alta',
        ]);

        Prontuario::create([
            'paciente_id' => $paciente->id,
            'medico_id' => $medico->id,
            'consulta_id' => $consulta1->id,
            'data_registro' => $data1,
            'queixa_principal' => 'Cefaleia e picos de PA 150/90 mmHg.',
            'diagnostico' => 'Hipertensão Arterial Sistêmica Estágio 1',
            'cid10' => 'I10',
            'prescricao_medica' => 'Losartana 50mg pela manhã.',
        ]);

        $response = $this->actingAs($medico)->get("/pacientes/{$paciente->id}/historico");
        $response->assertStatus(200);
        $response->assertSee('Antônio Carlos Moreira');
        $response->assertSee('Hipertensão Arterial Sistêmica Estágio 1');
    }

    public function test_relatorios_atendimento_filtro_medico_e_paciente()
    {
        $medico1 = User::create([
            'name' => 'Dr. Carlos Eduardo Mendes',
            'email' => 'carlos.rel@clinicaandrik.com.br',
            'password' => Hash::make('password'),
            'role' => 'medico',
            'crm' => 'CRM/SP 142850',
            'especialidade' => 'Cardiologia',
            'ativo' => true,
        ]);

        $medico2 = User::create([
            'name' => 'Dra. Mariana Alencar Ribeiro',
            'email' => 'mariana.rel@clinicaandrik.com.br',
            'password' => Hash::make('password'),
            'role' => 'medico',
            'crm' => 'CRM/SP 173402',
            'especialidade' => 'Dermatologia',
            'ativo' => true,
        ]);

        $paciente1 = Paciente::create([
            'nome' => 'Larissa Prado Guimarães',
            'cpf' => '284.918.372-56',
            'data_nascimento' => '1998-05-14',
            'sexo' => 'F',
            'telefone' => '(11) 98765-4321',
            'convenio' => 'Unimed',
        ]);

        $paciente2 = Paciente::create([
            'nome' => 'Fernando Henrique Lima',
            'cpf' => '312.456.789-73',
            'data_nascimento' => '1976-11-20',
            'sexo' => 'M',
            'telefone' => '(11) 91234-5678',
            'convenio' => 'Bradesco Saúde',
        ]);

        Consulta::create([
            'paciente_id' => $paciente1->id,
            'medico_id' => $medico1->id,
            'data_hora' => Carbon::now()->subDays(2),
            'duracao_minutos' => 30,
            'especialidade' => 'Cardiologia',
            'tipo_atendimento' => 'rotina',
            'status' => 'finalizada',
            'convenio' => 'Unimed',
            'valor' => 250.00,
            'motivo' => 'Avaliação cardiológica',
        ]);

        Consulta::create([
            'paciente_id' => $paciente2->id,
            'medico_id' => $medico2->id,
            'data_hora' => Carbon::now()->subDays(1),
            'duracao_minutos' => 30,
            'especialidade' => 'Dermatologia',
            'tipo_atendimento' => 'primeira_consulta',
            'status' => 'finalizada',
            'convenio' => 'Bradesco Saúde',
            'valor' => 300.00,
            'motivo' => 'Alergia de contato',
        ]);

        $responseMedico = $this->actingAs($medico1)->get("/relatorios?medico_id={$medico1->id}");
        $responseMedico->assertStatus(200);
        $responseMedico->assertSee('Médico em Foco');
        $responseMedico->assertSee('Dr. Carlos Eduardo Mendes');

        $responsePaciente = $this->actingAs($medico1)->get("/relatorios?paciente_id={$paciente2->id}");
        $responsePaciente->assertStatus(200);
        $responsePaciente->assertSee('Paciente / Cliente em Foco');
        $responsePaciente->assertSee('Fernando Henrique Lima');
    }
}
