<?php

namespace Database\Seeders;

use App\Modules\Tweet\Models\TweetDraft;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TweetDraftTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TweetDraft::factory(600)->create();
    }
}
