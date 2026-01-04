Shareholders & Distributions Module: The Profit Split

1. The User Story

"Every quarter, we take our Net Profit (Income - Expenses) and split it among the partners. The Office itself acts as a partner (20%), keeping its share to fund the next quarter's operations."

This module transforms raw accounting data into actionable wealth distribution. It enforces the "Office First" rule, ensuring the company always has operating capital before partners take their cut.

2. Core Entities

A. The Shareholders

Human Partners: Real people with bank accounts (e.g., "Fayaz", "Partner B").

The "Office" Entity: A virtual partner.

Flag: is_office_reserve = true.

Role: This entity effectively acts as a "Retained Earnings" bucket.

Equity Config: A percentage assigned to each entity (e.g., 20%, 20%, 20%, 20%, 20%).

Validation: Total must equal 100%.

B. The Distribution Event

Represents a specific closure period (e.g., "Q1 2026 Distribution").

State Machine:

Draft: Calculating numbers, not yet finalized.

Processed: Money has moved, ledgers are updated.

3. The Workflow: running a Distribution

Phase 1: The "Profit Snapshot" (Draft Mode)

Action: User clicks "New Distribution".

Inputs: Select Period (e.g., Jan 1 - Mar 31).

Automated Calculation (The Heavy Lifting):

Gross Revenue (PKR): Sum of all Income transactions in PKR + (USD Income converted at historical rates).

Total Expenses (PKR): Sum of all Expense transactions (Rent, Salaries, Fees).

Net Profit: Revenue - Expenses.

The Split Preview:

The system applies the % percentages to the Net Profit.

It shows a table: "Partner A gets Rs 500k", "Office gets Rs 500k".

Manual Adjustment: User can manually adjust the "Net Profit" figure if they want to hold back extra reserves before splitting.

Phase 2: Execution (The Payout)

Action: User clicks "Process Distribution".

System Logic (The "Office" Magic):

Step A: Human Partners (Payouts):

The system creates Withdrawal transactions (Debit) from the main bank account for each human partner.

Money leaves the company.

Step B: The Office Share (Retained):

The system DOES NOT create a withdrawal transaction for the Office entity.

Instead, it creates a Journal Entry (or simply logs it) noting that this amount was "Allocated to Reserve".

Money stays in the company account. This cash balance naturally becomes the opening balance for the next quarter's expenses.

Step C: Update Balances:

The Bank Account balance decreases by the sum of Human payouts only.

Phase 3: Reporting & History

The Distribution record is locked.

Dashboard Update: The "Distributable Profit" widget resets to 0 (or the beginning of the new quarter's profit).

Partner Statement: Generates a PDF for each partner showing:

Total Company Revenue

Total Expenses

Net Profit

Their % Share

Amount Transferred

4. The "Office Fund" Logic

Since the Office Share is Retained Earnings, how do we track it?

Concept: We don't need a separate "Office Bank Account". The commingled funds in the main account are the Office Fund.

Visualizing Health:

If Bank Balance < (Next Month's Estimated Expenses), the Office Fund is low.

The Dashboard should show a "Runway" metric: "Based on last month's burn rate, the Office Share covers 2.5 months of operations."

5. Constraints & Rules

Negative Profit: If Expenses > Revenue (Loss), the Distribution Module allows creating a "Negative Distribution" (Call for Capital). Partners might need to inject cash.

Currency: Distributions are calculated and paid in PKR.

If the company holds USD, the user must perform a Transfer/Liquidation (USD -> PKR) in the Operations Module before running the distribution to ensure enough PKR cash is available for the payouts.

6. Integration

Expenses Module: This module relies entirely on the accuracy of the transactions table. If expenses aren't recorded, Profit is inflated, and you overpay partners (draining the company).

Accounts Module: Payouts are actual bank withdrawals.
