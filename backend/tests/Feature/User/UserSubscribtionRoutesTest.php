<?php

namespace Tests\Feature\Notification;

use App\Modules\Notification\Models\DeviceToken;
use App\Modules\Notification\Resources\DeviceTokenResource;
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

    public function test_get_authorized_user_base_subscribtions_route_basic(): void
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

        $response->assertStatus(200)->assertJson($createdResources);
    }

    public function test_get_authorized_user_base_subscribtions_route_empty(): void
    {
        $usersData = new Collection();
        $createdResources = ShortUserResource::collection($usersData)->resolve();

        $response = $this->get(
            route('getUserSubscribtions', ['user' => $this->authorizedUser->id])
        );

        $response->assertStatus(200)->assertJson($createdResources);
    }

    public function test_get_authorized_user_base_subscribers_route_basic(): void
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

        $response->assertStatus(200)->assertJson($createdResources);
    }

    public function test_get_authorized_user_base_subscribers_route_empty(): void
    {
        $usersData = new Collection();
        $createdResources = ShortUserResource::collection($usersData)->resolve();

        $response = $this->get(
            route('getUserSubscribers', ['user' => $this->authorizedUser->id])
        );

        $response->assertStatus(200)->assertJson($createdResources);
    }

    public function test_base_subscribe_route_basic(): void
    {
        $response = $this->postJson(
            route('subscribeOnUser', ['user' => $this->anotherUser->id])
        );

        $response->assertStatus(200);
    }

    public function test_base_subscribe_route_incorrect_request_target(): void
    {
        $response = $this->postJson(
            route('subscribeOnUser', ['user' => User::latest()->first()->id + 10])
        );

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function test_base_subscribe_route_repeated(): void
    {
        $this->postJson(
            route('subscribeOnUser', ['user' => $this->anotherUser->id])
        );

        $response = $this->postJson(
            route('subscribeOnUser', ['user' => $this->anotherUser->id])
        );

        $response->assertStatus(204);
    }

    public function test_base_unsubscribe_route_basic(): void
    {
        $this->postJson(
            route('subscribeOnUser', ['user' => $this->anotherUser->id])
        );

        $response = $this->deleteJson(
            route('unsubscribeFromUser', ['user' => $this->anotherUser->id])
        );

        $response->assertStatus(200);
    }


    public function test_base_unsubscribe_route_incorrect_request_target(): void
    {
        $this->postJson(
            route('subscribeOnUser', ['user' => $this->anotherUser->id])
        );

        $response = $this->deleteJson(
            route('unsubscribeFromUser', ['user' => User::latest()->first()->id + 10])
        );

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function test_base_unsubscribe_route_without_base_subscribtion(): void
    {
        $response = $this->deleteJson(
            route('unsubscribeFromUser', ['user' => $this->anotherUser->id])
        );

        $response->assertStatus(204);
    }

    public function test_base_unsubscribe_route_repeated(): void
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

        $response->assertStatus(204);
    }
}
