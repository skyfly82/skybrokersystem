<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courier_services', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('logo_url')->nullable();
            $table->text('description')->nullable();
            $table->string('api_endpoint')->nullable();
            $table->json('api_credentials')->nullable(); // Zaszyfrowane
            $table->boolean('is_active')->default(true);
            $table->boolean('is_sandbox')->default(false);
            $table->json('supported_services');
            $table->json('service_configuration')->nullable();
            $table->json('pricing_rules')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            $table->index(['is_active', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courier_services');
    }
};