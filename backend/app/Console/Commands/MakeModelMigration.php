<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class MakeModelMigration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:model:migration {model}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates migration, factory and seeder for model';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // UserGroup
        $model = $this->argument('model');
        $model_snake_case = $this->pascalToSnake($model);
        Artisan::call('make:migration', ['name' => "create_{$model_snake_case}s_table"]);
        Artisan::call('make:factory', ['name' => "{$model}Factory"]);
        Artisan::call('make:seeder', ['name' => "{$model}TableSeeder"]);
    }

    protected function pascalToSnake($string)
    {
        return strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $string));
    }
}
