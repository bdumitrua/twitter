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
        UserSubscribtion::factory(15)->create();
    }
}
