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
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();

            $table->date('last_change_date')->nullable();

            $table->integer('quantity')->nullable();
            $table->integer('quantity_full')->nullable();

            $table->integer('in_way_to_client')->nullable();
            $table->integer('in_way_from_client')->nullable();

            $table->decimal('price', 12, 2)->nullable();
            $table->integer('discount')->nullable();

            $table->boolean('is_supply')->nullable();
            $table->boolean('is_realization')->nullable();

            $table->unsignedBigInteger('sc_code')->nullable()->index();

            $table->foreignId('product_id')->constrained('products');
            $table->foreignId('warehouse_id')->constrained('warehouses');
            $table->foreignId('date_id')->constrained('dim_dates');

            $table->unique(['product_id', 'warehouse_id', 'date_id']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stocks');
    }
};
