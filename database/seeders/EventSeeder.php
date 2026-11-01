<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Event;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        $category = Category::firstOrCreate([
            'name' => 'Atelier',
        ]);

        Event::firstOrCreate(
            ['title' => 'Atelier de démonstration'],
            [
                'category_id' => $category->id,
                'description' => 'Un événement de démonstration pour tester le parcours d’inscription.',
                'date' => now()->addDays(7)->setTime(18, 0),
                'location' => 'Cotonou',
                'capacity' => 20,
            ]
        );
    }
}
