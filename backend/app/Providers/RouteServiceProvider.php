<?php

namespace App\Providers;

use App\Modules\Auth\Controllers\AuthController;
use DirectoryIterator;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        // RateLimiter::for('api', function (Request $request) {
        //     return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        // });

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));

            $this->loadModuleRoutes();
        });
    }

    protected function loadModuleRoutes(): void
    {
        $moduleDir = app_path('Modules');

        // Перебираем все папки модулей
        foreach (new DirectoryIterator($moduleDir) as $moduleInfo) {
            if ($moduleInfo->isDir() && !$moduleInfo->isDot()) {
                $routesDir = $moduleInfo->getPathname() . '/Routes';

                // Проверяем, существует ли папка Routes в модуле
                if (is_dir($routesDir)) {
                    // Перебираем все файлы в папке Routes
                    foreach (new DirectoryIterator($routesDir) as $fileInfo) {
                        if ($fileInfo->isFile() && $fileInfo->getExtension() === 'php') {
                            $moduleRoutes = $fileInfo->getPathname();

                            // Подключаем маршруты из модуля с префиксом и middleware 'api'
                            Route::prefix('api')
                                ->middleware('api')
                                ->group($moduleRoutes);
                        }
                    }
                }
            }
        }
    }
}
