<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('company_name');
            $table->string('company_short_name')->nullable();
            $table->string('nip', 20)->unique()->nullable();
            $table->string('regon', 20)->nullable();
            $table->string('krs', 20)->nullable();
            $table->text('company_address');
            $table->string('city');
            $table->string('postal_code');
            $table->string('country', 2)->default('PL');
            $table->string('phone');
            $table->string('email');
            $table->string('website')->nullable();
            $table->enum('status', ['active', 'inactive', 'suspended', 'pending'])->default('pending');
            $table->decimal('credit_limit', 10, 2)->default(0.00);
            $table->decimal('current_balance', 10, 2)->default(0.00);
            $table->string('api_key', 64)->unique()->nullable();
            $table->json('settings')->nullable();
            $table->timestamp('contract_signed_at')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'verified_at']);
            $table->index('api_key');
            $table->index('nip');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
