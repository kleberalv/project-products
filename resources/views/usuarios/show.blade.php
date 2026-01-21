@extends('layouts.app')

@section('title', 'Visualizar Usuário')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="bi bi-eye"></i> Usuário #{{ $usuario->id }}</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-12">
                        <label class="form-label fw-bold">Nome Completo</label>
                        <p class="form-control-plaintext">
                            {{ $usuario->name }}
                            @if($usuario->id === Auth::id())
                                <span class="badge bg-info">Você</span>
                            @endif
                        </p>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-12">
                        <label class="form-label fw-bold">Email</label>
                        <p class="form-control-plaintext">{{ $usuario->email }}</p>
                    </div>
                </div>

                <hr>

                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Data de Criação</label>
                        <p class="form-control-plaintext">
                            <i class="bi bi-calendar"></i> {{ $usuario->created_at->format('d/m/Y H:i:s') }}
                        </p>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">Última Atualização</label>
                        <p class="form-control-plaintext">
                            <i class="bi bi-clock"></i> {{ $usuario->updated_at->format('d/m/Y H:i:s') }}
                        </p>
                    </div>
                </div>

                <hr>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('usuarios.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Voltar
                    </a>
                    <div>
                        <a href="{{ route('usuarios.edit', $usuario->id) }}" class="btn btn-warning">
                            <i class="bi bi-pencil"></i> Editar
                        </a>
                        @if($usuario->id !== Auth::id())
                        <button type="button" class="btn btn-danger" onclick="window.confirmarExclusao({{ $usuario->id }})">
                            <i class="bi bi-trash"></i> Deletar
                        </button>
                        @endif
                    </div>
                </div>

                @if($usuario->id !== Auth::id())
                <form id="delete-form-{{ $usuario->id }}" action="{{ route('usuarios.destroy', $usuario->id) }}" method="POST" style="display: none;">
                    @csrf
                    @method('DELETE')
                </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
