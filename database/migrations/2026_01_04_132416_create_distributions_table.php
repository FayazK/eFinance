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
        Schema::create('distributions', function (Blueprint $table) {
            $table->id();

            // Distribution identity
            $table->string('distribution_number')->unique();
            $table->enum('status', ['draft', 'processed'])->default('draft');

            // Period covered
            $table->date('period_start');
            $table->date('period_end');

            // Calculation in PKR (minor units - paisa)
            $table->bigInteger('total_revenue_pkr');
            $table->bigInteger('total_expenses_pkr');
            $table->bigInteger('calculated_net_profit_pkr');
            $table->bigInteger('adjusted_net_profit_pkr')->nullable();
            $table->bigInteger('distributed_amount_pkr')->default(0);

            // Metadata
            $table->date('processed_at')->nullable();
            $table->text('notes')->nullable();
            $table->text('adjustment_reason')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes for performance
            $table->index('status');
            $table->index('period_start');
            $table->index('period_end');
            $table->index(['status', 'period_start']); // Composite for filtering
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('distributions');
    }
};
