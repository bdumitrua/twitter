<?php

namespace App\Console\Commands;

use App\Traits\FileGeneratorTrait;
use DirectoryIterator;
use Illuminate\Console\Command;

class UpdateModule extends Command
{
    use FileGeneratorTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:module';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update existing modules';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $moduleDir = app_path() . '/Modules/';

        // Список всех папок, которые должны быть в модулях
        $dirs = ['Model', 'Controller', 'Service'];

        foreach (new DirectoryIterator($moduleDir) as $fileInfo) {
            if ($fileInfo->isDot()) continue;
            if ($fileInfo->isDir()) {
                $moduleName = $fileInfo->getFilename();

                foreach ($dirs as $dir) {
                    $path = $moduleDir . "/$moduleName/$dir";

                    if (!is_dir($path)) {
                        mkdir($path, 0777, true);
                        $fileName = "{$moduleName}{$dir}.php";
                        $fileContent = $this->generateFileContent($moduleName, $dir);
                        file_put_contents("{$path}/{$fileName}", $fileContent);
                        $this->info("Added $dir to $moduleName");
                    }
                }
            }
        }

        $this->info("Modules updated successfully.");
    }
}
