<?php

namespace Tests\Feature\Web;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TokenExpirationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Testa se o token expira após 30 minutos de inatividade
     */
    public function test_token_expires_after_30_minutes_of_inactivity(): void
    {
        // Criar usuário e fazer login
        $user = User::factory()->create([
            'email' => 'kleber@email.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => 'kleber@email.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/produtos');

        // Verificar que o usuário foi autenticado e um token foi criado
        $token = $user->tokens()->first();
        $this->assertNotNull($token);

        // Simular que o token expirou
        $token->update(['expires_at' => now()->subMinute()]);

        // Tentar acessar uma rota protegida - deve ser redirecionado para login
        $response = $this->get('/produtos');
        
        $response->assertRedirect('/login');

        // Verificar que o token foi revogado
        $this->assertFalse($user->tokens()->exists());
    }

    /**
     * Testa se o token não expira quando há atividade dentro de 30 minutos
     */
    public function test_token_does_not_expire_within_30_minutes(): void
    {
        // Criar usuário e fazer login
        $user = User::factory()->create([
            'email' => 'kleber@email.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => 'kleber@email.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/produtos');

        // Verificar que o usuário foi autenticado
        $token = $user->tokens()->first();
        $this->assertNotNull($token);

        // Simular que o token ainda está dentro do prazo de expiração
        $token->update(['expires_at' => now()->addMinutes(15)]);

        // Tentar acessar uma rota protegida - deve ter sucesso
        $response = $this->get('/produtos');
        
        $response->assertOk();

        // Verificar que o token continua válido
        $this->assertTrue($user->tokens()->exists());
    }

    /**
     * Testa se o last_used_at é atualizado a cada requisição
     */
    public function test_last_used_at_is_updated_on_each_request(): void
    {
        // Criar usuário e fazer login
        $user = User::factory()->create([
            'email' => 'kleber@email.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => 'kleber@email.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/produtos');

        // Obter o token recém criado (pode estar com last_used_at nulo)
        $token = $user->tokens()->first();
        $firstLastUsedAt = $token->last_used_at; // pode ser null

        // Aguardar um segundo e fazer outra requisição
        sleep(1);
        
        $response = $this->get('/produtos');
        $response->assertOk();

        // Verificar que last_used_at foi atualizado na segunda requisição
        $token->refresh();
        $this->assertNotNull($token->last_used_at);
        if ($firstLastUsedAt) {
            $this->assertGreaterThan($firstLastUsedAt, $token->last_used_at);
        }
    }
}
