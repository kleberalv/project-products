@extends('layouts.app')

@section('title', 'Visualizar Produto')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="bi bi-eye"></i> Produto #{{ $produto->id }}</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-12">
                        <label class="form-label fw-bold">Nome</label>
                        <p class="form-control-plaintext">{{ $produto->nome }}</p>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-12">
                        <label class="form-label fw-bold">Descrição</label>
                        <p class="form-control-plaintext">{{ $produto->descricao ?? 'Sem descrição' }}</p>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Preço</label>
                        <p class="form-control-plaintext">
                            <span class="badge bg-success fs-5">
                                R$ {{ number_format($produto->preco, 2, ',', '.') }}
                            </span>
                        </p>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">Quantidade em Estoque</label>
                        <p class="form-control-plaintext">
                            @if($produto->quantidade_estoque > 10)
                                <span class="badge bg-success fs-5">{{ $produto->quantidade_estoque }} unidades</span>
                            @elseif($produto->quantidade_estoque > 0)
                                <span class="badge bg-warning fs-5">{{ $produto->quantidade_estoque }} unidades</span>
                            @else
                                <span class="badge bg-danger fs-5">{{ $produto->quantidade_estoque }} unidades</span>
                            @endif
                        </p>
                    </div>
                </div>

                <hr>

                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Data de Criação</label>
                        <p class="form-control-plaintext">
                            <i class="bi bi-calendar"></i> {{ $produto->created_at->format('d/m/Y H:i:s') }}
                        </p>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">Última Atualização</label>
                        <p class="form-control-plaintext">
                            <i class="bi bi-clock"></i> {{ $produto->updated_at->format('d/m/Y H:i:s') }}
                        </p>
                    </div>
                </div>

                <hr>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('produtos.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Voltar
                    </a>
                    <div>
                        <a href="{{ route('produtos.edit', $produto->id) }}" class="btn btn-warning">
                            <i class="bi bi-pencil"></i> Editar
                        </a>
                        <button type="button" class="btn btn-danger" onclick="confirmarExclusao()">
                            <i class="bi bi-trash"></i> Deletar
                        </button>
                    </div>
                </div>

                <form id="delete-form" action="{{ route('produtos.destroy', $produto->id) }}" method="POST" style="display: none;">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function confirmarExclusao() {
    if (confirm('Tem certeza que deseja deletar este produto?')) {
        document.getElementById('delete-form').submit();
    }
}
</script>
@endpush
