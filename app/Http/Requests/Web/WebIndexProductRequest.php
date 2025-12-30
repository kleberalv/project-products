<?php

namespace App\Http\Requests\Web;

use Illuminate\Foundation\Http\FormRequest;

class WebIndexProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

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
}
