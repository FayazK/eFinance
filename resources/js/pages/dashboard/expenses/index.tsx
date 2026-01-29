import DataTable from '@/components/ui/DataTable';
import AppLayout from '@/layouts/app-layout';
import { create, data, edit, process } from '@/routes/expenses';
import type { Account, Expense, FilterConfig, TransactionCategory } from '@/types';
import { CheckCircleOutlined, EditOutlined, PlusOutlined, StopOutlined } from '@ant-design/icons';
import ExpenseVoidModal from './partials/expense-void-modal';
import { Link, router, usePage } from '@inertiajs/react';
import { Button, Modal, notification, Space, Tag, theme, Tooltip } from 'antd';
import React, { useState } from 'react';

const { useToken } = theme;

interface ExpensesIndexProps {
    accounts: Account[];
    categories: TransactionCategory[];
}

export default function ExpensesIndex() {
    const { token } = useToken();
    const { accounts, categories } = usePage<ExpensesIndexProps>().props;
    const [processModalOpen, setProcessModalOpen] = useState(false);
    const [selectedExpense, setSelectedExpense] = useState<Expense | null>(null);
    const [processing, setProcessing] = useState(false);
    const [voidModalOpen, setVoidModalOpen] = useState(false);
    const [expenseToVoid, setExpenseToVoid] = useState<Expense | null>(null);

    const handleProcessClick = (expense: Expense) => {
        setSelectedExpense(expense);
        setProcessModalOpen(true);
    };

    const handleProcessConfirm = () => {
        if (!selectedExpense) return;

        setProcessing(true);
        router.post(
            process.url(selectedExpense.id),
            {},
            {
                onSuccess: () => {
                    notification.success({
                        message: 'Expense Processed',
                        description: 'The expense has been processed and the transaction has been created.',
                    });
                    setProcessModalOpen(false);
                    setSelectedExpense(null);
                },
                onError: (errors) => {
                    notification.error({
                        message: 'Processing Failed',
                        description: Object.values(errors)[0] as string,
                    });
                },
                onFinish: () => {
                    setProcessing(false);
                },
            },
        );
    };

    const handleVoidClick = (expense: Expense) => {
        setExpenseToVoid(expense);
        setVoidModalOpen(true);
    };

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
                { label: 'Voided', value: 'voided' },
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
            width: 250,
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
                    voided: 'orange',
                    cancelled: 'red',
                };
                return <Tag color={colorMap[statusValue as keyof typeof colorMap]}>{statusValue}</Tag>;
            },
        },
        {
            title: 'Actions',
            key: 'actions',
            width: 120,
            render: (_: unknown, record: Expense) => {
                if (record.status === 'draft') {
                    return (
                        <Space size="small">
                            <Tooltip title="Edit">
                                <Link href={edit.url(record.id)}>
                                    <Button type="text" size="small" icon={<EditOutlined />} />
                                </Link>
                            </Tooltip>
                            <Tooltip title="Process">
                                <Button
                                    type="text"
                                    size="small"
                                    icon={<CheckCircleOutlined />}
                                    style={{ color: token.colorSuccess }}
                                    onClick={() => handleProcessClick(record)}
                                />
                            </Tooltip>
                        </Space>
                    );
                }

                if (record.status === 'processed') {
                    return (
                        <Tooltip title="Void">
                            <Button
                                type="text"
                                size="small"
                                icon={<StopOutlined />}
                                style={{ color: token.colorError }}
                                onClick={() => handleVoidClick(record)}
                            />
                        </Tooltip>
                    );
                }

                return <span style={{ color: token.colorTextDisabled }}>—</span>;
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

            <Modal
                title="Process Expense"
                open={processModalOpen}
                onOk={handleProcessConfirm}
                onCancel={() => {
                    setProcessModalOpen(false);
                    setSelectedExpense(null);
                }}
                okText="Process"
                okButtonProps={{ loading: processing }}
                cancelButtonProps={{ disabled: processing }}
            >
                <p>
                    This will create a transaction and deduct <strong>{selectedExpense?.formatted_amount}</strong> from the account{' '}
                    <strong>{selectedExpense?.account?.name}</strong>.
                </p>
                <p style={{ color: token.colorWarning, marginTop: 8 }}>This action cannot be undone. Continue?</p>
            </Modal>

            {expenseToVoid && (
                <ExpenseVoidModal
                    open={voidModalOpen}
                    onCancel={() => {
                        setVoidModalOpen(false);
                        setExpenseToVoid(null);
                    }}
                    expense={expenseToVoid}
                />
            )}
        </AppLayout>
    );
}

ExpensesIndex.layout = (page: React.ReactNode) => page;
