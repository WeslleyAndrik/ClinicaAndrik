<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Consulta;
use App\Paciente;
use App\User;
use App\Services\EvolutionWhatsAppService;

class ConsultaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $dataFiltro = $request->input('data');
        $medicoFiltro = $request->input('medico_id');
        $statusFiltro = $request->input('status');
        $buscaPaciente = $request->input('paciente');

        $query = Consulta::with(['paciente', 'medico', 'prontuario']);

        if (!empty($dataFiltro)) {
            $query->whereDate('data_hora', $dataFiltro);
        }
        if (!empty($medicoFiltro)) {
            $query->where('medico_id', $medicoFiltro);
        }
        if (!empty($statusFiltro)) {
            $query->where('status', $statusFiltro);
        }
        if (!empty($buscaPaciente)) {
            $query->whereHas('paciente', function ($q) use ($buscaPaciente) {
                $q->buscar($buscaPaciente);
            });
        }

        $consultas = $query->orderBy('data_hora', 'desc')->paginate(15)->appends($request->query());
        $medicos = User::medicos()->get();

        return view('consultas.index', compact(
            'consultas',
            'medicos',
            'dataFiltro',
            'medicoFiltro',
            'statusFiltro',
            'buscaPaciente'
        ));
    }

    public function create(Request $request)
    {
        $pacienteSelecionado = $request->has('paciente_id')
            ? Paciente::find($request->input('paciente_id'))
            : null;

        $pacientes = Paciente::orderBy('nome', 'asc')->get();
        $medicos = User::medicos()->get();

        return view('consultas.create', compact('pacientes', 'medicos', 'pacienteSelecionado'));
    }

    public function store(Request $request)
    {
        if (!$request->filled('especialidade') && $request->filled('medico_id')) {
            $medicoSel = User::find($request->input('medico_id'));
            if ($medicoSel && $medicoSel->especialidade) {
                $request->merge(['especialidade' => $medicoSel->especialidade]);
            }
        }

        $data = $request->validate([
            'paciente_id' => 'required|exists:pacientes,id',
            'medico_id' => 'required|exists:users,id',
            'data' => 'required|date|after_or_equal:today',
            'hora' => ['required', 'regex:/^([01]\d|2[0-3]):[0-5]\d$/'],
            'duracao_minutos' => 'required|integer|min:15|max:180',
            'especialidade' => 'required|string|min:3|max:100',
            'tipo_atendimento' => 'required|in:primeira_consulta,retorno,rotina,urgencia',
            'convenio' => 'required|string|max:50',
            'valor' => 'nullable|numeric|min:0|max:99999.99',
            'motivo' => 'nullable|string|max:1000',
            'observacoes' => 'nullable|string|max:1000',
        ], [
            'paciente_id.required' => 'Selecione o paciente.',
            'medico_id.required' => 'Selecione o médico responsável.',
            'data.required' => 'Informe a data da consulta.',
            'data.after_or_equal' => 'A data do agendamento não pode ser anterior a hoje.',
            'hora.required' => 'Informe o horário da consulta.',
            'hora.regex' => 'Informe um horário válido no formato HH:MM.',
            'especialidade.required' => 'Informe a especialidade médica.',
            'valor.numeric' => 'O valor da consulta deve ser numérico.',
            'valor.min' => 'O valor da consulta não pode ser negativo.',
        ]);

        $dataHora = Carbon::parse($data['data'] . ' ' . $data['hora']);
        $duracao = (int) $data['duracao_minutos'];

        if ($this->possuiConflitoAgenda($data['medico_id'], $dataHora, $duracao)) {
            return back()->withInput()->withErrors([
                'hora' => 'O médico selecionado já possui uma consulta agendada neste horário ou intervalo.',
            ]);
        }

        $consulta = Consulta::create([
            'paciente_id' => $data['paciente_id'],
            'medico_id' => $data['medico_id'],
            'data_hora' => $dataHora,
            'duracao_minutos' => $duracao,
            'especialidade' => $data['especialidade'],
            'tipo_atendimento' => $data['tipo_atendimento'],
            'status' => 'agendada',
            'convenio' => $data['convenio'],
            'valor' => $data['valor'] ?? 0.00,
            'motivo' => $data['motivo'] ?? null,
            'observacoes' => $data['observacoes'] ?? null,
        ]);

        $disparo = app(EvolutionWhatsAppService::class)->enviarConfirmacaoConsulta($consulta);
        $msgSucesso = 'Consulta agendada com sucesso.';
        if ($disparo['enviado']) {
            $msgSucesso .= ' ' . $disparo['mensagem'];
        }

        return redirect()->route('consultas.show', $consulta->id)
            ->with('success', $msgSucesso);
    }

    public function show($id)
    {
        $consulta = Consulta::with(['paciente', 'medico', 'prontuario'])->findOrFail($id);
        return view('consultas.show', compact('consulta'));
    }

    public function edit($id)
    {
        $consulta = Consulta::with(['paciente', 'medico'])->findOrFail($id);
        $pacientes = Paciente::orderBy('nome', 'asc')->get();
        $medicos = User::medicos()->get();

        return view('consultas.edit', compact('consulta', 'pacientes', 'medicos'));
    }

    public function update(Request $request, $id)
    {
        $consulta = Consulta::findOrFail($id);

        if (!$request->filled('especialidade') && $request->filled('medico_id')) {
            $medicoSel = User::find($request->input('medico_id'));
            if ($medicoSel && $medicoSel->especialidade) {
                $request->merge(['especialidade' => $medicoSel->especialidade]);
            }
        }

        $data = $request->validate([
            'medico_id' => 'required|exists:users,id',
            'data' => 'required|date',
            'hora' => ['required', 'regex:/^([01]\d|2[0-3]):[0-5]\d$/'],
            'duracao_minutos' => 'required|integer|min:15|max:180',
            'especialidade' => 'required|string|min:3|max:100',
            'tipo_atendimento' => 'required|in:primeira_consulta,retorno,rotina,urgencia',
            'status' => 'required|in:agendada,confirmada,aguardando,em_atendimento,finalizada,cancelada',
            'convenio' => 'required|string|max:50',
            'valor' => 'nullable|numeric|min:0|max:99999.99',
            'motivo' => 'nullable|string|max:1000',
            'observacoes' => 'nullable|string|max:1000',
        ], [
            'hora.regex' => 'Informe um horário válido no formato HH:MM.',
            'valor.min' => 'O valor da consulta não pode ser negativo.',
        ]);

        $dataHora = Carbon::parse($data['data'] . ' ' . $data['hora']);
        $duracao = (int) $data['duracao_minutos'];

        if ($data['status'] !== 'cancelada' && $this->possuiConflitoAgenda($data['medico_id'], $dataHora, $duracao, $consulta->id)) {
            return back()->withInput()->withErrors([
                'hora' => 'O médico selecionado já possui outra consulta agendada neste horário.',
            ]);
        }

        $consulta->update([
            'medico_id' => $data['medico_id'],
            'data_hora' => $dataHora,
            'duracao_minutos' => $duracao,
            'especialidade' => $data['especialidade'],
            'tipo_atendimento' => $data['tipo_atendimento'],
            'status' => $data['status'],
            'convenio' => $data['convenio'],
            'valor' => $data['valor'] ?? 0.00,
            'motivo' => $data['motivo'] ?? null,
            'observacoes' => $data['observacoes'] ?? null,
        ]);

        return redirect()->route('consultas.show', $consulta->id)
            ->with('success', 'Informações da consulta atualizadas.');
    }

    public function updateStatus(Request $request, $id)
    {
        $consulta = Consulta::findOrFail($id);

        $request->validate([
            'status' => 'required|in:agendada,confirmada,aguardando,em_atendimento,finalizada,cancelada'
        ]);

        $consulta->status = $request->input('status');
        $consulta->save();

        return back()->with('success', 'Status da consulta alterado para: ' . $consulta->status_label);
    }

    public function destroy($id)
    {
        $consulta = Consulta::findOrFail($id);
        $consulta->status = 'cancelada';
        $consulta->save();

        return redirect()->route('consultas.index')
            ->with('success', 'A consulta foi cancelada com sucesso.');
    }

    public function enviarWhatsapp($id, EvolutionWhatsAppService $whatsAppService)
    {
        $consulta = Consulta::with(['paciente', 'medico'])->findOrFail($id);
        $resultado = $whatsAppService->enviarConfirmacaoConsulta($consulta);

        if ($resultado['enviado']) {
            return back()->with('success', $resultado['mensagem']);
        }

        return back()->with('error', 'Falha no disparo WhatsApp: ' . $resultado['mensagem']);
    }

    protected function possuiConflitoAgenda($medicoId, Carbon $inicio, $duracaoMinutos, $ignorarId = null)
    {
        $fim = (clone $inicio)->addMinutes($duracaoMinutos);

        return Consulta::where('medico_id', $medicoId)
            ->whereNotIn('status', ['cancelada'])
            ->when($ignorarId, function ($q) use ($ignorarId) {
                return $q->where('id', '!=', $ignorarId);
            })
            ->whereBetween('data_hora', [$inicio, $fim->copy()->subMinute()])
            ->exists();
    }
}
