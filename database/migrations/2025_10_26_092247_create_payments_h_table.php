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
        Schema::create('payments_h', function (Blueprint $table) {
            $table->id();
            $table->string('receipt_no', 50)->unique();
            $table->date('receipt_date');
            $table->foreignId('cust_id')->constrained('custs')->cascadeOnDelete();
            $table->string('cust_name', 100);
            $table->string('cust_department', 255)->nullable();
            $table->string('cust_person', 255)->nullable();
            $table->foreignId('emp_id')->constrained('emps')->cascadeOnDelete();
            $table->string('subject', 255)->nullable();
            $table->decimal('total_amount', 14, 2);
            $table->string('remarks', 255)->nullable();
            $table->string('status', 20)->default('draft');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments_h');
    }
};