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
        Schema::table('tweet_favorites', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('tweet_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tweet_favorites', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['tweet_id']);
        });
    }
};
