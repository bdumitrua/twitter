<?php

namespace Database\Factories;

use App\Modules\Twitt\Models\Twitt;
use App\Modules\User\Models\User;
use App\Modules\User\Models\UserGroup;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Modules\Twitt\Models\TwittModel>
 */
class TwittFactory extends Factory
{
    protected $model = Twitt::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $user = User::all()->random();
        $userGroup = fake()->boolean(20) ? UserGroup::all()->random() : null;

        return [
            'user_id' => $user->id,
            'user_group_id' => $userGroup ? $userGroup->id : null,
            'text' => fake()->words(10, true),
        ];
    }
}
