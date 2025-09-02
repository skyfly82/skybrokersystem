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
        // CMS Pages table
        Schema::create('cms_pages', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title');
            $table->text('meta_description')->nullable();
            $table->text('meta_keywords')->nullable();
            $table->longText('content');
            $table->json('seo_data')->nullable();
            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->foreignId('created_by')->constrained('system_users');
            $table->foreignId('updated_by')->nullable()->constrained('system_users');
            $table->timestamps();

            $table->index(['slug', 'is_published']);
            $table->index('published_at');
        });

        // CMS Media table
        Schema::create('cms_media', function (Blueprint $table) {
            $table->id();
            $table->string('filename');
            $table->string('original_name');
            $table->string('mime_type');
            $table->integer('size');
            $table->string('path');
            $table->string('alt_text')->nullable();
            $table->text('description')->nullable();
            $table->json('metadata')->nullable();
            $table->foreignId('uploaded_by')->constrained('system_users');
            $table->timestamps();

            $table->index('mime_type');
            $table->index('uploaded_by');
        });

        // CMS Notification Banners table
        Schema::create('cms_notification_banners', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('message');
            $table->enum('type', ['info', 'warning', 'error', 'success'])->default('info');
            $table->enum('position', ['top', 'bottom'])->default('top');
            $table->boolean('is_active')->default(true);
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->integer('priority')->default(0);
            $table->json('display_rules')->nullable(); // for targeting specific pages/users
            $table->foreignId('created_by')->constrained('system_users');
            $table->timestamps();

            $table->index(['is_active', 'start_date', 'end_date']);
            $table->index('priority');
        });

        // CMS Settings table for various settings
        Schema::create('cms_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('text'); // text, json, boolean, number
            $table->string('group')->default('general');
            $table->text('description')->nullable();
            $table->foreignId('updated_by')->nullable()->constrained('system_users');
            $table->timestamps();

            $table->index(['key', 'group']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cms_settings');
        Schema::dropIfExists('cms_notification_banners');
        Schema::dropIfExists('cms_media');
        Schema::dropIfExists('cms_pages');
    }
};
