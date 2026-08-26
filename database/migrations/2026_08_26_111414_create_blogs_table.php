<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blogs', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->longText('content')->nullable();
            $table->text('excerpt')->nullable();
            $table->string('featured_image')->nullable();
            $table->string('image_alt')->nullable();
            $table->string('author_name')->nullable();
            $table->text('author_profile')->nullable();
            $table->string('status')->default('draft'); // draft|review|scheduled|published
            $table->string('content_type')->nullable(); // guide|news|case_study|technical|comparison|product_education
            $table->boolean('is_active')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_sticky')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->string('canonical_url')->nullable();
            $table->string('link_attribute')->default('dofollow'); // dofollow|nofollow|sponsored|ugc
            $table->longText('custom_schema')->nullable();
            $table->text('heading_structure')->nullable();
            $table->longText('table_of_contents')->nullable();
            $table->json('faq')->nullable();
            $table->json('related_blog_ids')->nullable();
            $table->json('related_products')->nullable();
            $table->timestamps();

            $table->index(['is_active', 'status', 'published_at']);
            $table->index('slug');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blogs');
    }
};
