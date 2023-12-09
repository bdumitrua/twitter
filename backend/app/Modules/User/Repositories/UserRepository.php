<?php

namespace App\Modules\User\Repositories;

use App\Helpers\TimeHelper;
use App\Modules\User\DTO\UserDTO;
use App\Modules\User\Models\User;
use App\Traits\GetCachedData;
use Elastic\ScoutDriverPlus\Support\Query;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserRepository
{
    use GetCachedData;

    protected User $user;

    public function __construct(
        User $user,
    ) {
        $this->user = $user;
    }

    protected function queryById(int $userId): Builder
    {
        return $this->user->newQuery()
            ->where('id', '=', $userId)
            ->withCount(['subscribtions', 'subscribers']);
    }

    public function search(string $text): Collection
    {
        $query = Query::match()
            ->field('name')
            ->query($text)
            ->fuzziness('AUTO');

        return $this->user->searchQuery($query)->execute()->models();
    }

    public function getAuthorizedUser(int $userId, bool $updateCache = false): User
    {
        $cacheKey = KEY_AUTH_USER_DATA . $userId;
        return $this->getCachedData($cacheKey, 5 * 60, function () use ($userId) {
            return $this->queryById($userId)
                ->with(['lists', 'lists_subscribtions', 'deviceTokens'])
                ->first() ?? new User();
        }, $updateCache);
    }

    public function getById(int $userId, bool $updateCache = false): User
    {
        $cacheKey = KEY_USER_DATA . $userId;
        return $this->getCachedData($cacheKey, 5 * 60, function () use ($userId) {
            return $this->queryById($userId)->first() ?? new User();
        }, $updateCache);
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
            $this->clearUserDataCache($user->id);
        }
    }

    public function clearUserDataCache(int $userId): void
    {
        $authorizedUserCacheKey = KEY_AUTH_USER_DATA . (string)$userId;
        $userCacheKey = KEY_USER_DATA . (string)$userId;

        $this->clearCache($authorizedUserCacheKey);
        $this->clearCache($userCacheKey);
    }
}
