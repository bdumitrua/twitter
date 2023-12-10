<?php

namespace App\Modules\Auth\Resources;

use App\Http\Resources\ActionsResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class PasswordResetConfirmedResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $resetId = $this->resource;
        $actions = ActionsResource::collection([
            [
                "EndPasswordReset",
                "end_password_reset",
                ['authReset' => $resetId],
            ]
        ]);

        return [
            'reset_id' => $resetId,
            'actions' => $actions,
        ];
    }
}
