<?php

namespace App\Modules\User\Repositories;

use App\Modules\User\Models\User;
use App\Modules\User\Models\UserGroup;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class UserGroupRepository
{
    protected $users;
    protected $userGroup;

    public function __construct(
        User $users,
        UserGroup $userGroup
    ) {
        $this->users = $users;
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
}
