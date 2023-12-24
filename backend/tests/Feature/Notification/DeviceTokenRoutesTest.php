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

    public function testGetAuthorizedUserTokensRouteBasic(): void
    {
        $deviceTokens = DeviceToken::factory(2)->create([
            'user_id' => $this->authorizedUser->id,
        ]);

        $response = $this->get(route('getAuthorizedUserDeviceTokens'));
        $createdResources = DeviceTokenResource::collection(collect($deviceTokens))->resolve();

        $response->assertStatus(Response::HTTP_OK)->assertJson($createdResources);
    }

    public function testGetAuthorizedUserTokensRouteEmpty(): void
    {
        $deviceTokens = new Collection();
        $response = $this->get(route('getAuthorizedUserDeviceTokens'));
        $createdResources = DeviceTokenResource::collection(collect($deviceTokens))->resolve();

        $response->assertStatus(Response::HTTP_OK)->assertJson($createdResources);
    }

    public function testCreateDeviceTokenRouteBasic(): void
    {
        $response = $this->postJson(
            route('createNewDeviceToken'),
            $this->getTokenObject()
        );

        $response->assertStatus(Response::HTTP_OK);
    }

    public function testCreateDeviceTokenRouteIncorrectRequest(): void
    {
        $response = $this->postJson(
            route('createNewDeviceToken'),
            $this->getInvalidTokenObject()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testUpdateDeviceTokenRouteBasic(): void
    {
        $deviceToken = DeviceToken::factory()->create();
        $response = $this->patch(
            route('updateDeviceToken', ['deviceToken' => $deviceToken->id]),
            $this->getTokenObject()
        );

        $response->assertStatus(Response::HTTP_OK);
    }

    public function testUpdateDeviceTokenRouteIncorrectRequest(): void
    {
        $deviceToken = DeviceToken::factory()->create();
        $response = $this->patch(
            route('updateDeviceToken', ['deviceToken' => $deviceToken->id]),
            $this->getInvalidTokenObject()
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testUpdateDeviceTokenRouteIncorrectRequestTarget(): void
    {
        DeviceToken::factory()->create();
        $response = $this->patch(
            route('updateDeviceToken', ['deviceToken' => DeviceToken::latest()->first()->id + 1]),
            $this->getTokenObject()
        );

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testDeleteDeviceTokenRouteBasic(): void
    {
        $deviceToken = DeviceToken::factory()->create();
        $response = $this->delete(
            route('deleteDeviceToken', ['deviceToken' => $deviceToken->id])
        );

        $response->assertStatus(Response::HTTP_OK);
    }

    public function testDeleteDeviceTokenRouteIncorrectRequestTarget(): void
    {
        DeviceToken::factory()->create();
        $response = $this->delete(
            route('deleteDeviceToken', ['deviceToken' => DeviceToken::latest()->first()->id + 1]),
        );

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }
}
