@extends('layouts.app')

@section('page_title', 'Pacientes Cadastrados')

@section('content')
<div class="card-box">
    <div class="card-box-header">
        <h2 class="card-box-title">Relação de Pacientes</h2>
        <a href="{{ route('pacientes.create') }}" class="btn-andrik-primary">
            Novo Paciente
        </a>
    </div>

    <div class="card-box-body bg-light border-bottom py-2">
        <form action="{{ route('pacientes.index') }}" method="GET" class="form-row align-items-center">
            <div class="col-md-5 mb-2 mb-md-0">
                <input type="text" name="busca" class="form-control" placeholder="Buscar por Nome, CPF ou Telefone..." value="{{ $busca }}">
            </div>
            <div class="col-md-4 mb-2 mb-md-0">
                <select name="convenio" class="form-control">
                    <option value="">Todos os Convênios</option>
                    @foreach($convenios as $conv)
                        <option value="{{ $conv }}" {{ $convenio === $conv ? 'selected' : '' }}>{{ $conv }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-flex">
                <button type="submit" class="btn-andrik-primary mr-2 flex-grow-1 justify-content-center">Filtrar</button>
                @if($busca || $convenio)
                    <a href="{{ route('pacientes.index') }}" class="btn-andrik-outline">Limpar</a>
                @endif
            </div>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table-andrik">
            <thead>
                <tr>
                    <th>Nome do Paciente</th>
                    <th>CPF</th>
                    <th>Telefone / E-mail</th>
                    <th>Convênio</th>
                    <th>Tipo Sang.</th>
                    <th>Alergias</th>
                    <th style="text-align: right;">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pacientes as $p)
                    <tr>
                        <td>
                            <div style="font-weight: 600;">
                                <a href="{{ route('pacientes.show', $p->id) }}" style="color: #0f172a;">
                                    {{ $p->nome }}
                                </a>
                            </div>
                            <small class="text-muted">
                                {{ $p->idade ? $p->idade . ' anos (' . $p->data_nascimento->format('d/m/Y') . ')' : $p->data_nascimento->format('d/m/Y') }}
                            </small>
                        </td>
                        <td><strong>{{ $p->cpf }}</strong></td>
                        <td>
                            <div>{{ $p->telefone }}</div>
                            @if($p->email)
                                <small class="text-muted">{{ $p->email }}</small>
                            @endif
                        </td>
                        <td>{{ $p->convenio ?? 'Particular' }}</td>
                        <td>{{ $p->tipo_sanguineo ?: '-' }}</td>
                        <td>
                            @if($p->alergias)
                                <span class="badge badge-danger" title="{{ $p->alergias }}">Alerta</span>
                            @else
                                <span class="text-muted">Nenhuma</span>
                            @endif
                        </td>
                        <td style="text-align: right;">
                            <a href="{{ route('consultas.create', ['paciente_id' => $p->id]) }}" class="btn-andrik-outline">Agendar</a>
                            <a href="{{ route('pacientes.historico', $p->id) }}" class="btn-andrik-outline ml-1">Histórico</a>
                            <a href="{{ route('pacientes.show', $p->id) }}" class="btn-andrik-outline ml-1">Ficha</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            Nenhum paciente localizado com os filtros informados.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($pacientes->hasPages())
        <div class="card-box-body bg-white border-top py-2">
            {{ $pacientes->links() }}
        </div>
    @endif
</div>
@endsection
