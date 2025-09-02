<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('customer_id')->constrained('customers');
            $table->foreignId('payment_id')->constrained('payments')->nullable();
            $table->morphs('transactionable'); // shipment, refund, etc.
            $table->enum('type', ['debit', 'credit']); // debit = minus, credit = plus
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('PLN');
            $table->decimal('balance_before', 10, 2);
            $table->decimal('balance_after', 10, 2);
            $table->string('description');
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['customer_id', 'created_at']);
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
