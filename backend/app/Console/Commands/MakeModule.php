<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MakeModule extends Command
{
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

        $dirs = ['Models', 'Repositories', 'Jobs'];

        foreach ($dirs as $dir) {
            $path = app_path() . "/$moduleName/$dir";
            if (!is_dir($path)) {
                mkdir($path, 0777, true);
            }

            $fileName = "{$moduleName}{$dir}.php";
            $fileContent = $this->generateFileContent($moduleName, $dir);
            file_put_contents("{$path}/{$fileName}", $fileContent);
        }

        $this->info("Module $moduleName created successfully.");
    }

    private function generateFileContent($moduleName, $dir)
    {
        $className = "{$moduleName}{$dir}";

        return "<?php

namespace App\\{$moduleName}\\{$dir};

class {$className}
{
    // TODO: Implement your class logic here
}
";
    }
}
