<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\User;

class UsuarioController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    public function index(Request $request)
    {
        $role = $request->input('role');
        $busca = $request->input('busca');

        $query = User::query();

        if (!empty($role)) {
            $query->where('role', $role);
        }

        if (!empty($busca)) {
            $query->where(function ($q) use ($busca) {
                $q->where('name', 'like', "%{$busca}%")
                  ->orWhere('email', 'like', "%{$busca}%")
                  ->orWhere('crm', 'like', "%{$busca}%")
                  ->orWhere('especialidade', 'like', "%{$busca}%");
            });
        }

        $usuarios = $query->orderBy('name', 'asc')->paginate(15)->appends($request->query());

        return view('usuarios.index', compact('usuarios', 'role', 'busca'));
    }

    public function create()
    {
        return view('usuarios.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:150', 'regex:/^[\pL\s\'\-\.]+$/u'],
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:6|max:100',
            'role' => 'required|in:admin,medico,recepcao',
            'crm' => ['nullable', 'required_if:role,medico', 'string', 'max:30', 'regex:/^(CRM\/?[A-Z]{2}\s?)?\d{3,10}$/i'],
            'especialidade' => 'nullable|required_if:role,medico|string|min:3|max:100',
            'telefone' => ['nullable', 'string', 'max:20', 'regex:/^\(?\d{2}\)?\s?\d{4,5}\-?\d{4}$/'],
        ], [
            'name.required' => 'O nome completo é obrigatório.',
            'name.regex' => 'O nome deve conter apenas letras e espaços.',
            'email.required' => 'O e-mail é obrigatório.',
            'email.unique' => 'Este e-mail já está sendo utilizado.',
            'password.min' => 'A senha deve ter no mínimo 6 caracteres.',
            'crm.required_if' => 'O CRM é obrigatório para usuários com perfil Médico.',
            'crm.regex' => 'Formato de CRM inválido. Ex: CRM/PB 8412 ou 8412.',
            'especialidade.required_if' => 'A especialidade é obrigatória para Médicos.',
            'telefone.regex' => 'Informe um telefone válido com DDD. Ex: (83) 98888-7777.',
        ]);

        $data['password'] = Hash::make($data['password']);
        $data['ativo'] = true;

        User::create($data);

        return redirect()->route('usuarios.index')
            ->with('success', 'Profissional cadastrado com sucesso.');
    }

    public function edit($id)
    {
        $usuario = User::findOrFail($id);
        return view('usuarios.edit', compact('usuario'));
    }

    public function update(Request $request, $id)
    {
        $usuario = User::findOrFail($id);

        $request->merge(['ativo' => $request->has('ativo') ? (bool) $request->input('ativo') : false]);

        $data = $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:150', 'regex:/^[\pL\s\'\-\.]+$/u'],
            'email' => 'required|email|max:255|unique:users,email,' . $usuario->id,
            'password' => 'nullable|string|min:6|max:100',
            'role' => 'required|in:admin,medico,recepcao',
            'crm' => ['nullable', 'required_if:role,medico', 'string', 'max:30', 'regex:/^(CRM\/?[A-Z]{2}\s?)?\d{3,10}$/i'],
            'especialidade' => 'nullable|required_if:role,medico|string|min:3|max:100',
            'telefone' => ['nullable', 'string', 'max:20', 'regex:/^\(?\d{2}\)?\s?\d{4,5}\-?\d{4}$/'],
            'ativo' => 'required|boolean',
        ], [
            'name.regex' => 'O nome deve conter apenas letras e espaços.',
            'crm.required_if' => 'O CRM é obrigatório para o perfil Médico.',
            'crm.regex' => 'Formato de CRM inválido. Ex: CRM/PB 8412.',
            'telefone.regex' => 'Informe um telefone válido com DDD.',
        ]);

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $usuario->update($data);

        return redirect()->route('usuarios.index')
            ->with('success', 'Dados do usuário atualizados com sucesso.');
    }

    public function destroy($id)
    {
        $usuario = User::findOrFail($id);
        if ($usuario->id === auth()->id()) {
            return back()->with('error', 'Você não pode desativar seu próprio usuário.');
        }

        $usuario->ativo = !$usuario->ativo;
        $usuario->save();

        $status = $usuario->ativo ? 'ativado' : 'desativado';
        return back()->with('success', "Usuário {$usuario->name} foi {$status}.");
    }
}
