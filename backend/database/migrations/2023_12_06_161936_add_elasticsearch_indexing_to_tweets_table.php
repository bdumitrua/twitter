<?php

use Elastic\Elasticsearch\ClientBuilder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        try {
            $client = ClientBuilder::create()
                ->setHosts([config('services.elasticsearch.host')])
                ->build();

            $params = [
                'index' => 'tweet',
                'body' => [
                    'mappings' => [
                        'properties' => [
                            'text' => [
                                'type' => 'text'
                            ],
                        ]
                    ]
                ]
            ];

            $client->indices()->create($params);
        } catch (\Elastic\Elasticsearch\Exception\ClientResponseException $e) {
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 
    }
};
