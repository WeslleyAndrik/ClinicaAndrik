<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Paciente;
use App\Consulta;
use App\Prontuario;
use App\User;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();
        $hoje = Carbon::today();

        // Query base de consultas
        $consultaQuery = Consulta::with(['paciente', 'medico']);
        if ($user->isMedico()) {
            $consultaQuery->where('medico_id', $user->id);
        }

        // Métricas
        $totalPacientes = Paciente::count();
        $consultasHojeCount = (clone $consultaQuery)->whereDate('data_hora', $hoje)->count();
        $consultasAguardandoCount = (clone $consultaQuery)
            ->whereDate('data_hora', $hoje)
            ->whereIn('status', ['confirmada', 'aguardando', 'em_atendimento'])
            ->count();

        $prontuariosCount = $user->isMedico()
            ? Prontuario::where('medico_id', $user->id)->count()
            : Prontuario::count();

        // Consultas do dia detalhadas
        $consultasHoje = (clone $consultaQuery)
            ->whereDate('data_hora', $hoje)
            ->orderBy('data_hora', 'asc')
            ->get();

        // Próximas consultas da semana
        $proximasConsultas = (clone $consultaQuery)
            ->where('data_hora', '>', Carbon::now())
            ->whereNotIn('status', ['cancelada', 'finalizada'])
            ->orderBy('data_hora', 'asc')
            ->limit(6)
            ->get();

        // Últimos prontuários registrados
        $ultimosProntuarios = Prontuario::with(['paciente', 'medico'])
            ->when($user->isMedico(), function ($q) use ($user) {
                return $q->where('medico_id', $user->id);
            })
            ->orderBy('data_registro', 'desc')
            ->limit(5)
            ->get();

        // Lista de médicos ativos para filtros rápidos
        $medicos = User::medicos()->get();

        return view('dashboard.index', compact(
            'totalPacientes',
            'consultasHojeCount',
            'consultasAguardandoCount',
            'prontuariosCount',
            'consultasHoje',
            'proximasConsultas',
            'ultimosProntuarios',
            'medicos',
            'user'
        ));
    }
}
