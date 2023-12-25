<?php

namespace Tests\Feature\Notification;

use App\Modules\User\Models\User;
use App\Modules\User\Models\UserSubscribtion;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;
use Illuminate\Support\Str;

class NotificationsSubscribtionRoutesTest extends TestCase
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

    public function testNotificationsSubscribeRouteBasic(): void
    {
        UserSubscribtion::create([
            'user_id' => $this->anotherUser->id,
            'subscriber_id' => $this->authorizedUser->id
        ]);

        $response = $this->postJson(
            route('subscribeOnUserNotification', ['user' => $this->anotherUser->id])
        );

        $response->assertStatus(Response::HTTP_OK);
    }

    public function testNotificationsSubscribeRouteIncorrectRequestTarget(): void
    {
        UserSubscribtion::create([
            'user_id' => $this->anotherUser->id,
            'subscriber_id' => $this->authorizedUser->id
        ]);

        $response = $this->postJson(
            route('subscribeOnUserNotification', ['user' => User::latest()->first()->id + 10])
        );

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testNotificationsSubscribeRouteOnYourself(): void
    {
        $response = $this->postJson(
            route('subscribeOnUserNotification', ['user' => $this->authorizedUser->id])
        );

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testNotificationsSubscribeRouteWithoutBaseSubscribtion(): void
    {
        $response = $this->postJson(
            route('subscribeOnUserNotification', ['user' => $this->anotherUser->id])
        );

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testNotificationsSubscribeRouteRepeated(): void
    {
        UserSubscribtion::create([
            'user_id' => $this->anotherUser->id,
            'subscriber_id' => $this->authorizedUser->id
        ]);

        $this->postJson(
            route('subscribeOnUserNotification', ['user' => $this->anotherUser->id])
        );

        $response = $this->postJson(
            route('subscribeOnUserNotification', ['user' => $this->anotherUser->id])
        );

        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }

    public function testNotificationsUnsubscribeRouteBasic(): void
    {
        UserSubscribtion::create([
            'user_id' => $this->anotherUser->id,
            'subscriber_id' => $this->authorizedUser->id
        ]);

        $this->postJson(
            route('subscribeOnUserNotification', ['user' => $this->anotherUser->id])
        );

        $response = $this->deleteJson(
            route('unsubscribeFromUserNotification', ['user' => $this->anotherUser->id])
        );

        $response->assertStatus(Response::HTTP_OK);
    }


    public function testNotificationsUnsubscribeRouteIncorrectRequestTarget(): void
    {
        UserSubscribtion::create([
            'user_id' => $this->anotherUser->id,
            'subscriber_id' => $this->authorizedUser->id
        ]);

        $this->postJson(
            route('subscribeOnUserNotification', ['user' => $this->anotherUser->id])
        );

        $response = $this->deleteJson(
            route('unsubscribeFromUserNotification', ['user' => User::latest()->first()->id + 10])
        );

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }


    public function testNotificationsUnsubscribeRouteOnYourself(): void
    {
        $response = $this->deleteJson(
            route('unsubscribeFromUserNotification', ['user' => $this->authorizedUser->id])
        );

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testNotificationsUnsubscribeRouteWithoutBaseSubscribtion(): void
    {
        $response = $this->deleteJson(
            route('unsubscribeFromUserNotification', ['user' => $this->anotherUser->id])
        );

        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }

    public function testNotificationsUnsubscribeRouteWithoutNotificationSubscribtion(): void
    {
        UserSubscribtion::create([
            'user_id' => $this->anotherUser->id,
            'subscriber_id' => $this->authorizedUser->id
        ]);

        $response = $this->deleteJson(
            route('unsubscribeFromUserNotification', ['user' => $this->anotherUser->id])
        );

        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }

    public function testNotificationsUnsubscribeRouteRepeated(): void
    {
        UserSubscribtion::create([
            'user_id' => $this->anotherUser->id,
            'subscriber_id' => $this->authorizedUser->id
        ]);

        $this->postJson(
            route('subscribeOnUserNotification', ['user' => $this->anotherUser->id])
        );

        $this->deleteJson(
            route('unsubscribeFromUserNotification', ['user' => $this->anotherUser->id])
        );

        $response = $this->deleteJson(
            route('unsubscribeFromUserNotification', ['user' => $this->anotherUser->id])
        );

        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }
}
