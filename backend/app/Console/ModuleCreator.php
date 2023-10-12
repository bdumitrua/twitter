<?php

namespace App\Console;

use App\Traits\FileGeneratorTrait;
use Illuminate\Support\Facades\Artisan;

class ModuleCreator
{
    use FileGeneratorTrait;

    public function createModule($moduleName)
    {
        $moduleDirectories = [
            'Model' => 'Models',
            'Controller' => 'Controllers',
            'Service' => 'Services',
            'Repository' => 'Repositories',
            'Event' => 'Events',
            'Queue' => 'Jobs',
            'Listener' => 'Listeners',
            'Resource' => 'Resources',
        ];

        foreach ($moduleDirectories as $entityName => $folderName) {
            $this->createModuleFile($moduleName, $entityName, $folderName);
        }

        $this->createMigration($moduleName);
        $this->createFactory($moduleName);
        $this->createSeeder($moduleName);
    }

    protected function createModuleFile($moduleName, $entityName, $folderName)
    {
        $path = app_path("Modules/$moduleName/$folderName");
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }

        $fileName = "{$moduleName}{$entityName}.php";
        $fileContent = $this->generateFileContent($moduleName, $entityName, $folderName);
        file_put_contents("$path/$fileName", $fileContent);
    }

    protected function createMigration($moduleName)
    {
        Artisan::call('make:migration', ['name' => "create_{$moduleName}_table"]);
    }

    protected function createFactory($moduleName)
    {
        Artisan::call('make:factory', [
            'name' => "{$moduleName}Factory",
            '--model' => "App\\Modules\\{$moduleName}\\Models\\{$moduleName}Model"
        ]);
    }

    protected function createSeeder($moduleName)
    {
        Artisan::call('make:seeder', ['name' => "{$moduleName}TableSeeder"]);
    }
}
