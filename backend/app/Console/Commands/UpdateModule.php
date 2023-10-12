<?php

namespace App\Console\Commands;

use App\Console\ModuleCreator;
use App\Traits\FileGeneratorTrait;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class UpdateModule extends Command
{
    use FileGeneratorTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:modules';

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
        $moduleDir = app_path('Modules');
        $moduleFolders = File::directories($moduleDir);
        $moduleCreator = new ModuleCreator();

        foreach ($moduleFolders as $modulePath) {
            $moduleName = last(explode('/', $modulePath));
            $moduleCreator->updateModules($moduleName);
        }

        $this->info("Modules updated successfully.");
    }
}
