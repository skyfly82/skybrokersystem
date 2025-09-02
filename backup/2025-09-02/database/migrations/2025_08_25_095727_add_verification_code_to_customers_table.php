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
        Schema::table('customers', function (Blueprint $table) {
            $table->string('verification_code', 6)->nullable()->after('verified_at');
            $table->timestamp('verification_code_expires_at')->nullable()->after('verification_code');
            $table->boolean('email_verified')->default(false)->after('verification_code_expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['verification_code', 'verification_code_expires_at', 'email_verified']);
        });
    }
};
