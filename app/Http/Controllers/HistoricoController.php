<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Paciente;
use App\Consulta;
use App\Prontuario;
use App\User;

class HistoricoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function paciente(Request $request, $paciente_id)
    {
        $paciente = Paciente::findOrFail($paciente_id);
        $periodo = $request->input('periodo', 'todos');
        $medicoFiltro = $request->input('medico_id');

        // Consultas
        $consultasQuery = Consulta::with(['medico', 'prontuario'])
            ->where('paciente_id', $paciente->id);

        // Prontuários avulsos ou vinculados
        $prontuariosQuery = Prontuario::with(['medico', 'consulta'])
            ->where('paciente_id', $paciente->id);

        if (!empty($medicoFiltro)) {
            $consultasQuery->where('medico_id', $medicoFiltro);
            $prontuariosQuery->where('medico_id', $medicoFiltro);
        }

        if ($periodo === '30d') {
            $dataLimite = Carbon::now()->subDays(30);
            $consultasQuery->where('data_hora', '>=', $dataLimite);
            $prontuariosQuery->where('data_registro', '>=', $dataLimite);
        } elseif ($periodo === '6m') {
            $dataLimite = Carbon::now()->subMonths(6);
            $consultasQuery->where('data_hora', '>=', $dataLimite);
            $prontuariosQuery->where('data_registro', '>=', $dataLimite);
        } elseif ($periodo === '1a') {
            $dataLimite = Carbon::now()->subYear();
            $consultasQuery->where('data_hora', '>=', $dataLimite);
            $prontuariosQuery->where('data_registro', '>=', $dataLimite);
        }

        $consultas = $consultasQuery->orderBy('data_hora', 'desc')->get();
        $prontuarios = $prontuariosQuery->orderBy('data_registro', 'desc')->get();

        // Médicos que já atenderam este paciente
        $medicoIds = $consultas->pluck('medico_id')->merge($prontuarios->pluck('medico_id'))->unique();
        $medicosAtendimento = User::whereIn('id', $medicoIds)->get();

        return view('pacientes.historico', compact(
            'paciente',
            'consultas',
            'prontuarios',
            'periodo',
            'medicoFiltro',
            'medicosAtendimento'
        ));
    }
}
