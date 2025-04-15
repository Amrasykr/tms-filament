<?php

namespace Database\Seeders;

use App\Models\Hero;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class HeroesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        for ($i = 1; $i <= 2; $i++) {
            Hero::create([
                'name' => "Hero $i",
                'image' => "post_image_$i.jpg",
                'order' => $i,
            ]);
        }

    }
}
