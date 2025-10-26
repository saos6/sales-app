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
        Schema::create('payments_m', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->constrained('payments_h')->cascadeOnDelete();
            $table->integer('line_no');
            $table->foreignUuid('sales_id')->nullable()->constrained('sales_h')->nullOnDelete();
            $table->decimal('apply_amount', 14, 2);
            $table->integer('payment_type');
            $table->string('bank_name', 100)->nullable();
            $table->string('bank_branch', 100)->nullable();
            $table->string('remarks', 255)->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments_m');
    }
};