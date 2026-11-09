<?php

namespace App\Services;

use App\Consulta;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EvolutionWhatsAppService
{
    /**
     * Formata o número de telefone para o padrão internacional DDI + DDD + Número (ex: 5583988887777).
     */
    public static function formatarNumero($telefone): string
    {
        $digitos = preg_replace('/\D/', '', (string) $telefone);
        $digitos = ltrim($digitos, '0');

        if ($digitos === '') {
            return '';
        }

        if (strlen($digitos) === 10 || strlen($digitos) === 11) {
            return '55' . $digitos;
        }

        return $digitos;
    }

    /**
     * Monta o texto da mensagem de confirmação de agendamento para o paciente.
     */
    public static function montarMensagemAgendamento(Consulta $consulta): string
    {
        $consulta->loadMissing(['paciente', 'medico']);

        $pacienteNome = $consulta->paciente ? $consulta->paciente->nome : 'Paciente';
        $medicoNome = $consulta->medico ? $consulta->medico->name : 'Corpo Clínico';
        $data = $consulta->data_hora ? $consulta->data_hora->format('d/m/Y') : '';
        $hora = $consulta->data_hora ? $consulta->data_hora->format('H:i') : '';

        return "Olá, *{$pacienteNome}*!\n"
            . "Sua consulta na *Clínica Andrik* foi agendada com sucesso.\n\n"
            . "*Data:* {$data}\n"
            . "*Horário:* {$hora}\n"
            . "*Médico:* {$medicoNome} ({$consulta->especialidade})\n"
            . "*Atendimento:* {$consulta->tipo_atendimento_label}\n"
            . "*Convênio:* {$consulta->convenio}\n\n"
            . "Endereço: Av. Rio Grande do Sul, 1558 - Estados, João Pessoa - PB.\n"
            . "Caso precise reagendar, entre em contato com nossa recepção.";
    }

    /**
     * Dispara a mensagem de confirmação de agendamento via Evolution API.
     */
    public function enviarConfirmacaoConsulta(Consulta $consulta): array
    {
        $consulta->loadMissing(['paciente', 'medico']);

        $telefoneBruto = $consulta->paciente ? $consulta->paciente->telefone : '';
        $numero = self::formatarNumero($telefoneBruto);

        if ($numero === '' || strlen($numero) < 12) {
            return [
                'enviado' => false,
                'mensagem' => 'Paciente sem número de WhatsApp válido cadastrado.',
            ];
        }

        $baseUrl = rtrim((string) config('services.evolution.url'), '/');
        $apiKey = (string) config('services.evolution.apikey');
        $instance = trim((string) config('services.evolution.instance'));

        if ($baseUrl === '' || $apiKey === '' || $instance === '') {
            Log::info('Evolution API não configurada no .env. Disparo ignorado.', [
                'consulta_id' => $consulta->id,
                'numero' => $numero,
            ]);

            return [
                'enviado' => false,
                'mensagem' => 'Credenciais da Evolution API não configuradas no ambiente (.env).',
            ];
        }

        $endpoint = "{$baseUrl}/message/sendText/" . rawurlencode($instance);
        $texto = self::montarMensagemAgendamento($consulta);

        try {
            $response = Http::withHeaders([
                'apikey' => $apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(8)->post($endpoint, [
                'number' => $numero,
                'text' => $texto,
                'textMessage' => [
                    'text' => $texto,
                ],
            ]);

            if ($response->successful()) {
                return [
                    'enviado' => true,
                    'numero' => $numero,
                    'mensagem' => "Notificação enviada via WhatsApp para {$consulta->paciente->telefone}.",
                ];
            }

            Log::warning('Falha no retorno da Evolution API ao enviar WhatsApp.', [
                'consulta_id' => $consulta->id,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [
                'enviado' => false,
                'mensagem' => 'A Evolution API retornou erro HTTP ' . $response->status() . '.',
            ];
        } catch (\Throwable $e) {
            Log::warning('Erro de conexão com a Evolution API ao enviar WhatsApp.', [
                'consulta_id' => $consulta->id,
                'erro' => $e->getMessage(),
            ]);

            return [
                'enviado' => false,
                'mensagem' => 'Não foi possível conectar à Evolution API (' . $e->getMessage() . ').',
            ];
        }
    }
}

