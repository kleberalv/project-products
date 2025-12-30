<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function pode_registrar_novo_usuario()
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'Kleber Alves',
            'email' => 'kleber@email.com',
            'password' => 'senha123',
            'password_confirmation' => 'senha123',
        ]);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'data' => [
                         'user',
                         'access_token',
                         'token_type',
                     ],
                     'message',
                 ]);

        $this->assertDatabaseHas('users', [
            'email' => 'kleber@email.com',
        ]);
    }

    /** @test */
    public function nao_pode_registrar_com_email_duplicado()
    {
        User::factory()->create(['email' => 'kleber@email.com']);

        $response = $this->postJson('/api/auth/register', [
            'name' => 'Kleber Alves',
            'email' => 'kleber@email.com',
            'password' => 'senha123',
            'password_confirmation' => 'senha123',
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['email']);
    }

    /** @test */
    public function pode_fazer_login_com_credenciais_validas()
    {
        $user = User::factory()->create([
            'email' => 'kleber@email.com',
            'password' => bcrypt('senha123'),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'kleber@email.com',
            'password' => 'senha123',
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data' => [
                         'user',
                         'access_token',
                         'token_type',
                     ],
                     'message',
                 ]);
    }

    /** @test */
    public function nao_pode_fazer_login_com_credenciais_invalidas()
    {
        User::factory()->create([
            'email' => 'kleber@email.com',
            'password' => bcrypt('senha123'),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'kleber@email.com',
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['email']);
    }

    /** @test */
    public function pode_obter_dados_do_usuario_autenticado()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer $token")
                         ->getJson('/api/auth/me');

        $response->assertStatus(200)
                 ->assertJsonPath('data.id', $user->id)
                 ->assertJsonPath('data.email', $user->email);
    }

    /** @test */
    public function pode_fazer_logout()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer $token")
                         ->postJson('/api/auth/logout');

        $response->assertStatus(200)
                 ->assertJsonPath('message', 'Logout realizado com sucesso');
    }

    /** @test */
    public function nao_pode_acessar_rotas_protegidas_sem_autenticacao()
    {
        $response = $this->getJson('/api/produtos');

        $response->assertStatus(401);
    }
}
