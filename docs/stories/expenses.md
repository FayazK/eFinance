Expenses Module: The Burn Rate

1. The User Story

"I need to record every cost—from the $100 server bill to the Rs. 500 for office tea—so I know exactly how much profit is left to distribute to partners at the end of the quarter."

The Expense Module tracks the "Cost of Doing Business." In your ecosystem, this is the module that "consumes" the 20% Office Share retained during distributions.

2. Core Entities

A. The Category Taxonomy

Parent Categories: "Operations", "Personnel", "Tech Stack", "Banking".

Child Categories:

Operations: Office Rent, Electricity, Internet, Kitchen/Groceries.

Tech: AWS/Server, Domain Names, Software Subscriptions.

Banking: Transfer Fees, Taxes.

Budgeting (Optional): Setting a "Safe Limit" for categories (e.g., Kitchen limit: Rs 20k/month).

B. The Vendor/Payee (Lightweight CRM)

Optional tracking of who got the money (e.g., "Landlord", "Nayatel", "Foodpanda").

Helps in answering: "How much did we pay Nayatel this year?"

3. The Workflows

Workflow A: The "Quick Entry" (Petty Cash)

Scenario: The office boy buys milk and biscuits.

Interface: A streamlined mobile-friendly form.

Inputs:

Amount: 500

Currency: PKR (Default)

Category: Kitchen

Source Account: Office Cash Wallet

Date: Today

Logic:

Deducts 500 from "Office Cash Wallet".

Records Expense linked to "Kitchen".

Workflow B: The Recurring Fixed Cost (Rent/Internet)

Scenario: Paying the office rent on the 5th of every month.

Feature: Recurring Profiles.

User sets up "Office Rent" template: 150,000 PKR, Monthly, Due on the 5th.

Automation:

System generates a "Draft Expense" or "Reminder" on the 5th.

User clicks "Confirm Payment" -> Deducts from Meezan Bank.

Workflow C: International Expenses (SaaS/Server)

Scenario: Paying $20 for GitHub Copilot.

Source: Payoneer (USD).

The "Net Profit" Challenge:

You paid $20.

Your Distribution is calculated in PKR.

Crucial Logic: When recording a USD expense, the system must calculate/estimate the PKR Equivalent at that moment.

Example: $20 Expense recorded. System fetches rate (278.0). Stores reporting_amount_pkr = 5560.

Result: When you run the Q1 Distribution Report, this expense deducts Rs. 5,560 from your Net Profit, ensuring accurate conversion math.

4. The "Office Fund" Connection

This is unique to your business logic.

The Concept: You retain 20% of profits to cover these expenses.

The Dashboard Metric: "Burn vs. Reserve"

Metric 1: Office Reserve: The amount kept during the last distribution (e.g., Rs 1,000,000).

Metric 2: Quarter-to-Date Expenses: The sum of all expenses since the quarter started (e.g., Rs 400,000).

Visual: A progress bar. "You have consumed 40% of the Office Reserve."

Alert: If Expenses > Reserve, the bar turns red. This means the partners are technically paying for expenses out of their next payout (Equity erosion).

5. Integration Points

Accounts Module: Every expense reduces an account balance immediately.

Shareholders Module: The SUM(expenses) for the quarter is the primary deduction in the Net Profit = Revenue - Expenses formula.

Invoicing Module: When you record an invoice payment with fees (e.g., $4750 received for $5000 billed), the system automatically creates a $250 Expense entry here under "Bank Charges".

6. Attachments & Proof

Feature: Drag-and-drop receipt upload.

Storage: Files stored securely (S3/Local) linked to the Transaction ID.

Audit: Essential for "Bank Charges" or big items (Laptops/Rent) in case of an audit.

7. Future Proofing

Approvals: If an employee tries to record an expense > 10,000 PKR, it requires Admin approval before deducting from the balance.

Reimbursements: An employee pays for lunch from their pocket.

Step 1: Record Expense (Paid by Employee).

Step 2: System creates a "Liability" (Company owes Employee).

Step 3: Payout via Payroll or Cash.
