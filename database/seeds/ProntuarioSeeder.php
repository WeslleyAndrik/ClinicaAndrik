<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;
use App\Prontuario;
use App\Consulta;
use App\Paciente;
use App\User;

class ProntuarioSeeder extends Seeder
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

        $marcos = Paciente::where('cpf', '284.918.372-56')->first();
        $beatriz = Paciente::where('cpf', '392.104.856-70')->first();
        $antonio = Paciente::where('cpf', '109.837.261-16')->first();

        // Buscar consultas finalizadas correspondentes
        $consultaMarcos1 = Consulta::where('paciente_id', $marcos->id)->where('tipo_atendimento', 'primeira_consulta')->first();
        $consultaMarcos2 = Consulta::where('paciente_id', $marcos->id)->where('tipo_atendimento', 'retorno')->first();
        $consultaBeatriz1 = Consulta::where('paciente_id', $beatriz->id)->where('status', 'finalizada')->first();
        $consultaAntonio1 = Consulta::where('paciente_id', $antonio->id)->where('status', 'finalizada')->first();

        $prontuarios = [
            // Prontuário 1 do Marcos
            [
                'paciente_id' => $marcos->id,
                'medico_id' => $drPedro->id,
                'consulta_id' => $consultaMarcos1 ? $consultaMarcos1->id : null,
                'data_registro' => Carbon::now()->subMonths(2)->setTime(10, 30),
                'queixa_principal' => 'Episódios recorrentes de tontura e cefaleia occipital nos últimos 2 meses.',
                'historia_doenca_atual' => 'Paciente relata que durante picos de estresse no ambiente corporativo sente cefaleia pulsátil na nuca, acompanhada de visão turva transitória. Aferiu pressão em farmácia com valores de 160/100 mmHg. Nega dor torácica típica, palpitações ou dispneia aos esforços. Sedentário, relata dieta com excesso de sódio e café.',
                'pressao_arterial' => '155/95 mmHg',
                'frequencia_cardiaca' => 78,
                'temperatura' => 36.4,
                'peso' => 84.50,
                'altura' => 1.78,
                'exame_fisico' => 'Bom estado geral, lúcido e orientado no tempo e espaço, anictérico, acianótico. RCR em 2T, bulhas normofonéticas sem sopros audíveis. MV presente bilateralmente sem ruídos adventícios. Abdome plano, indolor, ruídos hidroaéreos presentes. Pulsos periféricos amplos e simétricos. Sem edema de membros inferiores.',
                'diagnostico' => 'Hipertensão Arterial Sistêmica Estágio 1 - Não controlada.',
                'cid10' => 'I10 - Hipertensão essencial',
                'prescricao_medica' => "1. Losartana Potássica 50mg ------- Tomar 1 comprimido via oral pela manhã diariamente.\n2. Controle domiciliar de PA (MAPA caseiro) anotando de manhã e à noite por 7 dias.",
                'exames_solicitados' => "1. MAPA 24 horas\n2. Holter 24 horas\n3. Ecocardiograma transtorácico com Doppler\n4. Perfil lipídico completo, glicemia de jejum, creatinina e sódio/potássio séricos.",
                'conduta_plano' => 'Iniciada monoterapia anti-hipertensiva. Orientadas mudanças no estilo de vida: redução de sal na dieta, caminhadas leves de 30 minutos 3x por semana e abstinência de energéticos. Retorno em 30 a 45 dias com exames solicitados.',
            ],

            // Prontuário 2 do Marcos (Evolução / Retorno)
            [
                'paciente_id' => $marcos->id,
                'medico_id' => $drPedro->id,
                'consulta_id' => $consultaMarcos2 ? $consultaMarcos2->id : null,
                'data_registro' => Carbon::now()->subDays(15)->setTime(15, 0),
                'queixa_principal' => 'Retorno para avaliação de exames cardiológicos complementares.',
                'historia_doenca_atual' => 'Paciente em uso regular de Losartana 50mg há 40 dias com excelente tolerância gástrica. Nega novos episódios de cefaleia ou tontura. Refere melhora importante na disposição geral.',
                'pressao_arterial' => '125/80 mmHg',
                'frequencia_cardiaca' => 72,
                'temperatura' => 36.5,
                'peso' => 83.20,
                'altura' => 1.78,
                'exame_fisico' => 'Paciente eupneico, corado, hidratado. Murmúrio vesicular limpo bilateralmente. RCR 2T sem sopros. PA dentro da meta terapêutica alvo.',
                'diagnostico' => 'Hipertensão arterial sistêmica controlada com monoterapia.',
                'cid10' => 'I10 - Hipertensão essencial',
                'prescricao_medica' => "1. Manter Losartana Potássica 50mg ------- 1 cp via oral pela manhã contínuo.",
                'exames_solicitados' => 'Nenhum no momento. Manter rotina laboratorial semestral.',
                'conduta_plano' => 'Excelente resposta clínica e laboratorial. MAPA 24h demonstrou controle satisfatório da carga pressórica diurna e noturna. Paciente perdeu 1,3 kg com reeducação alimentar. Retorno agendado para 6 meses.',
            ],

            // Prontuário da Beatriz
            [
                'paciente_id' => $beatriz->id,
                'medico_id' => $drMauricio->id,
                'consulta_id' => $consultaBeatriz1 ? $consultaBeatriz1->id : null,
                'data_registro' => Carbon::now()->subWeeks(3)->setTime(11, 40),
                'queixa_principal' => 'Lesões eritematosas descamativas e prurido intenso no dorso das mãos.',
                'historia_doenca_atual' => 'Paciente refere início dos sintomas há cerca de 3 semanas, após intensificar uso de álcool em gel e produtos de limpeza doméstica. Quadro piora após lavagem frequente das mãos. Nega lesões semelhantes prévias.',
                'pressao_arterial' => '110/70 mmHg',
                'frequencia_cardiaca' => 68,
                'temperatura' => 36.2,
                'peso' => 58.00,
                'altura' => 1.65,
                'exame_fisico' => 'Dermatoscopia em nevos dorsais sem sinais de atipia celular. Presença de placas eritemato-descamativas com fissuras superficiais e áreas de liquenificação discreta em região interdigital e dorso de ambas as mãos.',
                'diagnostico' => 'Dermatite de Contato por Irritante Primário (Eczema das mãos).',
                'cid10' => 'L24 - Dermatite de contato por irritantes',
                'prescricao_medica' => "1. Desonida creme 0,05% ------- Aplicar fina camada sobre as áreas afetadas 2x ao dia por 7 dias.\n2. Cetaphil Creme Hidratante ou Cerave Hidratante ------- Aplicar em mãos após cada lavagem e ao deitar contínuo.\n3. Sabonete syndet suave (sem sabão comum).",
                'exames_solicitados' => 'Nenhum exame invasivo indicado no momento.',
                'conduta_plano' => 'Orientado uso rigoroso de luvas de vinil forradas com algodão para atividades domésticas com químicos e água quente. Suspender temporariamente contato com álcool líquido/gel. Retorno em 3 semanas para reavaliação.',
            ],

            // Prontuário do Antônio Carlos
            [
                'paciente_id' => $antonio->id,
                'medico_id' => $drPedro->id,
                'consulta_id' => $consultaAntonio1 ? $consultaAntonio1->id : null,
                'data_registro' => Carbon::now()->subMonths(1)->setTime(10, 0),
                'queixa_principal' => 'Rotina semestral: Hipertensão e Diabetes tipo 2.',
                'historia_doenca_atual' => 'Paciente assintomático, em uso regular de Metformina 850mg 2x/dia e Enalapril 10mg 1x/dia. Nega dores precordiais, tonturas ou alterações visuais. Relata bom seguimento dietético.',
                'pressao_arterial' => '130/80 mmHg',
                'frequencia_cardiaca' => 74,
                'temperatura' => 36.6,
                'peso' => 78.00,
                'altura' => 1.72,
                'exame_fisico' => 'BEG, corado, hidratado. RCR 2T BNF sem sopros. Exame dos pés diabéticos sem úlceras ou perda de sensibilidade tátil/vibratória.',
                'diagnostico' => 'Controle satisfatório de HAS e DM tipo 2.',
                'cid10' => 'E11 - Diabetes mellitus não-insulino-dependente',
                'prescricao_medica' => "1. Metformina 850mg ------- 1 cp após o almoço e 1 cp após o jantar.\n2. Maleato de Enalapril 10mg ------- 1 cp pela manhã.",
                'exames_solicitados' => 'HbA1c, glicemia jejum, microalbuminúria, ureia, creatinina, perfil lipídico.',
                'conduta_plano' => 'Manter prescrição medicamentosa. Retorno agendado para 6 meses.',
            ],
        ];

        foreach ($prontuarios as $p) {
            Prontuario::create($p);
        }
    }
}
