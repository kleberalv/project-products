<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    /**
     * Autoriza criação de produtos (controlado na rota/middleware).
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Regras de validação para criação de produto.
     */
    public function rules(): array
    {
        return [
            'nome' => 'required|string|max:255|unique:produtos,nome',
            'descricao' => 'nullable|string',
            'preco' => 'required|numeric|min:0.01|max:999999999',
            'quantidade_estoque' => 'required|integer|min:0',
        ];
    }

    /**
     * Mensagens de erro em português para criação de produto.
     */
    public function messages(): array
    {
        return [
            'nome.required' => 'O nome do produto é obrigatório.',
            'nome.string' => 'O nome do produto deve ser um texto.',
            'nome.max' => 'O nome do produto deve ter no máximo 255 caracteres.',
            'nome.unique' => 'Já existe um produto com este nome.',
            'descricao.string' => 'A descrição deve ser um texto.',
            'preco.required' => 'O preço é obrigatório.',
            'preco.numeric' => 'O preço deve ser numérico.',
            'preco.min' => 'O preço deve ser no mínimo 0,01.',
            'preco.max' => 'O preço não pode ultrapassar 999.999.999.',
            'quantidade_estoque.required' => 'A quantidade em estoque é obrigatória.',
            'quantidade_estoque.integer' => 'A quantidade em estoque deve ser um número inteiro.',
            'quantidade_estoque.min' => 'A quantidade em estoque deve ser no mínimo 0.',
        ];
    }
}
