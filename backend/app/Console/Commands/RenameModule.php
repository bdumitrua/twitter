<?php

namespace App\Console\Commands;

use App\Console\ModuleCreator;
use Illuminate\Console\Command;

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
    public function handle(): void
    {
        $oldName = $this->argument('oldName');
        $newName = $this->argument('newName');
        $moduleCreator = new ModuleCreator();

        if (!$moduleCreator->moduleExists($oldName)) {
            $this->error("Module $oldName doesn't exist!");
            return;
        }

        $moduleCreator->renameModule($oldName, $newName);

        $this->info("Module $oldName renamed to $newName successfully.");
    }
}
