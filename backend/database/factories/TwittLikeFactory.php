<?php

namespace Database\Factories;

use App\Modules\Twitt\Models\Twitt;
use App\Modules\Twitt\Models\TwittLike;
use App\Modules\User\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Model>
 */
class TwittLikeFactory extends Factory
{
    protected $model = TwittLike::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $user = User::all()->random();
        $twitt = Twitt::all()->random();

        return [
            'user_id' => $user->id,
            'twitt_id' => $twitt->id
        ];
    }
}
