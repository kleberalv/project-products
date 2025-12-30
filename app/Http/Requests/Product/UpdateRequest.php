<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $produtoRoute = $this->route('produto');
        $produtoId = is_object($produtoRoute) ? $produtoRoute->id : ($produtoRoute ?? $this->route('id'));

        return [
            'nome' => 'sometimes|required|string|max:255|unique:produtos,nome,' . $produtoId,
            'descricao' => 'nullable|string',
            'preco' => 'sometimes|required|numeric|min:0.01|max:999999999',
            'quantidade_estoque' => 'sometimes|required|integer|min:0',
        ];
    }
}
