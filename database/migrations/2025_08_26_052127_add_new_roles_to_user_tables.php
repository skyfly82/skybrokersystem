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
        // Add new roles for customer_users: accountant, warehouse
        Schema::table('customer_users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
        
        Schema::table('customer_users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'user', 'viewer', 'accountant', 'warehouse'])->default('user')->after('phone');
        });
        
        // Add new role for system_users: employee
        Schema::table('system_users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
        
        Schema::table('system_users', function (Blueprint $table) {
            $table->enum('role', ['super_admin', 'admin', 'moderator', 'employee'])->default('admin')->after('password');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert customer_users roles
        Schema::table('customer_users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
        
        Schema::table('customer_users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'user', 'viewer'])->default('user')->after('phone');
        });
        
        // Revert system_users roles
        Schema::table('system_users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
        
        Schema::table('system_users', function (Blueprint $table) {
            $table->enum('role', ['super_admin', 'admin', 'moderator'])->default('admin')->after('password');
        });
    }
};
