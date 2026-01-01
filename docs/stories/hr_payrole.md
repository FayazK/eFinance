HR & Payroll Module: The Team & Compensation

1. The User Story

"I need to manage my team's data and ensure everyone gets paid their agreed PKR salary on time, while tracking bonuses, deductions, and total expense to the company."

This module handles the largest recurring expense of the company. It sits between HR (People) and Finance (Money). Unlike international client payments, this flow is strictly local (PKR).

2. Core Entities

A. The Employee Profile

Basic Info: Name, Designation, Joining Date, Email.

Financial Config: * base_salary_pkr: The fixed monthly amount (e.g., 150,000 PKR).

account_details: IBAN/Bank Name for the transfer.

Status: Active vs. Terminated. (Inactive employees are excluded from new payroll generations).

B. The Payroll Record

Represents a specific payment event for an employee for a specific month (e.g., "Ali - Jan 2026").

Components: Base Salary + Bonus - Deductions = Net Payable.

3. The Workflow: Running Payroll

Unlike Invoices (which are ad-hoc), Payroll is a Batch Process that happens once a month.

Phase 1: Generation (Draft Mode)

Action: User clicks "Generate Payroll" and selects a month (e.g., "January 2026").

System Logic:

Checks if payroll already exists for this month (prevents duplicates).

Fetches all Active employees.

Creates a Payroll record for each employee.

Snapshots the current base_salary into the record.

Sets status to Pending.

UI Result: A grid showing all employees with their calculated salaries, ready for review.

Phase 2: Adjustments (Bonuses & Deductions)

Scenario: An employee worked overtime, or took an advance.

Action: User edits the specific row in the grid.

Input: Add 10,000 Bonus.

Input: Add 5,000 Deduction (for leave/advance).

System: Dynamically updates total_payable for that row.

Phase 3: Execution (The Payout)

Action: User clicks "Pay All" (or pays individually).

Selection: User selects the Source Account (e.g., Meezan Bank PKR).

Validation: System checks if Source Balance >= Total Net Payable.

Critical: If funds are low, the system prompts the user to go to the Transfers Module (to liquidate USD from Payoneer) first.

Atomic Transaction:

Debit: Creates a Transaction reducing the Source Account Balance.

Expense: Records the transaction under category "Salaries & Wages".

Update: Sets all selected Payroll records to Paid.

Link: Links the transaction_id to the payroll records for audit trails.

4. Integration with Finance

Currency Constraint: Payroll is strictly PKR.

You cannot pay payroll directly from Payoneer (USD). The flow must be: USD -> Transfer -> PKR -> Payroll.

Reporting: The "Salaries" category is a major deduction in the Quarterly Distribution Report. It reduces the Net Profit available for shareholders.

5. Payslips (PDF)

Just like Invoices, we use spatie/laravel-pdf to generate professional Payslips.

Content: Company Logo, Employee Details, Month, Breakdown of Salary, Bonus, Deductions, and the Net Amount transferred.

Delivery: System can email the PDF to the employee's registered email address upon payment.

6. Constraints & Rules

Immutability: Changing an employee's base_salary in their profile does not affect past payroll records. History must remain accurate to what was actually paid.

Partial Payments: While rare, the system should allow marking a payroll as Paid manually if the payment was made via cash or outside the system, to keep the books balanced.

7. Future Proofing

Loan Management: A feature to track long-term loans where a fixed amount is automatically added to the "Deduction" column each month until the loan is cleared.

Taxation: Auto-calculation of income tax based on government slabs (future feature).
