<?php

namespace App\Modules\User\Repositories;

use App\Modules\User\Models\User;
use App\Modules\User\Models\UserGroup;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class UserGroupRepository
{
    protected $userGroup;

    public function __construct(
        UserGroup $userGroup
    ) {
        $this->userGroup = $userGroup;
    }

    protected function baseQuery(): Builder
    {
        return $this->userGroup->newQuery();
    }

    protected function baseQueryWithRelations(array $relations = []): Builder
    {
        return $this->baseQuery()->with($relations);
    }

    protected function queryById(int $id): Builder
    {
        return $this->baseQuery()->where('id', '=', $id);
    }

    public function getById(int $id): UserGroup
    {
        return $this->queryById($id)->first();
    }

    public function create(int $userId, $data): void
    {
        $this->userGroup->create([
            'user_id' => $userId,
            'name' => $data->name,
            'description' => $data->description
        ]);
    }
    public function update(UserGroup $userGroup, $data): void
    {
        $userGroup->update([
            'name' => $data->name,
            'description' => $data->description
        ]);
    }
    public function delete(UserGroup $userGroup): void
    {
        $userGroup->delete();
    }

    public function addUser(): void
    {
        // 
    }

    public function removeUser(): void
    {
        // 
    }
}
