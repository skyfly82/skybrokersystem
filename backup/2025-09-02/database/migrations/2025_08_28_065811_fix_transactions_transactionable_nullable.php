<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('transactionable_type')->nullable()->change();
            $table->unsignedBigInteger('transactionable_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('transactionable_type')->nullable(false)->change();
            $table->unsignedBigInteger('transactionable_id')->nullable(false)->change();
        });
    }
};
