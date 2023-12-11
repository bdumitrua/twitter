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

    /**
     * @param int $userId
     * 
     * @return Collection
     */
    public function getByUserId(int $userId): Collection
    {
        return $this->deviceToken->where('user_id', '=', $userId)
            ->orderBy('created_at', 'desc')->get();
    }

    /**
     * @param int $userId
     * @param string $token
     * 
     * @return void
     */
    public function create(int $userId, string $token): void
    {
        $this->deviceToken->firstOrCreate([
            'user_id' => $userId,
            'token' => $token
        ]);
    }

    /**
     * @param DeviceToken $deviceToken
     * @param string $token
     * 
     * @return void
     */
    public function update(DeviceToken $deviceToken, string $token): void
    {
        $deviceToken->token = $token;
        $deviceToken->save();
    }

    /**
     * @param DeviceToken $deviceToken
     * 
     * @return void
     */
    public function delete(DeviceToken $deviceToken): void
    {
        $deviceToken->delete();
    }
}
