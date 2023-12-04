<?php

namespace Database\Factories;

use App\Helpers\StringHelper;
use App\Modules\User\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->name();
        $email = fake()->unique()->email();
        $link = StringHelper::createUserLink($email);

        return [
            'name' => $name,
            'link' => $link,
            'email' => $email,
            'birth_date' => fake()->date(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password,
        ];
    }
}
