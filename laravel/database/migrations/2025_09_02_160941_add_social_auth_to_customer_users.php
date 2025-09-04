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
        Schema::table('customer_users', function (Blueprint $table) {
            // Social authentication IDs
            $table->string('google_id')->nullable()->after('password');
            $table->string('facebook_id')->nullable()->after('google_id');
            $table->string('linkedin_id')->nullable()->after('facebook_id');
            
            // Avatar from social providers
            $table->string('avatar')->nullable()->after('linkedin_id');
            
            // Make password nullable for social-only registrations
            $table->string('password')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_users', function (Blueprint $table) {
            $table->dropColumn(['google_id', 'facebook_id', 'linkedin_id', 'avatar']);
            
            // Revert password to non-nullable
            $table->string('password')->nullable(false)->change();
        });
    }
};
