<?php

namespace Database\Factories;

use App\Modules\Tweet\Models\Tweet;
use App\Modules\Tweet\Models\TweetLike;
use App\Modules\User\Events\TweetLikeEvent;
use App\Modules\User\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Model>
 */
class TweetLikeFactory extends Factory
{
    protected $model = TweetLike::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $user = User::all()->random();
        $tweet = Tweet::all()->random();

        return [
            'user_id' => $user->id,
            'tweet_id' => $tweet->id
        ];
    }

    /**
     * Configure the model factory.
     *
     * @return $this
     */
    // public function configure()
    // {
    //     return $this->afterCreating(function (TweetLike $tweetLike) {
    //         event(new TweetLikeEvent($tweetLike, true));
    //     });
    // }
}
