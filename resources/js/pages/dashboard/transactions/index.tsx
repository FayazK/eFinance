import React from 'react';
import { Button, Space, Tag, theme } from 'antd';
import { PlusOutlined } from '@ant-design/icons';
import { Link } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import DataTable from '@/components/ui/DataTable';
import type { Transaction, FilterConfig } from '@/types';
import { data, create } from '@/routes/transactions';

const { useToken } = theme;

const filters: FilterConfig[] = [
    {
        type: 'select',
        key: 'type',
        label: 'Type',
        options: [
            { label: 'Credit', value: 'credit' },
            { label: 'Debit', value: 'debit' },
        ],
    },
    {
        type: 'dateRange',
        key: 'date',
        label: 'Date',
    },
];

export default function TransactionsIndex() {
    const { token } = useToken();

    const columns = [
        {
            title: 'Date',
            dataIndex: 'date',
            key: 'date',
            width: 120,
            sorter: true,
            filterable: true,
            render: (date: unknown) => new Date(date as string).toLocaleDateString(),
        },
        {
            title: 'Account',
            dataIndex: 'account',
            key: 'account',
            width: 200,
            render: (_: unknown, record: Transaction) => (
                <div>
                    <div style={{ fontWeight: 500 }}>{record.account?.name || '—'}</div>
                    {record.account?.currency_code && (
                        <div style={{ color: token.colorTextSecondary, fontSize: '12px' }}>
                            {record.account.currency_code}
                        </div>
                    )}
                </div>
            ),
        },
        {
            title: 'Description',
            dataIndex: 'description',
            key: 'description',
            searchable: true,
            render: (description: unknown) => (description as string) || '—',
        },
        {
            title: 'Category',
            dataIndex: 'category',
            key: 'category',
            width: 150,
            render: (_: unknown, record: Transaction) =>
                record.category ? (
                    <Tag color={record.category.color || 'default'}>{record.category.name}</Tag>
                ) : (
                    <span style={{ color: token.colorTextDisabled }}>Uncategorized</span>
                ),
        },
        {
            title: 'Type',
            dataIndex: 'type',
            key: 'type',
            width: 100,
            filterable: true,
            render: (type: unknown) =>
                type === 'credit' ? <Tag color="green">Credit</Tag> : <Tag color="red">Debit</Tag>,
        },
        {
            title: 'Amount',
            dataIndex: 'amount',
            key: 'amount',
            width: 150,
            sorter: true,
            render: (_: unknown, record: Transaction) => (
                <span
                    style={{
                        fontWeight: 600,
                        color: record.type === 'credit' ? token.colorSuccess : token.colorError,
                    }}
                >
                    {record.type === 'credit' ? '+' : '−'}
                    {record.formatted_amount}
                </span>
            ),
        },
    ];

    return (
        <AppLayout
            pageTitle="Transactions"
            actions={
                <Link href={create.url()}>
                    <Button type="primary" icon={<PlusOutlined />}>
                        Record Transaction
                    </Button>
                </Link>
            }
        >
            <DataTable<Transaction>
                fetchUrl={data.url()}
                columns={columns}
                filters={filters}
                searchPlaceholder="Search transactions by description..."
                defaultPageSize={15}
                emptyMessage="No transactions have been recorded yet."
                emptyFilterMessage="No transactions match your search criteria."
            />
        </AppLayout>
    );
}

TransactionsIndex.layout = (page: React.ReactNode) => page;
