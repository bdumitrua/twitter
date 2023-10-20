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
        Schema::create('twitts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger(USER_ID);
            $table->unsignedBigInteger(USER_GROUP_ID);
            $table->string('text');
            $table->integer('likes_count');
            $table->integer('reposts_count');
            $table->integer('replies_count');
            $table->integer('favorites_count');
            $table->boolean('is_comment');
            $table->unsignedBigInteger('commented_twitt_id');
            $table->boolean('is_quoute');
            $table->unsignedBigInteger('quoted_twitt_id');
            $table->boolean('is_repost');
            $table->unsignedBigInteger('reposted_twitt_id');
            $table->timestamps();

            $table->foreign(USER_ID)->references('id')->on('users')->onDelete('cascade');
            $table->foreign(USER_GROUP_ID)->references('id')->on('user_groups')->onDelete('cascade');
            $table->foreign('commented_twitt_id')->references('id')->on('twitts')->onDelete('cascade');
            $table->foreign('quoted_twitt_id')->references('id')->on('twitts')->onDelete('cascade');
            $table->foreign('reposted_twitt_id')->references('id')->on('twitts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('twitts');
    }
};
