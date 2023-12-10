<?php

namespace App\Modules\Notification\Repositories;

use App\Modules\Notification\Models\DeviceToken;
use Illuminate\Database\Eloquent\Collection;

class DeviceTokenRepository
{
    protected $deviceToken;

    public function __construct(DeviceToken $deviceToken)
    {
        $this->deviceToken = $deviceToken;
    }

    public function getByUserId(int $userId): Collection
    {
        return $this->deviceToken->where('user_id', '=', $userId)
            ->orderBy('created_at', 'desc')->get();
    }

    public function create(int $userId, string $token): void
    {
        $this->deviceToken->firstOrCreate([
            'user_id' => $userId,
            'token' => $token
        ]);
    }

    public function update(DeviceToken $deviceToken, string $token): void
    {
        $deviceToken->token = $token;
        $deviceToken->save();
    }

    public function delete(DeviceToken $deviceToken): void
    {
        $deviceToken->delete();
    }
}
