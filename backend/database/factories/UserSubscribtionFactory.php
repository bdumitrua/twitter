<?php

namespace Database\Factories;

use App\Modules\User\Events\UserSubscribtionEvent;
use App\Modules\User\Models\User;
use App\Modules\User\Models\UserSubscribtion;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserSubscribtionFactory extends Factory
{
    protected $model = UserSubscribtion::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $subscriber = User::all()->random();
        $user = User::where('id', '!=', $subscriber->id)->get()->random();

        return [
            'user_id' => $user->id,
            'subscriber_id' => $subscriber->id,
        ];
    }

    /**
     * Configure the model factory.
     *
     * @return $this
     */
    public function configure()
    {
        return $this->afterCreating(function (UserSubscribtion $subscribtion) {
            event(new UserSubscribtionEvent($subscribtion));
        });
    }
}
