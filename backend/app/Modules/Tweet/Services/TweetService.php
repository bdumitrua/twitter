<?php

namespace App\Modules\Tweet\Services;

use App\Exceptions\AccessDeniedException;
use App\Exceptions\NotFoundException;
use App\Exceptions\UnavailableMethodException;
use App\Exceptions\UnprocessableContentException;
use App\Modules\Tweet\DTO\TweetDTO;
use Illuminate\Http\Request;
use App\Modules\Tweet\Models\Tweet;
use App\Modules\Tweet\Repositories\TweetRepository;
use App\Modules\Tweet\Requests\CreateRepostRequest;
use App\Modules\Tweet\Requests\CreateThreadRequest;
use App\Modules\Tweet\Requests\TweetRequest;
use App\Modules\Tweet\Resources\TweetResource;
use App\Modules\User\Models\User;
use App\Modules\User\Models\UsersList;
use App\Modules\User\Repositories\UserGroupRepository;
use App\Modules\User\Repositories\UserRepository;
use App\Modules\User\Services\UsersListService;
use App\Traits\CreateDTO;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;
use Illuminate\Log\LogManager;
use Illuminate\Support\Facades\Auth;

class TweetService
{
    use CreateDTO;

    protected UserGroupRepository $userGroupRepository;
    protected UsersListService $usersListService;
    protected TweetRepository $tweetRepository;
    protected UserRepository $userRepository;
    protected LogManager $logger;
    protected ?int $authorizedUserId;

    public function __construct(
        UserGroupRepository $userGroupRepository,
        UsersListService $usersListService,
        TweetRepository $tweetRepository,
        UserRepository $userRepository,
        LogManager $logger,
    ) {
        $this->userGroupRepository = $userGroupRepository;
        $this->usersListService = $usersListService;
        $this->tweetRepository = $tweetRepository;
        $this->userRepository = $userRepository;
        $this->logger = $logger;
        $this->authorizedUserId = Auth::id();
    }

    /**
     * @return JsonResource
     */
    public function feed(): JsonResource
    {
        // Фильтр по группам происходит в репозитории (в самом запросе)
        return TweetResource::collection(
            $this->tweetRepository->getUserFeed($this->authorizedUserId)
        );
    }

    /**
     * @param User $user
     * 
     * @return JsonResource
     */
    public function user(User $user): JsonResource
    {
        $userTweets = $this->tweetRepository->getByUserId($user->id);
        $userTweets = $this->filterTweetsByGroup($userTweets, $this->authorizedUserId);

        return TweetResource::collection($userTweets);
    }

    /**
     * @param User $user
     * 
     * @return JsonResource
     */
    public function replies(User $user): JsonResource
    {
        $userReplies = $this->tweetRepository->getUserReplies($user->id);
        $userReplies = $this->filterTweetsByGroup($userReplies, $this->authorizedUserId);

        return TweetResource::collection($userReplies);
    }

    // ! DOESN'T WORK
    /**
     * @param User $user
     * 
     * @return JsonResource
     */
    public function media(User $user): JsonResource
    {
        throw new UnavailableMethodException('Media request doesn\'t work at the moment');

        $userTweetsWithMedia = $this->tweetRepository->getUserTweetsWithMedia($user->id);
        $userTweetsWithMedia = $this->filterTweetsByGroup($userTweetsWithMedia, $this->authorizedUserId);

        return TweetResource::collection($userTweetsWithMedia);
    }

    /**
     * @param User $user
     * 
     * @return JsonResource
     */
    public function likes(User $user): JsonResource
    {
        $userLikedTweets = $this->tweetRepository->getUserLikedTweets($user->id);
        $userLikedTweets = $this->filterTweetsByGroup($userLikedTweets, $this->authorizedUserId);

        return TweetResource::collection($userLikedTweets);
    }

    /**
     * @return JsonResource
     */
    public function bookmarks(): JsonResource
    {
        $userLikedTweets = $this->tweetRepository->getUserBookmarks($this->authorizedUserId);
        $userLikedTweets = $this->filterTweetsByGroup($userLikedTweets, $this->authorizedUserId);

        return TweetResource::collection($userLikedTweets);
    }

    /**
     * @param UsersList $usersList
     * 
     * @return JsonResource
     */
    public function list(UsersList $usersList): JsonResource
    {
        $usersList = $this->usersListService->filterPrivateLists(
            new Collection([$usersList]),
            $this->authorizedUserId
        )->first();

        if (empty($usersList)) {
            throw new AccessDeniedException();
        }

        // Фильтр по группам происходит в репозитории (в самом запросе)
        return TweetResource::collection(
            $this->tweetRepository->getFeedByUsersList($usersList, $this->authorizedUserId)
        );
    }

    /**
     * @param Tweet $tweet
     * 
     * @return JsonResource
     */
    public function show(Tweet $tweet): JsonResource
    {
        $tweet = $this->tweetRepository->getById($tweet->id);
        $tweetAfterFiltering = $this->filterTweetsByGroup(
            new Collection([$tweet]),
            $this->authorizedUserId
        )->first();

        if (empty($tweetAfterFiltering)) {
            throw new NotFoundException('Tweet');
        }

        return new TweetResource($tweetAfterFiltering);
    }

    /**
     * @param TweetRequest $tweetRequest
     * 
     * @return void
     */
    public function create(TweetRequest $tweetRequest): void
    {
        $this->logger->info('Validating tweet type data', $tweetRequest->toArray());
        $this->validateTweetTypeData($tweetRequest);

        $tweetDTO = $this->createDTO($tweetRequest, TweetDTO::class);
        $tweetDTO->userId = $this->authorizedUserId;

        $this->logger->info('Creating tweet from tweetDTO', $tweetDTO->toArray());
        $this->tweetRepository->create($tweetDTO);
    }

    /**
     * @param Tweet $tweet
     * 
     * @return Response
     */
    public function repost(Tweet $tweet): Response
    {
        $tweetDTO = new TweetDTO(
            $this->authorizedUserId,
            [
                'type' => 'repost',
                'linkedTweetId' => $tweet->id
            ]
        );

        $this->logger->info('Creating repost from tweetDTO', $tweetDTO->toArray());
        return $this->tweetRepository->repost($tweetDTO);
    }

    /**
     * @param CreateThreadRequest $сreateThreadRequest
     * 
     * @return void
     */
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

    /**
     * @param Tweet $tweet
     * @param Request $request
     * 
     * @return void
     */
    public function destroy(Tweet $tweet, Request $request): void
    {
        $this->logger->info('Deleting tweet', array_merge($tweet->toArray(), ['ip' => $request->ip()]));
        $this->tweetRepository->destroy($tweet);
    }

    /**
     * @param Tweet $tweet
     * @param Request $request
     * 
     * @return void
     */
    public function unrepost(Tweet $tweet, Request $request): void
    {
        $this->logger->info("Deleting repost of tweet {$tweet->id}", ['userId' => $this->authorizedUserId, 'ip' => $request->ip()]);
        $this->tweetRepository->unrepost($tweet->id, $this->authorizedUserId);
    }

    /**
     * @param Collection $tweets
     * 
     * @return Collection
     */
    protected function filterTweetsByGroup(Collection $tweets): Collection
    {
        $groupIds = [];
        if (!empty($this->authorizedUserId)) {
            $groupIds = $this->userGroupRepository->getUserAvailableGroupsIds($this->authorizedUserId);
        }

        return $tweets->filter(function ($tweet) use ($groupIds) {
            return is_object($tweet) && (is_null($tweet->user_group_id) || in_array($tweet->user_group_id, $groupIds));
        });
    }

    /**
     * @param TweetRequest $tweetRequest
     * 
     * @return void
     */
    private function validateTweetTypeData(TweetRequest $tweetRequest): void
    {
        if (!empty($tweetRequest->type) && empty($tweetRequest->linkedTweetId)) {
            throw new UnprocessableContentException('Linked tweet id can\'t be empty, if it\'s not default tweet');
        }
    }
}
