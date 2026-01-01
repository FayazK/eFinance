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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();

            // Basic Info
            $table->string('name');
            $table->string('designation'); // Free text
            $table->string('email')->unique();
            $table->date('joining_date');

            // Financial (PKR only, minor units - paisa)
            $table->bigInteger('base_salary_pkr'); // 150,000 PKR = 15000000 paisa

            // Bank Details
            $table->string('iban')->nullable();
            $table->string('bank_name')->nullable();

            // Status
            $table->enum('status', ['active', 'terminated'])->default('active');
            $table->date('termination_date')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('status');
            $table->index('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
