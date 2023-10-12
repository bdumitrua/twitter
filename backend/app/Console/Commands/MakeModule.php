<?php

namespace App\Console\Commands;

use App\Traits\FileGeneratorTrait;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class MakeModule extends Command
{
    use FileGeneratorTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:module {name}';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a new module';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $moduleName = $this->argument('name');
        $dirs = [
            'Model' => 'Models',
            'Controller' => 'Controllers',
            'Service' => 'Services',
            'Repository' => 'Repositories',
            'Event' => 'Events',
            'Queue' => 'Jobs',
            'Listener' => 'Listeners',
            'Resource' => 'Resources',
        ];

        foreach ($dirs as $entityName => $folderName) {
            // TODO
            // Separate logic 
            $path = app_path() . "/Modules/$moduleName/$folderName";
            if (!is_dir($path)) {
                mkdir($path, 0777, true);
            }

            $fileName = "{$moduleName}{$entityName}.php";
            $fileContent = $this->generateFileContent($moduleName, $entityName, $folderName);
            file_put_contents("{$path}/{$fileName}", $fileContent);

            Artisan::call('make:migration', ['name' => "create_{$moduleName}_table"]);
            Artisan::call('make:factory', [
                'name' => "{$moduleName}Factory",
                '--model' => "App\\Modules\\{$moduleName}\\Models\\{$moduleName}Model"
            ]);
            Artisan::call('make:seeder', ['name' => "{$moduleName}TableSeeder"]);
        }

        $this->info("Module $moduleName created successfully.");
    }
}
