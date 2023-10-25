<?php

namespace App\Providers;

use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(Client::class, function () {
            return ClientBuilder::create()
                ->setHosts([config('services.elasticsearch.host')])
                ->build();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->defineCacheKeysConstants();
    }

    private function defineCacheKeysConstants(): void
    {
        /*
        *   Общие ключи
        */

        define('KEY_WITH_RELATIONS', 'with_relations:');

        define('KEY_TWEET_DATA', 'tweet_data:');

        /*
        *   Ключи связанные напрямую с авторизованным пользователем
        */

        // Базовые данные о авторизованном пользователе 
        // Включают в себя данные пользователя и списки где он создатель/подписчик (для ленты)
        define('KEY_AUTH_USER_DATA', 'auth_user_data:');

        // Лента текущего пользователя
        define('KEY_AUTH_USER_FEED', 'auth_user_feed:');

        /*
        *   Ключи связанные с данными аккаунта
        */

        // Базовые данные при просмотре профиля
        // Включают в себя только данные пользователя (имя, ссылка и т.п.)
        define('KEY_USER_DATA', 'user_data:');

        // Массив твиттов пользователя
        define('KEY_USER_TWEETS', 'user_tweets:');

        // Массив групп пользователя
        define('KEY_USER_GROUPS', 'user_groups:');

        // Массив списков, в которых пользователь создатель/подписчик
        define('KEY_USER_LISTS', 'user_lists:');

        // Массив избранных твиттов пользователя
        define('KEY_USER_FAVORITES', 'auth_user_favorites:');

        // Массив избранных твиттов пользователя
        define('KEY_USER_LIKES', 'auth_user_likes:');

        /*
        *   Ключи связанные с данными списка
        */

        // Данные списка (включая подписчиков и участников)
        define('KEY_USERS_LIST_DATA', 'users_list_data:');

        // Лента списка
        define('KEY_USERS_LIST_FEED', 'users_list_feed:');
    }
}
