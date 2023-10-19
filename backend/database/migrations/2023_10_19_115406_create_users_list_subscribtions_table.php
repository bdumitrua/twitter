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
        Schema::create('users_list_subscribtions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger(USER_ID);
            $table->unsignedBigInteger('users_list_id');
            $table->timestamps();

            $table->foreign(USER_ID)->references('id')->on('users')->onDelete('cascade');
            $table->foreign('users_list_id')->references('id')->on('users_lists')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users_list_subscribtions');
    }
};
