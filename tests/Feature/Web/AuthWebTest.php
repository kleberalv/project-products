<?php

namespace Tests\Feature\Web;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthWebTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function login_web_com_credenciais_validas()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('Password@12345'),
        ]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'Password@12345',
        ]);

        $response->assertRedirect('/produtos');
        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function login_web_falha_com_senha_incorreta()
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('Password@12345'),
        ]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    /** @test */
    public function logout_web_revoga_token_com_soft_delete()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $user->createToken('test-token');
        $user->tokens()->latest()->first()->update([
            'expires_at' => now()->addHour(),
        ]);

        $response = $this->post('/logout');

        $this->assertNotNull($user->tokens()->withTrashed()->latest()->first()->deleted_at);
        $response->assertRedirect('/login');
    }
}
