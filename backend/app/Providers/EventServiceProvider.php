<?php

namespace App\Providers;

use App\Modules\User\Events\UserGroupMembersUpdateEvent;
use App\Modules\User\Events\UsersListMembersUpdateEvent;
use App\Modules\User\Events\UsersListSubscribtionEvent;
use App\Modules\User\Events\UserSubscribtionEvent;
use App\Modules\User\Listeners\UpdateGroupMembersCount;
use App\Modules\User\Listeners\UpdateListMembersCount;
use App\Modules\User\Listeners\UpdateListSubscribtionCount;
use App\Modules\User\Listeners\UpdateUserSubscribtionCount;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],

        // Users
        UserSubscribtionEvent::class => [
            UpdateUserSubscribtionCount::class,
        ],
        UserGroupMembersUpdateEvent::class => [
            UpdateGroupMembersCount::class,
        ],
        UsersListSubscribtionEvent::class => [
            UpdateListSubscribtionCount::class,
        ],
        UsersListMembersUpdateEvent::class => [
            UpdateListMembersCount::class,
        ],

        // Twitts
        TwittFavoriteEvent::class => [
            UpdateTwittFavoritesCount::class,
        ],
        TwittLikeEvent::class => [
            UpdateTwittLikesCount::class,
        ],
        TwittReplyEvent::class => [
            UpdateTwittRepliesCount::class,
        ],
        TwittRepostEvent::class => [
            UpdateTwittRepostsCount::class,
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
