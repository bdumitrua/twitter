<?php

namespace Database\Seeders;

use App\Modules\User\Models\UsersList;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UsersListTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        UsersList::factory(50)->create();
    }
}
