<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Step 1: Rename base_salary_pkr to base_salary in employees table
        Schema::table('employees', function (Blueprint $table) {
            $table->renameColumn('base_salary_pkr', 'base_salary');
        });

        // Step 2: Add salary_currency to employees table
        Schema::table('employees', function (Blueprint $table) {
            $table->enum('salary_currency', ['PKR', 'USD'])->default('PKR')->after('base_salary');
            $table->index('salary_currency');
        });

        // Step 3: Add currency to payrolls table
        Schema::table('payrolls', function (Blueprint $table) {
            $table->enum('currency', ['PKR', 'USD'])->default('PKR')->after('base_salary');
        });

        // Step 4: Set existing records to PKR
        DB::table('employees')->whereNull('salary_currency')->update(['salary_currency' => 'PKR']);
        DB::table('payrolls')->whereNull('currency')->update(['currency' => 'PKR']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Step 1: Remove currency from payrolls
        Schema::table('payrolls', function (Blueprint $table) {
            $table->dropColumn('currency');
        });

        // Step 2: Remove salary_currency from employees
        Schema::table('employees', function (Blueprint $table) {
            $table->dropIndex(['salary_currency']);
            $table->dropColumn('salary_currency');
        });

        // Step 3: Rename base_salary back to base_salary_pkr
        Schema::table('employees', function (Blueprint $table) {
            $table->renameColumn('base_salary', 'base_salary_pkr');
        });
    }
};
