<?php

namespace Database\Seeders;

use Database\Factories\DeviceTokenFactory;
use Illuminate\Database\Seeder;

class DeviceTokenTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DeviceTokenFactory::factory(5)->create();
    }
}
