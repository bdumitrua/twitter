<?php

namespace App\Traits;

use App\Helpers\FileGeneratorHelper;

trait FileGeneratorTrait
{
    private function generateFileContent($moduleName, $dir)
    {
        $className = ucfirst($moduleName) . $dir;
        $methodName = "get" . ucfirst($dir) . "Content";
        if (is_callable([FileGeneratorHelper::class, $methodName])) {
            return FileGeneratorHelper::$methodName($moduleName, $dir, $className);
        }

        // Запасной контент или логика, если специфический метод не найден
        return "<?php

namespace App\\Modules\\{$moduleName}\\{$dir}s;

class {$className}
{
    // TODO: Implement your class logic here
}
";
    }
}
