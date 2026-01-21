<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\UserStoreRequest;
use App\Http\Requests\Web\UserUpdateRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Exibe a lista paginada de todos os usuários registrados.
     *
     * @param Request $request A requisição HTTP contendo parâmetros de busca.
     * @return \Illuminate\View\View A view com a lista de usuários.
     *
     * @throws \Exception Se houver erro ao recuperar os usuários.
     */
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->has('busca') && !empty($request->input('busca'))) {
            $busca = $request->input('busca');
            $query->where('name', 'like', "%{$busca}%")
                  ->orWhere('email', 'like', "%{$busca}%");
        }

        $usuarios = $query->paginate(10);

        return view('usuarios.index', compact('usuarios'));
    }

    /**
     * Exibe o formulário para criação de um novo usuário.
     *
     * @return \Illuminate\View\View A view do formulário de criação de usuário.
     */
    public function create()
    {
        return view('usuarios.create');
    }

    /**
     * Armazena um novo usuário no sistema com senha criptografada.
     *
     * @param UserStoreRequest $request A requisição HTTP contendo dados do novo usuário (name, email, password).
     * @return \Illuminate\Http\RedirectResponse Redireciona para a lista de usuários com mensagem de sucesso.
     *
     * @throws \Exception Se houver erro na validação ou criação do usuário.
     */
    public function store(UserStoreRequest $request)
    {
        $validated = $request->validated();

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        return redirect('/usuarios')->with('success', 'Usuário criado com sucesso!');
    }

    /**
     * Exibe o formulário de edição para um usuário específico.
     *
     * @param User $usuario O usuário a ser editado.
     * @return \Illuminate\View\View A view do formulário de edição.
     */
    public function edit(User $usuario)
    {
        return view('usuarios.edit', compact('usuario'));
    }

    /**
     * Atualiza os dados de um usuário existente.
     *
     * @param UserUpdateRequest $request A requisição HTTP contendo dados atualizados (name, email, password opcional).
     * @param User $usuario O usuário a ser atualizado.
     * @return \Illuminate\Http\RedirectResponse Redireciona para a lista de usuários com mensagem de sucesso.
     *
     * @throws \Exception Se houver erro na validação ou atualização do usuário.
     */
    public function update(UserUpdateRequest $request, User $usuario)
    {
        $validated = $request->validated();

        $usuario->name = $validated['name'];
        $usuario->email = $validated['email'];

        if (!empty($validated['password'])) {
            $usuario->password = Hash::make($validated['password']);
        }

        $usuario->save();

        return redirect('/usuarios')->with('success', 'Usuário atualizado com sucesso!');
    }

    /**
     * Deleta um usuário do sistema, impedindo exclusão da própria conta.
     *
     * @param User $usuario O usuário a ser deletado.
     * @return \Illuminate\Http\RedirectResponse Redireciona para a lista de usuários com mensagem de sucesso ou erro.
     *
     * @throws \Exception Se o usuário tentar deletar sua própria conta.
     */
    public function destroy(User $usuario)
    {
        if ($usuario->id === auth()->id()) {
            return back()->withErrors('Você não pode deletar sua própria conta!');
        }

        $usuario->delete();
        return redirect('/usuarios')->with('success', 'Usuário deletado com sucesso!');
    }
}
