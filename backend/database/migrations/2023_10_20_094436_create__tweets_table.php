<?php

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
        Schema::create('tweets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('user_group_id')->nullable();
            $table->string('text');
            $table->boolean('is_comment')->default(false);
            $table->unsignedBigInteger('commented_tweet_id')->nullable();
            $table->boolean('is_reply')->default(false);
            $table->unsignedBigInteger('replied_tweet_id')->nullable();
            $table->boolean('is_repost')->default(false);
            $table->unsignedBigInteger('reposted_tweet_id')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('user_group_id')->references('id')->on('user_groups')->onDelete('cascade');
            $table->foreign('commented_tweet_id')->references('id')->on('tweets')->onDelete('cascade');
            $table->foreign('replied_tweet_id')->references('id')->on('tweets')->onDelete('cascade');
            $table->foreign('reposted_tweet_id')->references('id')->on('tweets')->onDelete('cascade');

            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tweets');
    }
};
