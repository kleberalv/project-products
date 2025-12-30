<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nome' => 'required|string|max:255|unique:produtos,nome',
            'descricao' => 'nullable|string',
            'preco' => 'required|numeric|min:0.01|max:999999999',
            'quantidade_estoque' => 'required|integer|min:0',
        ];
    }
}
