@extends('layouts.app')

@section('title', 'Editar Produto')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0"><i class="bi bi-pencil"></i> Editar Produto #{{ $produto->id }}</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('produtos.update', $produto->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control @error('nome') is-invalid @enderror" 
                               id="nome" 
                               name="nome" 
                               value="{{ old('nome', $produto->nome) }}" 
                               required 
                               autofocus>
                        @error('nome')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="descricao" class="form-label">Descrição</label>
                        <textarea class="form-control @error('descricao') is-invalid @enderror" 
                                  id="descricao" 
                                  name="descricao" 
                                  rows="4">{{ old('descricao', $produto->descricao) }}</textarea>
                        @error('descricao')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                    <label for="preco_display" class="form-label">Preço (R$) <span class="text-danger">*</span></label>
                                    <input type="hidden"
                                        name="preco"
                                        id="preco"
                                        value="{{ old('preco', $produto->preco) }}">
                                    <input type="text" 
                                        class="form-control currency-input @error('preco') is-invalid @enderror" 
                                        id="preco_display" 
                                        name="preco_display"
                                        data-target="preco"
                                        data-max="99999999.99"
                                        value="{{ old('preco', $produto->preco) }}" 
                                        inputmode="decimal"
                                        autocomplete="off"
                                        required>
                                @error('preco')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="quantidade_estoque" class="form-label">Quantidade em Estoque <span class="text-danger">*</span></label>
                                <input type="number" 
                                       class="form-control @error('quantidade_estoque') is-invalid @enderror" 
                                       id="quantidade_estoque" 
                                       name="quantidade_estoque" 
                                       value="{{ old('quantidade_estoque', $produto->quantidade_estoque) }}" 
                                       min="0" 
                                       required>
                                @error('quantidade_estoque')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info">
                        <small>
                            <strong>Criado em:</strong> {{ $produto->created_at->format('d/m/Y H:i') }} <br>
                            <strong>Última atualização:</strong> {{ $produto->updated_at->format('d/m/Y H:i') }}
                        </small>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('produtos.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-save"></i> Atualizar Produto
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
