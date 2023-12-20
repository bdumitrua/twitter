<?php

namespace Tests\Feature\Notification;

use App\Modules\Notification\Models\DeviceToken;
use App\Modules\Notification\Resources\DeviceTokenResource;
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

    protected $user;
    protected $anotherUser;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->anotherUser = User::factory()->create();
        $this->actingAs($this->user, 'api');
    }

    public function test_notifications_subscribe_route_basic(): void
    {
        UserSubscribtion::create([
            'user_id' => $this->anotherUser->id,
            'subscriber_id' => $this->user->id
        ]);

        $response = $this->postJson(
            route('userSubscribtionOnNotifications', ['user' => $this->anotherUser->id])
        );

        $response->assertStatus(200);
    }

    public function test_notifications_subscribe_route_incorrect_request_target(): void
    {
        UserSubscribtion::create([
            'user_id' => $this->anotherUser->id,
            'subscriber_id' => $this->user->id
        ]);

        $response = $this->postJson(
            route('userSubscribtionOnNotifications', ['user' => User::latest()->first()->id + 10])
        );

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function test_notifications_subscribe_route_without_base_subscribtion(): void
    {
        $response = $this->postJson(
            route('userSubscribtionOnNotifications', ['user' => $this->anotherUser->id])
        );

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_notifications_subscribe_route_repeated(): void
    {
        UserSubscribtion::create([
            'user_id' => $this->anotherUser->id,
            'subscriber_id' => $this->user->id
        ]);

        $this->postJson(
            route('userSubscribtionOnNotifications', ['user' => $this->anotherUser->id])
        );

        $response = $this->postJson(
            route('userSubscribtionOnNotifications', ['user' => $this->anotherUser->id])
        );

        $response->assertStatus(204);
    }

    public function test_notifications_unsubscribe_route_basic(): void
    {
        UserSubscribtion::create([
            'user_id' => $this->anotherUser->id,
            'subscriber_id' => $this->user->id
        ]);

        $this->postJson(
            route('userSubscribtionOnNotifications', ['user' => $this->anotherUser->id])
        );

        $response = $this->deleteJson(
            route('userUnsubscribtionFromNotifications', ['user' => $this->anotherUser->id])
        );

        $response->assertStatus(200);
    }


    public function test_notifications_unsubscribe_route_incorrect_request_target(): void
    {
        UserSubscribtion::create([
            'user_id' => $this->anotherUser->id,
            'subscriber_id' => $this->user->id
        ]);

        $this->postJson(
            route('userSubscribtionOnNotifications', ['user' => $this->anotherUser->id])
        );

        $response = $this->deleteJson(
            route('userUnsubscribtionFromNotifications', ['user' => User::latest()->first()->id + 10])
        );

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function test_notifications_unsubscribe_route_without_base_subscribtion(): void
    {
        $response = $this->deleteJson(
            route('userUnsubscribtionFromNotifications', ['user' => $this->anotherUser->id])
        );

        $response->assertStatus(204);
    }

    public function test_notifications_unsubscribe_route_without_notification_subscribtion(): void
    {
        UserSubscribtion::create([
            'user_id' => $this->anotherUser->id,
            'subscriber_id' => $this->user->id
        ]);

        $response = $this->deleteJson(
            route('userUnsubscribtionFromNotifications', ['user' => $this->anotherUser->id])
        );

        $response->assertStatus(204);
    }

    public function test_notifications_unsubscribe_route_repeated(): void
    {
        UserSubscribtion::create([
            'user_id' => $this->anotherUser->id,
            'subscriber_id' => $this->user->id
        ]);

        $this->postJson(
            route('userSubscribtionOnNotifications', ['user' => $this->anotherUser->id])
        );

        $this->deleteJson(
            route('userUnsubscribtionFromNotifications', ['user' => $this->anotherUser->id])
        );

        $response = $this->deleteJson(
            route('userUnsubscribtionFromNotifications', ['user' => $this->anotherUser->id])
        );

        $response->assertStatus(204);
    }
}
