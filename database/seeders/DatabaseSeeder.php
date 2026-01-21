<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Popula dados iniciais (usuario padrao e produtos).
     */
    public function run(): void
    {
        \App\Models\User::factory()->create([
            'name' => 'First Decision',
            'email' => 'firstdecision@email.com',
            'password' => bcrypt('senha123'),
        ]);

        $this->call([
            ProductSeeder::class,
        ]);
    }
}
