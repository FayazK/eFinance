<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 1. Accounts: The containers for money
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "Payoneer USD", "Meezan PKR"
            $table->string('type')->default('bank'); // bank, wallet, cash
            $table->char('currency_code', 3); // USD, PKR, EUR

            // Storing balance in 'minor units' (cents/paisa) as BigInt
            // Example: $100.00 is stored as 10000
            $table->bigInteger('current_balance')->default(0);

            $table->string('account_number')->nullable(); // IBAN or Email
            $table->string('bank_name')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Transaction Categories: For reporting (Rent, Salary, etc.)
        Schema::create('transaction_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type'); // 'income' or 'expense'
            $table->string('color')->nullable(); // For UI badges
            $table->timestamps();
        });

        // 3. Transactions: The central ledger
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->nullable()->constrained('transaction_categories');

            // Polymorphic relation to link this transaction to specific events
            // e.g., App\Models\Invoice, App\Models\Payroll, App\Models\Expense
            $table->nullableMorphs('reference');

            $table->string('type'); // 'credit' (in) or 'debit' (out)
            $table->bigInteger('amount'); // Always positive. Type determines direction.

            // For multi-currency reporting.
            // If account is USD, this column stores what that amount was worth in PKR at the time.
            $table->bigInteger('reporting_amount_pkr')->nullable();
            $table->decimal('reporting_exchange_rate', 10, 4)->nullable();

            $table->text('description')->nullable();
            $table->date('date');

            $table->timestamps();
            $table->softDeletes(); // Never hard delete financial records
        });

        // 4. Transfers: Linking two transactions together (Liquidation)
        Schema::create('transfers', function (Blueprint $table) {
            $table->id();
            // The money leaving (e.g., USD out)
            $table->foreignId('withdrawal_transaction_id')->constrained('transactions');
            // The money entering (e.g., PKR in)
            $table->foreignId('deposit_transaction_id')->constrained('transactions');

            // The implied rate: PKR Amount / USD Amount
            $table->decimal('exchange_rate', 10, 4);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('transfers');
        Schema::dropIfExists('transactions');
        Schema::dropIfExists('transaction_categories');
        Schema::dropIfExists('accounts');
    }
};
