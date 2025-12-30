<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Nome da tabela no banco de dados
     */
    protected $table = 'produtos';

    /**
     * Atributos que podem ser atribuídos em massa
     */
    protected $fillable = [
        'nome',
        'descricao',
        'preco',
        'quantidade_estoque',
    ];

    /**
     * Conversão de tipos para os atributos
     */
    protected $casts = [
        'preco' => 'decimal:2',
        'quantidade_estoque' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Validações de regras de negócio
     */
    public function podeSerVendido(int $quantidade): bool
    {
        return $this->quantidade_estoque >= $quantidade;
    }

    public function decrementarEstoque(int $quantidade): void
    {
        $this->quantidade_estoque -= $quantidade;
        $this->save();
    }

    public function incrementarEstoque(int $quantidade): void
    {
        $this->quantidade_estoque += $quantidade;
        $this->save();
    }
}
