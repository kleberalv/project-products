@extends('layouts.app')

@section('title', 'Novo Produto')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-plus-circle"></i> Novo Produto</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('produtos.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control @error('nome') is-invalid @enderror" 
                               id="nome" 
                               name="nome" 
                               value="{{ old('nome') }}" 
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
                                  rows="4">{{ old('descricao') }}</textarea>
                        @error('descricao')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Opcional - Descreva o produto</small>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="preco_display" class="form-label">Preço (R$) <span class="text-danger">*</span></label>
                                    <input type="hidden"
                                        name="preco"
                                        id="preco"
                                        value="{{ old('preco') }}">
                                    <input type="text" 
                                        class="form-control currency-input @error('preco') is-invalid @enderror" 
                                        id="preco_display" 
                                        name="preco_display"
                                        data-target="preco"
                                        data-max="99999999.99"
                                        value="{{ old('preco') }}" 
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
                                       value="{{ old('quantidade_estoque', 0) }}" 
                                       min="0" 
                                       required>
                                @error('quantidade_estoque')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('produtos.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Salvar Produto
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
