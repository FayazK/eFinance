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
        Schema::table('transactions', function (Blueprint $table) {
            if (! $this->indexExists('transactions', 'transactions_date_index')) {
                $table->index('date');
            }
            if (! $this->indexExists('transactions', 'transactions_type_index')) {
                $table->index('type');
            }
        });

        Schema::table('invoices', function (Blueprint $table) {
            if (! $this->indexExists('invoices', 'invoices_status_index')) {
                $table->index('status');
            }
            if (! $this->indexExists('invoices', 'invoices_due_date_index')) {
                $table->index('due_date');
            }
        });

        Schema::table('employees', function (Blueprint $table) {
            if (! $this->indexExists('employees', 'employees_status_index')) {
                $table->index('status');
            }
        });
    }

    private function indexExists(string $table, string $indexName): bool
    {
        $indexes = DB::select("SHOW INDEX FROM {$table} WHERE Key_name = ?", [$indexName]);

        return count($indexes) > 0;
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            if ($this->indexExists('transactions', 'transactions_date_index')) {
                $table->dropIndex(['date']);
            }
            if ($this->indexExists('transactions', 'transactions_type_index')) {
                $table->dropIndex(['type']);
            }
        });

        Schema::table('invoices', function (Blueprint $table) {
            if ($this->indexExists('invoices', 'invoices_status_index')) {
                $table->dropIndex(['status']);
            }
            if ($this->indexExists('invoices', 'invoices_due_date_index')) {
                $table->dropIndex(['due_date']);
            }
        });

        Schema::table('employees', function (Blueprint $table) {
            if ($this->indexExists('employees', 'employees_status_index')) {
                $table->dropIndex(['status']);
            }
        });
    }
};
