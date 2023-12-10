<?php

namespace App\Modules\Auth\Resources;

use App\Http\Resources\ActionsResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class RegistrationConfirmedResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $registrationId = $this->resource;
        $actions = ActionsResource::collection([
            [
                "EndRegistration",
                "end_registration",
                ['authRegistration' => $registrationId],
            ]
        ]);

        return [
            'registration_id' => $registrationId,
            'actions' => $actions,
        ];
    }
}
