<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('stock_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_id')->constrained('inventories')->onDelete('cascade');
            $table->enum('type', ['in', 'out']);
            $table->integer('quantity');
            $table->decimal('price_per_unit', 10, 2)->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('stock_transactions');
    }
};
