import DataTable from '@/components/ui/DataTable';
import AppLayout from '@/layouts/app-layout';
import { create, data } from '@/routes/expenses';
import type { Account, Expense, FilterConfig, TransactionCategory } from '@/types';
import { PlusOutlined } from '@ant-design/icons';
import { Link, usePage } from '@inertiajs/react';
import { Button, Tag, theme } from 'antd';
import React from 'react';

const { useToken } = theme;

interface ExpensesIndexProps {
    accounts: Account[];
    categories: TransactionCategory[];
}

export default function ExpensesIndex() {
    const { token } = useToken();
    const { accounts, categories } = usePage<ExpensesIndexProps>().props;

    const filters: FilterConfig[] = [
        {
            type: 'select',
            key: 'account_id',
            label: 'Account',
            options: accounts.map((account) => ({
                label: `${account.name} (${account.currency_code})`,
                value: account.id,
            })),
        },
        {
            type: 'select',
            key: 'category_id',
            label: 'Category',
            options: categories.map((category) => ({
                label: category.name,
                value: category.id,
            })),
        },
        {
            type: 'select',
            key: 'status',
            label: 'Status',
            options: [
                { label: 'Draft', value: 'draft' },
                { label: 'Processed', value: 'processed' },
                { label: 'Cancelled', value: 'cancelled' },
            ],
        },
        {
            type: 'dateRange',
            key: 'expense_date',
            label: 'Expense Date',
        },
    ];

    const columns = [
        {
            title: 'Date',
            dataIndex: 'expense_date',
            key: 'expense_date',
            width: 100,
            sorter: true,
            filterable: true,
            render: (date: unknown) => new Date(date as string).toLocaleDateString(),
        },
        {
            title: 'Vendor',
            dataIndex: 'vendor',
            key: 'vendor',
            width: 150,
            searchable: true,
            render: (vendor: unknown) => (vendor as string) || <span style={{ color: token.colorTextDisabled }}>—</span>,
        },
        {
            title: 'Description',
            dataIndex: 'description',
            key: 'description',
            width: 300,
            searchable: true,
            render: (description: unknown, record: Expense) => {
                const text = (description as string) || '—';
                const textStyle: React.CSSProperties = {
                    display: '-webkit-box',
                    WebkitLineClamp: 2,
                    WebkitBoxOrient: 'vertical',
                    overflow: 'hidden',
                };

                if (record.is_recurring) {
                    return (
                        <div>
                            <Tag color="blue" style={{ marginBottom: 4 }}>
                                Recurring
                            </Tag>
                            <div style={textStyle}>{text}</div>
                        </div>
                    );
                }
                return <div style={textStyle}>{text}</div>;
            },
        },
        {
            title: 'Account',
            dataIndex: 'account',
            key: 'account',
            width: 140,
            filterable: true,
            render: (_: unknown, record: Expense) => (
                <div>
                    <div style={{ fontWeight: 500 }}>{record.account?.name || '—'}</div>
                    {record.account?.currency_code && (
                        <div style={{ color: token.colorTextSecondary, fontSize: '12px' }}>{record.account.currency_code}</div>
                    )}
                </div>
            ),
        },
        {
            title: 'Category',
            dataIndex: 'category',
            key: 'category',
            width: 120,
            filterable: true,
            render: (_: unknown, record: Expense) =>
                record.category ? (
                    <Tag color={record.category.color || 'default'}>{record.category.name}</Tag>
                ) : (
                    <span style={{ color: token.colorTextDisabled }}>Uncategorized</span>
                ),
        },
        {
            title: 'Amount',
            dataIndex: 'amount',
            key: 'amount',
            width: 120,
            sorter: true,
            render: (_: unknown, record: Expense) => (
                <div>
                    <div style={{ fontWeight: 600, color: token.colorError }}>−{record.formatted_amount}</div>
                    {record.exchange_rate && record.currency_code !== 'PKR' && (
                        <div style={{ color: token.colorTextSecondary, fontSize: '12px' }}>@ {record.exchange_rate} PKR</div>
                    )}
                </div>
            ),
        },
        {
            title: 'Status',
            dataIndex: 'status',
            key: 'status',
            width: 100,
            filterable: true,
            render: (status: unknown) => {
                const statusValue = status as string;
                const colorMap = {
                    draft: 'default',
                    processed: 'green',
                    cancelled: 'red',
                };
                return <Tag color={colorMap[statusValue as keyof typeof colorMap]}>{statusValue}</Tag>;
            },
        },
    ];

    return (
        <AppLayout
            pageTitle="Expenses"
            actions={
                <Link href={create.url()}>
                    <Button type="primary" icon={<PlusOutlined />}>
                        Record Expense
                    </Button>
                </Link>
            }
        >
            <DataTable<Expense>
                fetchUrl={data.url()}
                columns={columns}
                filters={filters}
                searchPlaceholder="Search expenses by vendor or description..."
                defaultPageSize={15}
                emptyMessage="No expenses have been recorded yet."
                emptyFilterMessage="No expenses match your search criteria."
            />
        </AppLayout>
    );
}

ExpensesIndex.layout = (page: React.ReactNode) => page;
