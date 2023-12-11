<?php

namespace App\Modules\Auth\Resources;

use App\Http\Resources\ActionsResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class PasswordResetConfirmedResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $resetId = $this->resource;
        $actions = ActionsResource::collection([
            [
                "EndPasswordReset",
                "endPasswordReset",
                ['authReset' => $resetId],
            ]
        ]);

        return [
            'resetId' => $resetId,
            'actions' => $actions,
        ];
    }
}
