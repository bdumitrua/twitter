<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class RenameModule extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rename:module {oldName} {newName}';

    /**
     * The console command description.
     *
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
        // TODO 
        // Fix this '/Modules/' path in all commands
        $moduleDir = app_path() . '/Modules/';

        if (!is_dir($moduleDir . '/' . $oldName)) {
            $this->error("Module $oldName doesn't exist!");
            return;
        }

        // Rename the module directory
        rename($moduleDir . '/' . $oldName, $moduleDir . '/' . $newName);
        $this->info("Renamed module directory $oldName to $newName.");

        // Update the files names within the renamed directory
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($moduleDir . '/' . $newName, RecursiveDirectoryIterator::SKIP_DOTS), RecursiveIteratorIterator::SELF_FIRST);

        foreach ($iterator as $item) {
            if ($item->isFile() && $item->getExtension() === 'php') {
                // Rename the file if it contains the old module name
                if (strpos($item->getFilename(), $oldName) !== false) {
                    $newFileName = str_replace($oldName, $newName, $item->getFilename());
                    rename($item->getRealPath(), $item->getPath() . '/' . $newFileName);
                    $this->info("Renamed file {$item->getFilename()} to $newFileName");
                }
            }
        }

        $this->info("Module $oldName renamed to $newName successfully.");
    }
}
