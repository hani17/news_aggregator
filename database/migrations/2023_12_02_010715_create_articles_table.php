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
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->dateTime('published_at');
            $table->string('url', 2048)->nullable();
            $table->string('image_url', 2048)->nullable();
            $table->foreignId('category_id')
                ->nullable()
                ->references('id')
                ->on('categories');
            $table->foreignId('source_id')
                ->nullable()
                ->references('id')
                ->on('sources');
            $table->string('hash')->unique();
            $table->string('api_id', 2048)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
