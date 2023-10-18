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

    protected function baseQuery(array $relations = []): Builder
    {
        return $this->users->newQuery()->with($relations);
    }

    protected function queryById(int $id, array $relations = []): Builder
    {
        return $this->baseQuery($relations)->where('id', '=', $id);
    }

    public function getByIdWithRelations(int $id, array $relations = []): User
    {
        return $this->queryById($id, $relations)->first();
    }

    public function getById(int $id): User
    {
        return $this->queryById($id)->first();
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
