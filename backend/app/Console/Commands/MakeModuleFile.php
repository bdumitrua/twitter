<?php

namespace App\Console\Commands;

use App\Console\ModuleCreator;
use Illuminate\Console\Command;

class MakeModuleFile extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:module:file {module} {type} {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates entity in module folder';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $moduleName = ucfirst($this->argument('module'));
        $entityFolder = ucfirst($this->argument('type'));
        $fileName = $this->argument('name');

        $moduleCreator = new ModuleCreator();
        $moduleCreator->createModuleContent(
            $moduleName,
            $moduleCreator->moduleDirectories[$entityFolder],
            $entityFolder,
            $fileName
        );

        $this->info("Module file $fileName.php created successfully.");
    }
}
