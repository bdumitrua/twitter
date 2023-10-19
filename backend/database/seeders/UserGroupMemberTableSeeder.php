<?php

namespace Database\Seeders;

use App\Modules\User\Models\UserGroupMember;
use Illuminate\Database\Seeder;

class UserGroupMemberTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        UserGroupMember::factory(300)->create();
    }
}
