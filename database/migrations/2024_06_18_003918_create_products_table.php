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
        Schema::create('products', static function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('producer_id');
            $table->unsignedBigInteger('category_id');
            $table->string('name', 191);
            $table->string('image', 191)->nullable();
            $table->string('sku_code', 191);
//            $table->string('slug', 191);
            $table->string('monitor', 191)->default('Đang cập nhật...');
            $table->string('front_camera', 191)->default('Đang cập nhật...');
            $table->string('rear_camera', 191)->default('Đang cập nhật...');
            $table->string('CPU', 191)->default('Đang cập nhật...');
            $table->string('GPU', 191)->default('Đang cập nhật...');
            $table->integer('RAM')->default(0);
            $table->integer('ROM')->default(0);
            $table->string('OS', 191)->default('Đang cập nhật...');
            $table->string('pin', 191)->default('Đang cập nhật...');
            $table->longText('information_details')->nullable();
            $table->longText('product_introduction')->nullable();
            $table->double('rate', 2, 1)->default(0.0);
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
        Schema::dropIfExists('products');
    }
};
