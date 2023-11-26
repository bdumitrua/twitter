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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('link');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('bg_image')->nullable();
            $table->string('avatar')->nullable();
            $table->string('status_text')->nullable();
            $table->string('site_url')->nullable();
            $table->string('address')->nullable();
            $table->timestamp('birth_date');
            $table->timestamps();

            $table->softDeletes();
        });

        // try {
        //     $client = ClientBuilder::create()
        //         ->setHosts([config('services.elasticsearch.host')])
        //         ->build();

        //     $params = [
        //         'index' => 'users',
        //         'body' => [
        //             'mappings' => [
        //                 'properties' => [
        //                     'name' => [
        //                         'type' => 'text'
        //                     ],
        //                     'link' => [
        //                         'type' => 'text'
        //                     ],
        //                 ]
        //             ]
        //         ]
        //     ];

        //     $client->indices()->create($params);
        // } catch (\Elastic\Elasticsearch\Exception\ClientResponseException $e) {
        // }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Удаление таблицы пользователей из базы данных
        Schema::dropIfExists('users');

        // Удаление индекса из Elasticsearch
        $client = ClientBuilder::create()
            ->setHosts([config('services.elasticsearch.host')])
            ->build();

        $params = ['index' => 'users'];
        $client->indices()->delete($params);
    }
};
