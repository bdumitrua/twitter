<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Log Channel
    |--------------------------------------------------------------------------
    |
    | This option defines the default log channel that gets used when writing
    | messages to the logs. The name specified in this option should match
    | one of the channels defined in the "channels" configuration array.
    |
    */

    // 'default' => env('LOG_CHANNEL', 'elasticsearch'),
    'default' => 'file',

    /*
    |--------------------------------------------------------------------------
    | Deprecations Log Channel
    |--------------------------------------------------------------------------
    |
    | This option controls the log channel that should be used to log warnings
    | regarding deprecated PHP and library features. This allows you to get
    | your application ready for upcoming major versions of dependencies.
    |
    */

    'deprecations' => [
        'channel' => env('LOG_DEPRECATIONS_CHANNEL', 'null'),
        'trace' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Log Channels
    |--------------------------------------------------------------------------
    |
    | Here you may configure the log channels for your application. Out of
    | the box, Laravel uses the Monolog PHP logging library. This gives
    | you a variety of powerful log handlers / formatters to utilize.
    |
    | Available Drivers: "single", "daily", "slack", "syslog",
    |                    "errorlog", "monolog",
    |                    "custom", "stack"
    |
    */

    'channels' => [
        // 'elasticsearch' => [
        //     'driver' => 'monolog',
        //     'handler' => Monolog\Handler\ElasticsearchHandler::class,
        //     'handler_with' => [
        //         'client' => (new Elastic\Elasticsearch\ClientBuilder())->setHosts(['elasticsearch:9200'])->build(),
        //         'options' => [
        //             'index' => 'laravel-logs',
        //             'type'  => '_doc',
        //         ],
        //     ],
        //     'formatter' => Monolog\Formatter\ElasticsearchFormatter::class,
        //     'formatter_with' => [
        //         'index' => 'laravel-logs',
        //         'type'  => '_doc',
        //     ],
        // ],

        'emergency' => [
            'driver' => 'single', // Указываем драйвер
            'path' => storage_path('logs/emergency.log'),
            'level' => 'error', // Уровень логирования, можно изменить по потребности
        ],

        'file' => [
            'driver' => 'single', // Также указываем драйвер для канала 'file'
            'path' => storage_path('logs/logs.log'),
            'level' => 'debug', // Уровень логирования
        ],
    ],

];
