<?php

namespace App\Modules\User\Repositories;

use App\Modules\User\DTO\UserDTO;
use App\Modules\User\DTO\UserUpdateDTO;
use App\Modules\User\Models\User;
use Elastic\ScoutDriverPlus\Support\Query;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

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

    protected function baseQueryWithRelations(array $relations = []): Builder
    {
        return $this->baseQuery()->with($relations);
    }

    protected function queryById(int $id, array $relations = []): Builder
    {
        return $this->baseQueryWithRelations($relations)->where('id', '=', $id);
    }

    public function getByIdWithRelations(int $id, array $relations = []): User
    {
        return $this->queryById($id, $relations)->first() ?? new User();
    }

    public function getById(int $id): User
    {
        return $this->queryById($id)->first() ?? new User();
    }

    public function update(int $userId, UserDTO $dto): void
    {
        $user = $this->getById($userId);
        $dtoProperties = get_object_vars($dto);

        foreach ($dtoProperties as $property => $value) {
            $property = Str::snake($property);

            if (!empty($value)) {
                $user->$property = $property === 'password'
                    ? Hash::make($value)
                    : $value;
            }
        }

        $user->save();
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
