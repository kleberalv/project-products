@extends('layouts.app')

@section('title', 'Produtos - Listagem')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-white py-3">
                <div class="row align-items-center">
                    <div class="col">
                        <h4 class="mb-0"><i class="bi bi-box-seam"></i> Produtos</h4>
                    </div>
                    <div class="col-auto">
                        <a href="{{ route('produtos.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> Novo Produto
                        </a>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <!-- Filtros -->
                <form method="GET" action="{{ route('produtos.index') }}" class="row g-3 mb-4" data-show-spinner="true">
                    <div class="col-md-4">
                        <label for="search" class="form-label">Buscar</label>
                        <input type="text" class="form-control" id="search" name="search"
                            value="{{ request('search') }}"
                            placeholder="Nome ou descrição">
                    </div>
                    <div class="col-md-2">
                        <label for="preco_min_display" class="form-label">Preço Mín.</label>
                        <input type="hidden" id="preco_min" name="preco_min" value="{{ request('preco_min') }}">
                        <input type="text" class="form-control currency-input" id="preco_min_display" name="preco_min_display"
                            data-target="preco_min" data-max="99999999.99"
                            value="{{ request('preco_min') }}"
                            inputmode="decimal" autocomplete="off" placeholder="0,00">
                    </div>
                    <div class="col-md-2">
                        <label for="preco_max_display" class="form-label">Preço Máx.</label>
                        <input type="hidden" id="preco_max" name="preco_max" value="{{ request('preco_max') }}">
                        <input type="text" class="form-control currency-input" id="preco_max_display" name="preco_max_display"
                            data-target="preco_max" data-max="99999999.99"
                            value="{{ request('preco_max') }}"
                            inputmode="decimal" autocomplete="off" placeholder="999.999,99">
                    </div>
                    <div class="col-md-2">
                        <label for="estoque_min" class="form-label">Estoque Mín.</label>
                        <input type="number" class="form-control" id="estoque_min" name="estoque_min"
                            value="{{ request('estoque_min') }}"
                            min="0" step="1" placeholder="0">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-secondary">
                                <i class="bi bi-search"></i> Filtrar
                            </button>
                        </div>
                    </div>
                </form>

                @if(request()->hasAny(['search', 'preco_min', 'preco_max', 'estoque_min']))
                <div class="mb-3">
                    <a href="{{ route('produtos.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-x-circle"></i> Limpar Filtros
                    </a>
                </div>
                @endif

                <!-- Tabela -->
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">
                                    <a href="{{ route('produtos.index', array_merge(request()->query(), ['sort' => 'id', 'direction' => request('sort') === 'id' && request('direction') === 'asc' ? 'desc' : 'asc'])) }}"
                                        class="text-decoration-none text-dark" title="Ordenar por ID" data-action="Ordenando...">
                                        ID
                                        <i class="bi {{ request('sort') === 'id' ? (request('direction') === 'asc' ? 'bi-arrow-up' : 'bi-arrow-down') : 'bi-arrow-down-up' }}"></i>
                                    </a>
                                </th>
                                <th width="25%">
                                    <a href="{{ route('produtos.index', array_merge(request()->query(), ['sort' => 'nome', 'direction' => request('sort') === 'nome' && request('direction') === 'asc' ? 'desc' : 'asc'])) }}"
                                        class="text-decoration-none text-dark" title="Ordenar por Nome" data-action="Ordenando...">
                                        Nome
                                        <i class="bi {{ request('sort') === 'nome' ? (request('direction') === 'asc' ? 'bi-arrow-up' : 'bi-arrow-down') : 'bi-arrow-down-up' }}"></i>
                                    </a>
                                </th>
                                <th width="30%">Descrição</th>
                                <th width="12%">
                                    <a href="{{ route('produtos.index', array_merge(request()->query(), ['sort' => 'preco', 'direction' => request('sort') === 'preco' && request('direction') === 'asc' ? 'desc' : 'asc'])) }}"
                                        class="text-decoration-none text-dark" title="Ordenar por Preço" data-action="Ordenando...">
                                        Preço
                                        <i class="bi {{ request('sort') === 'preco' ? (request('direction') === 'asc' ? 'bi-arrow-up' : 'bi-arrow-down') : 'bi-arrow-down-up' }}"></i>
                                    </a>
                                </th>
                                <th width="10%">
                                    <a href="{{ route('produtos.index', array_merge(request()->query(), ['sort' => 'quantidade_estoque', 'direction' => request('sort') === 'quantidade_estoque' && request('direction') === 'asc' ? 'desc' : 'asc'])) }}"
                                        class="text-decoration-none text-dark" title="Ordenar por Estoque" data-action="Ordenando...">
                                        Estoque
                                        <i class="bi {{ request('sort') === 'quantidade_estoque' ? (request('direction') === 'asc' ? 'bi-arrow-up' : 'bi-arrow-down') : 'bi-arrow-down-up' }}"></i>
                                    </a>
                                </th>
                                <th width="18%" class="text-end">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($produtos as $produto)
                            <tr>
                                <td>{{ $produto->id }}</td>
                                <td><strong>{{ $produto->nome }}</strong></td>
                                <td>{{ Str::limit($produto->descricao, 50) }}</td>
                                <td>
                                    <span class="badge bg-success">
                                        R$ {{ number_format($produto->preco, 2, ',', '.') }}
                                    </span>
                                </td>
                                <td>
                                    @if($produto->quantidade_estoque > 10)
                                    <span class="badge bg-success">{{ $produto->quantidade_estoque }}</span>
                                    @elseif($produto->quantidade_estoque > 0)
                                    <span class="badge bg-warning">{{ $produto->quantidade_estoque }}</span>
                                    @else
                                    <span class="badge bg-danger">{{ $produto->quantidade_estoque }}</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('produtos.show', $produto->id) }}"
                                            class="btn btn-info" title="Visualizar">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('produtos.edit', $produto->id) }}"
                                            class="btn btn-warning" title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button type="button" class="btn btn-danger"
                                            onclick="confirmarExclusao({{ $produto->id }})"
                                            title="Deletar">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>

                                    <form id="delete-form-{{ $produto->id }}"
                                        action="{{ route('produtos.destroy', $produto->id) }}"
                                        method="POST" style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                                    <p class="text-muted mt-2">Nenhum produto encontrado</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Paginação -->
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div class="text-muted small">
                        Mostrando {{ $produtos->firstItem() ?? 0 }} - {{ $produtos->lastItem() ?? 0 }}
                        de {{ $produtos->total() }} produtos
                    </div>
                    <nav aria-label="Paginação">
                        {{ $produtos->appends(request()->query())->links('pagination::bootstrap-5') }}
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection