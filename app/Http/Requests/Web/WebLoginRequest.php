<?php

namespace App\Http\Requests\Web;

use Illuminate\Foundation\Http\FormRequest;

class WebLoginRequest extends FormRequest
{
    /**
     * Autoriza tentativa de login Web.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Regras de validação para login Web.
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email',
            'password' => 'required',
        ];
    }

    /**
     * Mensagens de erro em português para login Web.
     */
    public function messages(): array
    {
        return [
            'email.required' => 'O e-mail é obrigatório.',
            'email.email' => 'Informe um e-mail válido.',
            'password.required' => 'A senha é obrigatória.',
        ];
    }
}
