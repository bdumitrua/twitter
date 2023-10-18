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
}
