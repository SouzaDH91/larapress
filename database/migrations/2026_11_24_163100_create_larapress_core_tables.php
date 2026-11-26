<?php

declare(strict_types=1);

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
        // 1. media
        Schema::create('media', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('filename');
            $table->string('disk')->default('public');
            $table->string('path');
            $table->string('mime_type');
            $table->unsignedBigInteger('size')->default(0);
            $table->string('alt_text')->nullable();
            $table->string('title')->nullable();
            $table->text('caption')->nullable();
            $table->json('metadata_json')->nullable();
            $table->timestamps();
        });

        // 2. options
        Schema::create('options', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('option_name')->unique();
            $table->longText('option_value')->nullable();
            $table->boolean('autoload')->default(true);
            $table->timestamps();
        });

        // 3. terms
        Schema::create('terms', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('slug')->index();
            $table->text('description')->nullable();
            $table->foreignUuid('media_id')->nullable()->constrained('media')->nullOnDelete();
            $table->timestamps();
        });

        // 4. term_taxonomies
        Schema::create('term_taxonomies', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('term_id')->constrained('terms')->cascadeOnDelete();
            $table->string('taxonomy')->default('category')->index(); // category, post_tag, custom
            $table->uuid('parent_id')->nullable()->index();
            $table->unsignedBigInteger('count')->default(0);
            $table->timestamps();
        });

        // 5. posts
        Schema::create('posts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('author_id')->constrained('users')->cascadeOnDelete();
            $table->uuid('parent_id')->nullable()->index();
            $table->string('title');
            $table->string('slug')->index();
            $table->text('excerpt')->nullable();
            $table->json('content_json')->nullable();
            $table->longText('content_html')->nullable();
            $table->string('status')->default('draft')->index(); // draft, pending, published, private, password_protected, trash
            $table->string('type')->default('post')->index(); // post, page, cpt
            $table->string('password')->nullable();
            $table->string('comment_status')->default('open'); // open, closed
            $table->string('ping_status')->default('open');
            $table->integer('menu_order')->default(0);
            $table->foreignUuid('featured_media_id')->nullable()->constrained('media')->nullOnDelete();
            $table->timestamp('published_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        // 6. post_meta
        Schema::create('post_meta', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('post_id')->constrained('posts')->cascadeOnDelete();
            $table->string('meta_key')->index();
            $table->longText('meta_value')->nullable();
            $table->timestamps();
        });

        // 7. post_revisions
        Schema::create('post_revisions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('post_id')->constrained('posts')->cascadeOnDelete();
            $table->foreignUuid('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->json('content_json')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        // 8. post_term
        Schema::create('post_term', function (Blueprint $table) {
            $table->foreignUuid('post_id')->constrained('posts')->cascadeOnDelete();
            $table->foreignUuid('term_taxonomy_id')->constrained('term_taxonomies')->cascadeOnDelete();
            $table->primary(['post_id', 'term_taxonomy_id']);
        });

        // 9. comments
        Schema::create('comments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('post_id')->constrained('posts')->cascadeOnDelete();
            $table->foreignUuid('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->uuid('parent_id')->nullable()->index();
            $table->string('author_name')->nullable();
            $table->string('author_email')->nullable();
            $table->string('author_ip', 45)->nullable();
            $table->text('content');
            $table->string('status')->default('approved')->index(); // approved, pending, spam, trash
            $table->timestamps();
        });

        // 10. menus
        Schema::create('menus', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('location_slug')->nullable()->index();
            $table->timestamps();
        });

        // 11. menu_items
        Schema::create('menu_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('menu_id')->constrained('menus')->cascadeOnDelete();
            $table->uuid('parent_id')->nullable()->index();
            $table->string('title');
            $table->string('url')->nullable();
            $table->string('linkable_type')->nullable();
            $table->uuid('linkable_id')->nullable();
            $table->string('target')->default('_self');
            $table->string('css_class')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        // 12. plugins
        Schema::create('plugins', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('slug')->unique();
            $table->string('name');
            $table->string('version')->default('1.0.0');
            $table->boolean('is_active')->default(false);
            $table->json('settings_json')->nullable();
            $table->timestamps();
        });

        // 13. activity_logs
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action');
            $table->string('subject_type')->nullable();
            $table->uuid('subject_id')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->json('properties_json')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('plugins');
        Schema::dropIfExists('menu_items');
        Schema::dropIfExists('menus');
        Schema::dropIfExists('comments');
        Schema::dropIfExists('post_term');
        Schema::dropIfExists('post_revisions');
        Schema::dropIfExists('post_meta');
        Schema::dropIfExists('posts');
        Schema::dropIfExists('term_taxonomies');
        Schema::dropIfExists('terms');
        Schema::dropIfExists('options');
        Schema::dropIfExists('media');
    }
};
