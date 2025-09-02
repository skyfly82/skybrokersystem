<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('customer_id')->constrained('customers');
            $table->foreignId('customer_user_id')->constrained('customer_users')->nullable();
            $table->morphs('payable'); // shipment_id, invoice_id, etc.
            $table->string('external_id')->nullable(); // ID z bramki płatności
            $table->enum('type', ['shipment', 'topup', 'subscription', 'refund'])->default('shipment');
            $table->enum('method', ['card', 'bank_transfer', 'blik', 'paypal', 'wallet', 'simulation'])->default('simulation');
            $table->string('provider')->nullable(); // paynow, stripe, etc.
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('PLN');
            $table->enum('status', ['pending', 'processing', 'completed', 'failed', 'cancelled', 'refunded'])->default('pending');
            $table->json('provider_data')->nullable();
            $table->string('description')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->text('failure_reason')->nullable();
            $table->timestamps();

            $table->index(['customer_id', 'status']);
            $table->index(['status', 'created_at']);
            $table->index('external_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
