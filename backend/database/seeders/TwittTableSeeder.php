<?php

namespace Database\Seeders;

use App\Modules\Twitt\Models\Twitt;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TwittTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Twitt::factory(50)->create();
    }
}
