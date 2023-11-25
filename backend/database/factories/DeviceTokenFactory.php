<?php

namespace Database\Factories;

use App\Modules\Notification\Models\DeviceToken;
use App\Modules\Tweet\Models\Tweet;
use App\Modules\User\Models\User;
use App\Modules\User\Models\UserGroup;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Modules\Tweet\Models\TweetModel>
 */
class DeviceTokenFactory extends Factory
{
    protected $model = DeviceToken::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $user = User::all()->random();

        return [
            'user_id' => $user->id,
            'token' => Str::random(16),
        ];
    }
}
