<?php

namespace App\Providers;

use App\Kafka\KafkaConsumer;
use App\Prometheus\PrometheusService;
use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

        DB::listen(function ($query) {
            /** @var PrometheusService */
            $prometheusService = app(PrometheusService::class);
            $source = optional(request()->route())->getActionName() ?? 'unknown';
            $executionTimeInSeconds = floatval($query->time) / 1000;

            $prometheusService->incrementDatabaseQueryCount($source);
            $prometheusService->addDatabaseQueryTimeHistogram($executionTimeInSeconds, $source);
        });

        $this->defineCacheKeysConstants();
    }

    private function defineCacheKeysConstants(): void
    {
        /*
        *   Общие ключи
        */

        define('KEY_WITH_RELATIONS', 'with_relations:');

        define('KEY_TWEET_THREAD_START_ID', 'tweet_thread_start_id:');

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

        // Массив данных недавнего поиска
        define('KEY_USER_SEARCH', 'user_search:');

        // Массив ответов пользователя
        define('KEY_USER_REPLIES', 'user_replies:');

        // Массив твиттов с медиа пользователя
        define('KEY_USER_MEDIA_TWEETS', 'user_media_tweets:');

        // Массив твиттов лайкнутых пользователем
        define('KEY_USER_LIKED_TWEETS', 'user_liked_tweets:');

        // Массив групп пользователя
        define('KEY_USER_GROUPS', 'user_groups:');

        // Массив id списков, в которых пользователь создатель/подписчик
        define('KEY_USER_LISTS', 'user_lists:');

        // Массив избранных твиттов пользователя
        define('KEY_USER_FAVORITES', 'auth_user_favorites:');

        // Массив избранных твиттов пользователя
        define('KEY_USER_LIKES', 'auth_user_likes:');

        // Массив избранных твиттов пользователя
        define('KEY_USER_NOTIFICATIONS', 'auth_user_notifications:');

        /*
        *   Ключи связанные с данными списка
        */

        // Данные списка
        define('KEY_USERS_LIST_DATA', 'users_list_data:');

        // Данные списка (включая кол-во подписчиков и участников)
        define('KEY_USERS_LIST_SHOW_DATA', 'users_list_show_data:');

        // Лента списка
        define('KEY_USERS_LIST_FEED', 'users_list_feed:');
    }
}
