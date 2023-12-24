<?php

namespace Tests\Feature\Notification;

use App\Modules\User\Models\User;
use App\Modules\User\Models\UserSubscribtion;
use App\Modules\User\Repositories\UserRepository;
use App\Modules\User\Resources\ShortUserResource;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;
use Illuminate\Support\Str;

class UserSubscribtionRoutesTest extends TestCase
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

    public function testGetAuthorizedUserBaseSubscribtionsRouteBasic(): void
    {
        User::factory(10)->create();
        $userSubscribtions = UserSubscribtion::factory(5)->create([
            'subscriber_id' => $this->authorizedUser->id
        ]);

        $userRepository = app()->make(UserRepository::class);
        $usersData = $userRepository->getUsersdata($userSubscribtions->pluck('user_id')->toArray());
        $createdResources = ShortUserResource::collection($usersData)->resolve();

        $response = $this->get(
            route('getUserSubscribtions', ['user' => $this->authorizedUser->id])
        );

        $response->assertStatus(Response::HTTP_OK)->assertJson($createdResources);
    }

    public function testGetAuthorizedUserBaseSubscribtionsRouteEmpty(): void
    {
        $usersData = new Collection();
        $createdResources = ShortUserResource::collection($usersData)->resolve();

        $response = $this->get(
            route('getUserSubscribtions', ['user' => $this->authorizedUser->id])
        );

        $response->assertStatus(Response::HTTP_OK)->assertJson($createdResources);
    }

    public function testGetAuthorizedUserBaseSubscribersRouteBasic(): void
    {
        User::factory(10)->create();
        $userSubscribtions = UserSubscribtion::factory(5)->create([
            'user_id' => $this->authorizedUser->id
        ]);

        $userRepository = app()->make(UserRepository::class);
        $usersData = $userRepository->getUsersdata($userSubscribtions->pluck('subscriber_id')->toArray());
        $createdResources = ShortUserResource::collection($usersData)->resolve();

        $response = $this->get(
            route('getUserSubscribers', ['user' => $this->authorizedUser->id])
        );

        $response->assertStatus(Response::HTTP_OK)->assertJson($createdResources);
    }

    public function testGetAuthorizedUserBaseSubscribersRouteEmpty(): void
    {
        $usersData = new Collection();
        $createdResources = ShortUserResource::collection($usersData)->resolve();

        $response = $this->get(
            route('getUserSubscribers', ['user' => $this->authorizedUser->id])
        );

        $response->assertStatus(Response::HTTP_OK)->assertJson($createdResources);
    }

    public function testBaseSubscribeRouteBasic(): void
    {
        $response = $this->postJson(
            route('subscribeOnUser', ['user' => $this->anotherUser->id])
        );

        $response->assertStatus(Response::HTTP_OK);
    }

    public function testBaseSubscribeRouteIncorrectRequestTarget(): void
    {
        $response = $this->postJson(
            route('subscribeOnUser', ['user' => User::latest()->first()->id + 10])
        );

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testBaseSubscribeRouteRepeated(): void
    {
        $this->postJson(
            route('subscribeOnUser', ['user' => $this->anotherUser->id])
        );

        $response = $this->postJson(
            route('subscribeOnUser', ['user' => $this->anotherUser->id])
        );

        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }

    public function testBaseUnsubscribeRouteBasic(): void
    {
        $this->postJson(
            route('subscribeOnUser', ['user' => $this->anotherUser->id])
        );

        $response = $this->deleteJson(
            route('unsubscribeFromUser', ['user' => $this->anotherUser->id])
        );

        $response->assertStatus(Response::HTTP_OK);
    }


    public function testBaseUnsubscribeRouteIncorrectRequestTarget(): void
    {
        $this->postJson(
            route('subscribeOnUser', ['user' => $this->anotherUser->id])
        );

        $response = $this->deleteJson(
            route('unsubscribeFromUser', ['user' => User::latest()->first()->id + 10])
        );

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testBaseUnsubscribeRouteWithoutBaseSubscribtion(): void
    {
        $response = $this->deleteJson(
            route('unsubscribeFromUser', ['user' => $this->anotherUser->id])
        );

        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }

    public function testBaseUnsubscribeRouteRepeated(): void
    {
        $this->postJson(
            route('subscribeOnUser', ['user' => $this->anotherUser->id])
        );
        $this->deleteJson(
            route('unsubscribeFromUser', ['user' => $this->anotherUser->id])
        );
        $response = $this->deleteJson(
            route('unsubscribeFromUser', ['user' => $this->anotherUser->id])
        );

        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }
}
