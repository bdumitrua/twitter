<?php

namespace App\Modules\User\Repositories;

use App\Modules\User\Models\User;
use Elastic\ScoutDriverPlus\Support\Query;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class UserRepository
{
    protected $users;

    public function __construct(
        User $user,
    ) {
        $this->users = $user;
    }

    protected function baseQuery(): Builder
    {
        return $this->users->newQuery();
    }

    protected function baseQueryWithRelations(array $fullRelations = [], array $countRelations = []): Builder
    {
        $query = $this->baseQuery()->with($fullRelations);

        foreach ($countRelations as $countRelation) {
            $query->withCount($countRelation);
        }

        return $query;
    }

    protected function queryById(int $id, array $fullRelations = [], array $countRelations = []): Builder
    {
        return $this->baseQueryWithRelations($fullRelations, $countRelations)->where('id', '=', $id);
    }

    public function getByIdWithRelations(int $id, array $fullRelations = [],  array $countRelations = []): ?User
    {
        return $this->queryById($id, $fullRelations, $countRelations)->first() ?? new User();
    }

    public function getById(int $id): User
    {
        return $this->queryById($id)->first() ?? new User();
    }

    public function search(string $text): Collection
    {
        $query = Query::match()
            ->field('name')
            ->query($text)
            ->fuzziness('AUTO');

        return User::searchQuery($query)->execute()->models();
    }
}
