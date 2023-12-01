<?php

namespace App\Modules\User\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $lists = $this->whenLoaded('lists', function () {
            return $this->lists;
        });

        $lists_subscribtions = $this->whenLoaded('lists_subscribtions', function () {
            return $this->lists_subscribtions;
        });

        $deviceTokens = $this->whenLoaded('deviceTokens', function () {
            return $this->deviceTokens;
        });

        return [
            'id' => $this->id,
            'name' => $this->name,
            'link' => $this->link,
            'email' => $this->email,
            'bg_image' => $this->bg_image,
            'avatar' => $this->avatar,
            'status_text' => $this->status_text,
            'site_url' => $this->site_url,
            'address' => $this->address,
            'birth_date' => $this->birth_date,
            'created_at' => $this->created_at,
            "subscribtions_count" => $this->subscribtions_count,
            "subscribers_count" => $this->subscribers_count,
            "lists" => $lists ?? [],
            "lists_subscribtions" => $lists_subscribtions ?? [],
            "device_tokens" => $deviceTokens ?? []
        ];
    }
}
