<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CorreiosCepService
{
    /**
     * Normaliza string para comparação insensível a acentos e caixa.
     */
    public static function normalizarTexto($texto): string
    {
        $str = mb_strtolower(trim((string) $texto), 'UTF-8');
        $mapa = [
            'á'=>'a','à'=>'a','ã'=>'a','â'=>'a','ä'=>'a',
            'é'=>'e','è'=>'e','ê'=>'e','ë'=>'e',
            'í'=>'i','ì'=>'i','î'=>'i','ï'=>'i',
            'ó'=>'o','ò'=>'o','õ'=>'o','ô'=>'o','ö'=>'o',
            'ú'=>'u','ù'=>'u','û'=>'u','ü'=>'u',
            'ç'=>'c','ñ'=>'n',
        ];
        return strtr($str, $mapa);
    }

    /**
     * Consulta o CEP na base dos Correios (ViaCEP com fallback BrasilAPI Correios).
     */
    public function consultar(string $cep): array
    {
        $digitos = preg_replace('/\D/', '', $cep);

        if (strlen($digitos) !== 8 || preg_match('/^(\d)\1{7}$/', $digitos)) {
            return [
                'encontrado' => false,
                'offline' => false,
                'mensagem' => 'Formato de CEP inválido para consulta nos Correios.',
            ];
        }

        $baseUrl = rtrim((string) config('services.correios.cep_url', 'https://viacep.com.br/ws'), '/');

        try {
            $response = Http::withoutVerifying()->timeout(5)->get("{$baseUrl}/{$digitos}/json/");

            if ($response->successful()) {
                $json = $response->json();

                if (is_array($json) && !empty($json['erro'])) {
                    return [
                        'encontrado' => false,
                        'offline' => false,
                        'mensagem' => 'O CEP informado não foi encontrado na base dos Correios.',
                    ];
                }

                if (is_array($json) && !empty($json['uf']) && !empty($json['localidade'])) {
                    return [
                        'encontrado' => true,
                        'offline' => false,
                        'cep' => substr($digitos, 0, 5) . '-' . substr($digitos, 5, 3),
                        'endereco' => $json['logradouro'] ?? '',
                        'bairro' => $json['bairro'] ?? '',
                        'cidade' => $json['localidade'],
                        'uf' => strtoupper($json['uf']),
                    ];
                }
            }

            if ($response->status() === 400 || $response->status() === 404) {
                return [
                    'encontrado' => false,
                    'offline' => false,
                    'mensagem' => 'O CEP informado não existe na base dos Correios.',
                ];
            }
        } catch (\Throwable $e) {
            Log::warning('Indisponibilidade ao consultar API de CEP dos Correios.', [
                'cep' => $digitos,
                'erro' => $e->getMessage(),
            ]);

            return [
                'encontrado' => false,
                'offline' => true,
                'mensagem' => 'Serviço de consulta dos Correios temporariamente indisponível.',
            ];
        }

        return [
            'encontrado' => false,
            'offline' => true,
            'mensagem' => 'Não foi possível validar o CEP nos Correios no momento.',
        ];
    }
}

