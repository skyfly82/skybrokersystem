<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // shipment_created, payment_completed
            $table->string('type'); // email, sms
            $table->string('subject')->nullable();
            $table->text('content');
            $table->json('variables')->nullable(); // Available variables
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['name', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_templates');
    }
};