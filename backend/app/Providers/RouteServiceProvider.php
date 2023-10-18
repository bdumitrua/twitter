<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

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
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

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

        foreach (new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($moduleDir),
            RecursiveIteratorIterator::SELF_FIRST
        ) as $fileInfo) {
            if ($fileInfo->isFile() && $fileInfo->getFilename() === 'routes.php') {
                $moduleRoutes = $fileInfo->getPathname();

                // Подключите маршруты из модуля с префиксом и middleware 'api'
                Route::prefix('api')
                    ->middleware('api')
                    ->group($moduleRoutes);
            }
        }
    }
}
