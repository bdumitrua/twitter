<?php

namespace Tests\Feature\Notification;

use App\Modules\Notification\Models\DeviceToken;
use App\Modules\Notification\Resources\DeviceTokenResource;
use App\Modules\User\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;
use Illuminate\Support\Str;

class DeviceTokenRoutesTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $authorizedUser;

    public function setUp(): void
    {
        parent::setUp();

        $this->authorizedUser = User::factory()->create();
        $this->actingAs($this->authorizedUser, 'api');
    }

    protected function getTokenObject(): array
    {
        return ['token' => Str::random(16)];
    }

    protected function getInvalidTokenObject(): array
    {
        return ['token' => 123];
    }

    public function test_get_authorized_user_tokens_route_basic(): void
    {
        $deviceTokens = DeviceToken::factory(2)->create([
            'user_id' => $this->authorizedUser->id,
        ]);

        $response = $this->get(route('getAuthorizedUserDeviceTokens'));
        $createdResources = DeviceTokenResource::collection(collect($deviceTokens))->resolve();

        $response->assertStatus(200)->assertJson($createdResources);
    }

    public function test_get_authorized_user_tokens_route_empty(): void
    {
        $deviceTokens = new Collection();
        $response = $this->get(route('getAuthorizedUserDeviceTokens'));
        $createdResources = DeviceTokenResource::collection(collect($deviceTokens))->resolve();

        $response->assertStatus(200)->assertJson($createdResources);
    }

    public function test_create_device_token_route_basic(): void
    {
        $response = $this->postJson(
            route('createNewDeviceToken'),
            $this->getTokenObject()
        );

        $response->assertStatus(200);
    }

    public function test_create_device_token_route_incorrect_request(): void
    {
        $response = $this->postJson(
            route('createNewDeviceToken'),
            $this->getInvalidTokenObject()
        );

        $response->assertStatus(422);
    }

    public function test_update_device_token_route_basic(): void
    {
        $deviceToken = DeviceToken::factory()->create();
        $response = $this->patch(
            route('updateDeviceToken', ['deviceToken' => $deviceToken->id]),
            $this->getTokenObject()
        );

        $response->assertStatus(200);
    }

    public function test_update_device_token_route_incorrect_request(): void
    {
        $deviceToken = DeviceToken::factory()->create();
        $response = $this->patch(
            route('updateDeviceToken', ['deviceToken' => $deviceToken->id]),
            $this->getInvalidTokenObject()
        );

        $response->assertStatus(422);
    }

    public function test_update_device_token_route_incorrect_request_target(): void
    {
        DeviceToken::factory()->create();
        $response = $this->patch(
            route('updateDeviceToken', ['deviceToken' => DeviceToken::latest()->first()->id + 1]),
            $this->getTokenObject()
        );

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function test_delete_device_token_route_basic(): void
    {
        $deviceToken = DeviceToken::factory()->create();
        $response = $this->delete(
            route('deleteDeviceToken', ['deviceToken' => $deviceToken->id])
        );

        $response->assertStatus(200);
    }

    public function test_delete_device_token_route_incorrect_request_target(): void
    {
        DeviceToken::factory()->create();
        $response = $this->delete(
            route('deleteDeviceToken', ['deviceToken' => DeviceToken::latest()->first()->id + 1]),
        );

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }
}
