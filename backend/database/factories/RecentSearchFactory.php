<?php

namespace Database\Factories;

use App\Modules\Search\Models\RecentSearch;
use App\Modules\User\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Model>
 */
class RecentSearchFactory extends Factory
{
    protected $model = RecentSearch::class;

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
            'text' => fake()->word(3),
            'linked_user_id' => null
        ];
    }
}
