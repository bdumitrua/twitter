<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class ActionsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $actionName = $this->resource[0];
        $actionRoute = route($this->resource[1], $this->resource[2] ?? [], false);
        $actionMethod = $this->getRouteMethod($this->resource[1]);

        return [
            'name' => $actionName,
            'url' => $actionRoute,
            'method' => $actionMethod,
        ];
    }

    protected function getRouteMethod(string $routeName): string
    {
        $route = Route::getRoutes()->getByName($routeName);
        return $route->methods()[0];
    }
}
