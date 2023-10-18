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
        $this->defineConstants();
    }

    private function defineConstants(): void
    {
        define('USER_ID', 'user_id');
        define('SUBSCRIBER_ID', 'subscriber_id');
        define('USER_GROUP_ID', 'user_group_id');
        define('NAME', 'name');
        define('DESCRIPTION', 'description');
    }
}
