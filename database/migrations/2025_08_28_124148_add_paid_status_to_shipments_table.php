<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE shipments MODIFY COLUMN status ENUM('draft','created','paid','printed','dispatched','in_transit','out_for_delivery','delivered','returned','cancelled','failed') NOT NULL DEFAULT 'draft'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE shipments MODIFY COLUMN status ENUM('draft','created','printed','dispatched','in_transit','out_for_delivery','delivered','returned','cancelled','failed') NOT NULL DEFAULT 'draft'");
    }
};
