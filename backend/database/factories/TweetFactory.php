<?php

namespace Database\Factories;

use App\Modules\Tweet\Models\Tweet;
use App\Modules\User\Models\User;
use App\Modules\User\Models\UserGroup;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Modules\Tweet\Models\TweetModel>
 */
class TweetFactory extends Factory
{
    protected $model = Tweet::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // 'text' => 'nullable|string|max:255',
        // 'userGroupId' => 'nullable|exists:user_groups,id',
        // 'type' => 'nullable|in:repost,reply,quote,thread',
        // 'linkedTweetId' => 'nullable|exists:tweets,id',

        // $userGroup = fake()->boolean(20) ? UserGroup::all()->random() : null;

        $user = User::all()->random();
        $isFirst = empty(Tweet::count());
        $linkedTweetId = fake()->boolean(70) && !empty($isFirst) ? Tweet::all()->random() : null;
        $type = empty($linkedTweetId) ? 'default' : 'comment';

        return [
            'user_id' => $user->id,
            'text' => fake()->words(10, true),
            // 'user_group_id' => $userGroup ? $userGroup->id : null,
            'user_group_id' => null,
            'type' => $type,
            'linked_tweet_id' => $linkedTweetId,
        ];
    }
}
