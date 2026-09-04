<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('city_pages', function (Blueprint $table) {
            $table->id();
            $table->string('city_name');
            $table->string('state')->nullable();
            $table->string('slug')->unique();
            $table->string('template')->default('standard');
            $table->string('status')->default('draft');
            $table->string('seo_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->string('canonical_url', 500)->nullable();
            $table->string('og_title')->nullable();
            $table->text('og_description')->nullable();
            $table->string('og_image')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'city_name']);
            $table->unique('city_name');
        });

        Schema::create('city_page_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('city_page_id')->constrained('city_pages')->cascadeOnDelete();
            $table->string('section_type');
            $table->boolean('is_enabled')->default(false);
            $table->unsignedInteger('display_order')->default(0);
            $table->json('content')->nullable();
            $table->timestamps();

            $table->unique(['city_page_id', 'section_type']);
            $table->index(['city_page_id', 'is_enabled', 'display_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('city_page_sections');
        Schema::dropIfExists('city_pages');
    }
};
