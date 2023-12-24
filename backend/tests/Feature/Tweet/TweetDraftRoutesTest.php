<?php

namespace Tests\Feature\Notification;

use App\Modules\Tweet\Models\TweetDraft;
use App\Modules\Tweet\Resources\TweetDraftsResource;
use App\Modules\User\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;
use Illuminate\Support\Str;

class TweetDraftRoutesTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $authorizedUser;

    public function setUp(): void
    {
        parent::setUp();

        $this->authorizedUser = User::factory()->create();
        $this->actingAs($this->authorizedUser, 'api');
    }

    protected function getTextObject(): array
    {
        return ['text' => Str::random(16)];
    }

    protected function getInvalidTextObject(): array
    {
        return ['text' => 123];
    }

    public function test_get_authorized_user_drafts_route_basic(): void
    {
        $tweetDrafts = TweetDraft::factory(3)->create([
            'user_id' => $this->authorizedUser->id,
        ]);

        $response = $this->get(route('getAuthorizedUserDrafts'));
        $createdResources = TweetDraftsResource::make(collect($tweetDrafts))->resolve();

        $response->assertStatus(Response::HTTP_OK)->assertJson($createdResources);
    }

    public function test_get_authorized_user_drafts_route_empty(): void
    {
        $tweetDrafts = new Collection();
        $response = $this->get(route('getAuthorizedUserDrafts'));
        $createdResources = TweetDraftsResource::collection(collect($tweetDrafts))->resolve();

        $response->assertStatus(Response::HTTP_OK)->assertJson($createdResources);
    }

    public function test_create_tweet_draft_route_basic(): void
    {
        $response = $this->postJson(
            route('createTweetDraft'),
            $this->getTextObject()
        );

        $response->assertStatus(Response::HTTP_OK);
    }

    public function test_create_tweet_draft_route_incorrect_request(): void
    {
        $response = $this->postJson(
            route('createTweetDraft'),
            $this->getInvalidTextObject()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function test_create_tweet_draft_route_with_same_text(): void
    {
        $tweetDraft = TweetDraft::factory()->create();
        $response = $this->postJson(
            route('createTweetDraft'),
            ['text' => $tweetDraft->text]
        );

        $response->assertStatus(Response::HTTP_OK);
    }

    public function test_delete_tweet_draft_route_basic(): void
    {
        $tweetDraft = TweetDraft::factory(3)->create();
        $draftsIds = $tweetDraft->pluck('id')->toArray();
        $response = $this->delete(
            route('deleteTweetDrafts'),
            ['drafts' => $draftsIds]
        );

        $response->assertStatus(Response::HTTP_OK);
    }

    public function test_delete_tweet_draft_route_incorrect_request_target(): void
    {
        $response = $this->delete(
            route('deleteTweetDrafts'),
            ['drafts' => [123, 124, 125]]
        );

        $response->assertStatus(Response::HTTP_OK);
    }
}
