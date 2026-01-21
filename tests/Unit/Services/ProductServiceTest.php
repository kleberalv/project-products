<?php

namespace Tests\Unit;

use App\Models\Product;
use App\Models\User;
use App\Services\ProductService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Testes unitarios do ProductService (regras de negocio de produtos).
 */
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
     * Teste de criacao de produto.
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
     * Teste de validacao de nome duplicado.
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
     * Teste de obtencao de produto por ID.
     */
    public function test_servico_pode_obter_produto_por_id(): void
    {
        $product = Product::factory()->create();

        $retrieved = $this->productService->obterProduto($product->id);

        $this->assertEquals($product->id, $retrieved->id);
    }

    /**
     * Teste de produto nao encontrado.
     */
    public function test_servico_lanca_excecao_quando_produto_nao_encontrado(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Produto não encontrado');

        $this->productService->obterProduto(999);
    }

    /**
     * Teste de atualizacao de produto.
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
     * Teste de soft delete de produto.
     */
    public function test_servico_pode_soft_delete_produto(): void
    {
        $product = Product::factory()->create();

        $this->productService->deletarProduto($product->id);

        $this->assertNotNull($product->fresh()->deleted_at);
    }

    /**
     * Teste de restauracao de produto.
     */
    public function test_servico_pode_restaurar_produto_deletado(): void
    {
        $product = Product::factory()->create();
        $product->delete();

        $restored = $this->productService->restaurarProduto($product->id);

        $this->assertNull($restored->deleted_at);
    }

    

    /**
     * Teste de preco negativo na criacao.
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
     * Teste de quantidade negativa na criacao.
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
     * Teste de listagem com paginacao.
     */
    public function test_servico_lista_produtos_com_paginacao(): void
    {
        Product::factory(20)->create();

        $paginated = $this->productService->listarProdutos(10);

        $this->assertEquals(10, $paginated->count());
        $this->assertEquals(2, $paginated->lastPage());
    }

    /**
     * Teste de listagem de produtos deletados.
     */
    public function test_servico_lista_produtos_deletados(): void
    {
        Product::factory(3)->create()->each(fn($p) => $p->delete());

        $trashed = $this->productService->listarProdutosDeletados();

        $this->assertEquals(3, $trashed->total());
    }
}
