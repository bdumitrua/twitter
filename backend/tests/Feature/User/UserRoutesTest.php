<?php

namespace Tests\Feature\User;

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

    public function testGetAuthorizedUserDataRouteBasic(): void
    {
        $this->actingAs($this->authorizedUser, 'api');
        $response = $this->get(route('getAuthorizedUserData'));

        $response->assertStatus(Response::HTTP_OK);
    }

    public function testGetAuthorizedUserDataRouteWithoutAuthorization(): void
    {
        $response = $this->get(route('getAuthorizedUserData'));

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testGetUserDataByIdRouteBasic(): void
    {
        $response = $this->get(
            route('showUser', ['user' => $this->authorizedUser->id])
        );

        $response->assertStatus(Response::HTTP_OK);
    }

    public function testGetUserDataByIdRouteIncorrectRequestTarget(): void
    {
        $response = $this->get(
            route('showUser', ['user' => $this->authorizedUser->id + 10])
        );

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testUpdateUserDataRouteBasic(): void
    {
        $this->actingAs($this->authorizedUser, 'api');
        $response = $this->patchJson(
            route('updateUserData'),
            [
                'name' => $this->faker->name(),
                'about' => $this->faker->words(10, true),
            ]
        );

        $response->assertStatus(Response::HTTP_OK);
    }

    public function testUpdateUserDataRouteIncorrectRequest(): void
    {
        $this->actingAs($this->authorizedUser, 'api');
        $response = $this->patchJson(
            route('updateUserData'),
            [
                'name' => 1234,
                'about' => 1234,
            ]
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testUpdateUserDataRouteEmptyRequest(): void
    {
        $this->actingAs($this->authorizedUser, 'api');
        $response = $this->patchJson(
            route('updateUserData')
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
