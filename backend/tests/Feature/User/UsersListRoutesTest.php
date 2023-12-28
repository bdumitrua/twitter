<?php

namespace Tests\Feature\User;

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

    public function testGetMyListsRouteBasic(): void
    {
        $usersLists = UsersList::factory(3)->create([
            'user_id' => $this->authorizedUser->id
        ]);

        $createdResource = UsersListResource::collection($usersLists)->resolve();
        $response = $this->get(route('getAuthorizedUserUsersLists'));

        $response->assertStatus(Response::HTTP_OK)->assertJson($createdResource);
    }

    public function testGetMyListsRouteEmpty(): void
    {
        $usersLists = new Collection();
        $createdResource = UserGroupResource::collection($usersLists)->resolve();
        $response = $this->get(route('getAuthorizedUserUsersLists'));

        $response->assertStatus(Response::HTTP_OK)->assertJson($createdResource);
    }

    public function testCreateUsersListRouteBasic(): void
    {
        $name = $this->faker->words(2, true);
        $response = $this->postJson(
            route('createUsersList'),
            ['name' => $name],
        );

        $response->assertStatus(Response::HTTP_OK);
    }

    public function testCreateUsersListRouteIncorrectRequest(): void
    {
        $desription = 1324;
        $response = $this->postJson(
            route('createUsersList'),
            ['desription' => $desription]
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testShowUsersListRouteBasic(): void
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

        $response->assertStatus(Response::HTTP_OK)->assertJson($createdResource);
    }

    public function testShowUsersListRouteIncorrectRequestTarget(): void
    {
        $usersList = UsersList::factory()->create([
            'user_id' => $this->authorizedUser->id
        ]);
        $response = $this->get(
            route('showUsersList', ['usersList' => $usersList->id + 10])
        );

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testShowUsersListsRoutePrivateList(): void
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

    public function testUpdateUsersListRouteBasic(): void
    {
        $usersList = UsersList::factory()->create([
            'user_id' => $this->authorizedUser->id
        ]);
        $response = $this->patchJson(
            route('updateUsersList', ['usersList' => $usersList->id]),
            ['name' => $this->faker->words(3, true)]
        );

        $response->assertStatus(Response::HTTP_OK);
    }

    public function testUpdateUsersListRouteIncorrectRequest(): void
    {
        $usersList = UsersList::factory()->create([
            'user_id' => $this->authorizedUser->id
        ]);
        $response = $this->patchJson(
            route('updateUsersList', ['usersList' => $usersList->id]),
            ['name' => 1234]
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testUpdateUsersListRouteAnotherUserList(): void
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

    public function testDeleteUsersListRouteBasic(): void
    {
        $usersList = UsersList::factory()->create([
            'user_id' => $this->authorizedUser->id
        ]);
        $response = $this->delete(
            route('deleteUsersList', ['usersList' => $usersList->id]),
        );

        $response->assertStatus(Response::HTTP_OK);
    }

    public function testDeleteUsersListRouteIncorrectRequestTarget(): void
    {
        $usersList = UsersList::factory()->create([
            'user_id' => $this->authorizedUser->id
        ]);
        $response = $this->delete(
            route('deleteUsersList', ['usersList' => $usersList->id + 10]),
        );

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testDeleteUsersListRouteAnotherUserList(): void
    {
        $usersList = UsersList::factory()->create([
            'user_id' => $this->anotherUser->id
        ]);
        $response = $this->delete(
            route('deleteUsersList', ['usersList' => $usersList->id]),
        );

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testAddMemberToListRouteBasic(): void
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

        $response->assertStatus(Response::HTTP_OK);
    }

    public function testAddMemberToListRouteAnotherUserList(): void
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

    public function testAddMemberToListRouteIncorrectRequestGroupTarget(): void
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

    public function testAddMemberToListRouteIncorrectRequestUserTarget(): void
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

    public function testAddMemberToListRouteRepeat(): void
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

    public function testRemoveMemberFromListRouteBasic(): void
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

        $response->assertStatus(Response::HTTP_OK);
    }


    public function testRemoveMemberFromListRouteAnotherUserList(): void
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

    public function testRemoveMemberFromListRouteIncorrectRouteGroupTarget(): void
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

    public function testRemoveMemberFromListRouteIncorrectRouteUserTarget(): void
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

    public function testRemoveMemberFromListRouteRepeat(): void
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

    public function testRemoveMemberFromListRouteWithoutAdding(): void
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

    public function testGetListMembersBasic(): void
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

        $response->assertStatus(Response::HTTP_OK)->assertJsonCount(1);
    }

    public function testGetListMembersEmpty(): void
    {
        $usersList = UsersList::factory()->create([
            'user_id' => $this->authorizedUser->id
        ]);

        $response = $this->getJson(
            route('getUsersListMembers', ['usersList' => $usersList->id])
        );

        $response->assertStatus(Response::HTTP_OK)->assertJsonCount(0);
    }

    public function testGetListSubscribersBasic(): void
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

        $response->assertStatus(Response::HTTP_OK)->assertJsonCount(1);
    }

    public function testGetListSubscribersEmpty(): void
    {
        $usersList = UsersList::factory()->create([
            'user_id' => $this->anotherUser->id,
            'is_private' => false
        ]);

        $response = $this->getJson(
            route('getUsersListSubscribers', ['usersList' => $usersList->id])
        );

        $response->assertStatus(Response::HTTP_OK)->assertJsonCount(0);
    }

    public function testSubscribeOnListRouteBasic(): void
    {
        $usersList = UsersList::factory()->create([
            'user_id' => $this->authorizedUser->id
        ]);

        $response = $this->postJson(
            route('subscribeToUsersList', ['usersList' => $usersList->id])
        );

        $response->assertStatus(Response::HTTP_OK);
    }

    public function testSubscribeOnListRouteRepeat(): void
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

        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }

    public function testSubscribeOnListRouteInvalidRequestTarget(): void
    {
        UsersList::factory()->create();

        $response = $this->postJson(
            route('subscribeToUsersList', ['usersList' => UsersList::latest()->first()->id + 10])
        );

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testSubscribeOnListRoutePrivateList(): void
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

    public function testUnsubscribeFromListRouteBasic(): void
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

        $response->assertStatus(Response::HTTP_OK);
    }

    public function testUnsubscribeFromListRouteRepeat(): void
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

        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }

    public function testUnsubscribeFromListRouteWithoutSubscribtion(): void
    {
        $usersList = UsersList::factory()->create([
            'user_id' => $this->authorizedUser->id
        ]);

        $response = $this->deleteJson(
            route('unsubscribeFromUsersList', ['usersList' => $usersList->id])
        );

        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }

    public function testUnsubscribeFromListRouteInvalidRequestTarget(): void
    {
        UsersList::factory()->create();

        $response = $this->deleteJson(
            route('unsubscribeFromUsersList', ['usersList' => UsersList::latest()->first()->id + 10])
        );

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testUnsubscribeFromListRoutePrivateList(): void
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
