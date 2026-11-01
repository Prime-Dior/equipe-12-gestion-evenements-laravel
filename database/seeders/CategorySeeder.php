<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        foreach (['Conférence', 'Atelier', 'Culture', 'Communauté', 'Rencontre'] as $name) {
            Category::firstOrCreate(['name' => $name]);
        }
    }
}
