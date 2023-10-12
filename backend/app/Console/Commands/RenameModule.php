<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class RenameModule extends Command
{
    /**
     * @var string
     */
    protected $signature = 'rename:module {oldName} {newName}';

    /**
     * @var string
     */
    protected $description = 'Rename an existing module';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $oldName = $this->argument('oldName');
        $newName = $this->argument('newName');
        $moduleDir = app_path('Modules');

        // Проверка существования старой папки модуля
        if (!is_dir("$moduleDir/$oldName")) {
            $this->error("Module $oldName doesn't exist!");
            return;
        }

        // Переименование директории модуля
        rename("$moduleDir/$oldName", "$moduleDir/$newName");
        $this->info("Renamed module directory $oldName to $newName.");

        // Обновление имен файлов внутри переименованной директории
        $this->renameModuleFiles("$moduleDir/$newName", $oldName, $newName);

        $this->info("Module $oldName renamed to $newName successfully.");
    }

    protected function renameModuleFiles($modulePath, $oldName, $newName)
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
                    $this->info("Renamed file $oldFileName to $newFileName");
                }
            }
        }
    }
}
