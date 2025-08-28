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
        Schema::create('estimate_m', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('estimate_id')->constrained('estimate_h')->onDelete('cascade'); // Foreign key to estimate_h
            $table->integer('line_no');
            $table->string('item_code')->nullable();
            $table->string('item_name');
            $table->string('spec')->nullable();
            $table->decimal('quantity', 15, 2)->default(0);
            $table->string('unit')->nullable();
            $table->decimal('unit_price', 15, 2)->default(0);
            $table->decimal('amount', 15, 2)->default(0); // quantity * unit_price
            $table->string('tax_rate')->default('標準'); // e.g., '標準', '軽減', '非課税'
            $table->text('remarks')->nullable();
            $table->boolean('deleted')->default(false);
            $table->timestamps();

            $table->unique(['estimate_id', 'line_no']); // Ensure unique line numbers per estimate
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estimate_m');
    }
};