<?php

namespace App\Modules\Auth\Resources;

use App\Http\Resources\ActionsResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class RegistrationCodeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $registrationId = $this->resource;
        $actions = ActionsResource::collection([
            [
                "ConfirmCode",
                "confirmRegistrationCode",
                ['authRegistration' => $registrationId],
            ]
        ]);

        return [
            'registration_id' => $registrationId,
            'actions' => $actions,
        ];
    }
}
