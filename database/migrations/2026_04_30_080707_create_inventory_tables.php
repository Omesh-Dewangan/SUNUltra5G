<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // SKU from catalog
            $table->foreignId('category_id')->constrained()->onDelete('restrict');
            $table->string('name');
            $table->string('wattage')->nullable(); // e.g., '120W'
            $table->json('specifications')->nullable(); // Stores PCB Size, Fixture Size, etc.
            $table->integer('stock_quantity')->default(0);
            $table->integer('low_stock_threshold')->default(10);
            $table->string('unit')->default('Piece'); // Piece, Coil, Meter
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventories');
        Schema::dropIfExists('categories');
    }
};
