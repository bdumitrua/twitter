<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserTableSeeder::class,
            UserSubscribtionTableSeeder::class,
            UserGroupTableSeeder::class,
            UserGroupMemberTableSeeder::class,
            UsersListTableSeeder::class,
            UsersListMemberTableSeeder::class,
            UsersListSubscribtionTableSeeder::class,
            TwittTableSeeder::class,
            TwittLikeTableSeeder::class,
            TwittFavoriteTableSeeder::class,
        ]);
    }
}
