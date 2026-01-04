import DataTable from '@/components/ui/DataTable';
import AppLayout from '@/layouts/app-layout';
import api from '@/lib/axios';
import { destroy, edit, index } from '@/routes/accounts';
import type { Account, FilterConfig, Transaction } from '@/types';
import {
    ArrowLeftOutlined,
    BankOutlined,
    CheckCircleOutlined,
    DeleteOutlined,
    DollarOutlined,
    EditOutlined,
    StopOutlined,
    WalletOutlined,
} from '@ant-design/icons';
import { Link, router } from '@inertiajs/react';
import { Button, Card, Descriptions, Modal, notification, Space, Tabs, Tag, theme } from 'antd';
import { useState } from 'react';

const { useToken } = theme;

interface AccountShowProps {
    account: Account | { data: Account };
}

const accountTypeIcons = {
    bank: <BankOutlined />,
    wallet: <WalletOutlined />,
    cash: <DollarOutlined />,
};

const accountTypeLabels = {
    bank: 'Bank Account',
    wallet: 'Wallet',
    cash: 'Cash',
};

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

export default function AccountShow({ account: accountProp }: AccountShowProps) {
    // Unwrap the account data if it's wrapped in a data property
    const account = 'data' in accountProp ? accountProp.data : accountProp;

    const [activeTab, setActiveTab] = useState('details');
    const { token } = useToken();

    const handleDelete = () => {
        Modal.confirm({
            title: 'Delete Account',
            content: `Are you sure you want to delete "${account.name}"? This action cannot be undone and will affect all related transactions.`,
            okText: 'Delete',
            okType: 'danger',
            cancelText: 'Cancel',
            onOk: async () => {
                try {
                    await api.delete(destroy.url(account.id));
                    notification.success({
                        message: 'Account deleted successfully',
                    });
                    router.visit(index.url());
                } catch {
                    notification.error({
                        message: 'Failed to delete account',
                        description: 'An error occurred while deleting the account.',
                    });
                }
            },
        });
    };

    const transactionColumns = [
        {
            title: 'Date',
            dataIndex: 'date',
            key: 'date',
            width: 120,
            sorter: true,
            render: (date: unknown) => new Date(date as string).toLocaleDateString(),
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
            render: (type: unknown) => (type === 'credit' ? <Tag color="green">Credit</Tag> : <Tag color="red">Debit</Tag>),
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
                    {record.type === 'credit' ? '+' : '-'}
                    {record.formatted_amount}
                </span>
            ),
        },
    ];

    const tabItems = [
        {
            key: 'details',
            label: 'Account Details',
            children: (
                <Descriptions bordered column={2}>
                    <Descriptions.Item label="Account Name" span={2}>
                        {account.name}
                    </Descriptions.Item>
                    <Descriptions.Item label="Type">
                        <Space>
                            {accountTypeIcons[account.type]}
                            {accountTypeLabels[account.type]}
                        </Space>
                    </Descriptions.Item>
                    <Descriptions.Item label="Status">
                        {account.is_active ? (
                            <Tag color="green" icon={<CheckCircleOutlined />}>
                                Active
                            </Tag>
                        ) : (
                            <Tag color="default" icon={<StopOutlined />}>
                                Inactive
                            </Tag>
                        )}
                    </Descriptions.Item>
                    <Descriptions.Item label="Currency">
                        <Tag color="blue">{account.currency_code}</Tag>
                    </Descriptions.Item>
                    <Descriptions.Item label="Current Balance">
                        <span
                            style={{
                                fontSize: 18,
                                fontWeight: 700,
                                color: account.current_balance >= 0 ? token.colorSuccess : token.colorError,
                            }}
                        >
                            {account.formatted_balance}
                        </span>
                    </Descriptions.Item>
                    {account.bank_name && (
                        <Descriptions.Item label="Bank Name" span={2}>
                            {account.bank_name}
                        </Descriptions.Item>
                    )}
                    {account.account_number && (
                        <Descriptions.Item label="Account Number" span={2}>
                            {account.account_number}
                        </Descriptions.Item>
                    )}
                    <Descriptions.Item label="Created At">{new Date(account.created_at).toLocaleString()}</Descriptions.Item>
                    <Descriptions.Item label="Last Updated">{new Date(account.updated_at).toLocaleString()}</Descriptions.Item>
                </Descriptions>
            ),
        },
        {
            key: 'transactions',
            label: 'Transaction History',
            children: (
                <DataTable<Transaction>
                    fetchUrl={`/dashboard/accounts/${account.id}/transactions`}
                    columns={transactionColumns}
                    filters={filters}
                    searchPlaceholder="Search transactions..."
                    defaultPageSize={15}
                    emptyMessage="No transactions have been recorded yet."
                    emptyFilterMessage="No transactions match your search criteria."
                />
            ),
        },
    ];

    return (
        <AppLayout
            pageTitle={account.name}
            actions={
                <Space>
                    <Link href={index.url()}>
                        <Button icon={<ArrowLeftOutlined />}>Back to Accounts</Button>
                    </Link>
                    <Link href={edit.url(account.id)}>
                        <Button type="primary" icon={<EditOutlined />}>
                            Edit Account
                        </Button>
                    </Link>
                    <Button danger icon={<DeleteOutlined />} onClick={handleDelete}>
                        Delete
                    </Button>
                </Space>
            }
        >
            <Card>
                <Tabs activeKey={activeTab} onChange={setActiveTab} items={tabItems} />
            </Card>
        </AppLayout>
    );
}
