<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ], [
            'email.required' => 'O e-mail é obrigatório.',
            'email.email' => 'Informe um endereço de e-mail válido.',
            'password.required' => 'A senha é obrigatória.',
        ]);

        $remember = $request->has('remember');

        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();
            if (!$user->ativo) {
                Auth::logout();
                return back()->withErrors(['email' => 'Este usuário está inativo no sistema. Entre em contato com a administração.']);
            }

            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'))->with('success', 'Bem-vindo(a), ' . $user->name . '.');
        }

        return back()->withErrors([
            'email' => 'As credenciais fornecidas não conferem com nossos registros.',
        ])->withInput($request->only('email'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('info', 'Sessão encerrada.');
    }

    public function profile()
    {
        return view('auth.profile', ['user' => Auth::user()]);
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:150', 'regex:/^[\pL\s\'\-\.]+$/u'],
            'telefone' => ['nullable', 'string', 'max:20', 'regex:/^\(?\d{2}\)?\s?\d{4,5}\-?\d{4}$/'],
            'password' => 'nullable|string|min:6|confirmed',
        ], [
            'name.required' => 'O nome é obrigatório.',
            'name.regex' => 'O nome deve conter apenas letras e espaços.',
            'telefone.regex' => 'Informe um telefone válido com DDD. Ex: (83) 98888-7777.',
            'password.min' => 'A nova senha deve ter no mínimo 6 caracteres.',
            'password.confirmed' => 'A confirmação da nova senha não confere.',
        ]);

        $user->name = $data['name'];
        $user->telefone = $data['telefone'];

        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        return back()->with('success', 'Perfil atualizado com sucesso.');
    }
}
