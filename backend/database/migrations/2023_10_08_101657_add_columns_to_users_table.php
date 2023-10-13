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
        Schema::table('users', function (Blueprint $table) {
            $table->integer('age')->nullable();
            $table->string('gender')->nullable();
            $table->string('profile_image')->nullable();
            $table->string('phone')->nullable();
            $table->string('profession')->nullable();
            $table->string('qualification')->nullable();
            $table->string('address')->nullable();
            $table->longText('about')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
