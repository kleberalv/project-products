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
     * Login de usuário
     *
     * @OA\Post(
     *     path="/auth/login",
     *     summary="Autenticar usuário",
     *     description="Realiza login e retorna token de autenticação Sanctum",
     *     tags={"Autenticação"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="email", type="string", format="email", example="firstdecision@email.com"),
     *             @OA\Property(property="password", type="string", format="password", example="senha123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login bem-sucedido",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(response=422, description="Credenciais inválidas")
     * )
     * 
     * @param LoginRequest $request A requisição contendo email e senha do usuário.
     * @return JsonResponse A resposta JSON com dados do usuário e token de autenticação Sanctum.
     *
     * @throws ValidationException Se as credenciais fornecidas forem inválidas.
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

        $user->tokens()->delete();

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
     * Registrar novo usuário (requer autenticação)
     *
     * @OA\Post(
     *     path="/auth/register",
     *     summary="Registrar novo usuário",
     *     description="Cria um novo usuário no sistema (apenas usuários autenticados)",
     *     tags={"Autenticação"},
     *     security={{"bearerAuth":{}}},
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
     * 
     * @param RegisterRequest $request A requisição HTTP contendo dados do novo usuário (name, email, password, password_confirmation).
     * @return JsonResponse A resposta JSON com dados do usuário criado (código 201).
     *
     * @throws \Exception Se houver erro na criação do usuário.
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $data = $request->validated();

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        return response()->json([
            'data' => $user,
            'message' => 'Usuário registrado com sucesso',
        ], 201);
    }

    /**
     * Obter dados do usuário autenticado
     *
     * @OA\Get(
     *     path="/auth/me",
     *     summary="Obter usuário autenticado",
     *     description="Retorna os dados do usuário autenticado. Use o token obtido no login.",
     *     tags={"Autenticação"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Dados do usuário autenticado",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(response=401, description="Não autenticado")
     * )
     * 
     * @param Request $request A requisição HTTP com o usuário autenticado.
     * @return JsonResponse A resposta JSON com os dados do usuário autenticado.
     */
    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'data' => $request->user(),
        ]);
    }

    /**
     * Logout de usuário
     *
     * @OA\Post(
     *     path="/auth/logout",
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
     * 
     * @param Request $request A requisição HTTP com o usuário autenticado.
     * @return JsonResponse A resposta JSON indicando sucesso do logout.
     *
     * @throws \Exception Se houver erro ao revogar o token.
     */
    public function logout(Request $request): JsonResponse
    {
        $currentToken = $request->user()->currentAccessToken();
        
        if ($currentToken) {
            $currentToken->delete();
        }

        return response()->json([
            'message' => 'Logout realizado com sucesso',
        ]);
    }
}
