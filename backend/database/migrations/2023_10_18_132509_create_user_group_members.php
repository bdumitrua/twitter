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
        Schema::create('user_group_members', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger(USER_GROUP_ID);
            $table->unsignedBigInteger(USER_ID);
            $table->timestamps();

            $table->foreign(USER_ID)->references('id')->on('users')->onDelete('cascade');
            $table->foreign(USER_GROUP_ID)->references('id')->on('user_groups')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_group_members');
    }
};
