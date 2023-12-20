<?php

namespace Tests\Feature\Notifications;

use App\Modules\Notification\Models\DeviceToken;
use App\Modules\Notification\Resources\DeviceTokenResource;
use App\Modules\Search\Models\RecentSearch;
use App\Modules\Search\Resources\RecentSearchesResource;
use App\Modules\Search\Resources\RecentSearchResource;
use App\Modules\User\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;
use Illuminate\Support\Str;

class SearchRoutesTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $authorizedUser;

    public function setUp(): void
    {
        parent::setUp();

        $this->authorizedUser = User::factory()->create();
        $this->actingAs($this->authorizedUser, 'api');
    }

    protected function getSearchObject(): array
    {
        return ['search' => Str::random(16)];
    }

    protected function getInvalidSearchObject(): array
    {
        return ['search' => 123];
    }

    public function test_get_authorized_user_recent_searches_basic(): void
    {
        $searchedUser = User::factory()->create();
        RecentSearch::factory()->create([
            'user_id' => $this->authorizedUser->id,
            'linked_user_id' => $searchedUser->id
        ]);

        $recentSearches = RecentSearch::with('linkedUser')
            ->where('user_id', $this->authorizedUser->id)
            ->latest('updated_at')
            ->get();

        $response = $this->get(route('getAuthorizedUserRecentSearches'));
        $createdResources = RecentSearchesResource::make($recentSearches)->resolve();

        $response->assertStatus(200)->assertJson($createdResources);
    }

    public function test_get_authorized_user_recent_searches_empty(): void
    {
        $recentSearches = [];

        $response = $this->get(route('getAuthorizedUserRecentSearches'));
        $createdResources = RecentSearchesResource::make($recentSearches)->resolve();

        $response->assertStatus(200)->assertJson($createdResources);
    }

    public function test_create_new_recent_search_basic(): void
    {
        $newRecentSearchData = [
            'text' => $this->faker->word(5),
        ];

        $response = $this->postJson(
            route('createUserRecentSearch'),
            $newRecentSearchData
        );

        $response->assertStatus(200);
    }

    public function test_create_new_recent_search_with_linked_user(): void
    {
        $user = User::factory()->create();
        $newRecentSearchData = [
            'text' => $this->faker->word(5),
            'linkedUserId' => $user->id
        ];

        $response = $this->postJson(
            route('createUserRecentSearch'),
            $newRecentSearchData
        );

        $response->assertStatus(200);
    }

    public function test_create_new_recent_search_incorrect_request(): void
    {
        $newRecentSearchData = [
            'text' => 1234,
        ];

        $response = $this->postJson(
            route('createUserRecentSearch'),
            $newRecentSearchData
        );

        $response->assertStatus(422);
    }

    public function test_create_new_recent_search_invalid_request_with_linked_user(): void
    {
        User::factory()->create();
        $newRecentSearchData = [
            'text' => 1234,
            'linkedUserId' => User::latest()->first()->id + 1
        ];

        $response = $this->postJson(
            route('createUserRecentSearch'),
            $newRecentSearchData
        );

        $response->assertStatus(422);
    }

    public function test_clear_authorized_user_recent_searches_basic(): void
    {
        RecentSearch::factory(3)->create([
            'user_id' => $this->authorizedUser->id
        ]);

        $response = $this->deleteJson(route('clearAuthorizedUserRecentSearches'));

        $response->assertStatus(200);
    }
}
