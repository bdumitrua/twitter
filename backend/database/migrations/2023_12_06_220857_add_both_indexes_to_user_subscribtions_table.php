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
        Schema::table('user_subscribtions', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('subscriber_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_subscribtions', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['subscriber_id']);
        });
    }
};
