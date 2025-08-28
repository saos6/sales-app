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
        Schema::create('estimate_h', function (Blueprint $table) {
            $table->uuid('id')->primary(); // UUID as primary key
            $table->string('estimate_no')->unique();
            $table->date('estimate_date');
            $table->date('valid_until')->nullable();
            $table->foreignId('cust_id')->constrained('custs'); // Assuming 'custs' table exists
            $table->string('cust_name');
            $table->string('cust_department')->nullable();
            $table->string('cust_person')->nullable();
            $table->foreignId('emps_id')->constrained('emps'); // Assuming 'emps' table exists
            $table->string('subject');
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('tax', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->string('currency')->default('円');
            $table->text('remarks')->nullable();
            $table->string('status')->default('作成中'); // e.g., '作成中', '提出済', '失注', '受注'
            $table->boolean('deleted')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estimate_h');
    }
};