<?php

namespace Tests\Feature\Notification;

use App\Modules\User\Models\User;
use App\Modules\User\Models\UserGroup;
use App\Modules\User\Resources\UserGroupResource;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;
use Illuminate\Support\Str;

class UserGroupRoutesTest extends TestCase
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

    public function test_get_my_group_route_basic(): void
    {
        $userGroups = UserGroup::factory(3)->create([
            'user_id' => $this->authorizedUser->id
        ]);

        $createdResource = UserGroupResource::collection($userGroups)->resolve();
        $response = $this->get(route('getUserGroups'));

        $response->assertStatus(200)->assertJson($createdResource);
    }

    public function test_get_my_group_route_empty(): void
    {
        $userGroups = new Collection();
        $createdResource = UserGroupResource::collection($userGroups)->resolve();
        $response = $this->get(route('getUserGroups'));

        $response->assertStatus(200)->assertJson($createdResource);
    }

    public function test_create_user_group_route_basic(): void
    {
        $name = $this->faker->words(2, true);
        $response = $this->postJson(
            route('createUserGroup'),
            ['name' => $name]
        );

        $response->assertStatus(200);
    }

    public function test_create_user_group_route_incorrect_request(): void
    {
        $desription = 1324;
        $response = $this->postJson(
            route('createUserGroup'),
            ['desription' => $desription]
        );

        $response->assertStatus(422);
    }

    public function test_show_user_group_route_basic(): void
    {
        $userGroup = UserGroup::factory()->create([
            'user_id' => $this->authorizedUser->id
        ]);
        $createdResource = UserGroupResource::make($userGroup)->resolve();

        $response = $this->get(
            route('showUserGroup', ['userGroup' => $userGroup->id])
        );

        $response->assertStatus(200)->assertJson($createdResource);
    }

    public function test_show_user_group_route_incorrect_request_target(): void
    {
        $userGroup = UserGroup::factory()->create([
            'user_id' => $this->authorizedUser->id
        ]);
        $response = $this->get(
            route('showUserGroup', ['userGroup' => $userGroup->id + 10])
        );

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function test_show_user_group_route_another_user_group(): void
    {
        $userGroup = UserGroup::factory()->create([
            'user_id' => $this->anotherUser->id
        ]);
        $response = $this->get(
            route('showUserGroup', ['userGroup' => $userGroup->id])
        );

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_update_user_group_route_basic(): void
    {
        $userGroup = UserGroup::factory()->create([
            'user_id' => $this->authorizedUser->id
        ]);
        $response = $this->patchJson(
            route('updateUserGroup', ['userGroup' => $userGroup->id]),
            ['name' => $this->faker->words(3, true)]
        );

        $response->assertStatus(200);
    }

    public function test_update_user_group_route_incorrect_request(): void
    {
        $userGroup = UserGroup::factory()->create([
            'user_id' => $this->authorizedUser->id
        ]);
        $response = $this->patchJson(
            route('updateUserGroup', ['userGroup' => $userGroup->id]),
            ['name' => 1234]
        );

        $response->assertStatus(422);
    }

    public function test_update_user_group_route_another_user_group(): void
    {
        $userGroup = UserGroup::factory()->create([
            'user_id' => $this->anotherUser->id
        ]);
        $response = $this->patchJson(
            route('updateUserGroup', ['userGroup' => $userGroup->id]),
            ['name' => $this->faker->words(3, true)]
        );

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_delete_user_group_route_basic(): void
    {
        $userGroup = UserGroup::factory()->create([
            'user_id' => $this->authorizedUser->id
        ]);
        $response = $this->delete(
            route('deleteUserGroup', ['userGroup' => $userGroup->id]),
        );

        $response->assertStatus(200);
    }

    public function test_delete_user_group_route_incorrect_request_target(): void
    {
        $userGroup = UserGroup::factory()->create([
            'user_id' => $this->authorizedUser->id
        ]);
        $response = $this->delete(
            route('deleteUserGroup', ['userGroup' => $userGroup->id + 10]),
        );

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function test_delete_user_group_route_another_user_group(): void
    {
        $userGroup = UserGroup::factory()->create([
            'user_id' => $this->anotherUser->id
        ]);
        $response = $this->delete(
            route('deleteUserGroup', ['userGroup' => $userGroup->id]),
        );

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_add_user_to_group_route_basic(): void
    {
        $userGroup = UserGroup::factory()->create([
            'user_id' => $this->authorizedUser->id
        ]);

        $response = $this->postJson(
            route('addUserToUserGroup', [
                'userGroup' => $userGroup->id,
                'user' => $this->anotherUser->id
            ]),
        );

        $response->assertStatus(200);
    }

    public function test_add_user_to_group_route_incorrect_route_group_target(): void
    {
        $userGroup = UserGroup::factory()->create([
            'user_id' => $this->authorizedUser->id
        ]);

        $response = $this->postJson(
            route('addUserToUserGroup', [
                'userGroup' => $userGroup->id + 10,
                'user' => $this->anotherUser->id
            ]),
        );

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function test_add_user_to_group_route_incorrect_route_user_target(): void
    {
        $userGroup = UserGroup::factory()->create([
            'user_id' => $this->authorizedUser->id
        ]);

        $response = $this->postJson(
            route('addUserToUserGroup', [
                'userGroup' => $userGroup->id,
                'user' => $this->anotherUser->id + 10
            ]),
        );

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function test_add_user_to_group_route_repeat(): void
    {
        $userGroup = UserGroup::factory()->create([
            'user_id' => $this->authorizedUser->id
        ]);

        $this->postJson(
            route('addUserToUserGroup', [
                'userGroup' => $userGroup->id,
                'user' => $this->anotherUser->id
            ]),
        );

        $response = $this->postJson(
            route('addUserToUserGroup', [
                'userGroup' => $userGroup->id,
                'user' => $this->anotherUser->id
            ]),
        );

        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }

    public function test_remove_user_from_group_route_basic(): void
    {
        $userGroup = UserGroup::factory()->create([
            'user_id' => $this->authorizedUser->id
        ]);

        $this->postJson(
            route('addUserToUserGroup', [
                'userGroup' => $userGroup->id,
                'user' => $this->anotherUser->id
            ]),
        );
        $response = $this->deleteJson(
            route('removeUserFromUserGroup', [
                'userGroup' => $userGroup->id,
                'user' => $this->anotherUser->id
            ]),
        );

        $response->assertStatus(200);
    }

    public function test_remove_user_from_group_route_incorrect_route_group_target(): void
    {
        $userGroup = UserGroup::factory()->create([
            'user_id' => $this->authorizedUser->id
        ]);

        $this->postJson(
            route('addUserToUserGroup', [
                'userGroup' => $userGroup->id,
                'user' => $this->anotherUser->id
            ]),
        );
        $response = $this->deleteJson(
            route('removeUserFromUserGroup', [
                'userGroup' => $userGroup->id + 10,
                'user' => $this->anotherUser->id
            ]),
        );

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function test_remove_user_from_group_route_incorrect_route_user_target(): void
    {
        $userGroup = UserGroup::factory()->create([
            'user_id' => $this->authorizedUser->id
        ]);

        $this->postJson(
            route('addUserToUserGroup', [
                'userGroup' => $userGroup->id,
                'user' => $this->anotherUser->id
            ]),
        );
        $response = $this->deleteJson(
            route('removeUserFromUserGroup', [
                'userGroup' => $userGroup->id,
                'user' => $this->anotherUser->id + 10
            ]),
        );

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function test_remove_user_from_group_route_repeat(): void
    {
        $userGroup = UserGroup::factory()->create([
            'user_id' => $this->authorizedUser->id
        ]);

        $this->postJson(
            route('addUserToUserGroup', [
                'userGroup' => $userGroup->id,
                'user' => $this->anotherUser->id
            ]),
        );

        $this->deleteJson(
            route('removeUserFromUserGroup', [
                'userGroup' => $userGroup->id,
                'user' => $this->anotherUser->id
            ]),
        );

        $response = $this->deleteJson(
            route('removeUserFromUserGroup', [
                'userGroup' => $userGroup->id,
                'user' => $this->anotherUser->id
            ]),
        );

        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }

    public function test_remove_user_from_group_route_without_adding(): void
    {
        $userGroup = UserGroup::factory()->create([
            'user_id' => $this->authorizedUser->id
        ]);

        $response = $this->deleteJson(
            route('removeUserFromUserGroup', [
                'userGroup' => $userGroup->id,
                'user' => $this->anotherUser->id
            ]),
        );

        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }
}
