<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Registrar novo usuário
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $data = $request->validated();

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        $newToken = $user->createToken('auth_token');
        // Atualiza expiração e último uso
        $minutes = (int) env('SANCTUM_TOKEN_EXP_MINUTES', 30);
        if ($tokenModel = $user->tokens()->latest()->first()) {
            $tokenModel->update([
                'last_used_at' => now(),
                'expires_at' => now()->addMinutes($minutes),
            ]);
        }

        $token = $newToken->plainTextToken;

        return response()->json([
            'data' => [
                'user' => $user,
                'access_token' => $token,
                'token_type' => 'Bearer',
            ],
            'message' => 'Usuário registrado com sucesso',
        ], 201);
    }

    /**
     * Login de usuário
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $data = $request->validated();

        $user = User::where('email', $data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Credenciais inválidas.'],
            ]);
        }

        // Revoga tokens antigos (opcional - descomente se quiser um token por sessão)
        // $user->tokens()->delete();

        $newToken = $user->createToken('auth_token');
        $minutes = (int) env('SANCTUM_TOKEN_EXP_MINUTES', 30);
        if ($tokenModel = $user->tokens()->latest()->first()) {
            $tokenModel->update([
                'last_used_at' => now(),
                'expires_at' => now()->addMinutes($minutes),
            ]);
        }
        $token = $newToken->plainTextToken;

        return response()->json([
            'data' => [
                'user' => $user,
                'access_token' => $token,
                'token_type' => 'Bearer',
            ],
            'message' => 'Login realizado com sucesso',
        ]);
    }

    /**
     * Logout de usuário
     */
    public function logout(Request $request): JsonResponse
    {
        // Revoga o token atual preservando histórico com soft delete
        $user = $request->user();
        
        // currentAccessToken retorna do Sanctum, precisamos usar nosso modelo
        $currentToken = $request->user()->currentAccessToken();
        if ($currentToken && $tokenModel = $user->tokens()->where('id', $currentToken->id)->first()) {
            // Marcar como revogado e deletado (soft delete)
            $tokenModel->update([
                'revoked_at' => now(),
                'deleted_at' => now(),
            ]);
        }

        return response()->json([
            'message' => 'Logout realizado com sucesso',
        ]);
    }

    /**
     * Obter dados do usuário autenticado
     */
    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'data' => $request->user(),
        ]);
    }
}
