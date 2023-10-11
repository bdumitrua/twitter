<?php

namespace App\Helpers;

/*
* See @FileGeneratorTrait.php
*/

class FileGeneratorHelper
{
    public static function getModelContent($moduleName, $dir, $className)
    {
        return "<?php

namespace App\\Modules\\{$moduleName}\\{$dir}s;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class {$className} extends Model
{
    use HasFactory;

    protected \$fillable = [
        // Put your changeable fields here
    ];

    protected \$hidden = [
        // The attributes that should be hidden for serialization
    ];
}
";
    }

    public static function getServiceContent($moduleName, $dir, $className)
    {
        $modelVariableName = lcfirst($moduleName) . 'Model';
        $modelClassName = ucfirst($modelVariableName);

        return "<?php

namespace App\\Modules\\{$moduleName}\\{$dir}s;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Modules\\$moduleName\\Models\\$modelClassName;

class $className
{
    public function show($modelClassName \$$modelVariableName)
    {
        return \$$modelVariableName;
    }
}
";
    }

    public static function getControllerContent($moduleName, $dir, $className)
    {
        $serviceVariableName = lcfirst($moduleName) . 'Service';
        $serviceClassName = ucfirst($serviceVariableName);

        $modelVariableName = lcfirst($moduleName) . 'Model';
        $modelClassName = ucfirst($modelVariableName);

        return "<?php

namespace App\\Modules\\{$moduleName}\\{$dir}s;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\\$moduleName\\Models\\$modelClassName;
use App\Modules\\$moduleName\\Services\\$serviceClassName;

class $className extends Controller
{
    private \$$serviceVariableName;

    public function __construct($serviceClassName \$$serviceVariableName)
    {
        \$this->$serviceVariableName = \$$serviceVariableName;
    }

    // Method realization example
    public function show($modelClassName \$$modelVariableName)
    {
        return \$this->handleServiceCall(function () use (\$$modelVariableName) {
            return \$this->{$serviceVariableName}->show(\$$modelVariableName);
        });
    }

}
";
    }
}
