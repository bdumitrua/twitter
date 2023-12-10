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
        $additionalData = $this->resource[3] ?? null;

        $response = [
            $actionName => [
                'url' => $actionRoute,
                'method' => $actionMethod,
            ]
        ];

        if (!empty($additionalData)) {
            $response[array_key_first($additionalData)] = array_values($additionalData)[0];
        }

        return $response;
    }

    public static function collection($resource): object
    {
        // Для формирования удобного массива с ключ => данные
        $new = [];

        foreach ($resource as $action) {
            $actionResource = new ActionsResource($action);
            $actionArray = $actionResource->toArray(request());

            foreach ($actionArray as $key => $value) {
                $new[$key] = $value;
            }
        }

        return (object) $new;
    }

    protected function getRouteMethod(string $routeName): string
    {
        $route = Route::getRoutes()->getByName($routeName);
        return $route->methods()[0];
    }
}
