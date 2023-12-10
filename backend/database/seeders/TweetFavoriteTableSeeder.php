<?php

namespace Database\Seeders;

use App\Modules\Tweet\Models\TweetFavorite;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TweetFavoriteTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TweetFavorite::factory(30)->create();
    }
}
