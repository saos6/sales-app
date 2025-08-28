<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('item_code')->unique();
            $table->string('item_name');
            $table->string('spec')->nullable();
            $table->unsignedTinyInteger('category_id');
            $table->string('unit');
            $table->decimal('standard_price', 12, 2)->nullable();
            $table->decimal('cost_price', 12, 2)->nullable();
            $table->string('tax_rate')->default('standard');
            $table->string('currency')->default('JPY');
            $table->string('maker')->nullable();
            $table->string('jan_code')->nullable();
            $table->integer('stock_qty')->nullable();
            $table->integer('reorder_point')->nullable();
            $table->integer('lead_time')->nullable();
            $table->string('status')->default('active');
            $table->text('remarks')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};


