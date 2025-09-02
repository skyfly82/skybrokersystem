<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modify the enum to include marketing role
        DB::statement("ALTER TABLE system_users MODIFY COLUMN role ENUM('super_admin', 'admin', 'moderator', 'marketing') NOT NULL DEFAULT 'admin'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove marketing role from enum
        DB::statement("ALTER TABLE system_users MODIFY COLUMN role ENUM('super_admin', 'admin', 'moderator') NOT NULL DEFAULT 'admin'");
    }
};
