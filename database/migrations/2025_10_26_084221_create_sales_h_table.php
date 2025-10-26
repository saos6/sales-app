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
        Schema::create('sales_h', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('sales_no', 20)->unique();
            $table->date('sales_date');
            $table->date('posting_date');
            $table->bigInteger('invoice_id')->nullable();
            $table->foreignUuid('estimate_id')->nullable()->constrained('estimate_h');
            $table->foreignId('cust_id')->constrained('custs');
            $table->string('cust_name', 100);
            $table->string('cust_department', 100)->nullable();
            $table->string('cust_person', 100)->nullable();
            $table->foreignId('emps_id')->constrained('emps');
            $table->string('subject', 200);
            $table->decimal('subtotal', 14, 2);
            $table->decimal('tax', 14, 2);
            $table->decimal('total', 14, 2);
            $table->string('payment_status', 20)->nullable();
            $table->date('payment_date')->nullable();
            $table->string('receipt_no', 50)->nullable();
            $table->text('remarks')->nullable();
            $table->string('status', 20);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_h');
    }
};
