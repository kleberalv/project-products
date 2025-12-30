<?php

namespace Tests\Unit;

use App\Models\Product;
use App\Models\User;
use App\Services\ProductService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductServiceTest extends TestCase
{
    use RefreshDatabase;

    private ProductService $productService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->productService = app(ProductService::class);
    }

    /**
     * Teste de criação de produto
     */
    public function test_servico_pode_criar_produto(): void
    {
        $data = [
            'nome' => 'Produto Teste',
            'descricao' => 'Descrição teste',
            'preco' => 99.99,
            'quantidade_estoque' => 5,
        ];

        $product = $this->productService->criarProduto($data);

        $this->assertNotNull($product->id);
        $this->assertEquals('Produto Teste', $product->nome);
    }

    /**
     * Teste de validação de nome duplicado
     */
    public function test_servico_nao_cria_produto_com_nome_duplicado(): void
    {
        Product::factory()->create(['nome' => 'Nome Duplicado']);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Já existe um produto com este nome');

        $this->productService->criarProduto([
            'nome' => 'Nome Duplicado',
            'descricao' => 'Descrição',
            'preco' => 99.99,
            'quantidade_estoque' => 5,
        ]);
    }

    /**
     * Teste de obtenção de produto
     */
    public function test_servico_pode_obter_produto_por_id(): void
    {
        $product = Product::factory()->create();

        $retrieved = $this->productService->obterProduto($product->id);

        $this->assertEquals($product->id, $retrieved->id);
    }

    /**
     * Teste de produto não encontrado
     */
    public function test_servico_lanca_excecao_quando_produto_nao_encontrado(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Produto não encontrado');

        $this->productService->obterProduto(999);
    }

    /**
     * Teste de atualização de produto
     */
    public function test_servico_pode_atualizar_produto(): void
    {
        $product = Product::factory()->create(['nome' => 'Original']);

        $updated = $this->productService->atualizarProduto($product->id, [
            'nome' => 'Atualizado',
        ]);

        $this->assertEquals('Atualizado', $updated->nome);
    }

    /**
     * Teste de soft delete de produto
     */
    public function test_servico_pode_soft_delete_produto(): void
    {
        $product = Product::factory()->create();

        $this->productService->deletarProduto($product->id);

        $this->assertNotNull($product->fresh()->deleted_at);
    }

    /**
     * Teste de restauração de produto
     */
    public function test_servico_pode_restaurar_produto_deletado(): void
    {
        $product = Product::factory()->create();
        $product->delete();

        $restored = $this->productService->restaurarProduto($product->id);

        $this->assertNull($restored->deleted_at);
    }

    /**
     * Teste de verificação de disponibilidade de estoque
     */
    public function test_servico_verifica_disponibilidade_estoque(): void
    {
        $product = Product::factory()->create(['quantidade_estoque' => 10]);

        $available = $this->productService->verificarDisponibilidade($product->id, 5);

        $this->assertTrue($available);
    }

    /**
     * Teste de indisponibilidade de estoque
     */
    public function test_servico_retorna_falso_estoque_indisponivel(): void
    {
        $product = Product::factory()->create(['quantidade_estoque' => 3]);

        $available = $this->productService->verificarDisponibilidade($product->id, 5);

        $this->assertFalse($available);
    }

    /**
     * Teste de preço negativo na criação
     */
    public function test_servico_nao_cria_produto_com_preco_negativo(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Preço deve ser maior que zero');

        $this->productService->criarProduto([
            'nome' => 'Produto Negativo',
            'descricao' => 'Descrição',
            'preco' => -10.00,
            'quantidade_estoque' => 5,
        ]);
    }

    /**
     * Teste de quantidade negativa na criação
     */
    public function test_servico_nao_cria_produto_com_quantidade_negativa(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Quantidade em estoque não pode ser negativa');

        $this->productService->criarProduto([
            'nome' => 'Produto Negativo',
            'descricao' => 'Descrição',
            'preco' => 99.99,
            'quantidade_estoque' => -5,
        ]);
    }

    /**
     * Teste de listagem com paginação
     */
    public function test_servico_lista_produtos_com_paginacao(): void
    {
        Product::factory(20)->create();

        $paginated = $this->productService->listarProdutos(10);

        $this->assertEquals(10, $paginated->count());
        $this->assertEquals(2, $paginated->lastPage());
    }

    /**
     * Teste de listagem de produtos deletados
     */
    public function test_servico_lista_produtos_deletados(): void
    {
        Product::factory(3)->create()->each(fn($p) => $p->delete());

        $trashed = $this->productService->listarProdutosDeletados();

        $this->assertEquals(3, $trashed->total());
    }
}
