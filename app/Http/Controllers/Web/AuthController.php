<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\WebLoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Mostrar formulário de login
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Processar login
     */
    public function login(WebLoginRequest $request)
    {
        $credentials = $request->validated();

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::user();
            $newToken = $user->createToken('web-login');
            $minutes = (int) env('SANCTUM_TOKEN_EXP_MINUTES', 30);
            if ($tokenModel = $user->tokens()->latest()->first()) {
                $tokenModel->update([
                    'last_used_at' => now(),
                    'expires_at' => now()->addMinutes($minutes),
                ]);
            }

            return redirect()->intended('/produtos');
        }

        return back()->withErrors([
            'email' => 'Credenciais inválidas',
        ])->onlyInput('email');
    }

    /**
     * Fazer logout
     */
    public function logout(Request $request)
    {
        if ($user = Auth::user()) {
            $user->tokens()->whereNull('deleted_at')->update([
                'revoked_at' => now(),
                'deleted_at' => now(),
            ]);
        }
        
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}
