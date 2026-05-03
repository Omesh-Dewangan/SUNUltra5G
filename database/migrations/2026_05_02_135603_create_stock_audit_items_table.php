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
        Schema::create('stock_audit_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('audit_id')->constrained('stock_audits')->onDelete('cascade');
            $table->foreignId('inventory_id')->constrained('inventories')->onDelete('cascade');
            $table->integer('system_qty');
            $table->integer('physical_qty')->nullable();
            $table->integer('mismatch_qty')->nullable();
            $table->string('reason')->nullable(); // e.g. 'Theft', 'Damage', 'Data Error'
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_audit_items');
    }
};
