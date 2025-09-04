<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courier_points', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('courier_service_id')->constrained('courier_services');
            $table->string('code')->index();
            $table->string('type', 50)->index();
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('street')->nullable();
            $table->string('building_number')->nullable();
            $table->string('apartment_number')->nullable();
            $table->string('city')->nullable()->index();
            $table->string('postal_code', 12)->nullable()->index();
            $table->string('country_code', 2)->default('PL');
            $table->decimal('latitude', 10, 7)->index();
            $table->decimal('longitude', 10, 7)->index();
            $table->json('opening_hours')->nullable();
            $table->json('functions')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->json('metadata')->nullable();
            $table->string('external_id')->nullable();
            $table->timestamps();

            $table->unique(['courier_service_id', 'code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courier_points');
    }
};
