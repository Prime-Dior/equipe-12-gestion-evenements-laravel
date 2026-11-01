<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<\App\Models\Event> */
class EventFactory extends Factory
{
    public function definition(): array
    {
        return [
            'category_id' => Category::factory(),
            'title' => fake()->sentence(4),
            'description' => fake()->paragraphs(2, true),
            'date' => fake()->dateTimeBetween('+2 days', '+3 months'),
            'location' => fake()->city(),
            'capacity' => fake()->numberBetween(10, 200),
        ];
    }
}
