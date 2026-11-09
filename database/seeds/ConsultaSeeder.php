<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;
use App\Consulta;
use App\Paciente;
use App\User;

class ConsultaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $drPedro = User::where('email', 'pedro.miguel@clinicaandrik.com.br')->first();
        $drMauricio = User::where('email', 'mauricio.cunha@clinicaandrik.com.br')->first();
        $drVinicius = User::where('email', 'vinicius.oliveira@clinicaandrik.com.br')->first();
        $drJuliane = User::where('email', 'dr.juliane@clinicaandrik.com.br')->first();
        $drAndrik = User::where('email', 'admin@clinicaandrik.com.br')->first();

        $marcos = Paciente::where('cpf', '284.918.372-56')->first();
        $beatriz = Paciente::where('cpf', '392.104.856-70')->first();
        $antonio = Paciente::where('cpf', '109.837.261-16')->first();
        $larissa = Paciente::where('cpf', '441.928.375-01')->first();
        $fernando = Paciente::where('cpf', '312.456.789-73')->first();

        $hoje = Carbon::today();

        $consultas = [
            // Consultas Passadas (Finalizadas com prontuário)
            [
                'paciente_id' => $marcos->id,
                'medico_id' => $drPedro->id,
                'data_hora' => Carbon::now()->subMonths(2)->setTime(10, 0),
                'duracao_minutos' => 30,
                'especialidade' => 'Cardiologia',
                'tipo_atendimento' => 'primeira_consulta',
                'status' => 'finalizada',
                'motivo' => 'Avaliação cardiológica após episódio de tontura e picos hipertensivos no trabalho.',
                'convenio' => 'Unimed',
                'valor' => 250.00,
                'observacoes' => 'Paciente compareceu pontualmente com exames laboratoriais prévios.',
            ],
            [
                'paciente_id' => $marcos->id,
                'medico_id' => $drPedro->id,
                'data_hora' => Carbon::now()->subDays(15)->setTime(14, 30),
                'duracao_minutos' => 30,
                'especialidade' => 'Cardiologia',
                'tipo_atendimento' => 'retorno',
                'status' => 'finalizada',
                'motivo' => 'Retorno de rotina para apresentação de MAPA e Holter 24h.',
                'convenio' => 'Unimed',
                'valor' => 0.00,
                'observacoes' => 'Níveis pressóricos controlados com Losartana.',
            ],
            [
                'paciente_id' => $beatriz->id,
                'medico_id' => $drMauricio->id,
                'data_hora' => Carbon::now()->subWeeks(3)->setTime(11, 0),
                'duracao_minutos' => 40,
                'especialidade' => 'Dermatologia',
                'tipo_atendimento' => 'primeira_consulta',
                'status' => 'finalizada',
                'motivo' => 'Mapeamento de nevos (pintas) e queixa de ressecamento e prurido em mãos.',
                'convenio' => 'Bradesco Saúde',
                'valor' => 280.00,
                'observacoes' => 'Dermatoscopia realizada. Prescrito emoliente tópico.',
            ],
            [
                'paciente_id' => $antonio->id,
                'medico_id' => $drPedro->id,
                'data_hora' => Carbon::now()->subMonths(1)->setTime(9, 30),
                'duracao_minutos' => 30,
                'especialidade' => 'Cardiologia',
                'tipo_atendimento' => 'rotina',
                'status' => 'finalizada',
                'motivo' => 'Check-up semestral do paciente hipertenso e diabético.',
                'convenio' => 'SulAmérica',
                'valor' => 250.00,
                'observacoes' => 'Exames em ordem, orientada dieta hipossódica.',
            ],

            // Consultas de HOJE (Para dinamismo do Dashboard)
            [
                'paciente_id' => $larissa->id,
                'medico_id' => $drVinicius->id,
                'data_hora' => $hoje->copy()->setTime(9, 0),
                'duracao_minutos' => 45,
                'especialidade' => 'Ortopedia e Traumatologia',
                'tipo_atendimento' => 'urgencia',
                'status' => 'em_atendimento',
                'motivo' => 'Dor aguda e edema no joelho direito após corrida de rua no último domingo.',
                'convenio' => 'Particular',
                'valor' => 350.00,
                'observacoes' => 'Encaixe de urgência solicitado pela recepção.',
            ],
            [
                'paciente_id' => $fernando->id,
                'medico_id' => $drPedro->id,
                'data_hora' => $hoje->copy()->setTime(10, 30),
                'duracao_minutos' => 30,
                'especialidade' => 'Cardiologia',
                'tipo_atendimento' => 'rotina',
                'status' => 'aguardando',
                'motivo' => 'Avaliação física para início de academia e esteira ergométrica.',
                'convenio' => 'Amil',
                'valor' => 250.00,
                'observacoes' => 'Paciente já se encontra na recepção.',
            ],
            [
                'paciente_id' => $beatriz->id,
                'medico_id' => $drMauricio->id,
                'data_hora' => $hoje->copy()->setTime(14, 0),
                'duracao_minutos' => 30,
                'especialidade' => 'Dermatologia',
                'tipo_atendimento' => 'retorno',
                'status' => 'confirmada',
                'motivo' => 'Avaliação da resposta ao tratamento de dermatite de contato.',
                'convenio' => 'Bradesco Saúde',
                'valor' => 0.00,
                'observacoes' => 'Confirmada via WhatsApp pela recepção.',
            ],
            [
                'paciente_id' => $marcos->id,
                'medico_id' => $drJuliane->id,
                'data_hora' => $hoje->copy()->setTime(15, 30),
                'duracao_minutos' => 30,
                'especialidade' => 'Clínica Geral',
                'tipo_atendimento' => 'rotina',
                'status' => 'agendada',
                'motivo' => 'Renovação de receita de uso contínuo e aferição de PA.',
                'convenio' => 'Unimed',
                'valor' => 250.00,
                'observacoes' => 'Aguardando confirmação de presença.',
            ],

            // Consultas Futuras
            [
                'paciente_id' => $antonio->id,
                'medico_id' => $drVinicius->id,
                'data_hora' => Carbon::now()->addDays(3)->setTime(11, 30),
                'duracao_minutos' => 30,
                'especialidade' => 'Ortopedia e Traumatologia',
                'tipo_atendimento' => 'primeira_consulta',
                'status' => 'confirmada',
                'motivo' => 'Investigação de dor lombar irradiada para membro inferior esquerdo.',
                'convenio' => 'SulAmérica',
                'valor' => 250.00,
                'observacoes' => 'Solicitada ressonância prévia se houver.',
            ],
            [
                'paciente_id' => $larissa->id,
                'medico_id' => $drAndrik->id,
                'data_hora' => Carbon::now()->addDays(7)->setTime(16, 0),
                'duracao_minutos' => 30,
                'especialidade' => 'Clínica Geral & Gestão Médica',
                'tipo_atendimento' => 'retorno',
                'status' => 'agendada',
                'motivo' => 'Consulta de acompanhamento com a direção médica.',
                'convenio' => 'Particular',
                'valor' => 300.00,
                'observacoes' => 'Revisão geral.',
            ],
        ];

        foreach ($consultas as $c) {
            Consulta::create($c);
        }
    }
}
