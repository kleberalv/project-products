<?php

namespace App\Http\Requests\Web;

use Illuminate\Foundation\Http\FormRequest;

class UserUpdateRequest extends FormRequest
{
    /**
     * Autoriza atualização de usuários via Web (controlado por middleware).
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Regras de validação para atualização de usuário Web.
     */
    public function rules(): array
    {
        $userId = $this->route('usuario')?->id ?? $this->route('id');

        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $userId,
            'password' => 'nullable|min:8',
        ];
    }

    /**
     * Mensagens de erro em português para atualização de usuário Web.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'O nome é obrigatório.',
            'name.string' => 'O nome deve ser um texto.',
            'name.max' => 'O nome deve ter no máximo 255 caracteres.',
            'email.required' => 'O e-mail é obrigatório.',
            'email.email' => 'Informe um e-mail válido.',
            'email.unique' => 'Já existe um usuário com este e-mail.',
            'password.min' => 'A senha deve ter pelo menos 8 caracteres.',
        ];
    }
}
