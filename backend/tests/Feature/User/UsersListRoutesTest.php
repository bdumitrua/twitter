<?php

namespace Tests\Feature\Notification;

use App\Modules\User\Models\User;
use App\Modules\User\Models\UserGroup;
use App\Modules\User\Models\UsersList;
use App\Modules\User\Repositories\UsersListRepository;
use App\Modules\User\Resources\UserGroupResource;
use App\Modules\User\Resources\UsersListResource;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;
use Illuminate\Support\Str;

class UsersListRoutesTest extends TestCase
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

    public function test_get_my_lists_route_basic(): void
    {
        $usersLists = UsersList::factory(3)->create([
            'user_id' => $this->authorizedUser->id
        ]);

        $createdResource = UsersListResource::collection($usersLists)->resolve();
        $response = $this->get(route('getAuthorizedUserUsersLists'));

        $response->assertStatus(200)->assertJson($createdResource);
    }

    public function test_get_my_lists_route_empty(): void
    {
        $usersLists = new Collection();
        $createdResource = UserGroupResource::collection($usersLists)->resolve();
        $response = $this->get(route('getAuthorizedUserUsersLists'));

        $response->assertStatus(200)->assertJson($createdResource);
    }

    public function test_create_users_list_route_basic(): void
    {
        $name = $this->faker->words(2, true);
        $response = $this->postJson(
            route('createUsersList'),
            ['name' => $name],
        );

        $response->assertStatus(200);
    }

    public function test_create_users_list_route_incorrect_request(): void
    {
        $desription = 1324;
        $response = $this->postJson(
            route('createUsersList'),
            ['desription' => $desription]
        );

        $response->assertStatus(422);
    }

    public function test_show_users_list_route_basic(): void
    {
        $usersList = UsersList::factory()->create([
            'user_id' => $this->authorizedUser->id
        ]);
        $usersListRepository = app()->make(UsersListRepository::class);
        $usersListData = $usersListRepository->getById($usersList->id);
        $createdResource = UsersListResource::make($usersListData)->resolve();

        $response = $this->get(
            route('showUsersList', ['usersList' => $usersList->id])
        );

        $response->assertStatus(200)->assertJson($createdResource);
    }

    public function test_show_users_list_route_incorrect_request_target(): void
    {
        $usersList = UsersList::factory()->create([
            'user_id' => $this->authorizedUser->id
        ]);
        $response = $this->get(
            route('showUsersList', ['usersList' => $usersList->id + 10])
        );

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function test_show_users_lists_route_private_list(): void
    {
        $usersList = UsersList::factory()->create([
            'user_id' => $this->anotherUser->id,
            'is_private' => true
        ]);
        $response = $this->get(
            route('showUsersList', ['usersList' => $usersList->id])
        );

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_update_users_list_route_basic(): void
    {
        $usersList = UsersList::factory()->create([
            'user_id' => $this->authorizedUser->id
        ]);
        $response = $this->patchJson(
            route('updateUsersList', ['usersList' => $usersList->id]),
            ['name' => $this->faker->words(3, true)]
        );

        $response->assertStatus(200);
    }

    public function test_update_users_list_route_incorrect_request(): void
    {
        $usersList = UsersList::factory()->create([
            'user_id' => $this->authorizedUser->id
        ]);
        $response = $this->patchJson(
            route('updateUsersList', ['usersList' => $usersList->id]),
            ['name' => 1234]
        );

        $response->assertStatus(422);
    }

    public function test_update_users_list_route_another_user_list(): void
    {
        $usersList = UsersList::factory()->create([
            'user_id' => $this->anotherUser->id
        ]);
        $response = $this->patchJson(
            route('updateUsersList', ['usersList' => $usersList->id]),
            ['name' => $this->faker->words(3, true)]
        );

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_delete_users_list_route_basic(): void
    {
        $usersList = UsersList::factory()->create([
            'user_id' => $this->authorizedUser->id
        ]);
        $response = $this->delete(
            route('deleteUsersList', ['usersList' => $usersList->id]),
        );

        $response->assertStatus(200);
    }

    public function test_delete_users_list_route_incorrect_request_target(): void
    {
        $usersList = UsersList::factory()->create([
            'user_id' => $this->authorizedUser->id
        ]);
        $response = $this->delete(
            route('deleteUsersList', ['usersList' => $usersList->id + 10]),
        );

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function test_delete_users_list_route_another_user_list(): void
    {
        $usersList = UsersList::factory()->create([
            'user_id' => $this->anotherUser->id
        ]);
        $response = $this->delete(
            route('deleteUsersList', ['usersList' => $usersList->id]),
        );

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_add_member_to_list_route_basic(): void
    {
        $usersList = UsersList::factory()->create([
            'user_id' => $this->authorizedUser->id
        ]);

        $response = $this->postJson(
            route('addMemberToUsersList', [
                'usersList' => $usersList->id,
                'user' => $this->anotherUser->id
            ]),
        );

        $response->assertStatus(200);
    }

    public function test_add_member_to_list_route_another_user_list(): void
    {
        $usersList = UsersList::factory()->create([
            'user_id' => $this->anotherUser->id
        ]);

        $response = $this->postJson(
            route('addMemberToUsersList', [
                'usersList' => $usersList->id,
                'user' => $this->anotherUser->id
            ]),
        );

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_add_member_to_list_route_incorrect_request_group_target(): void
    {
        $usersList = UsersList::factory()->create([
            'user_id' => $this->authorizedUser->id
        ]);

        $response = $this->postJson(
            route('addMemberToUsersList', [
                'usersList' => $usersList->id + 10,
                'user' => $this->anotherUser->id
            ]),
        );

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function test_add_member_to_list_route_incorrect_request_user_target(): void
    {
        $usersList = UsersList::factory()->create([
            'user_id' => $this->authorizedUser->id
        ]);

        $response = $this->postJson(
            route('addMemberToUsersList', [
                'usersList' => $usersList->id,
                'user' => $this->anotherUser->id + 10
            ]),
        );

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function test_add_member_to_list_route_repeat(): void
    {
        $usersList = UsersList::factory()->create([
            'user_id' => $this->authorizedUser->id
        ]);

        $this->postJson(
            route('addMemberToUsersList', [
                'usersList' => $usersList->id,
                'user' => $this->anotherUser->id
            ]),
        );

        $response = $this->postJson(
            route('addMemberToUsersList', [
                'usersList' => $usersList->id,
                'user' => $this->anotherUser->id
            ]),
        );

        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }

    public function test_remove_member_from_list_route_basic(): void
    {
        $usersList = UsersList::factory()->create([
            'user_id' => $this->authorizedUser->id
        ]);

        $this->postJson(
            route('addMemberToUsersList', [
                'usersList' => $usersList->id,
                'user' => $this->anotherUser->id
            ]),
        );
        $response = $this->deleteJson(
            route('removeMemberFromUsersList', [
                'usersList' => $usersList->id,
                'user' => $this->anotherUser->id
            ]),
        );

        $response->assertStatus(200);
    }


    public function test_remove_member_from_list_route_another_user_list(): void
    {
        $usersList = UsersList::factory()->create([
            'user_id' => $this->anotherUser->id
        ]);

        $this->actingAs($this->anotherUser, 'api');
        $this->postJson(
            route('addMemberToUsersList', [
                'usersList' => $usersList->id,
                'user' => $this->anotherUser->id
            ]),
        );

        $this->actingAs($this->authorizedUser, 'api');
        $response = $this->deleteJson(
            route('removeMemberFromUsersList', [
                'usersList' => $usersList->id,
                'user' => $this->anotherUser->id
            ]),
        );

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_remove_member_from_list_route_incorrect_route_group_target(): void
    {
        $usersList = UsersList::factory()->create([
            'user_id' => $this->authorizedUser->id
        ]);

        $this->postJson(
            route('addMemberToUsersList', [
                'usersList' => $usersList->id,
                'user' => $this->anotherUser->id
            ]),
        );
        $response = $this->deleteJson(
            route('removeMemberFromUsersList', [
                'usersList' => $usersList->id + 10,
                'user' => $this->anotherUser->id
            ]),
        );

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function test_remove_member_from_list_route_incorrect_route_user_target(): void
    {
        $usersList = UsersList::factory()->create([
            'user_id' => $this->authorizedUser->id
        ]);

        $this->postJson(
            route('addMemberToUsersList', [
                'usersList' => $usersList->id,
                'user' => $this->anotherUser->id
            ]),
        );
        $response = $this->deleteJson(
            route('removeMemberFromUsersList', [
                'usersList' => $usersList->id,
                'user' => $this->anotherUser->id + 10
            ]),
        );

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function test_remove_member_from_list_route_repeat(): void
    {
        $usersList = UsersList::factory()->create([
            'user_id' => $this->authorizedUser->id
        ]);

        $this->postJson(
            route('addMemberToUsersList', [
                'usersList' => $usersList->id,
                'user' => $this->anotherUser->id
            ]),
        );

        $this->deleteJson(
            route('removeMemberFromUsersList', [
                'usersList' => $usersList->id,
                'user' => $this->anotherUser->id
            ]),
        );

        $response = $this->deleteJson(
            route('removeMemberFromUsersList', [
                'usersList' => $usersList->id,
                'user' => $this->anotherUser->id
            ]),
        );

        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }

    public function test_remove_member_from_list_route_without_adding(): void
    {
        $usersList = UsersList::factory()->create([
            'user_id' => $this->authorizedUser->id
        ]);

        $response = $this->deleteJson(
            route('removeMemberFromUsersList', [
                'usersList' => $usersList->id,
                'user' => $this->anotherUser->id
            ]),
        );

        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }

    public function test_get_list_members_basic(): void
    {
        $usersList = UsersList::factory()->create([
            'user_id' => $this->authorizedUser->id
        ]);

        $this->postJson(
            route('addMemberToUsersList', [
                'usersList' => $usersList->id,
                'user' => $this->anotherUser->id
            ]),
        );

        $response = $this->getJson(
            route('getUsersListMembers', ['usersList' => $usersList->id])
        );

        $response->assertStatus(200)->assertJsonCount(1);
    }

    public function test_get_list_members_empty(): void
    {
        $usersList = UsersList::factory()->create([
            'user_id' => $this->authorizedUser->id
        ]);

        $response = $this->getJson(
            route('getUsersListMembers', ['usersList' => $usersList->id])
        );

        $response->assertStatus(200)->assertJsonCount(0);
    }

    public function test_get_list_subscribers_basic(): void
    {
        $usersList = UsersList::factory()->create([
            'user_id' => $this->anotherUser->id,
            'is_private' => false
        ]);

        $this->postJson(
            route('subscribeToUsersList', [
                'usersList' => $usersList->id
            ]),
        );

        $response = $this->getJson(
            route('getUsersListSubscribers', ['usersList' => $usersList->id])
        );

        $response->assertStatus(200)->assertJsonCount(1);
    }

    public function test_get_list_subscribers_empty(): void
    {
        $usersList = UsersList::factory()->create([
            'user_id' => $this->anotherUser->id,
            'is_private' => false
        ]);

        $response = $this->getJson(
            route('getUsersListSubscribers', ['usersList' => $usersList->id])
        );

        $response->assertStatus(200)->assertJsonCount(0);
    }

    public function test_subscribe_on_list_route_basic(): void
    {
        $usersList = UsersList::factory()->create([
            'user_id' => $this->authorizedUser->id
        ]);

        $response = $this->postJson(
            route('subscribeToUsersList', ['usersList' => $usersList->id])
        );

        $response->assertStatus(200);
    }

    public function test_subscribe_on_list_route_repeat(): void
    {
        $usersList = UsersList::factory()->create([
            'user_id' => $this->authorizedUser->id
        ]);

        $this->postJson(
            route('subscribeToUsersList', ['usersList' => $usersList->id])
        );

        $response = $this->postJson(
            route('subscribeToUsersList', ['usersList' => $usersList->id])
        );

        $response->assertStatus(204);
    }

    public function test_subscribe_on_list_route_invalid_request_target(): void
    {
        UsersList::factory()->create();

        $response = $this->postJson(
            route('subscribeToUsersList', ['usersList' => UsersList::latest()->first()->id + 10])
        );

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function test_subscribe_on_list_route_private_list(): void
    {
        $usersList = UsersList::factory()->create([
            'user_id' => $this->anotherUser->id,
            'is_private' => true
        ]);

        $response = $this->postJson(
            route('subscribeToUsersList', ['usersList' => $usersList->id])
        );

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_unsubscribe_from_list_route_basic(): void
    {
        $usersList = UsersList::factory()->create([
            'user_id' => $this->authorizedUser->id
        ]);

        $this->postJson(
            route('subscribeToUsersList', ['usersList' => $usersList->id])
        );

        $response = $this->deleteJson(
            route('unsubscribeFromUsersList', ['usersList' => $usersList->id])
        );

        $response->assertStatus(200);
    }

    public function test_unsubscribe_from_list_route_repeat(): void
    {
        $usersList = UsersList::factory()->create([
            'user_id' => $this->authorizedUser->id
        ]);

        $this->postJson(
            route('subscribeToUsersList', ['usersList' => $usersList->id])
        );

        $this->deleteJson(
            route('unsubscribeFromUsersList', ['usersList' => $usersList->id])
        );

        $response = $this->deleteJson(
            route('unsubscribeFromUsersList', ['usersList' => $usersList->id])
        );

        $response->assertStatus(204);
    }

    public function test_unsubscribe_from_list_route_without_subscribtion(): void
    {
        $usersList = UsersList::factory()->create([
            'user_id' => $this->authorizedUser->id
        ]);

        $response = $this->deleteJson(
            route('unsubscribeFromUsersList', ['usersList' => $usersList->id])
        );

        $response->assertStatus(204);
    }

    public function test_unsubscribe_from_list_route_invalid_request_target(): void
    {
        UsersList::factory()->create();

        $response = $this->deleteJson(
            route('unsubscribeFromUsersList', ['usersList' => UsersList::latest()->first()->id + 10])
        );

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function test_unsubscribe_from_list_route_private_list(): void
    {
        $usersList = UsersList::factory()->create([
            'user_id' => $this->anotherUser->id,
            'is_private' => true
        ]);

        $response = $this->deleteJson(
            route('unsubscribeFromUsersList', ['usersList' => $usersList->id])
        );

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }
}
