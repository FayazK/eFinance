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
        Schema::create('distribution_lines', function (Blueprint $table) {
            $table->id();

            // Foreign keys
            $table->foreignId('distribution_id')->constrained()->onDelete('cascade');
            $table->foreignId('shareholder_id')->constrained()->onDelete('restrict');

            // Split calculation
            $table->decimal('equity_percentage_snapshot', 5, 2); // Historical snapshot
            $table->bigInteger('allocated_amount_pkr'); // Share in minor units
            $table->foreignId('transaction_id')->nullable()->constrained()->onDelete('set null');

            $table->timestamps();

            // Indexes
            $table->index('distribution_id');
            $table->index('shareholder_id');
            $table->unique(['distribution_id', 'shareholder_id']); // One line per shareholder per distribution
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('distribution_lines');
    }
};
