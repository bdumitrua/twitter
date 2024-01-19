<?php

namespace Tests\Feature\Search;

use App\Modules\Search\Models\RecentSearch;
use App\Modules\Search\Resources\RecentSearchesResource;
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

    public function testGetAuthorizedUserRecentSearchesBasic(): void
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

        $response->assertStatus(Response::HTTP_OK)->assertJson($createdResources);
    }

    public function testGetAuthorizedUserRecentSearchesEmpty(): void
    {
        $recentSearches = [];

        $response = $this->get(route('getAuthorizedUserRecentSearches'));
        $createdResources = RecentSearchesResource::make($recentSearches)->resolve();

        $response->assertStatus(Response::HTTP_OK)->assertJson($createdResources);
    }

    public function testCreateNewRecentSearchBasic(): void
    {
        $newRecentSearchData = [
            'text' => $this->faker->word(5),
        ];

        $response = $this->postJson(
            route('createUserRecentSearch'),
            $newRecentSearchData
        );

        $response->assertStatus(Response::HTTP_OK);
    }

    public function testCreateNewRecentSearchWithLinkedUser(): void
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

        $response->assertStatus(Response::HTTP_OK);
    }

    public function testCreateNewRecentSearchIncorrectRequest(): void
    {
        $newRecentSearchData = [
            'text' => 1234,
        ];

        $response = $this->postJson(
            route('createUserRecentSearch'),
            $newRecentSearchData
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testCreateNewRecentSearchInvalidRequestWithLinkedUser(): void
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

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testClearAuthorizedUserRecentSearchesBasic(): void
    {
        RecentSearch::factory(3)->create([
            'user_id' => $this->authorizedUser->id
        ]);

        $response = $this->deleteJson(route('clearAuthorizedUserRecentSearches'));

        $response->assertStatus(Response::HTTP_OK);
    }
}
