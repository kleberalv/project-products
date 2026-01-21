<?php

namespace Tests\Feature\Web;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Testes de CRUD de usuarios via Web.
 */
class UsuarioWebTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create();
    }

    /** @test */
    public function pode_listar_usuarios()
    {
        User::factory()->count(3)->create();
        $response = $this->actingAs($this->admin)->get('/usuarios');
        $response->assertStatus(200);
        $response->assertViewHas('usuarios');
    }

    /** @test */
    public function pode_criar_usuario()
    {
        $response = $this->actingAs($this->admin)->post('/usuarios', [
            'name' => 'Novo Usuário',
            'email' => 'novo@usuario.com',
            'password' => 'Senha@12345',
            'password_confirmation' => 'Senha@12345',
        ]);

        $response->assertRedirect('/usuarios');
        $this->assertDatabaseHas('users', ['email' => 'novo@usuario.com']);
    }

    /** @test */
    public function nao_pode_criar_usuario_com_email_duplicado()
    {
        User::factory()->create(['email' => 'duplicado@usuario.com']);

        $response = $this->actingAs($this->admin)->post('/usuarios', [
            'name' => 'Outro Usuário',
            'email' => 'duplicado@usuario.com',
            'password' => 'Senha@12345',
            'password_confirmation' => 'Senha@12345',
        ]);

        $response->assertSessionHasErrors('email');
    }

    /** @test */
    public function pode_editar_usuario()
    {
        $usuario = User::factory()->create(['email' => 'editar@usuario.com']);

        $response = $this->actingAs($this->admin)->put("/usuarios/{$usuario->id}", [
            'name' => 'Usuário Editado',
            'email' => 'editar@usuario.com',
        ]);

        $response->assertRedirect('/usuarios');
        $this->assertDatabaseHas('users', ['id' => $usuario->id, 'name' => 'Usuário Editado']);
    }

    /** @test */
    public function pode_deletar_usuario()
    {
        $usuario = User::factory()->create();

        $response = $this->actingAs($this->admin)->delete("/usuarios/{$usuario->id}");

        $response->assertRedirect('/usuarios');
        $this->assertSoftDeleted('users', ['id' => $usuario->id]);
    }

    /** @test */
    public function nao_pode_deletar_o_proprio_usuario()
    {
        $response = $this->actingAs($this->admin)->delete("/usuarios/{$this->admin->id}");
        $response->assertSessionHasErrors();
    }
}
