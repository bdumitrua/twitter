<?php

namespace App\Traits;

use App\Helpers\FileGeneratorHelper;

trait FileGeneratorTrait
{
    private function generateFileContent($moduleName, $folderName, $fileName, $entityName)
    {
        $methodName = "get" . ucfirst($entityName) . "Content";
        if (is_callable([FileGeneratorHelper::class, $methodName])) {
            return FileGeneratorHelper::$methodName($moduleName, $folderName, $fileName);
        }

        // Запасной контент или логика, если специфический метод не найден
        return FileGeneratorHelper::getBaseContent($moduleName, $folderName, $fileName);
    }
}
