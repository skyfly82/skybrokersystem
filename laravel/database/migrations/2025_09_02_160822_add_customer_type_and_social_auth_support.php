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
            // Customer type: 'company' or 'individual'
            $table->enum('type', ['company', 'individual'])->default('company')->after('uuid');
            
            // Make company fields nullable for individual customers
            $table->string('company_name')->nullable()->change();
            $table->string('company_address')->nullable()->change();
            
            // Add individual customer fields
            $table->string('individual_first_name')->nullable()->after('company_name');
            $table->string('individual_last_name')->nullable()->after('individual_first_name');
            
            // Social authentication fields
            $table->string('google_id')->nullable()->after('api_key');
            $table->string('facebook_id')->nullable()->after('google_id');
            $table->string('linkedin_id')->nullable()->after('facebook_id');
            $table->json('social_avatar')->nullable()->after('linkedin_id');
            
            // Registration source tracking
            $table->enum('registration_source', ['web_form', 'google', 'facebook', 'linkedin', 'api'])
                ->default('web_form')->after('social_avatar');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn([
                'type', 
                'individual_first_name', 
                'individual_last_name',
                'google_id',
                'facebook_id', 
                'linkedin_id',
                'social_avatar',
                'registration_source'
            ]);
            
            // Revert nullable changes
            $table->string('company_name')->nullable(false)->change();
            $table->string('company_address')->nullable(false)->change();
        });
    }
};
