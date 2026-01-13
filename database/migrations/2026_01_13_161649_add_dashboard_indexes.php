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
        Schema::table('transactions', function (Blueprint $table) {
            $table->index('date');
            $table->index('type');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->index('status');
            $table->index('due_date');
        });

        Schema::table('employees', function (Blueprint $table) {
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropIndex(['date']);
            $table->dropIndex(['type']);
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['due_date']);
        });

        Schema::table('employees', function (Blueprint $table) {
            $table->dropIndex(['status']);
        });
    }
};
