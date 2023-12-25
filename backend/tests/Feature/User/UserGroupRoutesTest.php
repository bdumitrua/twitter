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

    public function testGetMyGroupRouteBasic(): void
    {
        $userGroups = UserGroup::factory(3)->create([
            'user_id' => $this->authorizedUser->id
        ]);

        $createdResource = UserGroupResource::collection($userGroups)->resolve();
        $response = $this->get(route('getUserGroups'));

        $response->assertStatus(Response::HTTP_OK)->assertJson($createdResource);
    }

    public function testGetMyGroupRouteEmpty(): void
    {
        $userGroups = new Collection();
        $createdResource = UserGroupResource::collection($userGroups)->resolve();
        $response = $this->get(route('getUserGroups'));

        $response->assertStatus(Response::HTTP_OK)->assertJson($createdResource);
    }

    public function testCreateUserGroupRouteBasic(): void
    {
        $name = $this->faker->words(2, true);
        $response = $this->postJson(
            route('createUserGroup'),
            ['name' => $name]
        );

        $response->assertStatus(Response::HTTP_OK);
    }

    public function testCreateUserGroupRouteIncorrectRequest(): void
    {
        $desription = 1324;
        $response = $this->postJson(
            route('createUserGroup'),
            ['desription' => $desription]
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testShowUserGroupRouteBasic(): void
    {
        $userGroup = UserGroup::factory()->create([
            'user_id' => $this->authorizedUser->id
        ]);
        $createdResource = UserGroupResource::make($userGroup)->resolve();

        $response = $this->get(
            route('showUserGroup', ['userGroup' => $userGroup->id])
        );

        $response->assertStatus(Response::HTTP_OK)->assertJson($createdResource);
    }

    public function testShowUserGroupRouteIncorrectRequestTarget(): void
    {
        $userGroup = UserGroup::factory()->create([
            'user_id' => $this->authorizedUser->id
        ]);
        $response = $this->get(
            route('showUserGroup', ['userGroup' => $userGroup->id + 10])
        );

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testShowUserGroupRouteAnotherUserGroup(): void
    {
        $userGroup = UserGroup::factory()->create([
            'user_id' => $this->anotherUser->id
        ]);
        $response = $this->get(
            route('showUserGroup', ['userGroup' => $userGroup->id])
        );

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testUpdateUserGroupRouteBasic(): void
    {
        $userGroup = UserGroup::factory()->create([
            'user_id' => $this->authorizedUser->id
        ]);
        $response = $this->patchJson(
            route('updateUserGroup', ['userGroup' => $userGroup->id]),
            ['name' => $this->faker->words(3, true)]
        );

        $response->assertStatus(Response::HTTP_OK);
    }

    public function testUpdateUserGroupRouteIncorrectRequest(): void
    {
        $userGroup = UserGroup::factory()->create([
            'user_id' => $this->authorizedUser->id
        ]);
        $response = $this->patchJson(
            route('updateUserGroup', ['userGroup' => $userGroup->id]),
            ['name' => 1234]
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testUpdateUserGroupRouteAnotherUserGroup(): void
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

    public function testDeleteUserGroupRouteBasic(): void
    {
        $userGroup = UserGroup::factory()->create([
            'user_id' => $this->authorizedUser->id
        ]);
        $response = $this->delete(
            route('deleteUserGroup', ['userGroup' => $userGroup->id]),
        );

        $response->assertStatus(Response::HTTP_OK);
    }

    public function testDeleteUserGroupRouteIncorrectRequestTarget(): void
    {
        $userGroup = UserGroup::factory()->create([
            'user_id' => $this->authorizedUser->id
        ]);
        $response = $this->delete(
            route('deleteUserGroup', ['userGroup' => $userGroup->id + 10]),
        );

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testDeleteUserGroupRouteAnotherUserGroup(): void
    {
        $userGroup = UserGroup::factory()->create([
            'user_id' => $this->anotherUser->id
        ]);
        $response = $this->delete(
            route('deleteUserGroup', ['userGroup' => $userGroup->id]),
        );

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testAddUserToGroupRouteBasic(): void
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

        $response->assertStatus(Response::HTTP_OK);
    }

    public function testAddUserToGroupRouteIncorrectRouteGroupTarget(): void
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

    public function testAddUserToGroupRouteIncorrectRouteUserTarget(): void
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

    public function testAddUserToGroupRouteRepeat(): void
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

    public function testRemoveUserFromGroupRouteBasic(): void
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

        $response->assertStatus(Response::HTTP_OK);
    }

    public function testRemoveUserFromGroupRouteIncorrectRouteGroupTarget(): void
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

    public function testRemoveUserFromGroupRouteIncorrectRouteUserTarget(): void
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

    public function testRemoveUserFromGroupRouteRepeat(): void
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

    public function testRemoveUserFromGroupRouteWithoutAdding(): void
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
