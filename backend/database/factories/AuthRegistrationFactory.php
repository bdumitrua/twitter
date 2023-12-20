<?php

namespace Database\Factories;

use App\Modules\Auth\Models\AuthRegistration;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Model>
 */
class AuthRegistrationFactory extends Factory
{
    protected $model = AuthRegistration::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => '11111',
            'name' => fake()->name(),
            'email' => fake()->email(),
            'birth_date' => fake()->date('Y-m-d'),
            'confirmed' => false
        ];
    }
}
