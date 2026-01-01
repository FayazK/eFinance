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
        Schema::create('invoice_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->onDelete('restrict');
            $table->foreignId('account_id')->constrained()->onDelete('restrict');

            // Link to transactions created
            $table->foreignId('income_transaction_id')->constrained('transactions')->onDelete('restrict');
            $table->foreignId('fee_transaction_id')->nullable()->constrained('transactions')->onDelete('restrict');

            // Payment details
            $table->bigInteger('payment_amount'); // Full invoice amount recorded as revenue
            $table->bigInteger('amount_received'); // Actual amount hitting account
            $table->bigInteger('fee_amount')->default(0); // payment_amount - amount_received

            $table->date('payment_date');
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index('invoice_id');
            $table->index('account_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_payments');
    }
};
