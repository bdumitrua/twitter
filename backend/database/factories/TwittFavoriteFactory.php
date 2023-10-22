<?php

namespace Database\Factories;

use App\Modules\Twitt\Models\Twitt;
use App\Modules\Twitt\Models\TwittFavorite;
use App\Modules\User\Events\TwittFavoriteEvent;
use App\Modules\User\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Model>
 */
class TwittFavoriteFactory extends Factory
{
    protected $model = TwittFavorite::class;

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

    /**
     * Configure the model factory.
     *
     * @return $this
     */
    public function configure()
    {
        return $this->afterCreating(function (TwittFavorite $twittFavorite) {
            event(new TwittFavoriteEvent($twittFavorite, true));
        });
    }
}
