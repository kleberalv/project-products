<?php

namespace Tests\Feature\Web;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductWebTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function usuario_pode_listar_produtos_web()
    {
        Product::factory(3)->create();
        $response = $this->actingAs($this->user)->get('/produtos');
        $response->assertStatus(200);
        $response->assertViewHas('produtos');
    }

    /** @test */
    public function usuario_pode_criar_produto_web()
    {
        $response = $this->actingAs($this->user)->post('/produtos', [
            'nome' => 'Novo Produto',
            'descricao' => 'Descrição do produto',
            'preco' => 99.99,
            'quantidade_estoque' => 10,
        ]);
        $response->assertRedirect('/produtos');
        $this->assertDatabaseHas('produtos', ['nome' => 'Novo Produto']);
    }

    /** @test */
    public function usuario_pode_atualizar_produto_web()
    {
        $product = Product::factory()->create(['nome' => 'Produto Antigo']);
        $response = $this->actingAs($this->user)->put("/produtos/{$product->id}", [
            'nome' => 'Produto Atualizado',
            'descricao' => 'Descrição atualizada',
            'preco' => 199.99,
            'quantidade_estoque' => 20,
        ]);
        $response->assertRedirect('/produtos');
        $this->assertDatabaseHas('produtos', ['nome' => 'Produto Atualizado']);
    }

    /** @test */
    public function usuario_pode_deletar_produto_web()
    {
        $product = Product::factory()->create();
        $response = $this->actingAs($this->user)->delete("/produtos/{$product->id}");
        $response->assertRedirect('/produtos');
        $this->assertNotNull($product->fresh()->deleted_at);
    }

    /** @test */
    public function validacao_preco_maximo_web()
    {
        $response = $this->actingAs($this->user)->post('/produtos', [
            'nome' => 'Produto Caro',
            'descricao' => 'Descrição',
            'preco' => 999999999.99,
            'quantidade_estoque' => 10,
        ]);
        $response->assertSessionHasErrors('preco');
    }

    /** @test */
    public function nome_produto_deve_ser_unico_web()
    {
        Product::factory()->create(['nome' => 'Nome Único']);
        $response = $this->actingAs($this->user)->post('/produtos', [
            'nome' => 'Nome Único',
            'descricao' => 'Descrição',
            'preco' => 99.99,
            'quantidade_estoque' => 10,
        ]);
        $response->assertSessionHasErrors('nome');
    }

    /** @test */
    public function usuario_pode_listar_produtos_deletados_web()
    {
        Product::factory(2)->create()->each(fn($p) => $p->delete());
        $response = $this->actingAs($this->user)->get('/produtos');
        $response->assertStatus(200);
    }
}
