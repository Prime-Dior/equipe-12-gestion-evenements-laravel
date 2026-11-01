<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class EventiaSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([CategorySeeder::class, EventSeeder::class]);
    }
}
