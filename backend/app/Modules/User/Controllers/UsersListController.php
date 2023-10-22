<?php

namespace App\Modules\User\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\User\Models\User;
use App\Modules\User\Models\UsersList;
use App\Modules\User\Requests\SearchRequest;
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

    public function create(UsersListRequest $usersListRequest): JsonResponse
    {
        return $this->handleServiceCall(function () use ($usersListRequest) {
            return $this->usersListService->create($usersListRequest);
        });
    }

    public function update(UsersList $usersList, UsersListRequest $usersListRequest): JsonResponse
    {
        return $this->handleServiceCall(function () use ($usersList, $usersListRequest) {
            return $this->usersListService->update($usersList, $usersListRequest);
        });
    }

    public function destroy(UsersList $usersList): JsonResponse
    {
        return $this->handleServiceCall(function () use ($usersList) {
            return $this->usersListService->destroy($usersList);
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
