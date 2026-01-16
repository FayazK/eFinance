import { AntdIconProps } from '@ant-design/icons/lib/components/AntdIcon';
import { InertiaLinkProps } from '@inertiajs/react';
import { ForwardRefExoticComponent, RefAttributes } from 'react';

export interface Auth {
    user: User;
}

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface NavGroup {
    title: string;
    items: NavItem[];
}

export interface NavItem {
    title: string;
    href: NonNullable<InertiaLinkProps['href']>;
    icon?: ForwardRefExoticComponent<Omit<AntdIconProps, 'ref'> & RefAttributes<HTMLSpanElement>> | null;
    isActive?: boolean;
}

export interface SharedData {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
    sidebarOpen: boolean;
    [key: string]: unknown;
}

export interface User {
    id: number;
    first_name: string;
    last_name: string;
    email: string;
    phone?: string;
    date_of_birth?: string;
    avatar_url?: string;
    avatar_thumb_url?: string;
    bio?: string;
    timezone: string;
    locale: string;
    is_active: boolean;
    last_login_at?: string;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
    full_name: string; // Computed attribute from Laravel
    initials: string; // Computed attribute from Laravel
    [key: string]: string | number | boolean | null | undefined; // Required for DataTable generic constraint
}

export interface Country {
    id: number;
    name: string;
    iso2: string;
    emoji?: string;
}

export interface City {
    id: number;
    name: string;
}

export interface State {
    id: number;
    name: string;
}

export interface Currency {
    id: number;
    name: string;
    code: string;
    symbol: string;
}

export interface Company {
    id: number;
    name: string;
    logo_url: string | null;
    address: string | null;
    phone: string | null;
    email: string | null;
    tax_id: string | null;
    vat_number: string | null;
    created_at: string;
    updated_at: string;
    [key: string]: string | number | boolean | null | undefined;
}

export interface Client {
    id: number;
    name: string;
    email: string;
    country?: Country;
    state?: State;
    city?: City;
    currency?: Currency;
    address?: string;
    phone?: string;
    company?: string;
    tax_id?: string;
    website?: string;
    notes?: string;
    created_at: string;
    updated_at: string;
    [key: string]: string | number | boolean | Country | State | City | Currency | null | undefined;
}

export interface Contact {
    id: number;
    first_name: string;
    last_name: string;
    full_name: string;
    client?: {
        id: number;
        name: string;
    };
    address?: string;
    country?: Country;
    state?: State;
    city?: City;
    primary_phone?: string;
    primary_email: string;
    additional_phones: string[];
    additional_emails: string[];
    created_at: string;
    updated_at: string;
    [key: string]: string | number | boolean | Country | State | City | { id: number; name: string } | string[] | null | undefined;
}

export interface Media {
    id: number;
    name: string;
    file_name?: string;
    size: number;
    mime_type: string;
    url: string;
    created_at: string;
}

export interface ProjectLink {
    id: number;
    project_id: number;
    title: string;
    url: string;
    description?: string;
    created_at: string;
    updated_at: string;
}

export interface Project {
    id: number;
    name: string;
    description?: string;
    client?: Client;
    client_id: number;
    start_date?: string;
    completion_date?: string;
    status: 'Planning' | 'Active' | 'Completed' | 'Cancelled';
    budget?: number;
    actual_cost?: number;
    documents_count?: number;
    created_at: string;
    updated_at: string;
    deleted_at?: string;
    documents?: Media[];
    links?: ProjectLink[];
    [key: string]: string | number | boolean | Client | Media[] | ProjectLink[] | null | undefined;
}

export interface Account {
    id: number;
    name: string;
    type: 'bank' | 'wallet' | 'cash';
    currency_code: string;
    current_balance: number; // In major units for editing
    formatted_balance: string; // For display
    account_number?: string;
    bank_name?: string;
    is_active: boolean;
    created_at: string;
    updated_at: string;
    [key: string]: string | number | boolean | null | undefined;
}

export interface TransactionCategory {
    id: number;
    name: string;
    type: 'income' | 'expense';
    color?: string;
    created_at: string;
    updated_at: string;
}

export interface Transaction {
    id: number;
    account?: {
        id: number;
        name: string;
        currency_code: string;
    };
    category?: TransactionCategory;
    type: 'credit' | 'debit';
    amount: number; // In major units for editing
    formatted_amount: string; // For display
    description?: string;
    date: string;
    created_at: string;
    updated_at: string;
    [key: string]: string | number | boolean | { id: number; name: string; currency_code?: string } | TransactionCategory | null | undefined;
}

export interface Transfer {
    id: number;
    source_account?: {
        id: number;
        name: string;
        currency_code: string;
    };
    destination_account?: {
        id: number;
        name: string;
        currency_code: string;
    };
    source_amount: number;
    destination_amount: number;
    formatted_source_amount: string;
    formatted_destination_amount: string;
    exchange_rate: number;
    formatted_exchange_rate: string;
    fee_amount?: number;
    formatted_fee?: string;
    has_fee?: boolean;
    description?: string;
    date: string;
    created_at: string;
    updated_at: string;
    [key: string]: string | number | boolean | { id: number; name: string; currency_code: string } | undefined;
}

export type InvoiceStatus = 'draft' | 'sent' | 'partial' | 'paid' | 'void' | 'overdue';

export type InvoiceTemplate = 'modern' | 'classic' | 'minimal' | 'corporate' | 'creative';

export interface InvoiceTemplateOption {
    value: InvoiceTemplate;
    label: string;
    description: string;
}

export interface InvoiceItem {
    id: number;
    description: string;
    quantity: number;
    unit: string;
    unit_price: number; // In major units for editing
    amount: number; // In major units for editing
    formatted_unit_price: string; // For display
    formatted_amount: string; // For display
    sort_order: number;
}

export interface InvoicePayment {
    id: number;
    account_id: number;
    account?: {
        id: number;
        name: string;
        currency_code: string;
    };
    payment_amount: number; // In major units
    amount_received: number; // In major units
    fee_amount: number; // In major units
    formatted_payment_amount: string;
    formatted_amount_received: string;
    formatted_fee: string;
    has_fee: boolean;
    payment_date: string;
    notes?: string;
    created_at: string;
}

export interface Invoice {
    id: number;
    invoice_number: string;
    status: InvoiceStatus;
    template: InvoiceTemplate;
    company_id?: number;
    company?: {
        id: number;
        name: string;
        logo_url?: string;
    };
    client_id: number;
    client?: Client;
    project_id?: number;
    project?: {
        id: number;
        name: string;
    };
    currency_code: string;
    subtotal: number; // In major units
    tax_amount: number; // In major units
    total_amount: number; // In major units
    amount_paid: number; // In major units
    balance_due: number; // In major units
    formatted_subtotal: string;
    formatted_tax: string;
    formatted_total: string;
    formatted_amount_paid: string;
    formatted_balance: string;
    issue_date: string;
    due_date: string;
    paid_at?: string;
    sent_at?: string;
    voided_at?: string;
    notes?: string;
    terms?: string;
    client_notes?: string;
    created_at: string;
    updated_at: string;
    items?: InvoiceItem[];
    payments?: InvoicePayment[];
    is_overdue: boolean;
    is_payable: boolean;
    [key: string]: string | number | boolean | Client | { id: number; name: string } | InvoiceItem[] | InvoicePayment[] | null | undefined;
}

export interface Employee {
    id: number;
    name: string;
    designation: string;
    email: string;
    joining_date: string;
    base_salary: number; // In major units (always PKR)
    deposit_currency: 'PKR' | 'USD'; // How salary is deposited
    formatted_salary: string;
    iban?: string;
    bank_name?: string;
    status: 'active' | 'terminated';
    is_active: boolean;
    termination_date?: string;
    created_at: string;
    updated_at: string;
    payrolls?: Payroll[];
    [key: string]: string | number | boolean | Payroll[] | null | undefined;
}

export interface Payroll {
    id: number;
    employee_id: number;
    employee?: Employee;
    month: number;
    year: number;
    period_label: string;
    base_salary: number; // In major units (always PKR)
    deposit_currency: 'PKR' | 'USD'; // How salary is deposited
    bonus: number;
    deductions: number;
    net_payable: number;
    formatted_base_salary: string;
    formatted_bonus: string;
    formatted_deductions: string;
    formatted_net_payable: string;
    status: 'pending' | 'paid';
    is_pending: boolean;
    is_paid: boolean;
    paid_at?: string;
    transaction_id?: number;
    transaction?: Transaction;
    notes?: string;
    created_at: string;
    updated_at: string;
    [key: string]: string | number | boolean | Employee | Transaction | null | undefined;
}

export interface Shareholder {
    id: number;
    name: string;
    email?: string;
    equity_percentage: string; // As decimal string (e.g., "25.50")
    formatted_equity: string; // Formatted with % sign
    is_office_reserve: boolean;
    is_human_partner: boolean;
    is_active: boolean;
    notes?: string;
    created_at: string;
    updated_at: string;
    [key: string]: string | number | boolean | null | undefined;
}

export type DistributionStatus = 'draft' | 'processed';

export interface DistributionLine {
    id: number;
    distribution_id: number;
    shareholder_id: number;
    shareholder?: Shareholder;
    equity_percentage_snapshot: string;
    formatted_equity: string;
    allocated_amount_pkr: number; // In major units (PKR)
    formatted_allocated_amount: string;
    transaction_id?: number;
    transaction?: Transaction;
    created_at: string;
    updated_at: string;
}

export interface Distribution {
    id: number;
    distribution_number: string;
    status: DistributionStatus;
    is_draft: boolean;
    is_processed: boolean;
    period_start: string;
    period_end: string;
    period_label: string;
    total_revenue_pkr: number; // In major units (PKR)
    total_expenses_pkr: number;
    calculated_net_profit_pkr: number;
    adjusted_net_profit_pkr?: number;
    final_net_profit: number;
    distributed_amount_pkr: number;
    formatted_revenue: string;
    formatted_expenses: string;
    formatted_net_profit: string;
    is_manually_adjusted: boolean;
    adjustment_reason?: string;
    processed_at?: string;
    notes?: string;
    lines?: DistributionLine[];
    created_at: string;
    updated_at: string;
    [key: string]: string | number | boolean | DistributionLine[] | null | undefined;
}

export type ExpenseStatus = 'draft' | 'processed' | 'cancelled';

export interface Expense {
    id: number;
    account_id: number;
    account?: {
        id: number;
        name: string;
        currency_code: string;
    };
    category_id?: number;
    category?: TransactionCategory;
    transaction_id?: number;
    transaction?: Transaction;
    amount: number; // In major units for editing
    formatted_amount: string; // For display
    currency_code: string;
    vendor?: string;
    description?: string;
    expense_date: string;
    // Recurring fields
    is_recurring: boolean;
    is_active?: boolean;
    recurrence_frequency?: 'monthly' | 'quarterly' | 'yearly';
    recurrence_interval?: number;
    recurrence_start_date?: string;
    recurrence_end_date?: string;
    next_occurrence_date?: string;
    last_processed_date?: string;
    // International/multi-currency fields
    exchange_rate?: number;
    reporting_amount_pkr?: number; // In major units (PKR)
    formatted_reporting_amount?: string;
    // Status
    status: ExpenseStatus;
    is_pending?: boolean;
    is_processed?: boolean;
    is_recurring_template?: boolean;
    // Media
    receipts?: Media[];
    receipts_count?: number;
    // Timestamps
    created_at: string;
    updated_at: string;
    [key: string]:
        | string
        | number
        | boolean
        | { id: number; name: string; currency_code?: string }
        | TransactionCategory
        | Transaction
        | Media[]
        | null
        | undefined;
}

export interface PaginationLinks {
    first: string | null;
    last: string | null;
    prev: string | null;
    next: string | null;
}

export interface PaginationMeta {
    current_page: number;
    from: number | null;
    last_page: number;
    per_page: number;
    to: number | null;
    total: number;
    path: string;
}

export interface LaravelPaginatedResponse<T = unknown> {
    data: T[];
    links: PaginationLinks;
    meta: PaginationMeta;
}

// Re-export DataTable types from dedicated module
export type {
    BooleanFilterConfig,
    CustomFilterConfig,
    DataTableColumn,
    DataTableError,
    DataTableFilters,
    DataTableProps,
    DataTableQueryParams,
    DateRangeFilterConfig,
    FilterConfig,
    SelectFilterConfig,
    SortState,
} from './datatable';
