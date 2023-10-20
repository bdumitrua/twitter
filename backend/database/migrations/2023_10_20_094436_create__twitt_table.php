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
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('user_group_id')->nullable();
            $table->string('text');
            $table->integer('likes_count')->default(0);
            $table->integer('reposts_count')->default(0);
            $table->integer('replies_count')->default(0);
            $table->integer('favorites_count')->default(0);
            $table->boolean('is_comment')->default(false);
            $table->unsignedBigInteger('commented_twitt_id')->nullable();
            $table->boolean('is_quoute')->default(false);
            $table->unsignedBigInteger('quoted_twitt_id')->nullable();
            $table->boolean('is_repost')->default(false);
            $table->unsignedBigInteger('reposted_twitt_id')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('user_group_id')->references('id')->on('user_groups')->onDelete('cascade');
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
