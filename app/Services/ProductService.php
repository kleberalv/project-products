<?php

namespace App\Services;

use App\Models\Product;
use App\Repositories\ProductRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductService
{
    private ProductRepositoryInterface $repository;

    public function __construct(ProductRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Obter produtos com paginação e filtros
     */
    public function listarProdutos(int $perPage = 15, array $filters = [], string $sort = 'id', string $direction = 'desc'): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage, $filters, $sort, $direction);
    }

    /**
     * Obter detalhes de um produto
     *
     * @throws \Exception
     */
    public function obterProduto(int $id): Product
    {
        $product = $this->repository->findById($id);

        if (!$product) {
            throw new \Exception('Produto não encontrado', 404);
        }

        return $product;
    }

    /**
     * Criar um novo produto
     *
     * @throws \Exception
     */
    public function criarProduto(array $dados): Product
    {
        if ($this->repository->findByName($dados['nome'])) {
            throw new \Exception('Já existe um produto com este nome', 422);
        }

        if ($dados['preco'] <= 0) {
            throw new \Exception('Preço deve ser maior que zero', 422);
        }

        if ($dados['quantidade_estoque'] < 0) {
            throw new \Exception('Quantidade em estoque não pode ser negativa', 422);
        }

        return $this->repository->create($dados);
    }

    /**
     * Atualizar um produto
     *
     * @throws \Exception
     */
    public function atualizarProduto(int $id, array $dados): Product
    {
        $product = $this->obterProduto($id);

        if (isset($dados['nome']) && $dados['nome'] !== $product->nome) {
            $existingProduct = $this->repository->findByName($dados['nome']);
            if ($existingProduct && $existingProduct->id !== $id) {
                throw new \Exception('Já existe um produto com este nome', 422);
            }
        }

        if (isset($dados['preco']) && $dados['preco'] <= 0) {
            throw new \Exception('Preço deve ser maior que zero', 422);
        }

        if (isset($dados['quantidade_estoque']) && $dados['quantidade_estoque'] < 0) {
            throw new \Exception('Quantidade em estoque não pode ser negativa', 422);
        }

        return $this->repository->update($id, $dados);
    }

    /**
     * Deletar um produto
     *
     * @throws \Exception
     */
    public function deletarProduto(int $id): bool
    {
        $this->obterProduto($id); // Verifica se existe

        return $this->repository->delete($id);
    }

    /**
     * Verificar disponibilidade de estoque
     */
    public function verificarDisponibilidade(int $id, int $quantidade): bool
    {
        $product = $this->obterProduto($id);
        return $product->podeSerVendido($quantidade);
    }

    /**
     * Restaurar um produto deletado (soft delete recovery)
     *
     * @throws \Exception
     */
    public function restaurarProduto(int $id): Product
    {
        $product = Product::onlyTrashed()->find($id);

        if (!$product) {
            throw new \Exception('Produto deletado não encontrado', 404);
        }

        $product->restore();
        return $product;
    }

    /**
     * Listar todos os produtos deletados
     */
    public function listarProdutosDeletados(int $perPage = 15): LengthAwarePaginator
    {
        return Product::onlyTrashed()->paginate($perPage);
    }

    /**
     * Obter um produto deletado por ID
     *
     * @throws \Exception
     */
    public function obterProdutoDeletado(int $id): Product
    {
        $product = Product::onlyTrashed()->find($id);

        if (!$product) {
            throw new \Exception('Produto deletado não encontrado', 404);
        }

        return $product;
    }
}
