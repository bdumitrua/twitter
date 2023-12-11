<?php

namespace App\Helpers;

/*
* See @FileGeneratorTrait.php
*/

class FileGeneratorHelper
{
    public static function getBaseContent($moduleName, $folderName, $fileName): string
    {
        return "<?php

namespace App\\Modules\\{$moduleName}\\{$folderName};

class {$fileName}
{
    // TODO: Implement your class logic here
}
";
    }

    public static function getModelContent($moduleName, $folderName, $fileName): string
    {
        return "<?php

namespace App\\Modules\\{$moduleName}\\{$folderName};

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class {$fileName} extends Model
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

    public static function getServiceContent($moduleName, $folderName, $fileName): string
    {
        $modelVariableName = lcfirst($moduleName);
        $modelClassName = ucfirst($modelVariableName);

        $repositoryVariableName = lcfirst($moduleName) . 'Repository';
        $repositoryClassName = ucfirst($repositoryVariableName);

        return "<?php

namespace App\\Modules\\{$moduleName}\\{$folderName};

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Modules\\$moduleName\\Models\\{$modelClassName};

class {$fileName}
{
    private \$$repositoryVariableName;
    
    public function __construct(
        $repositoryClassName \$$repositoryVariableName
    ) {
        \$this->$repositoryVariableName = \$$repositoryVariableName;
    }
}
";
    }

    public static function getControllerContent($moduleName, $folderName, $fileName): string
    {
        $serviceVariableName = lcfirst($moduleName) . 'Service';
        $serviceClassName = ucfirst($serviceVariableName);

        $modelVariableName = lcfirst($moduleName);
        $modelClassName = ucfirst($modelVariableName);

        return "<?php

namespace App\\Modules\\{$moduleName}\\{$folderName};

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\\$moduleName\\Models\\{$modelClassName};
use App\Modules\\$moduleName\\Services\\{$serviceClassName};

class {$fileName} extends Controller
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

    public static function getRepositoryContent($moduleName, $folderName, $fileName): string
    {
        $modelVariableName = lcfirst($moduleName);
        $modelClassName = ucfirst($modelVariableName);

        return "<?php
        
namespace App\\Modules\\{$moduleName}\\{$folderName};

use App\Modules\\$moduleName\\Models\\{$modelClassName};

class {$fileName}
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

    public static function getEventContent($moduleName, $folderName, $fileName): string
    {
        $modelVariableName = lcfirst($moduleName);

        return "<?php

namespace App\\Modules\\{$moduleName}\\{$folderName};

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class {$fileName}
{
    public \$$modelVariableName;

    public function __construct(\$$modelVariableName)
    {
        \$this->{$modelVariableName} = \$$modelVariableName;
    }

}
";
    }

    public static function getListenerContent($moduleName, $folderName, $fileName): string
    {
        $eventVariableName = "new{$moduleName}Event";
        $eventClassName = ucfirst($eventVariableName);

        return "<?php

namespace App\\Modules\\{$moduleName}\\{$folderName};

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Modules\\$moduleName\\Events\\{$eventClassName};

class {$fileName}
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle($eventClassName \$$eventVariableName): void
    {
        //
    }
}
";
    }

    public static function getQueueContent($moduleName, $folderName, $fileName): string
    {
        $modelVariableName = lcfirst($moduleName);

        return "<?php

namespace App\\Modules\\{$moduleName}\\{$folderName};

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class {$fileName} implements ShouldQueue
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

    public static function getResourceContent($moduleName, $folderName, $fileName): string
    {
        return "<?php

namespace App\\Modules\\{$moduleName}\\{$folderName};

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class {$fileName} extends JsonResource
{
    public function toArray(Request \$request): array
    {
        // Implement your logic here
        return [
            'id' => \$this->id,
        ];
    }
}
";
    }

    public static function getRouteContent($moduleName, $folderName, $fileName): string
    {
        $controllerClassName = ucfirst($moduleName) . 'Controller';

        return "<?php

namespace App\\Modules\\{$moduleName}\\{$folderName};

use Illuminate\Support\Facades\Route;
use App\Modules\\$moduleName\\Controllers\\{$controllerClassName};

";
    }

    public static function getRequestContent($moduleName, $folderName, $fileName): string
    {
        return "<?php

namespace App\\Modules\\{$moduleName}\\{$folderName};

use Illuminate\Foundation\Http\FormRequest;

class {$fileName} extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // 
        ];
    }

    public function messages(): array
    {
        return [
            // 
        ];
    }
}
";
    }

    public static function getDTOContent($moduleName, $folderName, $fileName): string
    {
        return "<?php

namespace App\\Modules\\{$moduleName}\\{$folderName};

class {$fileName}
{
    public function __construct(
        // 
    ) {
        // 
    }
}
";
    }
}
