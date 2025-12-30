<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Criar usuário de teste
        \App\Models\User::factory()->create([
            'name' => 'Kleber Alves',
            'email' => 'kleber@email.com',
            'password' => bcrypt('senha123'),
        ]);

        // Executar seeders específicos
        $this->call([
            ProductSeeder::class,
        ]);
    }
}
