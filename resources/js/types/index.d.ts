import { InertiaLinkProps } from '@inertiajs/react';
import { ForwardRefExoticComponent, RefAttributes } from 'react';
import { AntdIconProps } from '@ant-design/icons/lib/components/AntdIcon';

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

export interface Currency {
    id: number;
    name: string;
    code: string;
    symbol: string;
}

export interface Client {
    id: number;
    name: string;
    email: string;
    country?: Country;
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
    [key: string]: string | number | boolean | Country | City | Currency | null | undefined;
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
    city?: string;
    state?: string;
    country?: Country;
    primary_phone?: string;
    primary_email: string;
    additional_phones: string[];
    additional_emails: string[];
    created_at: string;
    updated_at: string;
    [key: string]: string | number | boolean | Country | { id: number; name: string } | string[] | null | undefined;
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
    [key: string]:
        | string
        | number
        | boolean
        | { id: number; name: string; currency_code?: string }
        | TransactionCategory
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
    FilterConfig,
    BooleanFilterConfig,
    SelectFilterConfig,
    DateRangeFilterConfig,
    CustomFilterConfig,
    SortState,
    DataTableError,
    DataTableColumn,
    DataTableProps,
    DataTableFilters,
    DataTableQueryParams,
} from './datatable';
