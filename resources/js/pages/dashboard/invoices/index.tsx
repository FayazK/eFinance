import React from 'react';
import { Button, Space, Tag, Dropdown, theme } from 'antd';
import { PlusOutlined, MoreOutlined, EyeOutlined, EditOutlined, FileTextOutlined, DollarOutlined, SendOutlined, DeleteOutlined } from '@ant-design/icons';
import { Link, router } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import DataTable from '@/components/ui/DataTable';
import type { Invoice, FilterConfig, InvoiceStatus } from '@/types';
import { data, create, show, edit, pdf } from '@/routes/invoices';
import api from '@/lib/axios';
import { notification } from 'antd';

const { useToken } = theme;

const STATUS_COLORS: Record<InvoiceStatus, string> = {
    draft: 'default',
    sent: 'blue',
    partial: 'orange',
    paid: 'green',
    void: 'red',
    overdue: 'red',
};

const filters: FilterConfig[] = [
    {
        type: 'select',
        key: 'status',
        label: 'Status',
        options: [
            { label: 'Draft', value: 'draft' },
            { label: 'Sent', value: 'sent' },
            { label: 'Partial', value: 'partial' },
            { label: 'Paid', value: 'paid' },
            { label: 'Overdue', value: 'overdue' },
            { label: 'Void', value: 'void' },
        ],
    },
    {
        type: 'dateRange',
        key: 'date_range',
        label: 'Date Range',
    },
];

export default function InvoicesIndex() {
    const { token } = useToken();

    const handleDelete = async (id: number) => {
        try {
            await api.delete(`/dashboard/invoices/${id}`);
            notification.success({ message: 'Invoice deleted successfully' });
            router.reload();
        } catch (error: unknown) {
            const err = error as { response?: { data: { message: string } } };
            notification.error({
                message: 'Error',
                description: err.response?.data.message || 'Failed to delete invoice',
            });
        }
    };

    const columns = [
        {
            title: 'Invoice #',
            dataIndex: 'invoice_number',
            key: 'invoice_number',
            width: 150,
            searchable: true,
            sorter: true,
            render: (text: string, record: Invoice) => (
                <Link href={show.url({ id: record.id })} style={{ fontWeight: 500 }}>
                    {text}
                </Link>
            ),
        },
        {
            title: 'Client',
            dataIndex: 'client',
            key: 'client',
            width: 200,
            render: (_: unknown, record: Invoice) => (
                <div>
                    <div style={{ fontWeight: 500 }}>{record.client?.name || '—'}</div>
                    {record.project && (
                        <div style={{ color: token.colorTextSecondary, fontSize: '12px' }}>
                            {record.project.name}
                        </div>
                    )}
                </div>
            ),
        },
        {
            title: 'Status',
            dataIndex: 'status',
            key: 'status',
            width: 120,
            filterable: true,
            render: (status: InvoiceStatus) => (
                <Tag color={STATUS_COLORS[status]}>
                    {status.charAt(0).toUpperCase() + status.slice(1)}
                </Tag>
            ),
        },
        {
            title: 'Issue Date',
            dataIndex: 'issue_date',
            key: 'issue_date',
            width: 120,
            sorter: true,
            render: (date: string) => new Date(date).toLocaleDateString(),
        },
        {
            title: 'Due Date',
            dataIndex: 'due_date',
            key: 'due_date',
            width: 120,
            sorter: true,
            render: (date: string, record: Invoice) => {
                const isOverdue = record.is_overdue;
                return (
                    <span style={{ color: isOverdue ? token.colorError : undefined }}>
                        {new Date(date).toLocaleDateString()}
                    </span>
                );
            },
        },
        {
            title: 'Total',
            dataIndex: 'total_amount',
            key: 'total_amount',
            width: 150,
            sorter: true,
            render: (_: unknown, record: Invoice) => (
                <div>
                    <div style={{ fontWeight: 600 }}>{record.formatted_total}</div>
                    {record.amount_paid > 0 && (
                        <div style={{ fontSize: '12px', color: token.colorTextSecondary }}>
                            Paid: {record.formatted_amount_paid}
                        </div>
                    )}
                </div>
            ),
        },
        {
            title: 'Balance',
            dataIndex: 'balance_due',
            key: 'balance_due',
            width: 120,
            render: (_: unknown, record: Invoice) => (
                <span style={{ fontWeight: 600, color: record.balance_due > 0 ? token.colorWarning : token.colorSuccess }}>
                    {record.formatted_balance}
                </span>
            ),
        },
        {
            title: 'Actions',
            key: 'actions',
            width: 100,
            fixed: 'right' as const,
            render: (_: unknown, record: Invoice) => (
                <Dropdown
                    menu={{
                        items: [
                            {
                                key: 'view',
                                label: 'View',
                                icon: <EyeOutlined />,
                                onClick: () => router.visit(show.url({ id: record.id })),
                            },
                            ...(record.status === 'draft'
                                ? [
                                    {
                                        key: 'edit',
                                        label: 'Edit',
                                        icon: <EditOutlined />,
                                        onClick: () => router.visit(edit.url({ id: record.id })),
                                    },
                                ]
                                : []),
                            {
                                key: 'pdf',
                                label: 'Download PDF',
                                icon: <FileTextOutlined />,
                                onClick: () => window.open(pdf.url({ id: record.id }), '_blank'),
                            },
                            ...(record.is_payable
                                ? [
                                    {
                                        type: 'divider' as const,
                                    },
                                    {
                                        key: 'payment',
                                        label: 'Record Payment',
                                        icon: <DollarOutlined />,
                                        onClick: () => router.visit(show.url({ id: record.id })),
                                    },
                                ]
                                : []),
                            ...(record.status === 'draft'
                                ? [
                                    {
                                        type: 'divider' as const,
                                    },
                                    {
                                        key: 'send',
                                        label: 'Send to Client',
                                        icon: <SendOutlined />,
                                        onClick: async () => {
                                            try {
                                                await api.post(`/dashboard/invoices/${record.id}/send-email`);
                                                notification.success({ message: 'Invoice sent successfully' });
                                                router.reload();
                                            } catch {
                                                notification.error({ message: 'Failed to send invoice' });
                                            }
                                        },
                                    },
                                    {
                                        key: 'delete',
                                        label: 'Delete',
                                        icon: <DeleteOutlined />,
                                        danger: true,
                                        onClick: () => handleDelete(record.id),
                                    },
                                ]
                                : []),
                        ],
                    }}
                    trigger={['click']}
                >
                    <Button type="text" icon={<MoreOutlined />} />
                </Dropdown>
            ),
        },
    ];

    return (
        <AppLayout
            pageTitle="Invoices"
            actions={
                <Link href={create.url()}>
                    <Button type="primary" icon={<PlusOutlined />}>
                        Create Invoice
                    </Button>
                </Link>
            }
        >
            <DataTable<Invoice>
                fetchUrl={data.url()}
                columns={columns}
                filters={filters}
                searchPlaceholder="Search invoices by invoice number..."
                defaultPageSize={15}
                emptyMessage="No invoices have been created yet."
                emptyFilterMessage="No invoices match your search criteria."
            />
        </AppLayout>
    );
}

InvoicesIndex.layout = (page: React.ReactNode) => page;
