<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\IndexRequest;
use App\Http\Requests\Product\StoreRequest;
use App\Http\Requests\Product\UpdateRequest;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    private ProductService $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    /**
     * Listar produtos com paginação e filtros
     */
    public function index(IndexRequest $request): JsonResponse
    {
        $perPage = $request->input('per_page', 15);

        // Remove filtros vazios e usa os validados
        $validated = $request->validated();
        $filters = array_filter([
            'search' => $validated['search'] ?? null,
            'preco_min' => $validated['preco_min'] ?? null,
            'preco_max' => $validated['preco_max'] ?? null,
            'estoque_min' => $validated['estoque_min'] ?? null,
            'estoque_max' => $validated['estoque_max'] ?? null,
        ], fn($value) => !is_null($value));

        $produtos = $this->productService->listarProdutos($perPage, $filters);

        return response()->json([
            'data' => $produtos->items(),
            'meta' => [
                'current_page' => $produtos->currentPage(),
                'last_page' => $produtos->lastPage(),
                'per_page' => $produtos->perPage(),
                'total' => $produtos->total(),
            ],
            'message' => 'Produtos listados com sucesso',
        ]);
    }

    /**
     * Exibir um produto específico
     */
    public function show(int $id): JsonResponse
    {
        try {
            $produto = $this->productService->obterProduto($id);

            return response()->json([
                'data' => $produto,
                'message' => 'Produto encontrado',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], $e->getCode() ?: 500);
        }
    }

    /**
     * Criar novo produto
     */
    public function store(StoreRequest $request): JsonResponse
    {
        try {
            $produto = $this->productService->criarProduto($request->validated());

            return response()->json([
                'data' => $produto,
                'message' => 'Produto criado com sucesso',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], $e->getCode() ?: 500);
        }
    }

    /**
     * Atualizar produto existente
     */
    public function update(UpdateRequest $request, int $id): JsonResponse
    {
        try {
            $produto = $this->productService->atualizarProduto($id, $request->validated());

            return response()->json([
                'data' => $produto,
                'message' => 'Produto atualizado com sucesso',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], $e->getCode() ?: 500);
        }
    }

    /**
     * Deletar produto (soft delete)
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->productService->deletarProduto($id);

            return response()->json([
                'message' => 'Produto deletado com sucesso',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], $e->getCode() ?: 500);
        }
    }

    /**
     * Listar produtos deletados
     */
    public function trashed(Request $request): JsonResponse
    {
        $perPage = $request->input('per_page', 15);
        $produtos = $this->productService->listarProdutosDeletados($perPage);

        return response()->json([
            'data' => $produtos->items(),
            'meta' => [
                'current_page' => $produtos->currentPage(),
                'last_page' => $produtos->lastPage(),
                'per_page' => $produtos->perPage(),
                'total' => $produtos->total(),
            ],
            'message' => 'Produtos deletados listados com sucesso',
        ]);
    }

    /**
     * Restaurar produto deletado
     */
    public function restore(int $id): JsonResponse
    {
        try {
            $produto = $this->productService->restaurarProduto($id);

            return response()->json([
                'data' => $produto,
                'message' => 'Produto restaurado com sucesso',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], $e->getCode() ?: 500);
        }
    }
}
