<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Prontuario;
use App\Consulta;
use App\Paciente;

class ProntuarioController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:medico,admin')->except(['show']);
    }

    public function create(Request $request)
    {
        $pacienteId = $request->input('paciente_id');
        $consultaId = $request->input('consulta_id');

        $paciente = Paciente::with(['prontuarios.medico', 'consultas.medico'])->findOrFail($pacienteId);
        $consulta = !empty($consultaId) ? Consulta::find($consultaId) : null;

        return view('prontuarios.create', compact('paciente', 'consulta'));
    }

    public function store(Request $request)
    {
        $data = $this->validarProntuario($request, true);
        $data['medico_id'] = Auth::id();
        $data['data_registro'] = Carbon::now();

        $prontuario = Prontuario::create($data);

        if (!empty($data['consulta_id'])) {
            $consulta = Consulta::find($data['consulta_id']);
            if ($consulta) {
                $consulta->status = 'finalizada';
                $consulta->save();
            }
        }

        return redirect()->route('prontuarios.show', $prontuario->id)
            ->with('success', 'Atendimento médico e prontuário registrados com sucesso.');
    }

    public function show($id)
    {
        $prontuario = Prontuario::with(['paciente', 'medico', 'consulta'])->findOrFail($id);
        return view('prontuarios.show', compact('prontuario'));
    }

    public function edit($id)
    {
        $prontuario = Prontuario::with(['paciente', 'medico'])->findOrFail($id);

        if (Auth::user()->isMedico() && $prontuario->medico_id !== Auth::id()) {
            return redirect()->route('prontuarios.show', $id)
                ->with('error', 'Apenas o médico responsável pelo atendimento ou o administrador podem editar este prontuário.');
        }

        return view('prontuarios.edit', compact('prontuario'));
    }

    public function update(Request $request, $id)
    {
        $prontuario = Prontuario::findOrFail($id);

        if (Auth::user()->isMedico() && $prontuario->medico_id !== Auth::id()) {
            return redirect()->route('prontuarios.show', $id)
                ->with('error', 'Apenas o médico responsável ou o administrador podem alterar este prontuário.');
        }

        $data = $this->validarProntuario($request, false);
        $prontuario->update($data);

        return redirect()->route('prontuarios.show', $prontuario->id)
            ->with('success', 'Prontuário médico atualizado com sucesso.');
    }

    protected function validarProntuario(Request $request, $isCreate = true)
    {
        $rules = [
            'queixa_principal' => 'required|string|min:5|max:2000',
            'historia_doenca_atual' => 'nullable|string|max:3000',
            'pressao_arterial' => ['nullable', 'string', 'max:20', 'regex:/^\d{2,3}\/\d{2,3}(\s?mmHg)?$/i'],
            'frequencia_cardiaca' => 'nullable|integer|min:30|max:250',
            'temperatura' => 'nullable|numeric|min:30|max:45',
            'peso' => 'nullable|numeric|min:0.5|max:400',
            'altura' => 'nullable|numeric|min:0.3|max:2.5',
            'exame_fisico' => 'nullable|string|max:3000',
            'diagnostico' => 'required|string|min:3|max:1000',
            'cid10' => ['nullable', 'string', 'max:100', 'regex:/^[A-Z]\d{2}(\.\d{1,2})?(\s.*)?$/i'],
            'prescricao_medica' => 'nullable|string|max:3000',
            'exames_solicitados' => 'nullable|string|max:3000',
            'conduta_plano' => 'nullable|string|max:3000',
        ];

        if ($isCreate) {
            $rules['paciente_id'] = 'required|exists:pacientes,id';
            $rules['consulta_id'] = 'nullable|exists:consultas,id';
        }

        return $request->validate($rules, [
            'queixa_principal.required' => 'A queixa principal é de preenchimento obrigatório.',
            'queixa_principal.min' => 'Descreva a queixa principal com pelo menos 5 caracteres.',
            'diagnostico.required' => 'O diagnóstico é obrigatório.',
            'pressao_arterial.regex' => 'Informe a pressão arterial com valores positivos no formato sistólica/diastólica (ex: 120/80).',
            'frequencia_cardiaca.integer' => 'A frequência cardíaca deve ser um número inteiro positivo.',
            'frequencia_cardiaca.min' => 'A frequência cardíaca não pode ser negativa nem inferior a 30 bpm.',
            'frequencia_cardiaca.max' => 'A frequência cardíaca não pode ultrapassar 250 bpm.',
            'temperatura.min' => 'A temperatura não pode ser negativa nem inferior a 30 °C.',
            'temperatura.max' => 'A temperatura corporal não pode ultrapassar 45 °C.',
            'peso.min' => 'O peso não pode ser negativo ou zero (mínimo 0,5 kg).',
            'peso.max' => 'O peso informado excede o limite de 400 kg.',
            'altura.min' => 'A altura não pode ser negativa ou zero (mínimo 0,30 m).',
            'altura.max' => 'Informe a altura em metros entre 0,30 e 2,50 (ex: 1.75).',
            'cid10.regex' => 'Informe um código CID-10 válido (ex: I10, J03.9).',
        ]);
    }
}
