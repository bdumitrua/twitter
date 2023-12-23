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

    protected function createFactoryTweet(): Tweet
    {
        return Tweet::factory()->create();
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

    public function test_create_repost_tweet_basic(): void
    {
        $tweetToRepost = $this->createFactoryTweet();
        $response = $this->postJson(
            route('repostTweet', ['tweet' => $tweetToRepost->id]),
        );

        $response->assertStatus(200);
    }

    public function test_create_repost_tweet_invalid_request_target(): void
    {
        $this->createFactoryTweet();
        $tweetId = Tweet::latest()->first()->id + 10;

        $response = $this->postJson(
            route('repostTweet', ['tweet' => $tweetId]),
        );

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function test_create_reply_tweet_route_basic(): void
    {
        $type = 'reply';
        $text = $this->generateText();
        $tweetToRepost = $this->createFactoryTweet();

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
        $tweetToRepost = $this->createFactoryTweet();

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

        $this->createFactoryTweet();
        $tweetId = Tweet::latest()->first()->id + 10;

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
        $tweetToRepost = $this->createFactoryTweet();

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
        $tweetToRepost = $this->createFactoryTweet();

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

        $this->createFactoryTweet();
        $tweetId = Tweet::latest()->first()->id + 10;

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
}
