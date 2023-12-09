<?php

namespace Database\Factories;

use App\Modules\Tweet\Models\TweetDraft;
use App\Modules\User\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Model>
 */
class TweetDraftFactory extends Factory
{
    protected $model = TweetDraft::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $user = User::all()->random();

        return [
            'text' => fake()->words(10, true),
            'user_id' => $user->id,
        ];
    }
}
