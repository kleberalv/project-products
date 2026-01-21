<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\WebLoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Exibe o formulário de login para o usuário.
     *
     * @return \Illuminate\View\View A view do formulário de login.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Processa a autenticação do usuário através de email e senha.
     *
     * @param WebLoginRequest $request A requisição contendo email e senha do usuário.
     * @return \Illuminate\Http\RedirectResponse Redireciona para o dashboard se bem-sucedido ou retorna ao login com erros.
     */
    public function login(WebLoginRequest $request)
    {
        $credentials = $request->validated();

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            return redirect()->intended('/produtos');
        }

        return back()->withErrors([
            'email' => 'Credenciais inválidas',
        ])->onlyInput('email');
    }

    /**
     * Realiza o logout do usuário, revogando todos os tokens de acesso.
     *
     * @param Request $request A requisição HTTP.
     * @return \Illuminate\Http\RedirectResponse Redireciona para a página de login.
     *
     * @throws \Exception Se houver erro na revogação dos tokens.
     */
    public function logout(Request $request)
    {
        if ($user = Auth::user()) {
            $user->tokens()->whereNull('deleted_at')->delete();
        }
        
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}
