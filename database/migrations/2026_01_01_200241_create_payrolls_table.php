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
        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();

            $table->foreignId('employee_id')->constrained()->onDelete('restrict');

            // Period
            $table->unsignedTinyInteger('month'); // 1-12
            $table->unsignedSmallInteger('year'); // 2026

            // Salary Breakdown (all in minor units - paisa)
            $table->bigInteger('base_salary'); // Snapshot at generation time
            $table->bigInteger('bonus')->default(0);
            $table->bigInteger('deductions')->default(0);
            $table->bigInteger('net_payable'); // Calculated: base + bonus - deductions

            // Payment Status
            $table->enum('status', ['pending', 'paid'])->default('pending');
            $table->date('paid_at')->nullable();
            $table->foreignId('transaction_id')->nullable()->constrained()->onDelete('set null');

            // Audit
            $table->text('notes')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Unique Constraint: One payroll per employee per month
            $table->unique(['employee_id', 'month', 'year']);

            // Indexes
            $table->index('status');
            $table->index(['month', 'year']);
            $table->index(['status', 'month', 'year']); // Composite for batch queries
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payrolls');
    }
};
