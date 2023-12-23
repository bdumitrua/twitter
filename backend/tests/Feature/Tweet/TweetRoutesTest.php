<?php

namespace Tests\Feature\Notification;

use App\Modules\Tweet\Models\Tweet;
use App\Modules\User\Models\User;
use App\Modules\User\Models\UserGroup;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;
use Illuminate\Support\Str;

class TweetRoutesTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $authorizedUser;
    protected $anotherUser;

    public function setUp(): void
    {
        parent::setUp();

        $this->authorizedUser = User::factory()->create();
        $this->anotherUser = User::factory()->create();
        $this->actingAs($this->authorizedUser, 'api');
    }

    protected function generateText(): string
    {
        return $this->faker->words(10, true);
    }

    protected function getEmptyText(): ?string
    {
        return null;
    }

    protected function createTweet($data = []): Tweet
    {
        return Tweet::factory()->create($data);
    }

    protected function createRepost(): Tweet
    {
        $tweetToRepost = $this->createTweet();

        return Tweet::factory()->create([
            'type' => 'repost',
            'user_id' => $this->authorizedUser->id,
            'linked_tweet_id' => $tweetToRepost->id
        ]);
    }

    protected function getInvalidTweetId(): int
    {
        $this->createTweet();

        return Tweet::latest()->first()->id + 10;
    }

    protected function createFactoryGroup($userId): UserGroup
    {
        return UserGroup::factory()->create([
            'user_id' => $userId
        ]);
    }

    public function test_create_default_tweet_route_basic(): void
    {
        $text = $this->generateText();
        $group = $this->createFactoryGroup($this->authorizedUser->id);

        $response = $this->postJson(
            route('createTweet'),
            [
                'text' => $text,
                'userGroupId' => $group->id
            ]
        );

        $response->assertStatus(200);
    }

    public function test_create_default_tweet_route_with_empty_text(): void
    {
        $text = $this->getEmptyText();
        $response = $this->postJson(
            route('createTweet'),
            ['text' => $text]
        );

        $response->assertStatus(422);
    }

    public function test_create_default_tweet_route_with_invalid_group_id(): void
    {
        $text = $this->generateText();

        $this->createFactoryGroup($this->authorizedUser->id);
        $groupId = UserGroup::latest()->first()->id + 10;

        $response = $this->postJson(
            route('createTweet'),
            [
                'text' => $text,
                'userGroupId' => $groupId
            ]
        );

        $response->assertStatus(422);
    }

    public function test_repost_tweet_basic(): void
    {
        $tweetToRepost = $this->createTweet();
        $response = $this->postJson(
            route('repostTweet', ['tweet' => $tweetToRepost->id]),
        );

        $response->assertStatus(200);
    }

    public function test_repost_tweet_invalid_request_target(): void
    {
        $tweetId = $this->getInvalidTweetId();
        $response = $this->postJson(
            route('repostTweet', ['tweet' => $tweetId]),
        );

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function test_unrepost_tweet_basic(): void
    {
        $tweet = $this->createRepost();
        $response = $this->delete(
            route('unrepostTweet', ['tweet' => $tweet->linked_tweet_id]),
        );

        $response->assertStatus(200);
    }

    public function test_unrepost_tweet_invalid_request_target(): void
    {
        $response = $this->delete(
            route('unrepostTweet', ['tweet' => $this->getInvalidTweetId()]),
        );

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }


    public function test_unrepost_tweet_without_repost(): void
    {
        $tweet = $this->createTweet();
        $response = $this->delete(
            route('unrepostTweet', ['tweet' => $tweet->id]),
        );

        $response->assertStatus(204);
    }

    public function test_create_reply_tweet_route_basic(): void
    {
        $type = 'reply';
        $text = $this->generateText();
        $tweetToRepost = $this->createTweet();

        $response = $this->postJson(
            route('createTweet'),
            [
                'text' => $text,
                'type' => $type,
                'linkedTweetId' => $tweetToRepost->id
            ]
        );

        $response->assertStatus(200);
    }

    public function test_create_reply_tweet_route_with_empty_text(): void
    {
        $type = 'reply';
        $text = $this->getEmptyText();
        $tweetToRepost = $this->createTweet();

        $response = $this->postJson(
            route('createTweet'),
            [
                'text' => $text,
                'type' => $type,
                'linkedTweetId' => $tweetToRepost->id
            ]
        );

        $response->assertStatus(422);
    }

    public function test_create_reply_tweet_route_invalid_linked_tweet_id(): void
    {
        $type = 'reply';
        $text = $this->generateText();
        $tweetId = $this->getInvalidTweetId();

        $response = $this->postJson(
            route('createTweet'),
            [
                'text' => $text,
                'type' => $type,
                'linkedTweetId' => $tweetId
            ]
        );

        $response->assertStatus(422);
    }

    public function test_create_quote_tweet_route_basic(): void
    {
        $type = 'quote';
        $text = $this->generateText();
        $tweetToRepost = $this->createTweet();

        $response = $this->postJson(
            route('createTweet'),
            [
                'text' => $text,
                'type' => $type,
                'linkedTweetId' => $tweetToRepost->id
            ]
        );

        $response->assertStatus(200);
    }

    public function test_create_quote_tweet_route_with_empty_text(): void
    {
        $type = 'quote';
        $text = $this->getEmptyText();
        $tweetToRepost = $this->createTweet();

        $response = $this->postJson(
            route('createTweet'),
            [
                'text' => $text,
                'type' => $type,
                'linkedTweetId' => $tweetToRepost->id
            ]
        );

        $response->assertStatus(422);
    }

    public function test_create_quote_tweet_route_invalid_linked_tweet_id(): void
    {
        $type = 'quote';
        $text = $this->generateText();
        $tweetId = $this->getInvalidTweetId();

        $response = $this->postJson(
            route('createTweet'),
            [
                'text' => $text,
                'type' => $type,
                'linkedTweetId' => $tweetId
            ]
        );

        $response->assertStatus(422);
    }

    public function test_create_thread_route_basic(): void
    {
        $group = $this->createFactoryGroup($this->authorizedUser->id);
        $tweets = [];
        for ($i = 0; $i < 5; $i++) {
            $tweets[] = [
                'text' => $this->generateText()
            ];
        }

        $response = $this->postJson(
            route('createThread'),
            [
                'tweets' => $tweets,
                'userGroupId' => $group->id
            ]
        );

        $response->assertStatus(200);
    }

    public function test_create_thread_route_with_an_empty_text(): void
    {
        $group = $this->createFactoryGroup($this->authorizedUser->id);
        $tweets = [];
        for ($i = 0; $i < 5; $i++) {
            $tweets[] = [
                'text' => $this->generateText()
            ];
        }

        unset($tweets[4]['text']);

        $response = $this->postJson(
            route('createThread'),
            [
                'tweets' => $tweets,
                'userGroupId' => $group->id
            ]
        );

        $response->assertStatus(422);
    }

    public function test_create_thread_route_invalid_group_id(): void
    {
        $this->createFactoryGroup($this->authorizedUser->id);
        $groupId = UserGroup::latest()->first()->id + 10;
        $tweets = [];
        for ($i = 0; $i < 5; $i++) {
            $tweets[] = [
                'text' => $this->generateText()
            ];
        }

        $response = $this->postJson(
            route('createThread'),
            [
                'tweets' => $tweets,
                'userGroupId' => $groupId
            ]
        );

        $response->assertStatus(422);
    }

    public function test_create_thread_route_with_any_type(): void
    {
        $type = 'repost';
        $group = $this->createFactoryGroup($this->authorizedUser->id);
        $tweets = [];
        for ($i = 0; $i < 5; $i++) {
            $tweets[] = [
                'type' => $type,
                'text' => $this->generateText()
            ];
        }

        $response = $this->postJson(
            route('createThread'),
            [
                'tweets' => $tweets,
                'userGroupId' => $group->id
            ]
        );

        $response->assertStatus(200);
    }

    public function test_delete_tweet_basic(): void
    {
        $tweet = $this->createTweet([
            'user_id' => $this->authorizedUser->id
        ]);

        $response = $this->delete(
            route('deleteTweet', ['tweet' => $tweet->id]),
        );

        $response->assertStatus(200);
    }

    public function test_delete_tweet_invalid_request_target(): void
    {
        $response = $this->delete(
            route('deleteTweet', ['tweet' => $this->getInvalidTweetId()]),
        );

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function test_delete_tweet_of_another_user(): void
    {
        $tweet = $this->createTweet([
            'user_id' => $this->anotherUser->id
        ]);

        $response = $this->delete(
            route('deleteTweet', ['tweet' => $tweet->id]),
        );

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }
}
