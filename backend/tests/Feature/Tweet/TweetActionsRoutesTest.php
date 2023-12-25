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

    public function testLikeTweetRouteBasic(): void
    {
        $tweet = Tweet::factory()->create([
            'user_id' => $this->tweetsCreator->id
        ]);

        $response = $this->postJson(
            route('likeTweet', ['tweet' => $tweet->id])
        );

        $response->assertStatus(Response::HTTP_OK);
    }

    public function testLikeTweetRouteIncorrectRequestTarget(): void
    {
        $tweet = Tweet::factory()->create([
            'user_id' => $this->tweetsCreator->id
        ]);

        $response = $this->postJson(
            route('likeTweet', ['tweet' => $tweet->id + 1])
        );

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testLikeTweetRouteRepeated(): void
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

    public function testDislikeTweetRouteBasic(): void
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

        $response->assertStatus(Response::HTTP_OK);
    }

    public function testDislikeTweetRouteIncorrectRequestTarget(): void
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

    public function testDislikeTweetRouteWithoutLike(): void
    {
        $tweet = Tweet::factory()->create([
            'user_id' => $this->tweetsCreator->id
        ]);

        $response = $this->deleteJson(
            route('dislikeTweet', ['tweet' => $tweet->id])
        );

        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }

    public function testDislikeTweetRouteRepeated(): void
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

    public function testBookmarkTweetRouteBasic(): void
    {
        $tweet = Tweet::factory()->create([
            'user_id' => $this->tweetsCreator->id
        ]);

        $response = $this->postJson(
            route('addTweetToBookmarks', ['tweet' => $tweet->id])
        );

        $response->assertStatus(Response::HTTP_OK);
    }

    public function testBookmarkTweetRouteIncorrectRequestTarget(): void
    {
        $tweet = Tweet::factory()->create([
            'user_id' => $this->tweetsCreator->id
        ]);

        $response = $this->postJson(
            route('addTweetToBookmarks', ['tweet' => $tweet->id + 1])
        );

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testBookmarkTweetRouteRepeated(): void
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

    public function testUnbookmarkTweetRouteBasic(): void
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

        $response->assertStatus(Response::HTTP_OK);
    }

    public function testUnbookmarkTweetRouteIncorrectRequestTarget(): void
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

    public function testUnbookmarkTweetRouteWithoutLike(): void
    {
        $tweet = Tweet::factory()->create([
            'user_id' => $this->tweetsCreator->id
        ]);

        $response = $this->deleteJson(
            route('removeTweetFromBookmarks', ['tweet' => $tweet->id])
        );

        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }

    public function testUnbookmarkTweetRouteRepeated(): void
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
