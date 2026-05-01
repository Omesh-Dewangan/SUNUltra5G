<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Sales Orders Header Table
        Schema::create('sales_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique(); // SO-2026-0001
            $table->string('customer_name');
            $table->string('customer_phone')->nullable();
            $table->text('customer_address')->nullable();
            $table->enum('status', ['draft', 'confirmed', 'dispatched', 'completed', 'cancelled'])->default('draft');
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });

        // Sales Order Line Items Table
        Schema::create('sale_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_order_id')->constrained('sales_orders')->onDelete('cascade');
            $table->foreignId('inventory_id')->constrained('inventories')->onDelete('cascade');
            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('total_price', 12, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sale_order_items');
        Schema::dropIfExists('sales_orders');
    }
};
