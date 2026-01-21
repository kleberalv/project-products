<?php

namespace App\Http\Requests\Web;

use Illuminate\Foundation\Http\FormRequest;

class WebIndexProductRequest extends FormRequest
{
    /**
     * Autoriza filtros de listagem Web para produtos.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Regras de validação para filtros Web de produtos.
     */
    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string'],
            'preco_min' => ['nullable', 'numeric', 'min:0'],
            'preco_max' => ['nullable', 'numeric', 'min:0', 'gte:preco_min'],
            'estoque_min' => ['nullable', 'integer', 'min:0'],
            'estoque_max' => ['nullable', 'integer', 'min:0', 'gte:estoque_min'],
            'sort' => ['nullable', 'string'],
            'direction' => ['nullable', 'string', 'in:asc,desc'],
            'per_page' => ['nullable', 'integer', 'min:1'],
        ];
    }

    /**
     * Mensagens de erro em português para filtros Web de produtos.
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
            'sort.string' => 'O campo de ordenação deve ser um texto.',
            'direction.in' => 'A direção de ordenação deve ser asc ou desc.',
            'direction.string' => 'A direção de ordenação deve ser um texto.',
            'per_page.integer' => 'Itens por página deve ser um número inteiro.',
            'per_page.min' => 'Itens por página deve ser no mínimo 1.',
        ];
    }
}
