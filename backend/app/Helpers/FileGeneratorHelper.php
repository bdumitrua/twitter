<?php

namespace App\Helpers;

/*
* See @FileGeneratorTrait.php
*/

class FileGeneratorHelper
{
    public static function getBaseContent($moduleName, $dir, $className)
    {
        return "<?php

namespace App\\Modules\\{$moduleName}\\{$dir}s;

class {$className}
{
    // TODO: Implement your class logic here
}
";
    }

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

    public static function getRepositoryContent($moduleName, $dir, $className)
    {
        $modelVariableName = lcfirst($moduleName) . 'Model';
        $modelClassName = ucfirst($modelVariableName);

        return "<?php
        
namespace App\\Modules\\{$moduleName}\\{$dir}s;

use App\Modules\\$moduleName\\Models\\$modelClassName;

class $className
{
    protected \$$modelVariableName;

    public function __construct($modelClassName \$$modelVariableName)
    {
        \$this->$modelVariableName = \$$modelVariableName;
    }

    // Base method example
    public function findById(\$id)
    {
        return \$this->{$modelVariableName}->find(\$id);
    }
}
";
    }

    public static function getEventContent($moduleName, $dir, $className)
    {
        $modelVariableName = lcfirst($moduleName) . 'Model';

        return "<?php

namespace App\\Modules\\{$moduleName}\\{$dir}s;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class New{$className} implements ShouldBroadcast
{
    public \$$modelVariableName;

    public function __construct(\$$modelVariableName)
    {
        \$this->{$modelVariableName} = \$$modelVariableName;
    }

    public function broadcastOn()
    {
        // Implement your logic here
        // return new PrivateChannel();
    }
}
";
    }

    public static function getJobContent($moduleName, $dir, $className)
    {
        $modelVariableName = lcfirst($moduleName) . 'Model';

        return "<?php

namespace App\\Modules\\{$moduleName}\\{$dir}s;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class $className implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected \$$modelVariableName;
    
    public function __construct(\$$modelVariableName)
    {
        \$this->{$modelVariableName} = \$$modelVariableName;
    }

    public function handle()
    {
        // Implement your logic here
    }
}
";
    }
}
