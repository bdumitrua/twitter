<?php

namespace App\Modules\Notification\Repositories;

use App\Modules\Notification\DTO\NotificationDTO;
use App\Modules\Notification\Models\Notification;
use Illuminate\Database\Eloquent\Collection;

class NotificationRepository
{
    protected $notification;

    public function __construct(Notification $notification)
    {
        $this->notification = $notification;
    }

    public function getByUserId(int $userId): Collection
    {
        return $this->notification->where('user_id', '=', $userId)->take(20);
    }

    public function create(NotificationDTO $notificationDTO): void
    {
        foreach ($notificationDTO as $property => $value) {
            if (property_exists($this->notification, $property)) {
                $this->notification->{$property} = $value;
            }
        }

        $this->notification->save();
    }

    public function update(Notification $notification, string $newStatus): void
    {
        $notification->update([
            'status' => $newStatus
        ]);
    }
}
