<?php

namespace App\Modules\Auth\Resources;

use App\Http\Resources\ActionsResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class AuthTokenResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $actions = ActionsResource::collection([
            [
                "GetAuthorizedUserData",
                "get_authorized_user_data"
            ],
            [
                "Logout",
                "auth_logout"
            ],
            [
                "RefreshToken",
                "auth_refresh_token"
            ],
        ]);

        return [
            'token_type' => 'bearer',
            'access_token' => $this->resource,
            'expires_in' => Auth::factory()->getTTL() * 60,
            'actions' => $actions
        ];
    }
}
