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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            $table->string('g_number')->nullable();

            $table->dateTime('date');
            $table->date('last_change_date')->nullable();

            $table->decimal('total_price', 12, 2)->nullable();
            $table->integer('discount_percent')->nullable();

            $table->string('region_name')->nullable();

            $table->boolean('is_cancel')->nullable();
            $table->dateTime('cancel_dt')->nullable();

            $table->unsignedBigInteger('income_id')->nullable()->index();

            $table->foreignId('product_id')->constrained('products');
            $table->foreignId('warehouse_id')->constrained('warehouses');
            $table->foreignId('date_id')->constrained('dim_dates');

            $table->unique(['g_number', 'product_id', 'date_id']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
