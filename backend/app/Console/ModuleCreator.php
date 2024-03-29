<?php

namespace App\Console;

use App\Traits\FileGeneratorTrait;
use Illuminate\Support\Facades\Artisan;

class ModuleCreator
{
    use FileGeneratorTrait;

    // Для некоторых слов недостаточно добавить 's' в конце
    // Так что необходим такой массив названий для папок
    public $moduleDirectories = [
        'Model' => 'Models',
        'Controller' => 'Controllers',
        'Service' => 'Services',
        'Repository' => 'Repositories',
        'Event' => 'Events',
        'Queue' => 'Jobs',
        'Listener' => 'Listeners',
        'Resource' => 'Resources',
        'Route' => 'Routes',
        'Request' => 'Requests',
        'DTO' => 'DTO'
    ];

    public function createModule($moduleName): void
    {
        foreach ($this->moduleDirectories as $entityName => $folderName) {
            $this->createModuleContent($moduleName, $folderName, $entityName);
        }

        $this->createMigration($moduleName);
        $this->createFactory($moduleName);
        $this->createSeeder($moduleName);
    }

    public function createModuleContent($moduleName, $folderName, $entityName, $fileName = null): void
    {
        $folderPath = app_path("Modules/$moduleName/$folderName");
        $fileName = $fileName ?? $this->generateFileName($moduleName, $entityName);

        if (!is_dir($folderPath)) {
            mkdir($folderPath, 0777, true);
        }

        if (!file_exists("$folderPath/$fileName.php")) {
            $fileContent = $this->generateFileContent($moduleName, $folderName, $fileName, $entityName);
            file_put_contents("$folderPath/$fileName.php", $fileContent);
        }
    }

    protected function generateFileName($moduleName, $entityName): string
    {
        $fileNameMappings = [
            'Event' => "New{$moduleName}Event",
            'Listener' => "NotifyAboutNew{$moduleName}Event",
            'Route' => lcfirst($moduleName) . "{$entityName}s",
            'Model' => $moduleName,
        ];

        return $fileNameMappings[$entityName] ?? "{$moduleName}{$entityName}";
    }

    protected function createMigration($moduleName): void
    {
        Artisan::call('make:migration', ['name' => "create_{$moduleName}_table"]);
    }

    protected function createFactory($moduleName): void
    {
        Artisan::call('make:factory', [
            'name' => "{$moduleName}Factory",
            '--model' => "App\\Modules\\{$moduleName}\\Models\\{$moduleName}Model"
        ]);
    }

    protected function createSeeder($moduleName): void
    {
        Artisan::call('make:seeder', ['name' => "{$moduleName}TableSeeder"]);
    }
}
