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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();

            // Core invoice identity
            $table->string('invoice_number')->unique();
            $table->enum('status', ['draft', 'sent', 'partial', 'paid', 'void', 'overdue'])
                  ->default('draft');

            // Client and project relationships
            $table->foreignId('client_id')->constrained()->onDelete('restrict');
            $table->foreignId('project_id')->nullable()->constrained()->onDelete('set null');

            // Monetary values (all in minor units - cents)
            $table->char('currency_code', 3);
            $table->bigInteger('subtotal');
            $table->bigInteger('tax_amount')->default(0);
            $table->bigInteger('total_amount');
            $table->bigInteger('amount_paid')->default(0);
            $table->bigInteger('balance_due');

            // Important dates
            $table->date('issue_date');
            $table->date('due_date');
            $table->date('paid_at')->nullable();
            $table->date('sent_at')->nullable();
            $table->date('voided_at')->nullable();

            // Additional information
            $table->text('notes')->nullable();
            $table->text('terms')->nullable();
            $table->text('client_notes')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes for performance
            $table->index('status');
            $table->index('client_id');
            $table->index('project_id');
            $table->index('issue_date');
            $table->index('due_date');
            $table->index(['status', 'due_date']); // Composite for overdue queries
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
