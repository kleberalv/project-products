@extends('layouts.app')

@section('title', 'Usuários')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3">👥 Gerenciamento de Usuários</h1>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('usuarios.create') }}" class="btn btn-primary">
                <i class="bi bi-person-plus"></i> Novo Usuário
            </a>
        </div>
    </div>

    <!-- Filtro de busca -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('usuarios.index') }}" class="row g-3">
                <div class="col-md-6">
                    <input
                        type="text"
                        name="busca"
                        class="form-control"
                        placeholder="Buscar por nome ou email..."
                        value="{{ request('busca') }}"
                    >
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-outline-primary w-100">
                        <i class="bi bi-search"></i> Buscar
                    </button>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('usuarios.index') }}" class="btn btn-outline-secondary w-100">
                        <i class="bi bi-arrow-clockwise"></i> Limpar
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabela de usuários -->
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Cadastrado em</th>
                        <th class="text-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($usuarios as $usuario)
                        <tr>
                            <td>
                                <i class="bi bi-person-circle"></i>
                                {{ $usuario->name }}
                                @if($usuario->id === Auth::id())
                                    <span class="badge bg-info">Você</span>
                                @endif
                            </td>
                            <td>{{ $usuario->email }}</td>
                            <td>{{ $usuario->created_at->format('d/m/Y H:i') }}</td>
                            <td class="text-end">
                                <a href="{{ route('usuarios.edit', $usuario) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @if($usuario->id !== Auth::id())
                                    <form method="POST" action="{{ route('usuarios.destroy', $usuario) }}" style="display: inline;" onsubmit="return confirm('Tem certeza?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">
                                Nenhum usuário encontrado
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Paginação -->
    <div class="mt-4">
        <div class="d-flex justify-content-between align-items-center">
            <div class="text-muted">
                Mostrando {{ $usuarios->firstItem() ?? 0 }} a {{ $usuarios->lastItem() ?? 0 }} de {{ $usuarios->total() }} usuários
            </div>
            <nav aria-label="Paginação">
                {{ $usuarios->links('pagination::bootstrap-5') }}
            </nav>
        </div>
    </div>
</div>
@endsection
