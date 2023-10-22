<?php

namespace Database\Seeders;

use App\Modules\Twitt\Models\TwittFavorite;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TwittFavoriteTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TwittFavorite::factory(3000)->create();
    }
}
