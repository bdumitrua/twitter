<?php

namespace App\Modules\Notification\Resources;

use App\Http\Resources\ActionsResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DeviceTokenResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $actions = (array) ActionsResource::collection([
            [
                "UpdateDeviceToken",
                "updateDeviceToken",
                ["deviceToken" => $this->id]
            ],
            [
                "DeleteDeviceToken",
                "deleteDeviceToken",
                ["deviceToken" => $this->id]
            ],
        ]);

        return [
            'id' => $this->id,
            'userId' => $this->user_id,
            'token' => $this->token,
            'actions' => $actions,
        ];
    }
}
