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
        Schema::create('user_subscribtions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger(USER_ID);
            $table->unsignedBigInteger(SUBSCRIBER_ID);
            $table->timestamps();

            $table->foreign(USER_ID)->references('id')->on('users')->onDelete('cascade');
            $table->foreign(SUBSCRIBER_ID)->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_subscribtions');
    }
};
