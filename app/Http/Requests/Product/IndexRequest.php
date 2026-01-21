<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class IndexRequest extends FormRequest
{
    /**
     * Autoriza filtros de listagem para qualquer usuário autenticado.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Regras de validação para filtros de produtos (API).
     */
    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string'],
            'preco_min' => ['nullable', 'numeric', 'min:0'],
            'preco_max' => ['nullable', 'numeric', 'min:0', 'gte:preco_min'],
            'estoque_min' => ['nullable', 'integer', 'min:0'],
            'estoque_max' => ['nullable', 'integer', 'min:0', 'gte:estoque_min'],
        ];
    }

    /**
     * Mensagens de erro em português para filtros de produtos.
     */
    public function messages(): array
    {
        return [
            'search.string' => 'O termo de busca deve ser um texto.',
            'preco_min.numeric' => 'O preço mínimo deve ser numérico.',
            'preco_min.min' => 'O preço mínimo deve ser no mínimo 0.',
            'preco_max.numeric' => 'O preço máximo deve ser numérico.',
            'preco_max.min' => 'O preço máximo deve ser no mínimo 0.',
            'preco_max.gte' => 'O preço máximo deve ser maior ou igual ao preço mínimo.',
            'estoque_min.integer' => 'O estoque mínimo deve ser um número inteiro.',
            'estoque_min.min' => 'O estoque mínimo deve ser no mínimo 0.',
            'estoque_max.integer' => 'O estoque máximo deve ser um número inteiro.',
            'estoque_max.min' => 'O estoque máximo deve ser no mínimo 0.',
            'estoque_max.gte' => 'O estoque máximo deve ser maior ou igual ao estoque mínimo.',
        ];
    }
}
