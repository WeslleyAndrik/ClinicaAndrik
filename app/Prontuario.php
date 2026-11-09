<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Prontuario extends Model
{
    protected $table = 'prontuarios';

    protected $fillable = [
        'paciente_id',
        'medico_id',
        'consulta_id',
        'data_registro',
        'queixa_principal',
        'historia_doenca_atual',
        'pressao_arterial',
        'frequencia_cardiaca',
        'temperatura',
        'peso',
        'altura',
        'exame_fisico',
        'diagnostico',
        'cid10',
        'prescricao_medica',
        'exames_solicitados',
        'conduta_plano',
    ];

    protected $casts = [
        'data_registro' => 'datetime',
        'temperatura' => 'decimal:1',
        'peso' => 'decimal:2',
        'altura' => 'decimal:2',
    ];

    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'paciente_id');
    }

    public function medico()
    {
        return $this->belongsTo(User::class, 'medico_id');
    }

    public function consulta()
    {
        return $this->belongsTo(Consulta::class, 'consulta_id');
    }

    public function getImcAttribute()
    {
        if ($this->peso > 0 && $this->altura > 0) {
            return round($this->peso / ($this->altura * $this->altura), 1);
        }
        return null;
    }

    public function getImcClassificacaoAttribute()
    {
        $imc = $this->imc;
        if (!$imc) {
            return null;
        }

        if ($imc < 18.5) return 'Abaixo do peso';
        if ($imc < 25.0) return 'Peso normal';
        if ($imc < 30.0) return 'Sobrepeso';
        if ($imc < 35.0) return 'Obesidade Grau I';
        if ($imc < 40.0) return 'Obesidade Grau II';
        return 'Obesidade Grau III';
    }
}
