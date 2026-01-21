<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\StoreRequest;
use App\Http\Requests\Product\UpdateRequest;
use App\Http\Requests\Web\WebIndexProductRequest;
use App\Services\ProductService;

class ProductWebController extends Controller
{
    private ProductService $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    /**
     * Listar produtos
     */
    public function index(WebIndexProductRequest $request)
    {
        $perPage = $request->input('per_page', 15);
        $sort = $request->input('sort', 'id');
        $direction = $request->input('direction', 'asc');

        $allowedSortColumns = ['id', 'nome', 'preco', 'quantidade_estoque', 'created_at'];
        if (!in_array($sort, $allowedSortColumns)) {
            $sort = 'id';
        }
        
        $direction = in_array(strtolower($direction), ['asc', 'desc']) ? strtolower($direction) : 'asc';

        $validated = $request->validated();

        $filters = array_filter([
            'search' => $validated['search'] ?? null,
            'preco_min' => $validated['preco_min'] ?? null,
            'preco_max' => $validated['preco_max'] ?? null,
            'estoque_min' => $validated['estoque_min'] ?? null,
            'estoque_max' => $validated['estoque_max'] ?? null,
        ], fn($value) => !is_null($value));

        $produtos = $this->productService->listarProdutos($perPage, $filters, $sort, $direction);

        return view('produtos.index', compact('produtos', 'sort', 'direction'));
    }

    /**
     * Exibir formulário de criação
     */
    public function create()
    {
        return view('produtos.create');
    }

    /**
     * Salvar novo produto
     */
    public function store(StoreRequest $request)
    {
        try {
            $this->productService->criarProduto($request->validated());
            return redirect()->route('produtos.index')
                ->with('success', 'Produto criado com sucesso!');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Exibir produto específico
     */
    public function show(int $id)
    {
        try {
            $produto = $this->productService->obterProduto($id);
            return view('produtos.show', compact('produto'));
        } catch (\Exception $e) {
            return redirect()->route('produtos.index')
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Exibir formulário de edição
     */
    public function edit(int $id)
    {
        try {
            $produto = $this->productService->obterProduto($id);
            return view('produtos.edit', compact('produto'));
        } catch (\Exception $e) {
            return redirect()->route('produtos.index')
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Atualizar produto
     */
    public function update(UpdateRequest $request, int $id)
    {
        try {
            $this->productService->atualizarProduto($id, $request->validated());
            return redirect()->route('produtos.index')
                ->with('success', 'Produto atualizado com sucesso!');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Deletar produto
     */
    public function destroy(int $id)
    {
        try {
            $this->productService->deletarProduto($id);
            return redirect()->route('produtos.index')
                ->with('success', 'Produto deletado com sucesso!');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
