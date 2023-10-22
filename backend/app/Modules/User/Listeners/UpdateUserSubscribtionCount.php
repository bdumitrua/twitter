<?php

namespace App\Modules\User\Listeners;

use App\Modules\User\Models\User;
use App\Modules\User\Models\UserSubscribtion;

class UpdateUserSubscribtionCount
{
    public function handle($event)
    {
        /** @var UserSubscribtion */
        $userSubscribtion = $event->userSubscribtion;
        $add = $event->add;
        $subscriber = User::find($userSubscribtion->subscriber_id);
        $user = User::find($userSubscribtion->user_id);

        if (!empty($add)) {
            // Обновляем счётчик подписок для подписчика
            $subscriber->subscribtions_count = $subscriber->subscribers_count + 1;
            $user->subscribers_count = $user->subscribers_count + 1;
        } else {
            // Обновляем счётчик подписчиков для пользователя
            $subscriber->subscribtions_count = $subscriber->subscribers_count - 1;
            $user->subscribers_count = $user->subscribers_count - 1;
        }

        $subscriber->save();
        $user->save();
    }
}
