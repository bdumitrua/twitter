<?php

namespace App\Modules\User\Repositories;

use App\Exceptions\NotFoundException;
use App\Modules\Notification\Repositories\DeviceTokenRepository;
use App\Modules\Notification\Repositories\NotificationsSubscribtionRepository;
use App\Modules\User\DTO\UserDTO;
use App\Modules\User\Models\User;
use App\Traits\GetCachedData;
use App\Traits\UpdateFromDTO;
use Elastic\ScoutDriverPlus\Support\Query;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserRepository
{
    use GetCachedData, UpdateFromDTO;

    protected User $user;
    protected UsersListRepository $usersListRepository;
    protected DeviceTokenRepository $deviceTokenRepository;
    protected UserSubscribtionRepository $userSubscribtionRepository;
    protected NotificationsSubscribtionRepository $notificationsSubscribtionRepository;

    public function __construct(
        User $user,
        UsersListRepository $usersListRepository,
        DeviceTokenRepository $deviceTokenRepository,
        UserSubscribtionRepository $userSubscribtionRepository,
        NotificationsSubscribtionRepository $notificationsSubscribtionRepository,
    ) {
        $this->user = $user;
        $this->usersListRepository = $usersListRepository;
        $this->deviceTokenRepository = $deviceTokenRepository;
        $this->userSubscribtionRepository = $userSubscribtionRepository;
        $this->notificationsSubscribtionRepository = $notificationsSubscribtionRepository;
    }

    /**
     * @param int $userId
     * 
     * @return Builder
     */
    protected function queryById(int $userId): Builder
    {
        return $this->user->newQuery()
            ->where('id', '=', $userId)
            ->withCount(['subscribtions', 'subscribers']);
    }

    /**
     * @param string $text
     * 
     * @return Collection
     */
    public function search(string $text): Collection
    {
        $query = Query::match()
            ->field('name')
            ->query($text)
            ->fuzziness('AUTO');

        return $this->user->searchQuery($query)->execute()->models();
    }

    /**
     * @param int $userId
     * @param bool $updateCache
     * 
     * @return User
     */
    public function getAuthorizedUser(int $userId, bool $updateCache = false): User
    {
        $authorizedUser = $this->getById($userId, $updateCache);
        $this->assembleAuthorizedUser($authorizedUser);

        return $authorizedUser;
    }

    /**
     * @param int $userId
     * 
     * @return User
     */
    public function getById(int $userId): User
    {
        $user = $this->getUserData($userId);

        $authorizedUserId = Auth::id();
        if (!empty($authorizedUserId)) {
            $user->imSubscribed = !empty($this->userSubscribtionRepository->getByBothIds($userId, $authorizedUserId));
            $user->imSubscribedOnNotifications = !empty($this->notificationsSubscribtionRepository->getByBothIds($userId, $authorizedUserId));
        }

        return $user;
    }

    /**
     * @param int $userId
     * @param bool $updateCache
     * 
     * @return User
     */
    public function getUserData(int $userId, bool $updateCache = false): User
    {
        $cacheKey = KEY_USER_DATA . $userId;
        $user = $this->getCachedData($cacheKey, 5 * 60, function () use ($userId) {
            return $this->queryById($userId)->first();
        }, $updateCache);

        if (empty($user)) {
            throw new NotFoundException('User');
        }

        return $user;
    }

    /**
     * @param int $userId
     * @param UserDTO $dto
     * 
     * @return void
     */
    public function update(int $userId, UserDTO $dto): void
    {
        $user = $this->getUserData($userId);
        $savingStatus = $this->updateUserFromDto($user, $dto);

        if (!empty($savingStatus)) {
            $this->clearUserDataCache($user->id);
        }
    }

    /**
     * @param User $authorizedUser
     * 
     * @return void
     */
    public function assembleAuthorizedUser(User &$authorizedUser): void
    {
        $authorizedUser->deviceTokens = $this->deviceTokenRepository->getByUserId($authorizedUser->id);
        $authorizedUser->lists = $this->usersListRepository->getByUserId($authorizedUser->id);
    }

    /**
     * @param int $userId
     * 
     * @return void
     */
    protected function clearUserDataCache(int $userId): void
    {
        $userCacheKey = KEY_USER_DATA . (string)$userId;
        $this->clearCache($userCacheKey);
    }
}
