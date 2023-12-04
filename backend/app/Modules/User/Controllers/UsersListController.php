<?php

namespace App\Modules\User\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\User\Models\User;
use App\Modules\User\Models\UsersList;
use App\Modules\User\Requests\CreateUsersListRequest;
use App\Modules\User\Requests\SearchRequest;
use App\Modules\User\Requests\UpdateUsersListRequest;
use App\Modules\User\Requests\UsersListRequest;
use App\Modules\User\Services\UsersListService;
use Illuminate\Http\JsonResponse;

class UsersListController extends Controller
{
    private $usersListService;

    public function __construct(UsersListService $usersListService)
    {
        $this->usersListService = $usersListService;
    }

    public function index(): JsonResponse
    {
        return $this->handleServiceCall(function () {
            return $this->usersListService->index();
        });
    }

    public function show(UsersList $usersList): JsonResponse
    {
        return $this->handleServiceCall(function () use ($usersList) {
            return $this->usersListService->show($usersList);
        });
    }

    public function create(CreateUsersListRequest $createUsersListRequest): JsonResponse
    {
        return $this->handleServiceCall(function () use ($createUsersListRequest) {
            return $this->usersListService->create($createUsersListRequest);
        });
    }

    public function update(UsersList $usersList, UpdateUsersListRequest $updateUsersListRequest): JsonResponse
    {
        return $this->handleServiceCall(function () use ($usersList, $updateUsersListRequest) {
            return $this->usersListService->update($usersList, $updateUsersListRequest);
        });
    }

    public function destroy(UsersList $usersList, Request $request): JsonResponse
    {
        return $this->handleServiceCall(function () use ($usersList, $request) {
            return $this->usersListService->destroy($usersList, $request);
        });
    }

    public function add(UsersList $usersList, User $user): JsonResponse
    {
        return $this->handleServiceCall(function () use ($usersList, $user) {
            return $this->usersListService->add($usersList, $user);
        });
    }

    public function remove(UsersList $usersList, User $user): JsonResponse
    {
        return $this->handleServiceCall(function () use ($usersList, $user) {
            return $this->usersListService->remove($usersList, $user);
        });
    }

    public function subscribe(UsersList $usersList): JsonResponse
    {
        return $this->handleServiceCall(function () use ($usersList) {
            return $this->usersListService->subscribe($usersList);
        });
    }

    public function unsubscribe(UsersList $usersList): JsonResponse
    {
        return $this->handleServiceCall(function () use ($usersList) {
            return $this->usersListService->unsubscribe($usersList);
        });
    }
}
