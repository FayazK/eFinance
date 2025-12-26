Accounts Module: The Financial Backbone

1. The User Story

"As a business owner, I need to know exactly how much money I have in USD (Payoneer) versus PKR (Local), and I need to track the value lost when I transfer money between them."

In our context, an "Account" is not just a bank account. It is any container that holds value.

Payoneer (USD): High volume, receiving currency.

Meezan Bank (PKR): Spending currency, where salaries and rent are paid.

Office Cash (PKR): Petty cash for daily office snacks/supplies.

Upwork (USD): Temporary holding before moving to Payoneer/Local.

2. The Core Features

A. The Dashboard (Snapshot)

A simple grid showing cards for each account.

Visual Cue: Show the currency symbol clearly ($, Rs).

Computed Total: A "Total Net Worth" metric at the top, estimating the total value in PKR using a live or manual exchange rate.

B. The "Liquidation" Event (Transfer)

This is the most complex logic in the module. It is not a simple database update; it is an exchange of assets.

Scenario: You move $1,000 from Payoneer to Meezan.

The Problem: $1,000 leaves Payoneer. Rs. 278,000 arrives in Meezan.

The Calculation: The system must calculate the implied exchange rate ($1 = 278.0 PKR) and store it. This rate is crucial for your Quarterly Reports to calculate accurate Revenue.

C. The Ledger (Transactions)

Every movement is a "Transaction". We use a double-entry philosophy but simplified for this app:

Credit: Money In.

Debit: Money Out.

Polymorphism: A transaction can be linked to an Invoice (Income), a Payroll (Salary), or a Transfer (Liquidation).

3. Technical Implementation Plan

Step 1: Database Layer (Migration)

We need four tables to handle this module:

accounts: Stores the current balance and currency.

transaction_categories: Labels like "Rent", "Salary", "Client Payment".

transactions: The immutable history of every cent moved.

transfers: A pivot table that links a "Debit" transaction (USD out) to a "Credit" transaction (PKR in).

Step 2: The Models & Money Pattern

We will use Integer Math for all currency.

Rule: Database stores cents/paisa.

$100.00 -> Store 10000

Rs. 500.00 -> Store 50000

Accessors: Laravel Models will convert these to formatted strings ($100.00) for the frontend automatically.

Step 3: The Transfer Service

We will create a service class RecordTransfer that handles the atomic operation:

DB::transaction(function() {
    // 1. Deduct from Source
    // 2. Add to Destination
    // 3. Create Transaction Records
    // 4. Link them in 'transfers' table
});


4. Future Proofing

Reconciliation: Later, we can add a feature to "upload CSV" from Payoneer and match it against our database records.

Audit Logs: Since this is money, we will never "Delete" a transaction. We will only "Void" it (create a reversing entry).

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
