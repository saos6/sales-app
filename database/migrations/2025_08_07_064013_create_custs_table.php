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
        Schema::create('custs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('name_kana')->nullable();
            $table->string('department_name')->nullable();
            $table->string('contact_person_name')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('prefecture')->nullable();
            $table->string('address_line')->nullable();
            $table->string('tel')->nullable();
            $table->string('fax')->nullable();
            $table->string('email')->nullable();
            $table->string('website_url')->nullable();
            $table->integer('customer_type')->nullable();
            $table->integer('industry_type')->nullable();
            $table->date('first_trade_date')->nullable();
            $table->integer('customer_rank')->nullable();
            $table->integer('payment_terms')->nullable();
            $table->foreignId('depts_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('emps_id')->nullable()->constrained()->nullOnDelete();
            $table->text('remarks')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('custs');
    }
};