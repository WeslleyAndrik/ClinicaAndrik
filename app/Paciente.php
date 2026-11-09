<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Paciente extends Model
{
    protected $table = 'pacientes';

    protected $fillable = [
        'nome',
        'cpf',
        'rg',
        'data_nascimento',
        'sexo',
        'telefone',
        'email',
        'tipo_sanguineo',
        'alergias',
        'medicamentos_uso_continuo',
        'historico_cirurgico',
        'convenio',
        'numero_carteirinha',
        'contato_emergencia_nome',
        'contato_emergencia_telefone',
        'cep',
        'endereco',
        'numero',
        'bairro',
        'cidade',
        'estado',
        'observacoes',
    ];

    protected $casts = [
        'data_nascimento' => 'date',
    ];

    public const CONVENIOS_PADRAO = [
        'Particular',
        'Unimed',
        'Bradesco Saúde',
        'Hapvida',
        'Amil',
        'SulAmérica',
        'Cassi',
        'Geap',
        'Smile Saúde',
    ];

    public static function listarConvenios(): array
    {
        $lista = self::CONVENIOS_PADRAO;

        $arquivo = storage_path('app/convenios_custom.json');
        if (file_exists($arquivo)) {
            $salvos = json_decode((string) file_get_contents($arquivo), true);
            if (is_array($salvos)) {
                $lista = array_merge($lista, $salvos);
            }
        }

        try {
            $doBanco = self::whereNotNull('convenio')
                ->where('convenio', '!=', '')
                ->distinct()
                ->pluck('convenio')
                ->toArray();
            $lista = array_merge($lista, $doBanco);
        } catch (\Throwable $e) {
            // Ignora caso a tabela ainda não esteja migrada
        }

        $unicos = [];
        foreach ($lista as $item) {
            $limpo = trim((string) $item);
            if ($limpo !== '' && !in_array($limpo, $unicos, true)) {
                $unicos[] = $limpo;
            }
        }

        return $unicos;
    }

    public static function registrarConvenio(string $nome): string
    {
        $limpo = trim($nome);
        if ($limpo === '') {
            return '';
        }

        $arquivo = storage_path('app/convenios_custom.json');
        $salvos = [];
        if (file_exists($arquivo)) {
            $dec = json_decode((string) file_get_contents($arquivo), true);
            if (is_array($dec)) {
                $salvos = $dec;
            }
        }

        if (!in_array($limpo, $salvos, true) && !in_array($limpo, self::CONVENIOS_PADRAO, true)) {
            $salvos[] = $limpo;
            @file_put_contents($arquivo, json_encode(array_values($salvos), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        }

        return $limpo;
    }

    public static function validarCpf($cpf)
    {
        $cpf = preg_replace('/[^0-9]/', '', (string) $cpf);

        if (strlen($cpf) !== 11 || preg_match('/^(\d)\1{10}$/', $cpf)) {
            return false;
        }

        for ($t = 9; $t < 11; $t++) {
            $d = 0;
            for ($c = 0; $c < $t; $c++) {
                $d += (int) $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ((int) $cpf[$t] !== $d) {
                return false;
            }
        }

        return true;
    }

    public static function formatarCpf($cpf)
    {
        $digitos = preg_replace('/[^0-9]/', '', (string) $cpf);
        if (strlen($digitos) === 11) {
            return substr($digitos, 0, 3) . '.' . substr($digitos, 3, 3) . '.' . substr($digitos, 6, 3) . '-' . substr($digitos, 9, 2);
        }
        return trim((string) $cpf);
    }

    public function getIdadeAttribute()
    {
        return $this->data_nascimento ? Carbon::parse($this->data_nascimento)->age : null;
    }

    public function consultas()
    {
        return $this->hasMany(Consulta::class, 'paciente_id')->orderBy('data_hora', 'desc');
    }

    public function prontuarios()
    {
        return $this->hasMany(Prontuario::class, 'paciente_id')->orderBy('data_registro', 'desc');
    }

    public function proximaConsulta()
    {
        return $this->hasOne(Consulta::class, 'paciente_id')
            ->where('data_hora', '>=', now())
            ->whereNotIn('status', ['cancelada', 'finalizada'])
            ->orderBy('data_hora', 'asc');
    }

    public function scopeBuscar($query, $termo)
    {
        if (empty($termo)) {
            return $query;
        }

        $termoLimpo = preg_replace('/[^0-9]/', '', $termo);

        return $query->where(function ($q) use ($termo, $termoLimpo) {
            $q->where('nome', 'like', "%{$termo}%")
              ->orWhere('email', 'like', "%{$termo}%")
              ->orWhere('telefone', 'like', "%{$termo}%")
              ->orWhere('cpf', 'like', "%{$termo}%");

            if (!empty($termoLimpo)) {
                if (strlen($termoLimpo) === 11) {
                    $cpfFormatado = self::formatarCpf($termoLimpo);
                    $q->orWhere('cpf', 'like', "%{$cpfFormatado}%");
                }
                $q->orWhereRaw("REPLACE(REPLACE(cpf, '.', ''), '-', '') LIKE ?", ["%{$termoLimpo}%"]);
            }
        });
    }
}
