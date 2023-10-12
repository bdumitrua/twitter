<?php

namespace App\Traits;

use App\Helpers\FileGeneratorHelper;

trait FileGeneratorTrait
{
    private function generateFileContent($moduleName, $entityName, $folderName)
    {
        $className = ucfirst($moduleName) . $entityName;
        $methodName = "get" . ucfirst($entityName) . "Content";
        if (is_callable([FileGeneratorHelper::class, $methodName])) {
            return FileGeneratorHelper::$methodName($moduleName, $folderName, $className);
        }

        // Запасной контент или логика, если специфический метод не найден
        return FileGeneratorHelper::getBaseContent($moduleName, $folderName, $className);
    }
}
