<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Event;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        $examples = [
            [
                'title' => 'Les voix du futur',
                'category' => 'Conférence',
                'description' => 'Une matinée pour découvrir les métiers du numérique et échanger autour des idées qui transforment nos villes.',
                'date' => now()->addDays(8)->setTime(9, 30),
                'location' => 'IFRI, Université d’Abomey-Calavi',
                'capacity' => 120,
            ],
            [
                'title' => 'Fabrique ton premier site',
                'category' => 'Atelier',
                'description' => 'Un atelier pratique pour découvrir le HTML et construire une première page web, pas à pas.',
                'date' => now()->addDays(13)->setTime(14, 0),
                'location' => 'Cotonou',
                'capacity' => 30,
            ],
            [
                'title' => 'Une soirée au musée',
                'category' => 'Culture',
                'description' => 'Une visite guidée pour découvrir les collections et les histoires du musée avec un regard nouveau.',
                'date' => now()->addDays(19)->setTime(18, 0),
                'location' => 'Musée Honmè, Porto-Novo',
                'capacity' => 50,
            ],
        ];

        foreach ($examples as $example) {
            $category = Category::where('name', $example['category'])->firstOrFail();
            unset($example['category']);

            Event::updateOrCreate(
                ['title' => $example['title']],
                [...$example, 'category_id' => $category->id],
            );
        }
    }
}
