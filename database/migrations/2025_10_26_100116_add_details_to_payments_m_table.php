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
        Schema::table('payments_m', function (Blueprint $table) {
            $table->string('payment_category')->after('line_no')->nullable();
            $table->string('bank_info')->after('payment_category')->nullable();
            $table->decimal('amount', 14, 2)->after('bank_info')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments_m', function (Blueprint $table) {
            $table->dropColumn('payment_category');
            $table->dropColumn('bank_info');
            $table->dropColumn('amount');
        });
    }
};