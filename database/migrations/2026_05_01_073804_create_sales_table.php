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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();

            $table->string('sale_id')->unique();

            $table->date('last_change_date')->nullable();

            $table->string('g_number')->nullable();
            $table->unsignedTinyInteger('spp')->nullable();

            $table->string('oblast_okrug_name', 200)->nullable();
            $table->string('country_name', 200)->nullable();
            $table->string('region_name', 200)->nullable();

            $table->decimal('total_price', 12, 2)->nullable();
            $table->decimal('finished_price', 12, 2)->nullable();
            $table->decimal('price_with_disc', 12, 2)->nullable();
            $table->decimal('for_pay', 12, 2)->nullable();
            $table->integer('discount_percent')->nullable();

            $table->boolean('is_storno')->nullable();
            $table->boolean('is_supply')->nullable();
            $table->boolean('is_realization')->nullable();

            $table->unsignedBigInteger('income_id')->nullable()->index();

            $table->foreignId('product_id')->constrained('products');
            $table->foreignId('warehouse_id')->constrained('warehouses');
            $table->foreignId('date_id')->constrained('dim_dates');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
