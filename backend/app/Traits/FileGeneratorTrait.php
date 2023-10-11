<?php

namespace App\Traits;

trait FileGeneratorTrait
{
    private function generateFileContent($moduleName, $dir)
    {
        $className = "{$moduleName}{$dir}";

        return "<?php

namespace App\\Modules\\{$moduleName}\\{$dir};

class {$className}
{
    // TODO: Implement your class logic here
}
";
    }
}
