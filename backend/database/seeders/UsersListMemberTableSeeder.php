<?php

namespace Database\Seeders;

use App\Modules\User\Models\UsersListMember;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UsersListMemberTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        UsersListMember::factory(6)->create();
    }
}
