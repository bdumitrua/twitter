<?php

namespace App\Modules\Auth\Resources;

use App\Http\Resources\ActionsResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class RegistrationCodeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $registrationId = $this->resource;
        $actions = new ActionsResource([
            "ConfirmCode",
            "confirm_registration_code",
            ['authRegistration' => $registrationId],
        ]);

        return [
            'registration_id' => $registrationId,
            'actions' => $actions,
        ];
    }
}
