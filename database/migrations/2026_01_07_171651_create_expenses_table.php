<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();

            // Foreign keys
            $table->foreignId('account_id')->constrained()->onDelete('restrict');
            $table->foreignId('category_id')->constrained('transaction_categories')->onDelete('restrict');
            $table->foreignId('transaction_id')->nullable()->constrained()->onDelete('set null');

            // Basic expense data
            $table->bigInteger('amount'); // Minor units (cents/paisa)
            $table->char('currency_code', 3); // USD, PKR, EUR, GBP, AED
            $table->string('vendor')->nullable();
            $table->text('description')->nullable();
            $table->date('expense_date');

            // Recurring expense support (nullable for one-time expenses)
            $table->string('recurrence_frequency')->nullable(); // monthly, quarterly, yearly
            $table->integer('recurrence_interval')->default(1);
            $table->date('recurrence_start_date')->nullable();
            $table->date('recurrence_end_date')->nullable();
            $table->date('next_occurrence_date')->nullable();
            $table->date('last_processed_date')->nullable();
            $table->boolean('is_recurring')->default(false);
            $table->boolean('is_active')->default(true);

            // International expense support (for multi-currency)
            $table->decimal('exchange_rate', 10, 4)->nullable();
            $table->bigInteger('reporting_amount_pkr')->nullable(); // PKR equivalent in minor units

            // Status
            $table->string('status')->default('draft'); // draft, processed, cancelled

            $table->timestamps();
            $table->softDeletes();

            // Indexes for common queries
            $table->index(['status', 'expense_date']);
            $table->index(['is_recurring', 'is_active', 'next_occurrence_date']);
            $table->index('account_id');
            $table->index('category_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
