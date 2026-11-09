<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Consulta;
use App\Paciente;
use App\User;

class RelatorioController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        // Período padrão: Início do mês atual até hoje (ou últimos 30 dias)
        $dataInicio = $request->input('data_inicio', Carbon::now()->subMonths(2)->startOfMonth()->format('Y-m-d'));
        $dataFim = $request->input('data_fim', Carbon::now()->addMonths(1)->endOfMonth()->format('Y-m-d'));
        $medicoId = $request->input('medico_id');
        $pacienteId = $request->input('paciente_id');
        $status = $request->input('status');
        $convenio = $request->input('convenio');

        $query = Consulta::with(['paciente', 'medico', 'prontuario'])
            ->whereDate('data_hora', '>=', $dataInicio)
            ->whereDate('data_hora', '<=', $dataFim);

        if (!empty($medicoId)) {
            $query->where('medico_id', $medicoId);
        }

        if (!empty($pacienteId)) {
            $query->where('paciente_id', $pacienteId);
        }

        if (!empty($status)) {
            $query->where('status', $status);
        }

        if (!empty($convenio)) {
            $query->where('convenio', $convenio);
        }

        $consultas = $query->orderBy('data_hora', 'desc')->get();

        // Métricas e Totalizadores do Relatório
        $totalAtendimentos = $consultas->count();
        $totalFinalizadas = $consultas->where('status', 'finalizada')->count();
        $totalCanceladas = $consultas->where('status', 'cancelada')->count();
        $totalAgendadas = $consultas->whereIn('status', ['agendada', 'confirmada', 'aguardando', 'em_atendimento'])->count();
        $valorTotal = $consultas->where('status', '!=', 'cancelada')->sum('valor');

        // Taxa de comparecimento / conclusão
        $taxaConclusao = $totalAtendimentos > 0 
            ? round(($totalFinalizadas / $totalAtendimentos) * 100, 1) 
            : 0;

        // Listas para os filtros
        $medicos = User::medicos()->orderBy('name', 'asc')->get();
        $pacientes = Paciente::orderBy('nome', 'asc')->get();
        $convenios = Consulta::whereNotNull('convenio')->distinct()->pluck('convenio');

        // Identificação do médico e paciente selecionados para o cabeçalho do relatório impresso
        $medicoSelecionado = $medicoId ? User::find($medicoId) : null;
        $pacienteSelecionado = $pacienteId ? Paciente::find($pacienteId) : null;

        return view('relatorios.index', compact(
            'consultas',
            'dataInicio',
            'dataFim',
            'medicoId',
            'pacienteId',
            'status',
            'convenio',
            'totalAtendimentos',
            'totalFinalizadas',
            'totalCanceladas',
            'totalAgendadas',
            'valorTotal',
            'taxaConclusao',
            'medicos',
            'pacientes',
            'convenios',
            'medicoSelecionado',
            'pacienteSelecionado'
        ));
    }
}
