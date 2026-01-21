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

    /**
     * Inicializa o controlador com o serviço de produtos.
     *
     * @param ProductService $productService Serviço responsável pela lógica de negócio dos produtos.
     */
    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    /**
     * Listar produtos com paginação e filtros
     *
     * @OA\Get(
     *     path="/produtos",
     *     summary="Listar produtos",
     *     description="Retorna lista paginada de produtos com suporte a filtros opcionais",
     *     tags={"Produtos"},
    *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Número da página",
     *         required=false,
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Itens por página",
     *         required=false,
     *         @OA\Schema(type="integer", default=15)
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Buscar por nome do produto",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="preco_min",
     *         in="query",
     *         description="Preço mínimo",
     *         required=false,
     *         @OA\Schema(type="number", format="float")
     *     ),
     *     @OA\Parameter(
     *         name="preco_max",
     *         in="query",
     *         description="Preço máximo",
     *         required=false,
     *         @OA\Schema(type="number", format="float")
     *     ),
     *     @OA\Parameter(
     *         name="estoque_min",
     *         in="query",
     *         description="Estoque mínimo",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="estoque_max",
     *         in="query",
     *         description="Estoque máximo",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Produtos listados com sucesso",
     *         @OA\JsonContent()
     *     )
     * )
     * 
     * @param IndexRequest $request A requisição contendo parâmetros de filtro e paginação.
     * @return JsonResponse A resposta JSON com lista paginada de produtos.
     *
     * @throws \Exception Se houver erro ao recuperar os produtos.
     */
    public function index(IndexRequest $request): JsonResponse
    {
        $perPage = $request->input('per_page', 15);

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
     *
     * @OA\Get(
     *     path="/produtos/{produto}",
     *     summary="Obter produto por ID",
     *     description="Retorna os dados detalhados de um produto específico",
     *     tags={"Produtos"},
    *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="produto",
     *         in="path",
     *         required=true,
     *         description="ID do produto",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Produto encontrado",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(response=404, description="Produto não encontrado")
     * )
     * 
     * @param int $id O ID do produto a ser exibido.
     * @return JsonResponse A resposta JSON com os detalhes do produto.
     *
     * @throws \Exception Se o produto não for encontrado (código 404).
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
     *
     * @OA\Post(
     *     path="/produtos",
     *     summary="Criar novo produto",
     *     description="Cria um novo produto na base de dados",
     *     tags={"Produtos"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nome","preco","quantidade_estoque"},
     *             @OA\Property(property="nome", type="string", example="Teclado Mecânico"),
     *             @OA\Property(property="preco", type="number", format="float", example=450.00),
     *             @OA\Property(property="quantidade_estoque", type="integer", example=50),
     *             @OA\Property(property="descricao", type="string", nullable=true, example="Teclado RGB com switches mecânicos")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Produto criado com sucesso",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(response=401, description="Não autenticado"),
     *     @OA\Response(response=422, description="Erro de validação")
     * )
     * 
     * @param StoreRequest $request A requisição HTTP contendo os dados do novo produto.
     * @return JsonResponse A resposta JSON com os dados do produto criado (código 201).
     *
     * @throws \Exception Se houver erro na validação ou criação do produto.
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
     *
     * @OA\Put(
     *     path="/produtos/{produto}",
     *     summary="Atualizar produto",
     *     description="Atualiza os dados de um produto existente",
     *     tags={"Produtos"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="produto",
     *         in="path",
     *         required=true,
     *         description="ID do produto",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="nome", type="string", example="Teclado Mecânico RGB"),
     *             @OA\Property(property="preco", type="number", format="float", example=500.00),
     *             @OA\Property(property="quantidade_estoque", type="integer", example=45),
     *             @OA\Property(property="descricao", type="string", nullable=true, example="Teclado RGB com switches mecânicos premium")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Produto atualizado com sucesso",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(response=401, description="Não autenticado"),
     *     @OA\Response(response=404, description="Produto não encontrado"),
     *     @OA\Response(response=422, description="Erro de validação")
     * )
     * 
     * @param UpdateRequest $request A requisição contendo dados atualizados do produto.
     * @param int $id O ID do produto a ser atualizado.
     * @return JsonResponse A resposta JSON com os dados do produto atualizado.
     *
     * @throws \Exception Se o produto não for encontrado ou houver erro na validação.
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
     *
     * @OA\Delete(
     *     path="/produtos/{produto}",
     *     summary="Deletar produto",
     *     description="Deleta um produto (soft delete - pode ser restaurado)",
     *     tags={"Produtos"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="produto",
     *         in="path",
     *         required=true,
     *         description="ID do produto",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Produto deletado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Não autenticado"),
     *     @OA\Response(response=404, description="Produto não encontrado")
     * )
     * 
     * @param int $id O ID do produto a ser deletado.
     * @return JsonResponse A resposta JSON indicando sucesso da operação.
     *
     * @throws \Exception Se o produto não for encontrado.
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
     *
     * @OA\Get(
     *     path="/produtos/trashed",
     *     summary="Listar produtos deletados",
     *     description="Retorna lista paginada de produtos deletados (soft delete)",
     *     tags={"Produtos"},
    *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Número da página",
     *         required=false,
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Itens por página",
     *         required=false,
     *         @OA\Schema(type="integer", default=15)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Produtos deletados listados com sucesso",
     *         @OA\JsonContent()
     *     )
     * )
     * 
     * @param Request $request A requisição contendo parâmetros de paginação.
     * @return JsonResponse A resposta JSON com lista paginada de produtos deletados.
     *
     * @throws \Exception Se houver erro ao recuperar os produtos deletados.
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
     *
     * @OA\Post(
     *     path="/produtos/{produto}/restore",
     *     summary="Restaurar produto",
     *     description="Restaura um produto que foi deletado (soft delete)",
     *     tags={"Produtos"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="produto",
     *         in="path",
     *         required=true,
     *         description="ID do produto deletado",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Produto restaurado com sucesso",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(response=401, description="Não autenticado"),
     *     @OA\Response(response=404, description="Produto não encontrado")
     * )
     * 
     * @param int $id O ID do produto deletado a ser restaurado.
     * @return JsonResponse A resposta JSON com os dados do produto restaurado.
     *
     * @throws \Exception Se o produto deletado não for encontrado.
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
