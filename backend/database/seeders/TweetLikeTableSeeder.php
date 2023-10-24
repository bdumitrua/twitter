<?php

namespace Database\Seeders;

use App\Modules\Tweet\Models\TweetLike;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TweetLikeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TweetLike::factory(300)->create();
    }
}
