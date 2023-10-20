<?php

namespace Database\Factories;

use App\Modules\User\Models\User;
use App\Modules\User\Models\UsersList;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Model>
 */
class UsersListFactory extends Factory
{
    protected $model = UsersList::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $user = User::all()->random();

        return [
            'name' => fake()->words(3, true),
            'description' => fake()->words(15, true),
            'user_id' => $user->id,
            'bg_image' => fake()->imageUrl(),
            'is_private' => fake()->boolean(50),
        ];
    }
}
