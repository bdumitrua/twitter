<?php

namespace Tests\Feature\Tweet;

use App\Modules\Tweet\Models\Tweet;
use App\Modules\Tweet\Resources\TweetResource;
use App\Modules\User\Models\User;
use App\Modules\User\Models\UserGroup;
use App\Modules\User\Models\UserGroupMember;
use App\Modules\User\Models\UsersList;
use App\Modules\User\Models\UsersListSubscribtion;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;
use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;

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

    protected function createGroup(array $data = []): UserGroup
    {
        if (empty($data['user_id'])) {
            $data['user_id'] = $this->authorizedUser->id;
        }

        return UserGroup::factory()->create($data);
    }

    public function testCreateDefaultTweetRouteBasic(): void
    {
        $text = $this->generateText();
        $group = $this->createGroup();

        $response = $this->postJson(
            route('createTweet'),
            [
                'text' => $text,
                'userGroupId' => $group->id
            ]
        );

        $response->assertStatus(Response::HTTP_OK);
    }

    public function testCreateDefaultTweetRouteWithEmptyText(): void
    {
        $text = $this->getEmptyText();
        $response = $this->postJson(
            route('createTweet'),
            ['text' => $text]
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testCreateDefaultTweetRouteWithInvalidGroupId(): void
    {
        $text = $this->generateText();

        $this->createGroup();
        $groupId = UserGroup::latest()->first()->id + 10;

        $response = $this->postJson(
            route('createTweet'),
            [
                'text' => $text,
                'userGroupId' => $groupId
            ]
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testRepostTweetBasic(): void
    {
        $tweetToRepost = $this->createTweet();
        $response = $this->postJson(
            route('repostTweet', ['tweet' => $tweetToRepost->id]),
        );

        $response->assertStatus(Response::HTTP_OK);
    }

    public function testRepostTweetInvalidRequestTarget(): void
    {
        $tweetId = $this->getInvalidTweetId();
        $response = $this->postJson(
            route('repostTweet', ['tweet' => $tweetId]),
        );

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testUnrepostTweetBasic(): void
    {
        $tweet = $this->createRepost();
        $response = $this->delete(
            route('unrepostTweet', ['tweet' => $tweet->linked_tweet_id]),
        );

        $response->assertStatus(Response::HTTP_OK);
    }

    public function testUnrepostTweetInvalidRequestTarget(): void
    {
        $response = $this->delete(
            route('unrepostTweet', ['tweet' => $this->getInvalidTweetId()]),
        );

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }


    public function testUnrepostTweetWithoutRepost(): void
    {
        $tweet = $this->createTweet();
        $response = $this->delete(
            route('unrepostTweet', ['tweet' => $tweet->id]),
        );

        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }

    public function testCreateReplyTweetRouteBasic(): void
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

        $response->assertStatus(Response::HTTP_OK);
    }

    public function testCreateReplyTweetRouteWithEmptyText(): void
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

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testCreateReplyTweetRouteInvalidLinkedTweetId(): void
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

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testCreateQuoteTweetRouteBasic(): void
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

        $response->assertStatus(Response::HTTP_OK);
    }

    public function testCreateQuoteTweetRouteWithEmptyText(): void
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

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testCreateQuoteTweetRouteInvalidLinkedTweetId(): void
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

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testCreateThreadRouteBasic(): void
    {
        $group = $this->createGroup();
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

        $response->assertStatus(Response::HTTP_OK);
    }

    public function testCreateThreadRouteWithAnEmptyText(): void
    {
        $group = $this->createGroup();
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

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testCreateThreadRouteInvalidGroupId(): void
    {
        $this->createGroup();
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

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testCreateThreadRouteWithAnyType(): void
    {
        $type = 'repost';
        $group = $this->createGroup();
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

        $response->assertStatus(Response::HTTP_OK);
    }

    public function testDeleteTweetBasic(): void
    {
        $tweet = $this->createTweet([
            'user_id' => $this->authorizedUser->id
        ]);

        $response = $this->delete(
            route('deleteTweet', ['tweet' => $tweet->id]),
        );

        $response->assertStatus(Response::HTTP_OK);
    }

    public function testDeleteTweetInvalidRequestTarget(): void
    {
        $response = $this->delete(
            route('deleteTweet', ['tweet' => $this->getInvalidTweetId()]),
        );

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testDeleteTweetOfAnotherUser(): void
    {
        $tweet = $this->createTweet([
            'user_id' => $this->secondUser->id
        ]);

        $response = $this->delete(
            route('deleteTweet', ['tweet' => $tweet->id]),
        );

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testShowTweetRouteBasic(): void
    {
        $tweetId = $this->createTweet()->id;
        $tweetData = Tweet::find($tweetId)->with('replies')->first();
        $response = $this->get(
            route('getTweetById', ['tweet' => $tweetData->id])
        );

        $createdResource = TweetResource::make($tweetData)->resolve();
        $response->assertStatus(Response::HTTP_OK)->assertJson($createdResource);
    }

    public function testShowTweetRouteInvalidRequestTarget(): void
    {
        $response = $this->get(
            route('getTweetById', ['tweet' => $this->getInvalidTweetId()])
        );

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testShowTweetRouteGroupAuthor(): void
    {
        $group = $this->createGroup([
            'user_id' => $this->authorizedUser->id
        ]);

        $tweetId = $this->createTweet([
            'user_id' => $this->authorizedUser->id,
            'user_group_id' => $group->id,
        ])->id;

        $response = $this->get(
            route('getTweetById', ['tweet' => $tweetId])
        );

        $response->assertStatus(Response::HTTP_OK);
    }

    public function testShowTweetRouteGroupAccessAllowed(): void
    {
        $group = $this->createGroup([
            'user_id' => $this->secondUser->id
        ]);

        UserGroupMember::factory()->create([
            'user_group_id' => $group->id,
            'user_id' => $this->authorizedUser->id
        ]);

        $tweetId = $this->createTweet([
            'user_id' => $this->secondUser->id,
            'user_group_id' => $group->id,
        ])->id;

        $response = $this->get(
            route('getTweetById', ['tweet' => $tweetId])
        );

        $response->assertStatus(Response::HTTP_OK);
    }

    public function testShowTweetRouteGroupAccessDenied(): void
    {
        $group = $this->createGroup([
            'user_id' => $this->secondUser->id
        ]);

        $tweetId = $this->createTweet([
            'user_id' => $this->secondUser->id,
            'user_group_id' => $group->id,
        ])->id;

        $response = $this->get(
            route('getTweetById', ['tweet' => $tweetId])
        );

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testShowTweetRouteLinkedGroupAccessDenied(): void
    {
        $group = $this->createGroup([
            'user_id' => $this->secondUser->id
        ]);

        $linkedTweet = $this->createTweet([
            'user_id' => $this->secondUser->id,
            'user_group_id' => $group->id
        ]);

        $tweetId = $this->createTweet([
            'type' => 'reply',
            'user_id' => $this->secondUser->id,
            'linked_tweet_id' => $linkedTweet->id
        ]);

        $response = $this->get(
            route('getTweetById', ['tweet' => $tweetId])
        );

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testGetBookmarksRouteBasic(): void
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
        $response->assertStatus(Response::HTTP_OK)->assertJson($createdResource);
    }

    public function testGetBookmarksRouteEmpty(): void
    {
        $response = $this->get(
            route('getAuthorizedUserBookmarks')
        );

        $bookmarks = new Collection();
        $createdResource = TweetResource::collection($bookmarks)->resolve();
        $response->assertStatus(Response::HTTP_OK)->assertJson($createdResource);
    }

    public function testGetUserTweetsBasic(): void
    {
        $tweetsCount = 3;
        $this->createTweets([
            'user_id' => $this->authorizedUser->id
        ], $tweetsCount);

        $response = $this->getJson(
            route('getUserTweets', ['user' => $this->authorizedUser->id])
        );

        $response->assertStatus(Response::HTTP_OK)->assertJsonCount($tweetsCount);
    }

    public function testGetUserTweetsEmpty(): void
    {
        $response = $this->getJson(
            route('getUserTweets', ['user' => $this->authorizedUser->id])
        );

        $response->assertStatus(Response::HTTP_OK)->assertJsonCount(0);
    }

    public function testGetUserRepliesBasic(): void
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

        $response->assertStatus(Response::HTTP_OK)->assertJsonCount($repliesCount);
    }

    public function testGetUserRepliesEmpty(): void
    {
        $defaultTweetsCount = 2;
        $this->createTweets([
            'user_id' => $this->authorizedUser->id
        ], $defaultTweetsCount);

        $response = $this->getJson(
            route('getUserReplies', ['user' => $this->authorizedUser->id])
        );

        $response->assertStatus(Response::HTTP_OK)->assertJsonCount(0);
    }

    public function testGetUserLikesBasic(): void
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
        $response->assertStatus(Response::HTTP_OK)->assertJson($createdResource);
    }

    public function testGetUserLikesEmpty(): void
    {
        $response = $this->getJson(
            route('getUserLikes', ['user' => $this->authorizedUser->id])
        );

        $response->assertStatus(Response::HTTP_OK)->assertJsonCount(0);
    }

    public function testGetUserFeedRouteBasic(): void
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
        $response->assertStatus(Response::HTTP_OK)->assertJsonCount($secondUserTweetsCount);
    }

    public function testGetUserFeedRouteEmptySubscribtions(): void
    {
        $response = $this->getJson(route('getUserFeed'));
        $response->assertStatus(Response::HTTP_OK)->assertJsonCount(0);
    }

    public function testGetUserFeedRouteEmptyTweets(): void
    {
        $this->post(route('subscribeOnUser', ['user' => $this->secondUser->id]));
        $this->post(route('subscribeOnUser', ['user' => $this->thirdUser->id]));

        $response = $this->getJson(route('getUserFeed'));
        $response->assertStatus(Response::HTTP_OK)->assertJsonCount(0);
    }

    public function testGetListTweetsRouteBasic(): void
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

        $response
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonCount($authorizedUserTweetsCount);
    }

    public function testGetListTweetsRouteEmptyMembers(): void
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

        $response->assertStatus(Response::HTTP_OK)->assertJsonCount(0);
    }

    public function testGetListTweetsRouteEmptyTweets(): void
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
        $response->assertStatus(Response::HTTP_OK)->assertJsonCount(0);
    }

    public function testGetListTweetsRoutePrivateListAllowed(): void
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
        $response->assertStatus(Response::HTTP_OK)->assertJsonCount(0);
    }

    public function testGetListTweetsRoutePrivateListPrivate(): void
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
