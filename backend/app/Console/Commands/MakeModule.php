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
        // TODO
        // Make key=>value for folders/namespaces
        $dirs = ['Model', 'Controller', 'Service', 'Repository', 'Event', 'Job', 'Listener', 'Resource'];

        foreach ($dirs as $dir) {
            // TODO
            // Separate logic 
            $path = app_path() . "/Modules/$moduleName/$dir" . 's';
            if (!is_dir($path)) {
                mkdir($path, 0777, true);
            }

            $fileName = "{$moduleName}{$dir}.php";
            $fileContent = $this->generateFileContent($moduleName, $dir);
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
