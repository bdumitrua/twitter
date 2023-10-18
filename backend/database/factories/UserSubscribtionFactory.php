<?php

namespace Database\Factories;

use App\Modules\User\Models\User;
use App\Modules\User\Models\UserSubscribtion;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserSubscribtionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model = UserSubscribtion::class;

    public function definition(): array
    {
        $subscriber = User::all()->random();
        $user = User::where('id', '!=', $subscriber->id)->get()->random();

        return [
            'subscriber_id' => $subscriber->id,
            'user_id' => $user->id,
        ];
    }
}
