<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    /**
     * Estado padrao do model Product.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nome' => fake()->unique()->words(3, true),
            'descricao' => fake()->paragraphs(2, true),
            'preco' => fake()->numberBetween(1000, 100000) / 100,
            'quantidade_estoque' => fake()->numberBetween(0, 1000),
        ];
    }

    /**
     * Estado para produtos sem estoque.
     */
    public function semEstoque()
    {
        return $this->state(fn (array $attributes) => [
            'quantidade_estoque' => 0,
        ]);
    }

    /**
     * Estado para produtos premium.
     */
    public function premium()
    {
        return $this->state(fn (array $attributes) => [
            'preco' => fake()->numberBetween(10000, 500000) / 100,
        ]);
    }
}
