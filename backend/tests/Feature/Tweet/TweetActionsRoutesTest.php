<?php

namespace Tests\Feature\Notification;

use App\Modules\Tweet\Models\Tweet;
use App\Modules\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;
use Illuminate\Support\Str;

class TweetActionsRoutesTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $authorizedUser;
    protected $tweetsCreator;

    public function setUp(): void
    {
        parent::setUp();

        $this->authorizedUser = User::factory()->create();
        $this->tweetsCreator = User::factory()->create();
        $this->actingAs($this->authorizedUser, 'api');
    }

    public function test_like_tweet_route_basic(): void
    {
        $tweet = Tweet::factory()->create([
            'user_id' => $this->tweetsCreator->id
        ]);

        $response = $this->postJson(
            route('likeTweet', ['tweet' => $tweet->id])
        );

        $response->assertStatus(200);
    }

    public function test_like_tweet_route_incorrect_request_target(): void
    {
        $tweet = Tweet::factory()->create([
            'user_id' => $this->tweetsCreator->id
        ]);

        $response = $this->postJson(
            route('likeTweet', ['tweet' => $tweet->id + 1])
        );

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function test_like_tweet_route_repeated(): void
    {
        $tweet = Tweet::factory()->create([
            'user_id' => $this->tweetsCreator->id
        ]);

        $this->postJson(
            route('likeTweet', ['tweet' => $tweet->id])
        );

        $response = $this->postJson(
            route('likeTweet', ['tweet' => $tweet->id])
        );

        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }

    public function test_dislike_tweet_route_basic(): void
    {
        $tweet = Tweet::factory()->create([
            'user_id' => $this->tweetsCreator->id
        ]);

        $this->postJson(
            route('likeTweet', ['tweet' => $tweet->id])
        );

        $response = $this->deleteJson(
            route('dislikeTweet', ['tweet' => $tweet->id])
        );

        $response->assertStatus(200);
    }

    public function test_dislike_tweet_route_incorrect_request_target(): void
    {
        $tweet = Tweet::factory()->create([
            'user_id' => $this->tweetsCreator->id
        ]);

        $this->postJson(
            route('likeTweet', ['tweet' => $tweet->id])
        );

        $response = $this->deleteJson(
            route('dislikeTweet', ['tweet' => $tweet->id + 1])
        );

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function test_dislike_tweet_route_without_like(): void
    {
        $tweet = Tweet::factory()->create([
            'user_id' => $this->tweetsCreator->id
        ]);

        $response = $this->deleteJson(
            route('dislikeTweet', ['tweet' => $tweet->id])
        );

        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }

    public function test_dislike_tweet_route_repeated(): void
    {
        $tweet = Tweet::factory()->create([
            'user_id' => $this->tweetsCreator->id
        ]);

        $this->postJson(
            route('likeTweet', ['tweet' => $tweet->id])
        );

        $this->deleteJson(
            route('dislikeTweet', ['tweet' => $tweet->id])
        );

        $response = $this->deleteJson(
            route('dislikeTweet', ['tweet' => $tweet->id])
        );

        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }

    // FAVORITES START =====================================

    public function test_bookmark_tweet_route_basic(): void
    {
        $tweet = Tweet::factory()->create([
            'user_id' => $this->tweetsCreator->id
        ]);

        $response = $this->postJson(
            route('addTweetToBookmarks', ['tweet' => $tweet->id])
        );

        $response->assertStatus(200);
    }

    public function test_bookmark_tweet_route_incorrect_request_target(): void
    {
        $tweet = Tweet::factory()->create([
            'user_id' => $this->tweetsCreator->id
        ]);

        $response = $this->postJson(
            route('addTweetToBookmarks', ['tweet' => $tweet->id + 1])
        );

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function test_bookmark_tweet_route_repeated(): void
    {
        $tweet = Tweet::factory()->create([
            'user_id' => $this->tweetsCreator->id
        ]);

        $this->postJson(
            route('addTweetToBookmarks', ['tweet' => $tweet->id])
        );

        $response = $this->postJson(
            route('addTweetToBookmarks', ['tweet' => $tweet->id])
        );

        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }

    public function test_unbookmark_tweet_route_basic(): void
    {
        $tweet = Tweet::factory()->create([
            'user_id' => $this->tweetsCreator->id
        ]);

        $this->postJson(
            route('addTweetToBookmarks', ['tweet' => $tweet->id])
        );

        $response = $this->deleteJson(
            route('removeTweetFromBookmarks', ['tweet' => $tweet->id])
        );

        $response->assertStatus(200);
    }

    public function test_unbookmark_tweet_route_incorrect_request_target(): void
    {
        $tweet = Tweet::factory()->create([
            'user_id' => $this->tweetsCreator->id
        ]);

        $this->postJson(
            route('addTweetToBookmarks', ['tweet' => $tweet->id])
        );

        $response = $this->deleteJson(
            route('removeTweetFromBookmarks', ['tweet' => $tweet->id + 1])
        );

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function test_unbookmark_tweet_route_without_like(): void
    {
        $tweet = Tweet::factory()->create([
            'user_id' => $this->tweetsCreator->id
        ]);

        $response = $this->deleteJson(
            route('removeTweetFromBookmarks', ['tweet' => $tweet->id])
        );

        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }

    public function test_unbookmark_tweet_route_repeated(): void
    {
        $tweet = Tweet::factory()->create([
            'user_id' => $this->tweetsCreator->id
        ]);

        $this->postJson(
            route('addTweetToBookmarks', ['tweet' => $tweet->id])
        );

        $this->deleteJson(
            route('removeTweetFromBookmarks', ['tweet' => $tweet->id])
        );

        $response = $this->deleteJson(
            route('removeTweetFromBookmarks', ['tweet' => $tweet->id])
        );

        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }
}
