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
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->onDelete('cascade');

            $table->string('description');
            $table->integer('quantity')->default(1);
            $table->string('unit')->default('unit');
            $table->bigInteger('unit_price'); // In cents
            $table->bigInteger('amount'); // Denormalized: quantity * unit_price
            $table->integer('sort_order')->default(0);

            $table->timestamps();

            $table->index('invoice_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
    }
};
