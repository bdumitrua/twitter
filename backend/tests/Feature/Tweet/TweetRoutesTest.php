<?php

namespace Tests\Feature\Notification;

use App\Modules\Tweet\Models\Tweet;
use App\Modules\Tweet\Resources\TweetResource;
use App\Modules\User\Models\User;
use App\Modules\User\Models\UserGroup;
use App\Modules\User\Models\UsersList;
use App\Modules\User\Models\UsersListSubscribtion;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;
use Illuminate\Support\Str;

class TweetRoutesTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $authorizedUser;
    protected $secondUser;
    protected $thirdUser;

    public function setUp(): void
    {
        parent::setUp();

        $this->authorizedUser = User::factory()->create();
        $this->secondUser = User::factory()->create();
        $this->thirdUser = User::factory()->create();
        $this->actingAs($this->authorizedUser, 'api');
    }

    protected function generateText(): string
    {
        return $this->faker->words(10, true);
    }

    protected function getEmptyText(): ?string
    {
        return null;
    }

    protected function createTweet($data = []): Tweet
    {
        return Tweet::factory()->create($data);
    }

    protected function createTweets($data = [], $count = 1): Collection
    {
        return Tweet::factory($count)->create($data);
    }

    protected function createRepost(): Tweet
    {
        $tweetToRepost = $this->createTweet();

        return Tweet::factory()->create([
            'type' => 'repost',
            'user_id' => $this->authorizedUser->id,
            'linked_tweet_id' => $tweetToRepost->id
        ]);
    }

    protected function getInvalidTweetId(): int
    {
        $this->createTweet();

        return Tweet::latest()->first()->id + 10;
    }

    protected function createFactoryGroup($userId): UserGroup
    {
        return UserGroup::factory()->create([
            'user_id' => $userId
        ]);
    }

    public function test_create_default_tweet_route_basic(): void
    {
        $text = $this->generateText();
        $group = $this->createFactoryGroup($this->authorizedUser->id);

        $response = $this->postJson(
            route('createTweet'),
            [
                'text' => $text,
                'userGroupId' => $group->id
            ]
        );

        $response->assertStatus(200);
    }

    public function test_create_default_tweet_route_with_empty_text(): void
    {
        $text = $this->getEmptyText();
        $response = $this->postJson(
            route('createTweet'),
            ['text' => $text]
        );

        $response->assertStatus(422);
    }

    public function test_create_default_tweet_route_with_invalid_group_id(): void
    {
        $text = $this->generateText();

        $this->createFactoryGroup($this->authorizedUser->id);
        $groupId = UserGroup::latest()->first()->id + 10;

        $response = $this->postJson(
            route('createTweet'),
            [
                'text' => $text,
                'userGroupId' => $groupId
            ]
        );

        $response->assertStatus(422);
    }

    public function test_repost_tweet_basic(): void
    {
        $tweetToRepost = $this->createTweet();
        $response = $this->postJson(
            route('repostTweet', ['tweet' => $tweetToRepost->id]),
        );

        $response->assertStatus(200);
    }

    public function test_repost_tweet_invalid_request_target(): void
    {
        $tweetId = $this->getInvalidTweetId();
        $response = $this->postJson(
            route('repostTweet', ['tweet' => $tweetId]),
        );

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function test_unrepost_tweet_basic(): void
    {
        $tweet = $this->createRepost();
        $response = $this->delete(
            route('unrepostTweet', ['tweet' => $tweet->linked_tweet_id]),
        );

        $response->assertStatus(200);
    }

    public function test_unrepost_tweet_invalid_request_target(): void
    {
        $response = $this->delete(
            route('unrepostTweet', ['tweet' => $this->getInvalidTweetId()]),
        );

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }


    public function test_unrepost_tweet_without_repost(): void
    {
        $tweet = $this->createTweet();
        $response = $this->delete(
            route('unrepostTweet', ['tweet' => $tweet->id]),
        );

        $response->assertStatus(204);
    }

    public function test_create_reply_tweet_route_basic(): void
    {
        $type = 'reply';
        $text = $this->generateText();
        $tweetToRepost = $this->createTweet();

        $response = $this->postJson(
            route('createTweet'),
            [
                'text' => $text,
                'type' => $type,
                'linkedTweetId' => $tweetToRepost->id
            ]
        );

        $response->assertStatus(200);
    }

    public function test_create_reply_tweet_route_with_empty_text(): void
    {
        $type = 'reply';
        $text = $this->getEmptyText();
        $tweetToRepost = $this->createTweet();

        $response = $this->postJson(
            route('createTweet'),
            [
                'text' => $text,
                'type' => $type,
                'linkedTweetId' => $tweetToRepost->id
            ]
        );

        $response->assertStatus(422);
    }

    public function test_create_reply_tweet_route_invalid_linked_tweet_id(): void
    {
        $type = 'reply';
        $text = $this->generateText();
        $tweetId = $this->getInvalidTweetId();

        $response = $this->postJson(
            route('createTweet'),
            [
                'text' => $text,
                'type' => $type,
                'linkedTweetId' => $tweetId
            ]
        );

        $response->assertStatus(422);
    }

    public function test_create_quote_tweet_route_basic(): void
    {
        $type = 'quote';
        $text = $this->generateText();
        $tweetToRepost = $this->createTweet();

        $response = $this->postJson(
            route('createTweet'),
            [
                'text' => $text,
                'type' => $type,
                'linkedTweetId' => $tweetToRepost->id
            ]
        );

        $response->assertStatus(200);
    }

    public function test_create_quote_tweet_route_with_empty_text(): void
    {
        $type = 'quote';
        $text = $this->getEmptyText();
        $tweetToRepost = $this->createTweet();

        $response = $this->postJson(
            route('createTweet'),
            [
                'text' => $text,
                'type' => $type,
                'linkedTweetId' => $tweetToRepost->id
            ]
        );

        $response->assertStatus(422);
    }

    public function test_create_quote_tweet_route_invalid_linked_tweet_id(): void
    {
        $type = 'quote';
        $text = $this->generateText();
        $tweetId = $this->getInvalidTweetId();

        $response = $this->postJson(
            route('createTweet'),
            [
                'text' => $text,
                'type' => $type,
                'linkedTweetId' => $tweetId
            ]
        );

        $response->assertStatus(422);
    }

    public function test_create_thread_route_basic(): void
    {
        $group = $this->createFactoryGroup($this->authorizedUser->id);
        $tweets = [];
        for ($i = 0; $i < 5; $i++) {
            $tweets[] = [
                'text' => $this->generateText()
            ];
        }

        $response = $this->postJson(
            route('createThread'),
            [
                'tweets' => $tweets,
                'userGroupId' => $group->id
            ]
        );

        $response->assertStatus(200);
    }

    public function test_create_thread_route_with_an_empty_text(): void
    {
        $group = $this->createFactoryGroup($this->authorizedUser->id);
        $tweets = [];
        for ($i = 0; $i < 5; $i++) {
            $tweets[] = [
                'text' => $this->generateText()
            ];
        }

        unset($tweets[4]['text']);

        $response = $this->postJson(
            route('createThread'),
            [
                'tweets' => $tweets,
                'userGroupId' => $group->id
            ]
        );

        $response->assertStatus(422);
    }

    public function test_create_thread_route_invalid_group_id(): void
    {
        $this->createFactoryGroup($this->authorizedUser->id);
        $groupId = UserGroup::latest()->first()->id + 10;
        $tweets = [];
        for ($i = 0; $i < 5; $i++) {
            $tweets[] = [
                'text' => $this->generateText()
            ];
        }

        $response = $this->postJson(
            route('createThread'),
            [
                'tweets' => $tweets,
                'userGroupId' => $groupId
            ]
        );

        $response->assertStatus(422);
    }

    public function test_create_thread_route_with_any_type(): void
    {
        $type = 'repost';
        $group = $this->createFactoryGroup($this->authorizedUser->id);
        $tweets = [];
        for ($i = 0; $i < 5; $i++) {
            $tweets[] = [
                'type' => $type,
                'text' => $this->generateText()
            ];
        }

        $response = $this->postJson(
            route('createThread'),
            [
                'tweets' => $tweets,
                'userGroupId' => $group->id
            ]
        );

        $response->assertStatus(200);
    }

    public function test_delete_tweet_basic(): void
    {
        $tweet = $this->createTweet([
            'user_id' => $this->authorizedUser->id
        ]);

        $response = $this->delete(
            route('deleteTweet', ['tweet' => $tweet->id]),
        );

        $response->assertStatus(200);
    }

    public function test_delete_tweet_invalid_request_target(): void
    {
        $response = $this->delete(
            route('deleteTweet', ['tweet' => $this->getInvalidTweetId()]),
        );

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function test_delete_tweet_of_another_user(): void
    {
        $tweet = $this->createTweet([
            'user_id' => $this->secondUser->id
        ]);

        $response = $this->delete(
            route('deleteTweet', ['tweet' => $tweet->id]),
        );

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_show_tweet_route_basic(): void
    {
        $tweetId = $this->createTweet()->id;
        $tweetData = Tweet::find($tweetId)->with('replies')->first();
        $response = $this->get(
            route('getTweetById', ['tweet' => $tweetData->id])
        );

        $createdResource = TweetResource::make($tweetData)->resolve();
        $response->assertStatus(200)->assertJson($createdResource);
    }

    public function test_show_tweet_route_invalid_request_target(): void
    {
        $response = $this->get(
            route('getTweetById', ['tweet' => $this->getInvalidTweetId()])
        );

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function test_get_bookmarks_route_basic(): void
    {
        $tweetId = $this->createTweet()->id;
        $this->post(route('addTweetToBookmarks', ['tweet' => $tweetId]));

        $response = $this->get(
            route('getAuthorizedUserBookmarks')
        );

        // Заготовка для предпологаемых состояний
        $tweetData = Tweet::find($tweetId)->first();
        $tweetData->isFavorite = true;
        $tweetData->favorites_count = empty($tweetData->favorites_count) ? 1 : $tweetData->favorites_count + 1;

        $createdResource = TweetResource::collection([$tweetData])->resolve();
        $response->assertStatus(200)->assertJson($createdResource);
    }

    public function test_get_bookmarks_route_empty(): void
    {
        $response = $this->get(
            route('getAuthorizedUserBookmarks')
        );

        $bookmarks = new Collection();
        $createdResource = TweetResource::collection($bookmarks)->resolve();
        $response->assertStatus(200)->assertJson($createdResource);
    }

    public function test_get_user_tweets_basic(): void
    {
        $tweetsCount = 3;
        $this->createTweets([
            'user_id' => $this->authorizedUser->id
        ], $tweetsCount);

        $response = $this->getJson(
            route('getUserTweets', ['user' => $this->authorizedUser->id])
        );

        $response->assertStatus(200)->assertJsonCount($tweetsCount);
    }

    public function test_get_user_tweets_empty(): void
    {
        $response = $this->getJson(
            route('getUserTweets', ['user' => $this->authorizedUser->id])
        );

        $response->assertStatus(200)->assertJsonCount(0);
    }

    public function test_get_user_replies_basic(): void
    {
        $defaultTweetsCount = 2;
        $this->createTweets([
            'user_id' => $this->authorizedUser->id
        ], $defaultTweetsCount);

        $repliesCount = 3;
        $this->createTweets([
            'user_id' => $this->authorizedUser->id,
            'type' => 'reply'
        ], $repliesCount);

        $response = $this->getJson(
            route('getUserReplies', ['user' => $this->authorizedUser->id])
        );

        $response->assertStatus(200)->assertJsonCount($repliesCount);
    }

    public function test_get_user_replies_empty(): void
    {
        $defaultTweetsCount = 2;
        $this->createTweets([
            'user_id' => $this->authorizedUser->id
        ], $defaultTweetsCount);

        $response = $this->getJson(
            route('getUserReplies', ['user' => $this->authorizedUser->id])
        );

        $response->assertStatus(200)->assertJsonCount(0);
    }

    public function test_get_user_likes_basic(): void
    {
        $tweetId = $this->createTweet()->id;
        $this->post(route('likeTweet', ['tweet' => $tweetId]));

        $response = $this->get(
            route('getUserLikes', ['user' => $this->authorizedUser->id])
        );

        // Заготовка для предпологаемых состояний
        $tweetData = Tweet::find($tweetId)->first();
        $tweetData->isLiked = true;
        $tweetData->likes_count = empty($tweetData->likes_count) ? 1 : $tweetData->likes_count + 1;

        $createdResource = TweetResource::collection([$tweetData])->resolve();
        $response->assertStatus(200)->assertJson($createdResource);
    }

    public function test_get_user_likes_empty(): void
    {
        $response = $this->getJson(
            route('getUserLikes', ['user' => $this->authorizedUser->id])
        );

        $response->assertStatus(200)->assertJsonCount(0);
    }

    public function test_get_user_feed_route_basic(): void
    {
        $secondUserTweetsCount = 3;
        $thirdUserTweetsCount = 2;

        $this->createTweets([
            'user_id' => $this->secondUser->id
        ], $secondUserTweetsCount);
        $this->createTweets([
            'user_id' => $this->thirdUser->id
        ], $thirdUserTweetsCount);

        $this->post(route('subscribeOnUser', ['user' => $this->secondUser->id]));

        $response = $this->getJson(route('getUserFeed'));
        $response->assertStatus(200)->assertJsonCount($secondUserTweetsCount);
    }

    public function test_get_user_feed_route_empty_subscribtions(): void
    {
        $response = $this->getJson(route('getUserFeed'));
        $response->assertStatus(200)->assertJsonCount(0);
    }

    public function test_get_user_feed_route_empty_tweets(): void
    {
        $this->post(route('subscribeOnUser', ['user' => $this->secondUser->id]));
        $this->post(route('subscribeOnUser', ['user' => $this->thirdUser->id]));

        $response = $this->getJson(route('getUserFeed'));
        $response->assertStatus(200)->assertJsonCount(0);
    }

    public function test_get_list_tweets_route_basic(): void
    {
        $usersList = UsersList::factory()->create([
            'user_id' => $this->authorizedUser->id,
            'is_private' => false,
        ]);

        $this->postJson(route('addMemberToUsersList', [
            'usersList' => $usersList->id,
            'user' => $this->authorizedUser->id,
        ]));

        $authorizedUserTweetsCount = 3;
        $secondUserTweetsCount = 2;

        $this->createTweets([
            'user_id' => $this->authorizedUser->id
        ], $authorizedUserTweetsCount);
        $this->createTweets([
            'user_id' => $this->secondUser->id
        ], $secondUserTweetsCount);

        $response = $this->getJson(route('getUsersListTweets', ['usersList' => $usersList->id]));

        $response->assertStatus(200)->assertJsonCount($authorizedUserTweetsCount);
    }

    public function test_get_list_tweets_route_empty_members(): void
    {
        $usersList = UsersList::factory()->create([
            'user_id' => $this->authorizedUser->id,
            'is_private' => false,
        ]);

        $authorizedUserTweetsCount = 3;
        $secondUserTweetsCount = 2;

        $this->createTweets([
            'user_id' => $this->authorizedUser->id
        ], $authorizedUserTweetsCount);
        $this->createTweets([
            'user_id' => $this->secondUser->id
        ], $secondUserTweetsCount);

        $response = $this->getJson(route('getUsersListTweets', ['usersList' => $usersList->id]));

        $response->assertStatus(200)->assertJsonCount(0);
    }

    public function test_get_list_tweets_route_empty_tweets(): void
    {
        $usersList = UsersList::factory()->create([
            'user_id' => $this->authorizedUser->id,
            'is_private' => false,
        ]);

        $this->postJson(route('addMemberToUsersList', [
            'usersList' => $usersList->id,
            'user' => $this->authorizedUser->id,
        ]));

        $response = $this->getJson(route('getUsersListTweets', ['usersList' => $usersList->id]));
        $response->assertStatus(200)->assertJsonCount(0);
    }

    public function test_get_list_tweets_route_private_list_allowed(): void
    {
        $usersList = UsersList::factory()->create([
            'user_id' => $this->authorizedUser->id,
            'is_private' => false,
        ]);

        $this->actingAs($this->secondUser, 'api');
        $this->postJson(route('subscribeToUsersList', [
            'usersList' => $usersList->id,
        ]));

        // Т.е. закрываем список уже после подписки (иначе на него никак не подписаться)
        $usersList->is_private = true;
        $usersList->save();

        $response = $this->getJson(route('getUsersListTweets', ['usersList' => $usersList->id]));
        $response->assertStatus(200)->assertJsonCount(0);
    }

    public function test_get_list_tweets_route_private_list_private(): void
    {
        $usersList = UsersList::factory()->create([
            'user_id' => $this->authorizedUser->id,
            'is_private' => true,
        ]);

        $this->actingAs($this->secondUser, 'api');
        $response = $this->getJson(route('getUsersListTweets', ['usersList' => $usersList->id]));
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }
}
