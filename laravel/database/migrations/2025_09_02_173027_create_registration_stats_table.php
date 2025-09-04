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
        Schema::create('registration_stats', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            
            // Registration details
            $table->enum('customer_type', ['company', 'individual']);
            $table->enum('registration_method', ['web_form', 'google', 'facebook', 'linkedin']);
            $table->string('source')->nullable(); // UTM source, referrer, etc.
            $table->string('campaign')->nullable(); // UTM campaign
            $table->string('medium')->nullable(); // UTM medium
            
            // User agent and location data
            $table->string('user_agent')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('country', 2)->nullable();
            $table->string('city')->nullable();
            
            // Registration flow data
            $table->timestamp('started_at')->nullable(); // When user started registration
            $table->timestamp('completed_at')->nullable(); // When registration was completed
            $table->boolean('successful')->default(false);
            $table->json('form_steps')->nullable(); // Track which steps user completed
            $table->json('errors')->nullable(); // Track any validation errors
            
            // Associated records
            $table->foreignId('customer_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('customer_user_id')->nullable()->constrained()->onDelete('set null');
            
            $table->timestamps();
            
            // Indexes for analytics
            $table->index(['customer_type', 'created_at']);
            $table->index(['registration_method', 'created_at']);
            $table->index(['successful', 'created_at']);
            $table->index(['source', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registration_stats');
    }
};
