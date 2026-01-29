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
        Schema::table('invoices', function (Blueprint $table) {
            $table->text('void_reason')->nullable()->after('client_notes');
        });

        Schema::table('invoice_payments', function (Blueprint $table) {
            $table->timestamp('voided_at')->nullable()->after('notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('void_reason');
        });

        Schema::table('invoice_payments', function (Blueprint $table) {
            $table->dropColumn('voided_at');
        });
    }
};
