<?php

namespace Database\Seeders;

use App\Modules\User\Models\UsersListSubscribtion;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UsersListSubscribtionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        UsersListSubscribtion::factory(300)->create();
    }
}
