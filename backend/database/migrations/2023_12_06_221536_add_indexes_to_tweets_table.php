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
        Schema::table('tweets', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('user_group_id');
            $table->index('linked_tweet_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tweets', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['user_group_id']);
            $table->dropIndex(['linked_tweet_id']);
        });
    }
};
