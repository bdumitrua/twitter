<?php

namespace Database\Seeders;

use App\Modules\User\Models\UserSubscribtion;
use Illuminate\Database\Seeder;

class UserSubscribtionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $start = microtime(true);

        UserSubscribtion::factory(500)->create();
        $end = microtime(true);
        $time = $end - $start;
        echo "Seeding time: {$time} seconds";
    }
}
