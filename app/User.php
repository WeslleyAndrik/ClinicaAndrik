<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'crm',
        'especialidade',
        'telefone',
        'ativo',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'ativo' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function (User $user) {
            if ($user->role === 'medico' && !empty($user->name)) {
                $user->name = self::padronizarNomeMedico($user->name);
            }
        });
    }

    public static function padronizarNomeMedico($nome)
    {
        $limpo = trim(preg_replace('/^(dr\.?|dra\.?)\s+/iu', '', trim((string) $nome)));
        return $limpo !== '' ? 'Dr. ' . $limpo : 'Dr.';
    }

    public function getNameAttribute($value)
    {
        if (($this->attributes['role'] ?? null) === 'medico' && !empty($value)) {
            return self::padronizarNomeMedico($value);
        }
        return $value;
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isMedico()
    {
        return $this->role === 'medico';
    }

    public function isRecepcao()
    {
        return $this->role === 'recepcao';
    }

    public function consultas()
    {
        return $this->hasMany(Consulta::class, 'medico_id');
    }

    public function prontuarios()
    {
        return $this->hasMany(Prontuario::class, 'medico_id');
    }

    public function scopeMedicos($query)
    {
        return $query->where('role', 'medico')->where('ativo', true);
    }
}
