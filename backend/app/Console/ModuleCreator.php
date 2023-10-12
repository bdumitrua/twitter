<?php

namespace App\Console;

use App\Traits\FileGeneratorTrait;
use Illuminate\Support\Facades\Artisan;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class ModuleCreator
{
    use FileGeneratorTrait;

    /*
    *   Creating module logic start
    */
    public function createModule($moduleName): void
    {
        // Для некоторых слов недостаточно добавить 's' в конце
        // Так что необходим такой массив названий для папок
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
            $folderPath = $this->createModuleFolder($moduleName, $folderName);
            $this->createModuleFile($moduleName, $entityName, $folderName, $folderPath);
        }

        $this->createMigration($moduleName);
        $this->createFactory($moduleName);
        $this->createSeeder($moduleName);
    }

    protected function createModuleFolder($moduleName, $folderName): string
    {
        $folderPath = app_path("Modules/$moduleName/$folderName");
        if (!is_dir($folderPath)) {
            mkdir($folderPath, 0777, true);
        }

        return $folderPath;
    }

    protected function createModuleFile($moduleName, $entityName, $folderName, $folderPath): void
    {
        $fileName = $this->generateFileName($moduleName, $entityName);
        $fileContent = $this->generateFileContent($moduleName, $folderName, $fileName, $entityName);

        file_put_contents("$folderPath/$fileName.php", $fileContent);
    }

    protected function generateFileName($moduleName, $entityName): string
    {
        $fileNameMappings = [
            'Event' => "New{$moduleName}Event",
            'Listener' => "NotifyAboutNew{$moduleName}Event",
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
    /*
    *   Creating module logic end
    */

    /*
    *   Renaming module logic start
    */
    public function moduleExists($moduleName): bool
    {
        $moduleDir = app_path('Modules');
        return is_dir("$moduleDir/$moduleName");
    }

    public function renameModule($oldName, $newName): void
    {
        $moduleDir = app_path('Modules');

        // Переименование директории модуля
        rename("$moduleDir/$oldName", "$moduleDir/$newName");

        // Обновление имен файлов внутри переименованной директории
        $this->renameModuleFiles("$moduleDir/$newName", $oldName, $newName);
    }

    protected function renameModuleFiles($modulePath, $oldName, $newName): void
    {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($modulePath, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $item) {
            if ($item->isFile() && $item->getExtension() === 'php') {
                $oldFileName = $item->getFilename();
                $newFileName = str_replace($oldName, $newName, $oldFileName);
                $newFilePath = $item->getPath() . '/' . $newFileName;

                // Переименование файла, если он содержит старое имя модуля
                if (strpos($oldFileName, $oldName) !== false) {
                    rename($item->getRealPath(), $newFilePath);
                }
            }
        }
    }
    /*
    *   Renaming module logic end
    */
}
