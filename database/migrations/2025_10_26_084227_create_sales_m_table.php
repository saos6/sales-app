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
        Schema::create('sales_m', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('sales_id')->constrained('sales_h');
            $table->integer('line_no');
            $table->string('item_code', 50)->nullable();
            $table->string('item_name', 200);
            $table->string('spec', 200)->nullable();
            $table->decimal('quantity', 12, 4);
            $table->string('unit', 20);
            $table->decimal('unit_price', 14, 4);
            $table->decimal('amount', 14, 2);
            $table->decimal('tax_rate', 5, 4);
            $table->decimal('tax_amount', 14, 2)->nullable();
            $table->decimal('delivery_qty', 12, 4)->nullable();
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
        Schema::dropIfExists('sales_m');
    }
};
