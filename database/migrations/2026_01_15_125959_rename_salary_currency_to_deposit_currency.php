<?php

declare(strict_types=1);

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
        // Rename salary_currency to deposit_currency in employees table
        Schema::table('employees', function (Blueprint $table) {
            $table->renameColumn('salary_currency', 'deposit_currency');
        });

        // Rename currency to deposit_currency in payrolls table
        Schema::table('payrolls', function (Blueprint $table) {
            $table->renameColumn('currency', 'deposit_currency');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->renameColumn('deposit_currency', 'currency');
        });

        Schema::table('employees', function (Blueprint $table) {
            $table->renameColumn('deposit_currency', 'salary_currency');
        });
    }
};
