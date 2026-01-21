<?php

namespace Tests\Feature\Web;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * Testes de expiracao e atualizacao de tokens na camada Web.
 */
class TokenExpirationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Testa se o token expira após 30 minutos de inatividade
     */
    public function test_token_expires_after_30_minutes_of_inactivity(): void
    {
        $user = User::factory()->create([
            'email' => 'test@email.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => 'test@email.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/produtos');

        $token = $user->tokens()->first();
        $this->assertNotNull($token);

        $token->update(['expires_at' => now()->subMinute()]);

        $response = $this->get('/produtos');
        
        $response->assertRedirect('/login');

        $this->assertFalse($user->tokens()->exists());
    }

    /**
     * Testa se o token não expira quando há atividade dentro de 30 minutos
     */
    public function test_token_does_not_expire_within_30_minutes(): void
    {
        $user = User::factory()->create([
            'email' => 'test@email.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => 'test@email.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/produtos');

        $token = $user->tokens()->first();
        $this->assertNotNull($token);

        $token->update(['expires_at' => now()->addMinutes(15)]);

        $response = $this->get('/produtos');
        
        $response->assertOk();

        $this->assertTrue($user->tokens()->exists());
    }

    /**
     * Testa se o last_used_at é atualizado a cada requisição
     */
    public function test_last_used_at_is_updated_on_each_request(): void
    {
        $user = User::factory()->create([
            'email' => 'test@email.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => 'test@email.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/produtos');

        $token = $user->tokens()->first();
        $firstLastUsedAt = $token->last_used_at;

        sleep(1);
        
        $response = $this->get('/produtos');
        $response->assertOk();

        $token->refresh();
        $this->assertNotNull($token->last_used_at);
        if ($firstLastUsedAt) {
            $this->assertGreaterThan($firstLastUsedAt, $token->last_used_at);
        }
    }
}
