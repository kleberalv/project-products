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
     *
     * @OA\Post(
     *     path="/api/register",
     *     summary="Registrar novo usuário",
     *     description="Cria um novo usuário e retorna token de autenticação",
     *     tags={"Autenticação"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","email","password","password_confirmation"},
     *             @OA\Property(property="name", type="string", example="João Silva"),
     *             @OA\Property(property="email", type="string", format="email", example="joao@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="senha123"),
     *             @OA\Property(property="password_confirmation", type="string", format="password", example="senha123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Usuário registrado com sucesso",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(response=422, description="Erro de validação")
     * )
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
     *
     * @OA\Post(
     *     path="/api/login",
     *     summary="Autenticar usuário",
     *     description="Realiza login e retorna token de autenticação Sanctum",
     *     tags={"Autenticação"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="email", type="string", format="email", example="admin@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login bem-sucedido",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(response=422, description="Credenciais inválidas")
     * )
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
     *
     * @OA\Post(
     *     path="/api/logout",
     *     summary="Fazer logout",
     *     description="Revoga o token de autenticação do usuário logado",
     *     tags={"Autenticação"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Logout realizado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Logout realizado com sucesso")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Não autenticado")
     * )
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
     *
     * @OA\Get(
     *     path="/api/me",
     *     summary="Obter usuário autenticado",
     *     description="Retorna os dados do usuário autenticado",
     *     tags={"Autenticação"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Dados do usuário",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(response=401, description="Não autenticado")
     * )
     */
    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'data' => $request->user(),
        ]);
    }
}
