Operations & Transfers Module: The Flow of Capital

1. Introduction

While the Accounts module represents the "State" of your finances (how much you have right now), the Operations module represents the "Events" (how the money got there). This module handles the three fundamental actions a business can take with its capital:

Income (Credit): Money entering the system (e.g., Bank Interest, Capital Injection, Refunds).

Expense (Debit): Money leaving the system (e.g., Office Rent, Server Costs, Snacks).

Transfers (Movement): Money moving between internal containers (e.g., Withdrawing USD to a PKR Bank, withdrawing Cash for the office).

2. The Core Concepts

A. The "Direct" Transaction (Income/Expense)

These are single-sided events. Money either appears or disappears.

The User Action: The user selects an account (e.g., "Meezan Bank"), selects a Category (e.g., "Office Rent"), and enters an amount.

The Logic:

The system creates a Transaction record.

The system updates the Account balance.

Reporting: The transaction is tagged with a category_id. This is critical for the Quarterly Distribution report (to calculate Net Profit).

Multi-Currency Handling: If an expense happens in USD (e.g., Server Cost), the system must also record the estimated PKR value at that moment. This ensures that when you calculate "Total Expenses in PKR" at the end of the quarter, the USD expenses are weighted correctly.

B. The "Transfer" Transaction (Internal Movement)

This is a double-sided event. Money is not spent; it is relocated.

The Difference: Transfers do not affect Net Profit (mostly). They affect Liquidity.

The Structure: A transfer is technically two transactions tied together:

A Debit transaction from the Source Account (Money leaving).

A Credit transaction to the Destination Account (Money entering).

A Transfer record bridging them, storing the exchange rate.

3. Transfer Workflows

We have two distinct types of transfers based on whether currencies match or differ.

A. The "Liquidation" Workflow (Cross-Currency)

Use Case: Withdrawing Payoneer (USD) to Local Bank (PKR).

User Input:

Amount Sent: $1,000 (Leaving Payoneer).

Amount Received: Rs 278,500 (Entering Local Bank).

System Logic:

Detects currency mismatch (USD vs PKR).

Calculates Implied Exchange Rate: 278.5.

Fee Handling: Any fees are typically absorbed into the exchange rate loss. The system records the full $1,000 leaving and the Rs 278,500 entering. The "loss" is realized when the value is converted.

B. The "Fee-Based" Workflow (Same-Currency)

Use Case: Moving funds between two Payoneer accounts or Bank to Bank where a fee applies.

The Scenario:
You transfer $500 from Payoneer Account A to Payoneer Account B. Payoneer charges you a $3 transfer fee.

Total Deducted from A: $503

Total Received in B: $500

The User Journey:

Initiation: User selects "Transfer".

Input:

Amount Sent: $503 (Total amount leaving the source).

Amount Received: $500 (Amount arriving at destination).

System Logic:

Detects currencies are identical (USD -> USD).

Detects a difference of $3.

Identifies this difference explicitly as a Transfer Fee/Tax.

The Database Operations (Atomic Transaction):
Unlike a simple transfer, this generates three records to ensure the $3 fee appears on your P&L (Profit & Loss) statement.

Transfer Debit: Reduce Source Balance by $500. Create Transaction (Linked to Transfer).

Transfer Credit: Increase Destination Balance by $500. Create Transaction (Linked to Transfer).

Fee Expense: Reduce Source Balance by $3. Create a separate Transaction tagged with the category "Bank Charges & Fees".

Result: The transfer of $500 is neutral (ignored in P&L), but the $3 fee is recorded as an expense, correctly reducing your Net Profit for the quarter.

4. The Role of Categories

Categories are the primary filter for your "Net Profit" calculation.

Income Categories: Sales, Refunds, Dividends.

Expense Categories: Rent, Salaries, Utilities, Software Subscriptions, Bank Charges.

Transfer Categories: Transfers usually have a null category, or a system-level "Internal Transfer" category, because moving money is not an expense.

Critical Logic:
When generating the Quarterly Distribution Report:

The system sums all Transactions where category_type = 'income'.

The system subtracts all Transactions where category_type = 'expense'.

Transfers are ignored in the P&L statement. This is why the Fee-Based Workflow above is so important: by separating the $3 fee into its own Expense transaction, we ensure it is counted in the P&L, while the principal movement ($500) is ignored.

5. Constraints & Validations

Insufficient Funds: The system must check if the Source Account has enough balance before allowing a Transfer or Expense.

Currency Integrity:

Direct Transactions must match the currency of the Account (e.g., you cannot record a USD expense on a PKR-only account without doing a conversion first).

Transfers can mix currencies, but only if the user provides the "Amount Received" so the system can calculate the rate.

Immutability: Financial records should rarely be deleted. If a mistake is made, the standard practice is to "Void" (create a reversing entry) or update the record while keeping an audit log. For this MVP, editing is permitted but must update the Account Balances accordingly.

6. Future-Proofing: Reconciliation

Later, you may want to upload a CSV from Payoneer.

The system will look at the transactions table.

It will try to match the CSV rows with your database records based on Date and Amount.

Having a clean transfers table allows the system to understand that a "Debit" on Payoneer matches a "Credit" on Meezan, automatically linking them in the reconciliation view.
