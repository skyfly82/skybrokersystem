<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('customer_id')->constrained('customers');
            $table->foreignId('customer_user_id')->constrained('customer_users');
            $table->foreignId('courier_service_id')->constrained('courier_services');
            $table->string('tracking_number')->nullable()->index();
            $table->string('external_id')->nullable();
            $table->string('reference_number')->nullable(); // Numer referencyjny klienta
            $table->enum('status', [
                'draft', 'created', 'printed', 'dispatched', 
                'in_transit', 'out_for_delivery', 'delivered', 
                'returned', 'cancelled', 'failed'
            ])->default('draft');
            $table->string('service_type', 100);
            $table->json('sender_data');
            $table->json('recipient_data');
            $table->json('package_data');
            $table->json('cost_data')->nullable();
            $table->decimal('cod_amount', 10, 2)->nullable();
            $table->decimal('insurance_amount', 10, 2)->nullable();
            $table->json('additional_services')->nullable();
            $table->string('label_url')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->json('tracking_events')->nullable();
            $table->timestamps();
            
            $table->index(['customer_id', 'status']);
            $table->index(['status', 'created_at']);
            $table->index('reference_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};