@extends('layouts.app')

@section('page_title', 'Corpo Clínico e Equipe')

@section('content')
<div class="card-box">
    <div class="card-box-header">
        <h2 class="card-box-title">Profissionais e Usuários da Clínica</h2>
        <a href="{{ route('usuarios.create') }}" class="btn-andrik-primary">Novo Profissional</a>
    </div>

    <div class="card-box-body bg-light border-bottom py-2">
        <form action="{{ route('usuarios.index') }}" method="GET" class="form-row align-items-center">
            <div class="col-md-5 mb-2 mb-md-0">
                <input type="text" name="busca" class="form-control" placeholder="Buscar por Nome, E-mail ou CRM..." value="{{ $busca }}">
            </div>
            <div class="col-md-4 mb-2 mb-md-0">
                <select name="role" class="form-control">
                    <option value="">Todos os Perfis</option>
                    <option value="medico" {{ $role === 'medico' ? 'selected' : '' }}>Médicos</option>
                    <option value="recepcao" {{ $role === 'recepcao' ? 'selected' : '' }}>Recepção / Atendimento</option>
                    <option value="admin" {{ $role === 'admin' ? 'selected' : '' }}>Administradores / Direção</option>
                </select>
            </div>
            <div class="col-md-3 d-flex">
                <button type="submit" class="btn-andrik-primary mr-2 flex-grow-1 justify-content-center">Filtrar</button>
                @if($busca || $role)
                    <a href="{{ route('usuarios.index') }}" class="btn-andrik-outline">Limpar</a>
                @endif
            </div>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table-andrik">
            <thead>
                <tr>
                    <th>Profissional</th>
                    <th>Perfil</th>
                    <th>CRM / Registro</th>
                    <th>Especialidade</th>
                    <th>Telefone</th>
                    <th>Status</th>
                    <th style="text-align: right;">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($usuarios as $u)
                    <tr>
                        <td>
                            <strong>{{ $u->name }}</strong>
                            <small class="text-muted d-block">{{ $u->email }}</small>
                        </td>
                        <td>
                            @if($u->role === 'admin')
                                <span class="badge badge-dark">Direção Clínica</span>
                            @elseif($u->role === 'medico')
                                <span class="badge badge-primary">Corpo Médico</span>
                            @else
                                <span class="badge badge-secondary">Recepção</span>
                            @endif
                        </td>
                        <td>{{ $u->crm ?: '--' }}</td>
                        <td>{{ $u->especialidade ?: '--' }}</td>
                        <td>{{ $u->telefone ?: '--' }}</td>
                        <td>
                            @if($u->ativo)
                                <span class="badge-status confirmada">Ativo</span>
                            @else
                                <span class="badge-status cancelada">Inativo</span>
                            @endif
                        </td>
                        <td style="text-align: right;">
                            <a href="{{ route('usuarios.edit', $u->id) }}" class="btn-andrik-outline">Editar</a>
                            @if($u->id !== Auth::id())
                                <form action="{{ route('usuarios.destroy', $u->id) }}" method="POST" class="d-inline ml-1" onsubmit="return confirm('Deseja alterar o status deste usuário?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-andrik-outline">
                                        {{ $u->ativo ? 'Desativar' : 'Ativar' }}
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            Nenhum profissional localizado.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($usuarios->hasPages())
        <div class="card-box-body bg-white border-top py-2">
            {{ $usuarios->links() }}
        </div>
    @endif
</div>
@endsection
