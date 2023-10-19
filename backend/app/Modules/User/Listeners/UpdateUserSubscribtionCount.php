<?php

namespace App\Modules\User\Listeners;

use App\Modules\User\Models\User;
use App\Modules\User\Models\UserSubscribtion;

class UpdateUserSubscribtionCount
{
    public function handle($event)
    {
        /** @var UserSubscribtion */
        $subscription = $event->subscription;

        // Обновляем счётчик подписок для подписчика
        $subscriber = User::find($subscription->subscriber_id);
        $subscriber->subscribtions_count = UserSubscribtion::where('subscriber_id', $subscriber->id)->count();
        $subscriber->save();

        // Обновляем счётчик подписчиков для пользователя
        $user = User::find($subscription->user_id);
        $user->subscribers_count = UserSubscribtion::where('user_id', $user->id)->count();
        $user->save();
    }
}
