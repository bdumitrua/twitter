<?php

namespace Tests\Feature\Notification;

use App\Modules\User\Models\User;
use App\Modules\User\Resources\UserResource;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;
use Illuminate\Support\Str;

class UserRoutesTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $authorizedUser;

    public function setUp(): void
    {
        parent::setUp();

        $this->authorizedUser = User::factory()->create();
    }

    public function test_get_authorized_user_data_route_basic(): void
    {
        $this->actingAs($this->authorizedUser, 'api');
        $response = $this->get(route('getAuthorizedUserData'));

        $response->assertStatus(200);
    }

    public function test_get_authorized_user_data_route_without_authorization(): void
    {
        $response = $this->get(route('getAuthorizedUserData'));

        $response->assertStatus(401);
    }

    public function test_get_user_data_by_id_route_basic(): void
    {
        $response = $this->get(
            route('showUser', ['user' => $this->authorizedUser->id])
        );

        $createdResource = UserResource::make($this->authorizedUser)->resolve();

        $response->assertStatus(200)->assertJson($createdResource);
    }

    public function test_get_user_data_by_id_route_incorrect_request_target(): void
    {
        $response = $this->get(
            route('showUser', ['user' => $this->authorizedUser->id + 10])
        );

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function test_update_user_data_route_basic(): void
    {
        $this->actingAs($this->authorizedUser, 'api');
        $response = $this->patchJson(
            route('updateUserData'),
            [
                'name' => $this->faker->name(),
                'about' => $this->faker->words(10, true),
            ]
        );

        $response->assertStatus(200);
    }

    public function test_update_user_data_route_incorrect_request(): void
    {
        $this->actingAs($this->authorizedUser, 'api');
        $response = $this->patchJson(
            route('updateUserData'),
            [
                'name' => 1234,
                'about' => 1234,
            ]
        );

        $response->assertStatus(422);
    }

    public function test_update_user_data_route_empty_request(): void
    {
        $this->actingAs($this->authorizedUser, 'api');
        $response = $this->patchJson(
            route('updateUserData')
        );

        $response->assertStatus(422);
    }
}
