<?php

namespace App\Console\Commands;

use App\Console\ModuleCreator;
use App\Traits\FileGeneratorTrait;
use Illuminate\Console\Command;

class MakeModule extends Command
{
    use FileGeneratorTrait;

    /**
     * @var string
     */
    protected $signature = 'make:module {name}';


    /**
     * @var string
     */
    protected $description = 'Creates a new module';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $moduleName = $this->argument('name');
        $moduleCreator = new ModuleCreator();
        $moduleCreator->createModule($moduleName);

        $this->info("Module $moduleName created successfully.");
    }
}
