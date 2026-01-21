<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Insere catálogo inicial de produtos de autopeças.
     */
    public function run(): void
    {
        Product::create([
            'nome' => 'Vela de Ignição Ngk Iridium',
            'descricao' => 'Vela de ignição de alta performance com eletrodo de irídio, maior durabilidade',
            'preco' => 35.90,
            'quantidade_estoque' => 120,
        ]);

        Product::create([
            'nome' => 'Bobina de Ignição',
            'descricao' => 'Bobina de ignição original com resistência aprimorada',
            'preco' => 159.90,
            'quantidade_estoque' => 45,
        ]);

        Product::create([
            'nome' => 'Bico Injetor de Combustível',
            'descricao' => 'Bico injetor limpo e testado, pulverização perfeita',
            'preco' => 125.50,
            'quantidade_estoque' => 30,
        ]);

        Product::create([
            'nome' => 'Pneu 195/65 R15 Michelin',
            'descricao' => 'Pneu radial 195/65 R15 com excelente aderência e durabilidade',
            'preco' => 289.90,
            'quantidade_estoque' => 40,
        ]);

        Product::create([
            'nome' => 'Roda Aro 15 5 Furos',
            'descricao' => 'Roda de alumínio aro 15, 5 furos, acabamento fosco',
            'preco' => 350.00,
            'quantidade_estoque' => 20,
        ]);

        Product::create([
            'nome' => 'Filtro de Ar Motor',
            'descricao' => 'Filtro de ar motor de alta eficiência, aumenta performance',
            'preco' => 48.90,
            'quantidade_estoque' => 80,
        ]);

        Product::create([
            'nome' => 'Filtro de Óleo Motor',
            'descricao' => 'Filtro de óleo com bypass integrado, proteção total do motor',
            'preco' => 32.50,
            'quantidade_estoque' => 100,
        ]);

        Product::create([
            'nome' => 'Óleo Motor 5L Shell Helix',
            'descricao' => 'Óleo sintético 5W-30, proteção contra desgaste prematuro',
            'preco' => 65.90,
            'quantidade_estoque' => 50,
        ]);

        Product::create([
            'nome' => 'Bateria 60Ah Moura',
            'descricao' => 'Bateria automotiva 60Ah, alta capacidade de descarga',
            'preco' => 385.00,
            'quantidade_estoque' => 25,
        ]);

        Product::create([
            'nome' => 'Alternador 90A',
            'descricao' => 'Alternador de reposição 90A, carregamento rápido',
            'preco' => 420.00,
            'quantidade_estoque' => 15,
        ]);

        Product::create([
            'nome' => 'Motor de Arranque',
            'descricao' => 'Motor de partida original, torque de partida otimizado',
            'preco' => 310.00,
            'quantidade_estoque' => 12,
        ]);

        Product::create([
            'nome' => 'Correia Dentada',
            'descricao' => 'Correia de distribuição de alta resistência e precisão',
            'preco' => 185.90,
            'quantidade_estoque' => 35,
        ]);

        Product::create([
            'nome' => 'Pastilha de Freio Dianteira',
            'descricao' => 'Pastilha cerâmica com excelente poder de frenagem',
            'preco' => 125.00,
            'quantidade_estoque' => 60,
        ]);

        Product::create([
            'nome' => 'Disco de Freio Ventilado',
            'descricao' => 'Disco ventilado com tratamento térmico especial',
            'preco' => 195.90,
            'quantidade_estoque' => 40,
        ]);

        Product::create([
            'nome' => 'Amortecedor Dianteiro',
            'descricao' => 'Amortecedor com sistema de suspensão progressiva',
            'preco' => 285.00,
            'quantidade_estoque' => 20,
        ]);

        Product::create([
            'nome' => 'Sensor de Oxigênio Lambda',
            'descricao' => 'Sensor lambda de banda larga, calibração de injeção precisa',
            'preco' => 165.50,
            'quantidade_estoque' => 25,
        ]);

        Product::create([
            'nome' => 'Radiador Alumínio',
            'descricao' => 'Radiador de alumínio com ventilador integrado',
            'preco' => 450.00,
            'quantidade_estoque' => 18,
        ]);

        Product::create([
            'nome' => 'Mangueira de Radiador Premium',
            'descricao' => 'Mangueira reforçada com ganchos de fixação inclusos',
            'preco' => 54.90,
            'quantidade_estoque' => 55,
        ]);

        Product::create([
            'nome' => 'Termostato 82º',
            'descricao' => 'Termostato de alta precisão, abre a 82 graus',
            'preco' => 89.90,
            'quantidade_estoque' => 40,
        ]);

        Product::create([
            'nome' => 'Corrente de Distribuição',
            'descricao' => 'Corrente forjada com proteção contra estiramento',
            'preco' => 320.00,
            'quantidade_estoque' => 10,
        ]);

        Product::create([
            'nome' => 'Cabos de Velas Juntos',
            'descricao' => 'Jogo completo de cabos com isolamento de alta tensão',
            'preco' => 78.50,
            'quantidade_estoque' => 50,
        ]);

        Product::create([
            'nome' => 'Bucha de Suspensão Dianteira',
            'descricao' => 'Jogo de buchas de borracha para suspensão dianteira',
            'preco' => 145.00,
            'quantidade_estoque' => 30,
        ]);

        Product::create([
            'nome' => 'Cilindro Mestre de Freio',
            'descricao' => 'Cilindro mestre com pistão de precisão',
            'preco' => 220.00,
            'quantidade_estoque' => 12,
        ]);

        Product::create([
            'nome' => 'Cilindro Escravo de Embreagem',
            'descricao' => 'Cilindro com haste de aço temperado',
            'preco' => 95.00,
            'quantidade_estoque' => 25,
        ]);

        Product::create([
            'nome' => 'Disco de Embreagem',
            'descricao' => 'Disco com forro de fricção de durabilidade comprovada',
            'preco' => 185.00,
            'quantidade_estoque' => 20,
        ]);

        Product::create([
            'nome' => 'Platô de Embreagem',
            'descricao' => 'Platô completo com molas de pressão precisas',
            'preco' => 280.00,
            'quantidade_estoque' => 15,
        ]);

        Product::create([
            'nome' => 'Rolamento de Roda',
            'descricao' => 'Rolamento de esferas de alta velocidade com selos',
            'preco' => 110.00,
            'quantidade_estoque' => 40,
        ]);

        Product::create([
            'nome' => 'Palheta Limpador Parabrisa 24"',
            'descricao' => 'Palheta com borracha natural, visão cristalina',
            'preco' => 45.90,
            'quantidade_estoque' => 90,
        ]);

        Product::create([
            'nome' => 'Fluido de Freio DOT 4',
            'descricao' => 'Fluido de freio sintético com ponto de ebulição alto',
            'preco' => 38.50,
            'quantidade_estoque' => 70,
        ]);

        Product::create([
            'nome' => 'Fluido de Embreagem Sinético',
            'descricao' => 'Fluido sinético para sistemas de embreagem hidráulica',
            'preco' => 42.90,
            'quantidade_estoque' => 60,
        ]);

        Product::create([
            'nome' => 'Fluido Refrigerante Concentrado',
            'descricao' => 'Concentrado pronto para diluição, proteção contra congelamento',
            'preco' => 35.00,
            'quantidade_estoque' => 75,
        ]);

        Product::create([
            'nome' => 'Jogo de Anéis do Pistão',
            'descricao' => 'Anéis de compressão e roscado com tratamento térmico',
            'preco' => 250.00,
            'quantidade_estoque' => 8,
        ]);

        Product::create([
            'nome' => 'Válvula Termostática',
            'descricao' => 'Válvula de controle de temperatura de precisão',
            'preco' => 125.00,
            'quantidade_estoque' => 20,
        ]);

        Product::create([
            'nome' => 'Bomba de Água',
            'descricao' => 'Bomba centrífuga com rotor de alumínio',
            'preco' => 280.00,
            'quantidade_estoque' => 14,
        ]);

        Product::create([
            'nome' => 'Válvula PCV Motor',
            'descricao' => 'Válvula de ventilação positiva do cárter',
            'preco' => 85.00,
            'quantidade_estoque' => 35,
        ]);

        Product::create([
            'nome' => 'Conector de Combustível',
            'descricao' => 'Conector rápido para linha de combustível',
            'preco' => 28.90,
            'quantidade_estoque' => 80,
        ]);
    }
}
