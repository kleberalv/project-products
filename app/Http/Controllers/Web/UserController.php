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
     * Listar todos os usuários
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
     * Mostrar formulário de criação
     */
    public function create()
    {
        return view('usuarios.create');
    }

    /**
     * Armazenar novo usuário
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
     * Mostrar formulário de edição
     */
    public function edit(User $usuario)
    {
        return view('usuarios.edit', compact('usuario'));
    }

    /**
     * Atualizar usuário
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
     * Deletar usuário
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
