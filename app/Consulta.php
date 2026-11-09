<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Consulta extends Model
{
    protected $table = 'consultas';

    protected $fillable = [
        'paciente_id',
        'medico_id',
        'data_hora',
        'duracao_minutos',
        'especialidade',
        'tipo_atendimento',
        'status',
        'motivo',
        'convenio',
        'valor',
        'observacoes',
    ];

    protected $casts = [
        'data_hora' => 'datetime',
        'valor' => 'decimal:2',
    ];

    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'paciente_id');
    }

    public function medico()
    {
        return $this->belongsTo(User::class, 'medico_id');
    }

    public function prontuario()
    {
        return $this->hasOne(Prontuario::class, 'consulta_id');
    }

    public function getStatusLabelAttribute()
    {
        $labels = [
            'agendada' => 'Agendada',
            'confirmada' => 'Confirmada',
            'aguardando' => 'Aguardando na Recepção',
            'em_atendimento' => 'Em Atendimento',
            'finalizada' => 'Finalizada',
            'cancelada' => 'Cancelada',
        ];

        return $labels[$this->status] ?? ucfirst($this->status);
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'agendada' => 'badge-info',
            'confirmada' => 'badge-primary',
            'aguardando' => 'badge-warning',
            'em_atendimento' => 'badge-success',
            'finalizada' => 'badge-secondary',
            'cancelada' => 'badge-danger',
        ];

        return $badges[$this->status] ?? 'badge-secondary';
    }

    public function getTipoAtendimentoLabelAttribute()
    {
        $labels = [
            'primeira_consulta' => 'Primeira Consulta',
            'retorno' => 'Retorno',
            'rotina' => 'Consulta de Rotina',
            'urgencia' => 'Urgência / Encaixe',
        ];

        return $labels[$this->tipo_atendimento] ?? ucfirst($this->tipo_atendimento);
    }

    public function scopeHoje($query)
    {
        return $query->whereDate('data_hora', Carbon::today());
    }

    public function scopeDoMedico($query, $medicoId)
    {
        return $query->where('medico_id', $medicoId);
    }

    public function scopeAtivas($query)
    {
        return $query->whereNotIn('status', ['cancelada', 'finalizada']);
    }
}
