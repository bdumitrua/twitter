<?php

namespace App\Providers;

use App\Modules\Auth\Events\PasswordResetStartedEvent;
use App\Modules\Auth\Events\RegistrationStartedEvent;
use App\Modules\Auth\Listeners\PasswordResetStartedListener;
use App\Modules\Auth\Listeners\RegistrationStartedListener;
use App\Modules\Tweet\Events\NewTweetEvent;
use App\Modules\Tweet\Events\TweetFavoriteEvent;
use App\Modules\Tweet\Events\TweetLikeEvent;
use App\Modules\Tweet\Events\TweetNoticeEvent;
use App\Modules\Tweet\Events\TweetReplyEvent;
use App\Modules\Tweet\Events\TweetRepostEvent;
use App\Modules\Tweet\Listeners\NewLikesListener;
use App\Modules\Tweet\Listeners\NewNoticeListener;
use App\Modules\Tweet\Listeners\NewTweetsListener;
use App\Modules\User\Events\DeletedUsersListEvent;
use App\Modules\User\Events\UserGroupMembersUpdateEvent;
use App\Modules\User\Events\UsersListMembersUpdateEvent;
use App\Modules\User\Events\UsersListSubscribtionEvent;
use App\Modules\User\Events\UserSubscribtionEvent;
use App\Modules\User\Listeners\DeletedUsersListsListener;
use App\Modules\User\Listeners\NewSubscribtionsListener;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        // Auth
        RegistrationStartedEvent::class => [
            RegistrationStartedListener::class
        ],
        PasswordResetStartedEvent::class => [
            PasswordResetStartedListener::class
        ],

        // Users
        UserSubscribtionEvent::class => [
            NewSubscribtionsListener::class
        ],
        UserGroupMembersUpdateEvent::class => [
            // 
        ],
        DeletedUsersListEvent::class => [
            DeletedUsersListsListener::class,
        ],
        UsersListSubscribtionEvent::class => [
            // 
        ],
        UsersListMembersUpdateEvent::class => [
            // 
        ],

        // Tweets
        NewTweetEvent::class => [
            NewTweetsListener::class,
        ],
        TweetNoticeEvent::class => [
            NewNoticeListener::class
        ],
        TweetLikeEvent::class => [
            NewLikesListener::class
        ],
        TweetFavoriteEvent::class => [
            // 
        ],
        TweetReplyEvent::class => [
            // 
        ],
        TweetRepostEvent::class => [
            // 
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
