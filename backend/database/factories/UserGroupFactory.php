<?php

namespace Database\Factories;

use App\Modules\User\Models\User;
use App\Modules\User\Models\UserGroup;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Modules\UserGroup\Models\UserGroupModel>
 */
class UserGroupFactory extends Factory
{
    protected $model = UserGroup::class;
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
            'name' => fake()->words(3, true),
            'description' => fake()->words(10, true)
        ];
    }
}
