<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('api_keys', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->foreignId('customer_id')->nullable()->constrained('customers');
            $table->string('name')->nullable();
            $table->json('scopes')->nullable();
            $table->enum('status', ['active', 'suspended', 'revoked'])->default('active')->index();
            $table->unsignedInteger('rate_limit_per_minute')->nullable();
            $table->unsignedInteger('rate_limit_per_day')->nullable();
            $table->unsignedInteger('usage_minute')->nullable()->default(0);
            $table->unsignedInteger('usage_day')->nullable()->default(0);
            $table->timestamp('usage_minute_reset_at')->nullable();
            $table->timestamp('usage_day_reset_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('api_keys');
    }
};

