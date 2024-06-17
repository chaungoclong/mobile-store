<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('users', static function (Blueprint $table) {
            $table->id();
            $table->string('name', 191);
            $table->string('email', 191)->unique()->nullable();
            $table->string('phone', 191)->unique()->nullable();
            $table->string('address', 191)->nullable();
            $table->string('avatar_image', 191)->nullable();
            $table->string('provider', 191)->nullable();
            $table->string('provider_id', 191)->nullable();
            $table->boolean('admin')->default(false);
            $table->boolean('active')->default(false);
            $table->string('password', 191);
            $table->string('active_token', 191)->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
