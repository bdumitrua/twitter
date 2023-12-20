<?php

namespace Database\Factories;

use App\Modules\Auth\Models\AuthReset;
use App\Modules\User\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Model>
 */
class AuthResetFactory extends Factory
{
    protected $model = AuthReset::class;

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
            'code' => '11111',
            'confirmed' => false,
        ];
    }
}
