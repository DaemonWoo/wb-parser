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
        Schema::create('incomes', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('income_id');

            $table->date('last_change_date')->nullable();
            $table->date('date_close')->nullable();

            $table->integer('quantity')->nullable();

            $table->decimal('total_price', 12, 2)->nullable();

            $table->foreignId('product_id')->constrained('products');
            $table->foreignId('warehouse_id')->constrained('warehouses');
            $table->foreignId('date_id')->constrained('dim_dates');

            $table->unique(['income_id', 'product_id']);

            $table->index(['income_id']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incomes');
    }
};
