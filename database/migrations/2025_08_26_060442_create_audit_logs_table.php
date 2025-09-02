<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->string('auditable_type'); // Model class name (e.g., Customer, CustomerUser)
            $table->unsignedBigInteger('auditable_id'); // ID of the audited model
            $table->string('user_type'); // system_user or customer_user
            $table->unsignedBigInteger('user_id'); // ID of the user who made the change
            $table->string('user_name'); // Name of the user for easier display
            $table->string('user_email'); // Email of the user for easier display
            $table->string('event'); // created, updated, deleted, login, logout
            $table->json('old_values')->nullable(); // Previous values
            $table->json('new_values')->nullable(); // New values
            $table->string('ip_address')->nullable(); // IP address of the user
            $table->string('user_agent')->nullable(); // User agent
            $table->text('description')->nullable(); // Human-readable description
            $table->timestamps();

            $table->index(['auditable_type', 'auditable_id']);
            $table->index(['user_type', 'user_id']);
            $table->index(['event']);
            $table->index(['created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
