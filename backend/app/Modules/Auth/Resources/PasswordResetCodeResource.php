<?php

namespace App\Modules\Auth\Resources;

use App\Http\Resources\ActionsResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class PasswordResetCodeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $resetId = $this->resource;
        $actions = new ActionsResource([
            "ConfirmCode",
            "confirm_password_reset_code",
            ['authReset' => $resetId],
        ]);

        return [
            'reset_id' => $resetId,
            'actions' => $actions,
        ];
    }
}
