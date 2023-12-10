<?php

namespace Database\Seeders;

use App\Modules\Tweet\Models\Tweet;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TweetTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Tweet::factory(25)->create();
    }
}
