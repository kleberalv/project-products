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
     *
     * @param int $perPage Número de itens por página
     * @param array $filters Filtros a serem aplicados (search, preco_min, preco_max, estoque_min, estoque_max)
     * @param string $sort Campo para ordenação
     * @param string $direction Direção da ordenação (asc ou desc)
     * @return LengthAwarePaginator Lista paginada de produtos
     */
    public function listarProdutos(int $perPage = 15, array $filters = [], string $sort = 'id', string $direction = 'asc'): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage, $filters, $sort, $direction);
    }

    /**
     * Obter um produto por ID
     *
     * @param int $id ID do produto
     * @return Product O produto encontrado
     * @throws \Exception Se o produto não for encontrado
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
     * @param array $dados Dados do produto (nome, descricao, preco, quantidade_estoque)
     * @return Product O produto criado
     * @throws \Exception Se já existir produto com mesmo nome, preço inválido ou estoque negativo
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
     * Atualizar um produto existente
     *
     * @param int $id ID do produto
     * @param array $dados Dados a serem atualizados
     * @return mixed O produto atualizado
     * @throws \Exception Se já existir outro produto com mesmo nome, preço inválido ou estoque negativo
     */
    public function atualizarProduto(int $id, array $dados)
    {
        $this->obterProduto($id);
        if (isset($dados['nome'])) {
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
     * Deletar um produto (soft delete)
     *
     * @param int $id ID do produto
     * @return bool True se deletado com sucesso
     * @throws \Exception Se o produto não for encontrado
     */
    public function deletarProduto(int $id): bool
    {
        $this->obterProduto($id);

        return $this->repository->delete($id);
    }

    /**
     * Restaurar um produto deletado (soft delete recovery)
     *
     * @param int $id ID do produto deletado
     * @return Product O produto restaurado
     * @throws \Exception Se o produto não for encontrado
     */
    public function restaurarProduto(int $id): Product
    {
        $product = $this->obterProdutoDeletado($id);
        $product->restore();
        return $product;
    }

    /**
     * Listar todos os produtos deletados
     *
     * @param int $perPage Número de itens por página
     * @return LengthAwarePaginator Lista paginada de produtos deletados
     */
    public function listarProdutosDeletados(int $perPage = 15): LengthAwarePaginator
    {
        return Product::onlyTrashed()->paginate($perPage);
    }

    /**
     * Obter um produto deletado por ID
     *
     * @param int $id ID do produto deletado
     * @return Product O produto deletado encontrado
     * @throws \Exception Se o produto deletado não for encontrado
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
