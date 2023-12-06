<?php

namespace App\Modules\Tweet\Services;

use App\Exceptions\AccessDeniedException;
use App\Exceptions\NotFoundException;
use App\Exceptions\UnavailableMethodException;
use App\Exceptions\UnprocessableContentException;
use App\Helpers\TimeHelper;
use App\Modules\Tweet\DTO\TweetDTO;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Modules\Tweet\Models\Tweet;
use App\Modules\Tweet\Repositories\TweetRepository;
use App\Modules\Tweet\Requests\CreateThreadRequest;
use App\Modules\Tweet\Requests\TweetRequest;
use App\Modules\Tweet\Resources\TweetResource;
use App\Modules\User\Models\User;
use App\Modules\User\Models\UsersList;
use App\Modules\User\Repositories\UserRepository;
use App\Traits\CreateDTO;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Log\LogManager;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class TweetService
{
    use CreateDTO;

    private $tweetRepository;
    private $userRepository;
    private $authorizedUserId;
    protected LogManager $logger;

    public function __construct(
        TweetRepository $tweetRepository,
        UserRepository $userRepository,
        LogManager $logger,
    ) {
        $this->tweetRepository = $tweetRepository;
        $this->userRepository = $userRepository;
        $this->logger = $logger;
        $this->authorizedUserId = Auth::id();
    }

    /**
     * Для пользователя - кэш на 5 минут, при создании - рекэш на 30 мин
     * 
     * Для списков - кэш на 15 сек
     * 
     * Для ленты - кэш на 15 сек
     */

    public function feed(): JsonResource
    {
        return TweetResource::collection($this->tweetRepository->getUserFeed($this->authorizedUserId));
    }

    public function user(User $user): JsonResource
    {
        $userTweets = $this->tweetRepository->getByUserId($user->id);
        $userTweets = $this->filterTweetsByGroup($userTweets, $this->authorizedUserId);

        return TweetResource::collection($userTweets);
    }

    public function replies(User $user): JsonResource
    {
        $userReplies = $this->tweetRepository->getUserReplies($user->id);
        $userReplies = $this->filterTweetsByGroup($userReplies, $this->authorizedUserId);

        return TweetResource::collection($userReplies);
    }

    // ! DOESN'T WORK
    public function media(User $user): JsonResource
    {
        throw new UnavailableMethodException('Media request doesn\'t work at the moment');

        $userTweetsWithMedia = $this->tweetRepository->getUserTweetsWithMedia($user->id);
        $userTweetsWithMedia = $this->filterTweetsByGroup($userTweetsWithMedia, $this->authorizedUserId);

        return TweetResource::collection($userTweetsWithMedia);
    }

    public function likes(User $user): JsonResource
    {
        $userLikedTweets = $this->tweetRepository->getUserLikedTweets($user->id);
        $userLikedTweets = $this->filterTweetsByGroup($userLikedTweets, $this->authorizedUserId);

        return TweetResource::collection($userLikedTweets);
    }

    public function list(UsersList $usersList): JsonResource
    {
        if ($usersList->is_private) {
            if (!in_array($this->authorizedUserId, $this->pluckKey($usersList->subscribers(), 'user_id'))) {
                throw new AccessDeniedException();
            }
        }

        $usersListFeed = $this->tweetRepository->getFeedByUsersList($usersList);
        $tweets = $this->filterTweetsByGroup($usersListFeed, $this->authorizedUserId);

        return TweetResource::collection($tweets);
    }

    public function show(Tweet $tweet): JsonResource
    {
        $tweet = $this->tweetRepository->getById($tweet->id);
        $tweetAfterFiltering = $this->filterTweetsByGroup(new Collection([$tweet]), $this->authorizedUserId);

        if (empty($tweetAfterFiltering->first())) {
            throw new NotFoundException('Tweet');
        }

        return new TweetResource($tweetAfterFiltering->first());
    }

    public function create(TweetRequest $tweetRequest): void
    {
        $this->logger->info('Validating tweet type data', $tweetRequest->toArray());
        $this->validateTweetTypeData($tweetRequest);
        $this->validateTweetText($tweetRequest);

        $tweetDTO = $this->createDTO($tweetRequest, TweetDTO::class);
        $tweetDTO->userId = $this->authorizedUserId;

        $this->logger->info('Creating tweet from tweetDTO', $tweetDTO->toArray());
        $this->tweetRepository->create($tweetDTO);
    }

    public function thread(CreateThreadRequest $сreateThreadRequest): void
    {
        $tweetsData = $сreateThreadRequest->tweets;
        $userGroupId = $сreateThreadRequest->userGroupId;

        $this->logger->info('Creating TweetDTOs from create thread request', $сreateThreadRequest->toArray());
        $tweetDTOs = array_map(function ($newTweetData) use ($userGroupId) {
            return new TweetDTO(
                $this->authorizedUserId,
                [
                    'text' => $newTweetData['text'],
                    'userGroupId' => $userGroupId,
                    'type' => 'thread'
                ]
            );
        }, $tweetsData);

        $this->logger->info('Creating thread tweets from tweetDTOs', $tweetDTOs);
        $this->tweetRepository->createThread($tweetDTOs);
    }

    public function destroy(Tweet $tweet, Request $request): void
    {
        $this->logger->info('Deleting tweet', [$tweet->toArray(), 'ip' => $request->ip()]);
        $this->tweetRepository->destroy($tweet);
    }

    protected function filterTweetsByGroup(Collection $tweets): Collection
    {
        $groupIds = [];
        if ($this->authorizedUserId) {
            $groupIds = $this->getUserGroupIds($this->authorizedUserId);
        }

        return $tweets->filter(function ($tweet) use ($groupIds) {
            return is_object($tweet) && (is_null($tweet->user_group_id) || in_array($tweet->user_group_id, $groupIds));
        });
    }

    protected function getUserGroupIds(int $userId): array
    {
        $user = $this->userRepository->getById($userId);
        return $this->pluckKey($user->groups_member, 'id');
    }

    private function validateTweetTypeData(TweetRequest $tweetRequest): void
    {
        if (!empty($tweetRequest->type) && empty($tweetRequest->linkedTweetId)) {
            throw new UnprocessableContentException('Linked tweet id can\'t be empty, if it\'s not default tweet');
        }
    }

    private function validateTweetText(TweetRequest $tweetRequest): void
    {
        if (empty($tweetRequest->text) && $tweetRequest->type !== 'repost') {
            throw new UnprocessableContentException('Tweet text is required');
        }
    }

    private function pluckKey($relation, string $key): array
    {
        return $relation->pluck($key)->toArray();
    }
}
