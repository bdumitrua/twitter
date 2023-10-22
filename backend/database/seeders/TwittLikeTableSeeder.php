<?php

namespace Database\Seeders;

use App\Modules\Twitt\Models\TwittLike;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TwittLikeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TwittLike::factory(3000)->create();
    }
}
