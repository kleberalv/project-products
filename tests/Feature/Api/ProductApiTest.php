<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Testes de CRUD e filtros de produtos via API.
 */
class ProductApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Sanctum::actingAs(User::factory()->create());
    }

    /** @test */
    public function pode_listar_produtos()
    {
        Product::factory()->count(5)->create();

        $response = $this->getJson('/api/produtos');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data' => [
                         '*' => ['id', 'nome', 'descricao', 'preco', 'quantidade_estoque']
                     ],
                     'meta',
                     'message',
                 ])
                 ->assertJsonCount(5, 'data');
    }

    /** @test */
    public function pode_criar_produto_com_dados_validos()
    {
        $dados = [
            'nome' => 'Produto Teste API',
            'descricao' => 'Descrição do produto',
            'preco' => 299.99,
            'quantidade_estoque' => 50,
        ];

        $response = $this->postJson('/api/produtos', $dados);

        $response->assertStatus(201)
                 ->assertJsonPath('message', 'Produto criado com sucesso')
                 ->assertJsonPath('data.nome', 'Produto Teste API');

        $this->assertDatabaseHas('produtos', $dados);
    }

    /** @test */
    public function nao_pode_criar_produto_com_nome_duplicado()
    {
        Product::factory()->create(['nome' => 'Produto Existente']);

        $response = $this->postJson('/api/produtos', [
            'nome' => 'Produto Existente',
            'preco' => 100,
            'quantidade_estoque' => 10,
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['nome']);
    }

    /** @test */
    public function nao_pode_criar_produto_com_preco_negativo()
    {
        $response = $this->postJson('/api/produtos', [
            'nome' => 'Produto Teste',
            'preco' => -10,
            'quantidade_estoque' => 10,
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['preco']);
    }

    /** @test */
    public function pode_visualizar_produto_especifico()
    {
        $produto = Product::factory()->create();

        $response = $this->getJson("/api/produtos/{$produto->id}");

        $response->assertStatus(200)
                 ->assertJsonPath('data.id', $produto->id)
                 ->assertJsonPath('data.nome', $produto->nome);
    }

    /** @test */
    public function pode_atualizar_produto()
    {
        $produto = Product::factory()->create();

        $response = $this->putJson("/api/produtos/{$produto->id}", [
            'nome' => 'Produto Atualizado',
            'preco' => 399.99,
            'quantidade_estoque' => 100,
        ]);

        $response->assertStatus(200)
                 ->assertJsonPath('message', 'Produto atualizado com sucesso');

        $this->assertDatabaseHas('produtos', [
            'id' => $produto->id,
            'nome' => 'Produto Atualizado',
        ]);
    }

    /** @test */
    public function pode_deletar_produto()
    {
        $produto = Product::factory()->create();

        $response = $this->deleteJson("/api/produtos/{$produto->id}");

        $response->assertStatus(200)
                 ->assertJsonPath('message', 'Produto deletado com sucesso');

        $this->assertSoftDeleted('produtos', ['id' => $produto->id]);
    }

    /** @test */
    public function pode_listar_produtos_deletados()
    {
        Product::factory()->count(3)->create();
        $produtoDeletado = Product::factory()->create();
        $produtoDeletado->delete();

        $response = $this->getJson('/api/produtos/trashed');

        $response->assertStatus(200)
                 ->assertJsonCount(1, 'data');
    }

    /** @test */
    public function pode_restaurar_produto_deletado()
    {
        $produto = Product::factory()->create();
        $produto->delete();

        $response = $this->postJson("/api/produtos/{$produto->id}/restore");

        $response->assertStatus(200)
                 ->assertJsonPath('message', 'Produto restaurado com sucesso');

        $this->assertDatabaseHas('produtos', [
            'id' => $produto->id,
            'deleted_at' => null,
        ]);
    }

    /** @test */
    public function pode_filtrar_produtos_por_busca()
    {
        Product::factory()->create(['nome' => 'Notebook Dell']);
        Product::factory()->create(['nome' => 'Mouse Logitech']);

        $response = $this->getJson('/api/produtos?search=Notebook');

        $response->assertStatus(200)
                 ->assertJsonCount(1, 'data')
                 ->assertJsonPath('data.0.nome', 'Notebook Dell');
    }

    /** @test */
    public function pode_filtrar_produtos_por_faixa_de_preco()
    {
        Product::factory()->create(['preco' => 50]);
        Product::factory()->create(['preco' => 150]);
        Product::factory()->create(['preco' => 250]);

        $response = $this->getJson('/api/produtos?preco_min=100&preco_max=200');

        $response->assertStatus(200)
                 ->assertJsonCount(1, 'data');
    }

    /** @test */
    public function pode_filtrar_produtos_por_estoque_minimo()
    {
        Product::factory()->create(['quantidade_estoque' => 2]);
        Product::factory()->create(['quantidade_estoque' => 5]);
        Product::factory()->create(['quantidade_estoque' => 10]);

        $response = $this->getJson('/api/produtos?estoque_min=6');

        $response->assertStatus(200)
                 ->assertJsonCount(1, 'data')
                 ->assertJsonPath('data.0.quantidade_estoque', 10);
    }
}
