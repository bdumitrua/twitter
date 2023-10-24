<?php

return [
    'default' => 'rabbitmq',

    'connections' => [
        'rabbitmq' => [
            'driver' => 'rabbitmq',
            'host' => 'rabbitmq',
            'port' => 5672,
            'vhost' => '/',
            'login' => 'admin',
            'password' => 'admin',
        ]
    ],

    'batching' => [
        'database' => env('DB_CONNECTION', 'mysql'),
        'table' => 'job_batches',
    ],

    'failed' => [
        'driver' => env('QUEUE_FAILED_DRIVER', 'database-uuids'),
        'database' => env('DB_CONNECTION', 'mysql'),
        'table' => 'failed_jobs',
    ],

];
