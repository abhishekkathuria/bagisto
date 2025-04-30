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
        Schema::create('hero_sections', function (Blueprint $table) {
            $table->id();
            $table->string('youtube_url');
            $table->string('youtube_height');
            $table->string('youtube_width');
            $table->string('top_image_url');
            $table->string('top_image_width');
            $table->string('top_image_height');
            $table->string('top_image');
            $table->string('top_image_alt')->nullable();
            $table->string('bottom_image_url');
            $table->string('bottom_image_width');
            $table->string('bottom_image_height');
            $table->string('bottom_image');
            $table->string('bottom_image_alt')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hero_section');
    }
};
