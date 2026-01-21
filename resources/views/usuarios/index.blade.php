@extends('layouts.app')

@section('title', 'Usuários - Listagem')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-white py-3">
                <div class="row align-items-center">
                    <div class="col">
                        <h4 class="mb-0"><i class="bi bi-person-circle"></i> Usuários</h4>
                    </div>
                    <div class="col-auto">
                        <a href="{{ route('usuarios.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> Novo Usuário
                        </a>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <form method="GET" action="{{ route('usuarios.index') }}" class="row g-3 mb-4" data-show-spinner="true">
                    <div class="col-md-8">
                        <label for="search" class="form-label">Buscar</label>
                        <input type="text" class="form-control" id="search" name="busca"
                            value="{{ request('busca') }}"
                            placeholder="Nome ou email">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-secondary">
                                <i class="bi bi-search"></i> Filtrar
                            </button>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-grid">
                            <a href="{{ route('usuarios.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle"></i> Limpar
                            </a>
                        </div>
                    </div>
                </form>

                @if(request('busca'))
                <div class="mb-3">
                    <a href="{{ route('usuarios.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-x-circle"></i> Limpar Filtros
                    </a>
                </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">ID</th>
                                <th width="30%">Nome</th>
                                <th width="35%">Email</th>
                                <th width="15%">Cadastrado em</th>
                                <th width="15%" class="text-end">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($usuarios as $usuario)
                            <tr>
                                <td>{{ $usuario->id }}</td>
                                <td>
                                    <strong>{{ $usuario->name }}</strong>
                                    @if($usuario->id === Auth::id())
                                        <span class="badge bg-info">Você</span>
                                    @endif
                                </td>
                                <td>{{ $usuario->email }}</td>
                                <td>{{ $usuario->created_at->format('d/m/Y H:i') }}</td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('usuarios.show', $usuario->id) }}"
                                            class="btn btn-info" title="Visualizar">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('usuarios.edit', $usuario->id) }}"
                                            class="btn btn-warning" title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        @if($usuario->id !== Auth::id())
                                        <button type="button" class="btn btn-danger"
                                            onclick="window.confirmarExclusao({{ $usuario->id }})"
                                            title="Deletar">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                        @endif
                                    </div>

                                    @if($usuario->id !== Auth::id())
                                    <form id="delete-form-{{ $usuario->id }}"
                                        action="{{ route('usuarios.destroy', $usuario->id) }}"
                                        method="POST" style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4">
                                    <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                                    <p class="text-muted mt-2">Nenhum usuário encontrado</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        @if($usuarios->hasPages())
        <div class="card mt-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted">
                        Mostrando {{ $usuarios->firstItem() ?? 0 }} a {{ $usuarios->lastItem() ?? 0 }} de {{ $usuarios->total() }} usuários
                    </div>
                    <nav>
                        {{ $usuarios->appends(request()->query())->links('pagination::bootstrap-5') }}
                    </nav>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
