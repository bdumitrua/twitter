<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CreateEnvFile extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:env';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new .env file from .env.example';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (File::exists(base_path('.env'))) {
            $this->error('.env file already exists!');
            return;
        }

        if (!File::exists(base_path('.env.example'))) {
            $this->error('.env.example file does not exist!');
            return;
        }

        File::copy(base_path('.env.example'), base_path('.env'));

        $this->info('.env file successfully created from .env.example!');
    }
}
