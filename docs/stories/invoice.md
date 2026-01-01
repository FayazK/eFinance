Invoicing Module: Revenue & Realization

1. The User Story

"I need to bill my clients professionally in USD, track who owes me money, and handle the fact that Payoneer/Banks always take a cut before the money hits my account."

Invoicing is the bridge between the CRM (Clients) and the Ledger (Accounts). It is not just a PDF generator; it is the primary source of truth for Gross Revenue.

2. The Core Features

A. The Invoice Builder (Inertia/React)

A dynamic form to create invoices.

Context: Pre-filled with Client data and Currency (usually USD).

Line Items: Description, Quantity, Rate, Amount. (State managed by Zustand/React).

Projects Link: Ability to select a "Project" to tag the invoice for profitability reporting later.

PDF Generation: Uses spatie/laravel-pdf (Puppeteer) to render the invoice using the exact same Tailwind classes as the web view. This ensures the PDF looks identical to the preview.

B. The Lifecycle (States)

We use the State Pattern to manage the invoice journey:

Draft: Editable, invisible to client.

Sent: Locked. Waiting for payment.

Partial: Some money received, but balance remains.

Paid: Fully settled.

Void: Cancelled (reverses any financial impact if mistakes were made).

Overdue: Automatically flagged if due_date passes.

3. The "Split Payment" Workflow (Critical Logic)

This is the most important logic in this module. It handles the scenario where you invoice $5,000 but receive $4,750.

The Problem

If you simply record "$4,750 Income", your Revenue Report will understate your business performance. Your revenue was $5,000; your cost of business (banking) was $250.

The Solution: "Gross Up" Recording

When the user clicks "Record Payment" on an invoice:

1. User Input:

Account: Payoneer USD.

Amount Received: $4,750.

Payment Date: Today.

2. System Detection:

The system sees the Invoice Total is $5,000.

It calculates a difference of $250.

Prompt: "Is the remaining $250 a partial payment, or is it transaction fees?"

Action: User selects "Transaction Fees".

3. Database Operations (Atomic Transaction):
The system creates two financial transactions to ensure accurate accounting:

Step A (Record Revenue):

Creates a Transaction (Income) for +$5,000.

Result: Payoneer Balance implicitly hits $5,000. Revenue Report shows $5k.

Step B (Record Fee Expense):

Creates a Transaction (Expense) for -$250 linked to category "Bank Charges".

Result: Payoneer Balance drops to $4,750 (Matching reality). Expenses Report shows $250.

Step C (Close Invoice):

Updates invoice_payments table to log the split.

Marks Invoice Status as PAID.

4. Multi-Currency Invoicing

While 95% of invoices are USD, 5% are PKR.

Rule: An invoice has a fixed currency (currency_code).

Constraint: You cannot receive payment for a USD invoice into a PKR account directly through the invoice module (to prevent rate complexity).

Workflow:

Receive USD into Payoneer (USD Invoice -> USD Account).

Use the Transfers Module to move that money to PKR Account later.

Exception: If a client pays a USD invoice directly in Cash/PKR (rare), the system will ask for the Exchange Rate at the moment of payment to convert the USD invoice value into PKR ledger value.

5. Integration Points

Dashboard: Shows "Total Unpaid Invoices" (Accounts Receivable).

Client Portal (Optional): A public link where clients can view/download their invoice.

Email: System sends PDF attachments via email (using Laravel Mailable).

6. Future Proofing

Recurring Profiles: Automatically generate Invoice #102 next month based on Invoice #101.

Payment Gateway: Integration with Stripe/Payoneer API to detect incoming payments automatically (Webhooks).
