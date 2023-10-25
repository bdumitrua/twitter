<?php

namespace App\Modules\User\Repositories;

use App\Helpers\TimeHelper;
use App\Modules\User\DTO\UserDTO;
use App\Modules\User\Models\User;
use Elastic\ScoutDriverPlus\Support\Query;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
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

    protected function queryById(int $userId): Builder
    {
        return $this->users->newQuery()
            ->where('id', '=', $userId)
            ->withCount(['subscribtions', 'subscribers']);
    }

    public function getAuthUser(int $userId, bool $updateCache = false): User
    {
        $cacheKey = KEY_AUTH_USER_DATA . $userId;

        if ($updateCache) {
            $userData = $this->queryById($userId)
                ->with(['lists', 'lists_subscribtions'])
                ->first() ?? new User();

            Cache::put($cacheKey, $userData, TimeHelper::getMinutes(5));
        }

        return Cache::remember($cacheKey, TimeHelper::getMinutes(5), function () use ($userId) {
            return $this->queryById($userId)
                ->with(['lists', 'lists_subscribtions'])
                ->first() ?? new User();
        });
    }

    public function getById(int $userId, bool $updateCache = false): User
    {
        $cacheKey = KEY_USER_DATA . $userId;

        if ($updateCache) {
            $userData = $this->queryById($userId)->first() ?? new User();
            Cache::put($cacheKey, $userData, TimeHelper::getMinutes(5));
        }

        return Cache::remember($cacheKey, TimeHelper::getMinutes(5), function () use ($userId) {
            return $this->queryById($userId)->first() ?? new User();
        });
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

        $savingStatus = $user->save();

        if (!empty($savingStatus)) {
            $this->recacheUserData($user->id);
        }
    }

    public function search(string $text): Collection
    {
        $query = Query::match()
            ->field('name')
            ->query($text)
            ->fuzziness('AUTO');

        return User::searchQuery($query)->execute()->models();
    }

    public function recacheUserData(int $userId): void
    {
        $this->getById($userId, true);
        $this->getAuthUser($userId, true);
    }
}
