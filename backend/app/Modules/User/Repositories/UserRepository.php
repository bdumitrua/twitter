<?php

namespace App\Modules\User\Repositories;

use App\Modules\User\DTO\UserDTO;
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

    public function getById(int $id, array $relations = []): User
    {
        return $this->users->with($relations)
            ->where('id', '=', $id)
            ->first() ?? new User();
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
