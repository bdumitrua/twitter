<?php

namespace App\Traits;

use App\Helpers\FileGeneratorHelper;

trait FileGeneratorTrait
{
    /**
     * @param mixed $moduleName
     * @param mixed $folderName
     * @param mixed $fileName
     * @param mixed $entityName
     * 
     * @return string
     */
    private function generateFileContent($moduleName, $folderName, $fileName, $entityName): string
    {
        $methodName = "get" . ucfirst($entityName) . "Content";
        if (is_callable([FileGeneratorHelper::class, $methodName])) {
            return FileGeneratorHelper::$methodName($moduleName, $folderName, $fileName);
        }

        // Запасной контент или логика, если специфический метод не найден
        return FileGeneratorHelper::getBaseContent($moduleName, $folderName, $fileName);
    }
}
