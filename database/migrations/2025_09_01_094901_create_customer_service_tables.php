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
        // Complaint Topics table
        Schema::create('complaint_topics', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->json('metadata')->nullable(); // For additional settings
            $table->timestamps();
            
            $table->index(['is_active', 'sort_order']);
        });

        // Customer Complaints table  
        Schema::create('customer_complaints', function (Blueprint $table) {
            $table->id();
            $table->string('complaint_number')->unique(); // AUTO-generated: COMP-YYYY-XXXXXX
            $table->foreignId('customer_id')->constrained('customers');
            $table->foreignId('customer_user_id')->constrained('customer_users');
            $table->foreignId('shipment_id')->nullable()->constrained('shipments');
            $table->foreignId('complaint_topic_id')->constrained('complaint_topics');
            
            $table->string('subject');
            $table->text('description');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->enum('status', ['open', 'in_progress', 'waiting_customer', 'resolved', 'closed'])->default('open');
            
            // Freshdesk integration
            $table->string('freshdesk_ticket_id')->nullable();
            $table->json('freshdesk_data')->nullable();
            
            // Assignment
            $table->foreignId('assigned_to')->nullable()->constrained('system_users');
            $table->timestamp('assigned_at')->nullable();
            
            // Resolution tracking
            $table->text('resolution')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->foreignId('resolved_by')->nullable()->constrained('system_users');
            
            // Contact details
            $table->string('contact_email')->nullable();
            $table->string('contact_phone')->nullable();
            $table->enum('preferred_contact_method', ['email', 'phone', 'both'])->default('email');
            
            $table->timestamps();
            
            $table->index(['customer_id', 'status']);
            $table->index(['complaint_number']);
            $table->index(['status', 'priority']);
            $table->index('freshdesk_ticket_id');
        });

        // Complaint Messages table (conversation history)
        Schema::create('complaint_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('complaint_id')->constrained('customer_complaints')->onDelete('cascade');
            $table->enum('sender_type', ['customer', 'admin', 'system']);
            $table->foreignId('sender_id')->nullable(); // customer_user_id or system_user_id
            $table->text('message');
            $table->boolean('is_internal')->default(false); // Internal notes not visible to customer
            $table->json('attachments')->nullable(); // File attachments
            $table->timestamps();
            
            $table->index(['complaint_id', 'created_at']);
        });

        // Complaint Files table
        Schema::create('complaint_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('complaint_id')->constrained('customer_complaints')->onDelete('cascade');
            $table->string('filename');
            $table->string('original_name');
            $table->string('mime_type');
            $table->integer('size');
            $table->string('path');
            $table->foreignId('uploaded_by')->constrained('customer_users');
            $table->timestamps();
            
            $table->index('complaint_id');
        });

        // Freshdesk/Freshcaller Integration Settings
        Schema::create('customer_service_integrations', function (Blueprint $table) {
            $table->id();
            $table->string('service_name'); // 'freshdesk', 'freshcaller'
            $table->boolean('is_enabled')->default(false);
            $table->json('configuration'); // API keys, domain, etc.
            $table->json('webhook_settings')->nullable();
            $table->timestamp('last_sync_at')->nullable();
            $table->json('sync_status')->nullable();
            $table->timestamps();
            
            $table->unique('service_name');
        });

        // Service Level Agreements
        Schema::create('complaint_sla_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('complaint_topic_id')->constrained('complaint_topics');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent']);
            $table->integer('first_response_hours'); // Hours to first response
            $table->integer('resolution_hours'); // Hours to resolution
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->unique(['complaint_topic_id', 'priority']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('complaint_sla_rules');
        Schema::dropIfExists('customer_service_integrations');
        Schema::dropIfExists('complaint_files');
        Schema::dropIfExists('complaint_messages');
        Schema::dropIfExists('customer_complaints');
        Schema::dropIfExists('complaint_topics');
    }
};
