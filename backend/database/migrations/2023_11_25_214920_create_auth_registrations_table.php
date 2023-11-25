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
        Schema::create('auth_registrations', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->boolean('confirmed')->default(false);
            $table->string('name');
            $table->string('email');
            $table->timestamp('birth_date');
            $table->timestamps();

            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auth_registrations');
    }
};
