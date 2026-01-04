import AppLayout from '@/layouts/app-layout';
import api from '@/lib/axios';
import { edit, index, pdf } from '@/routes/invoices';
import type { Account, Invoice, InvoicePayment, InvoiceStatus } from '@/types';
import { ArrowLeftOutlined, DollarOutlined, EditOutlined, FileTextOutlined, SendOutlined, StopOutlined } from '@ant-design/icons';
import { Link, router } from '@inertiajs/react';
import { Button, Card, Descriptions, Modal, notification, Space, Table, Tabs, Tag, theme } from 'antd';
import React, { useState } from 'react';
import InvoicePaymentModal from './partials/invoice-payment-modal';

const { useToken } = theme;

const STATUS_COLORS: Record<InvoiceStatus, string> = {
    draft: 'default',
    sent: 'blue',
    partial: 'orange',
    paid: 'green',
    void: 'red',
    overdue: 'red',
};

interface InvoiceShowProps {
    invoice: Invoice;
    accounts: Account[];
}

export default function InvoiceShow({ invoice, accounts }: InvoiceShowProps) {
    const { token } = useToken();
    const [paymentModalOpen, setPaymentModalOpen] = useState(false);

    const handleVoidInvoice = () => {
        Modal.confirm({
            title: 'Void Invoice',
            content: 'Are you sure you want to void this invoice? This action cannot be undone.',
            okText: 'Void Invoice',
            okType: 'danger',
            cancelText: 'Cancel',
            onOk: async () => {
                try {
                    await api.post(`/dashboard/invoices/${invoice.id}/void`);
                    notification.success({ message: 'Invoice voided successfully' });
                    router.reload();
                } catch (error: unknown) {
                    const err = error as { response?: { data: { message: string } } };
                    notification.error({
                        message: 'Error',
                        description: err.response?.data.message || 'Failed to void invoice',
                    });
                }
            },
        });
    };

    const handleSendInvoice = async () => {
        try {
            await api.post(`/dashboard/invoices/${invoice.id}/send-email`);
            notification.success({ message: 'Invoice sent successfully' });
            router.reload();
        } catch (error: unknown) {
            const err = error as { response?: { data: { message: string } } };
            notification.error({
                message: 'Error',
                description: err.response?.data.message || 'Failed to send invoice',
            });
        }
    };

    const paymentColumns = [
        {
            title: 'Date',
            dataIndex: 'payment_date',
            key: 'payment_date',
            render: (date: string) => new Date(date).toLocaleDateString(),
        },
        {
            title: 'Account',
            dataIndex: 'account',
            key: 'account',
            render: (account: { name: string; currency_code: string }) => `${account.name} (${account.currency_code})`,
        },
        {
            title: 'Payment Amount',
            dataIndex: 'formatted_payment_amount',
            key: 'payment_amount',
        },
        {
            title: 'Amount Received',
            dataIndex: 'formatted_amount_received',
            key: 'amount_received',
        },
        {
            title: 'Fee',
            dataIndex: 'formatted_fee',
            key: 'fee',
            render: (text: string, record: InvoicePayment) =>
                record.has_fee ? <span style={{ color: token.colorError }}>{text}</span> : <span>—</span>,
        },
        {
            title: 'Notes',
            dataIndex: 'notes',
            key: 'notes',
            render: (text: string) => text || '—',
        },
    ];

    const itemColumns = [
        {
            title: 'Description',
            dataIndex: 'description',
            key: 'description',
        },
        {
            title: 'Quantity',
            dataIndex: 'quantity',
            key: 'quantity',
            width: 100,
        },
        {
            title: 'Unit',
            dataIndex: 'unit',
            key: 'unit',
            width: 100,
        },
        {
            title: 'Unit Price',
            dataIndex: 'formatted_unit_price',
            key: 'unit_price',
            width: 150,
        },
        {
            title: 'Amount',
            dataIndex: 'formatted_amount',
            key: 'amount',
            width: 150,
            render: (text: string) => <span style={{ fontWeight: 600 }}>{text}</span>,
        },
    ];

    const tabItems = [
        {
            key: 'preview',
            label: 'Preview',
            children: (
                <Card>
                    <div style={{ maxWidth: 800, margin: '0 auto' }}>
                        {/* Invoice Header */}
                        <div style={{ borderBottom: `2px solid ${token.colorBorder}`, paddingBottom: 24, marginBottom: 24 }}>
                            <h1 style={{ fontSize: 32, fontWeight: 700, margin: 0 }}>INVOICE</h1>
                            <p style={{ fontSize: 16, color: token.colorTextSecondary, margin: '8px 0 0' }}>{invoice.invoice_number}</p>
                        </div>

                        {/* Client and Invoice Info */}
                        <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 24, marginBottom: 32 }}>
                            <div>
                                <h3 style={{ fontSize: 12, fontWeight: 600, textTransform: 'uppercase', marginBottom: 8 }}>Bill To</h3>
                                <div style={{ fontSize: 14 }}>
                                    <div style={{ fontWeight: 600, marginBottom: 4 }}>{invoice.client?.name}</div>
                                    {invoice.client?.email && <div>{invoice.client.email}</div>}
                                    {invoice.client?.company && <div>{invoice.client.company}</div>}
                                </div>
                            </div>
                            <div>
                                <h3 style={{ fontSize: 12, fontWeight: 600, textTransform: 'uppercase', marginBottom: 8 }}>Invoice Details</h3>
                                <div style={{ fontSize: 14 }}>
                                    <div style={{ display: 'flex', justifyContent: 'space-between', marginBottom: 4 }}>
                                        <span>Issue Date:</span>
                                        <span>{new Date(invoice.issue_date).toLocaleDateString()}</span>
                                    </div>
                                    <div style={{ display: 'flex', justifyContent: 'space-between', marginBottom: 4 }}>
                                        <span>Due Date:</span>
                                        <span>{new Date(invoice.due_date).toLocaleDateString()}</span>
                                    </div>
                                    <div style={{ display: 'flex', justifyContent: 'space-between' }}>
                                        <span>Currency:</span>
                                        <span>{invoice.currency_code}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {/* Line Items */}
                        <Table dataSource={invoice.items || []} columns={itemColumns} pagination={false} rowKey="id" bordered size="small" />

                        {/* Totals */}
                        <div style={{ display: 'flex', justifyContent: 'flex-end', marginTop: 24 }}>
                            <div style={{ width: 300 }}>
                                <div
                                    style={{
                                        display: 'flex',
                                        justifyContent: 'space-between',
                                        padding: '8px 0',
                                        borderBottom: `1px solid ${token.colorBorder}`,
                                    }}
                                >
                                    <span>Subtotal:</span>
                                    <span>{invoice.formatted_subtotal}</span>
                                </div>
                                <div
                                    style={{
                                        display: 'flex',
                                        justifyContent: 'space-between',
                                        padding: '8px 0',
                                        borderBottom: `1px solid ${token.colorBorder}`,
                                    }}
                                >
                                    <span>Tax:</span>
                                    <span>{invoice.formatted_tax}</span>
                                </div>
                                <div style={{ display: 'flex', justifyContent: 'space-between', padding: '12px 0', fontSize: 18, fontWeight: 700 }}>
                                    <span>Total:</span>
                                    <span>{invoice.formatted_total}</span>
                                </div>
                                {invoice.amount_paid > 0 && (
                                    <>
                                        <div
                                            style={{
                                                display: 'flex',
                                                justifyContent: 'space-between',
                                                padding: '8px 0',
                                                borderTop: `1px solid ${token.colorBorder}`,
                                                color: token.colorSuccess,
                                            }}
                                        >
                                            <span>Amount Paid:</span>
                                            <span>{invoice.formatted_amount_paid}</span>
                                        </div>
                                        <div
                                            style={{
                                                display: 'flex',
                                                justifyContent: 'space-between',
                                                padding: '8px 0',
                                                fontSize: 16,
                                                fontWeight: 600,
                                                color: invoice.balance_due > 0 ? token.colorWarning : token.colorSuccess,
                                            }}
                                        >
                                            <span>Balance Due:</span>
                                            <span>{invoice.formatted_balance}</span>
                                        </div>
                                    </>
                                )}
                            </div>
                        </div>

                        {/* Notes */}
                        {invoice.client_notes && (
                            <div style={{ marginTop: 32, padding: 16, backgroundColor: token.colorBgLayout, borderRadius: 8 }}>
                                <h4 style={{ fontSize: 12, fontWeight: 600, textTransform: 'uppercase', marginBottom: 8 }}>Notes</h4>
                                <p style={{ margin: 0, whiteSpace: 'pre-wrap' }}>{invoice.client_notes}</p>
                            </div>
                        )}

                        {invoice.terms && (
                            <div style={{ marginTop: 16, padding: 16, backgroundColor: token.colorBgLayout, borderRadius: 8 }}>
                                <h4 style={{ fontSize: 12, fontWeight: 600, textTransform: 'uppercase', marginBottom: 8 }}>Terms & Conditions</h4>
                                <p style={{ margin: 0, whiteSpace: 'pre-wrap' }}>{invoice.terms}</p>
                            </div>
                        )}
                    </div>
                </Card>
            ),
        },
        {
            key: 'details',
            label: 'Details',
            children: (
                <Card>
                    <Descriptions bordered column={2}>
                        <Descriptions.Item label="Invoice Number">{invoice.invoice_number}</Descriptions.Item>
                        <Descriptions.Item label="Status">
                            <Tag color={STATUS_COLORS[invoice.status]}>{invoice.status?.charAt(0).toUpperCase() + invoice.status?.slice(1)}</Tag>
                        </Descriptions.Item>
                        <Descriptions.Item label="Client">{invoice.client?.name}</Descriptions.Item>
                        <Descriptions.Item label="Project">{invoice.project?.name || '—'}</Descriptions.Item>
                        <Descriptions.Item label="Issue Date">{new Date(invoice.issue_date).toLocaleDateString()}</Descriptions.Item>
                        <Descriptions.Item label="Due Date">{new Date(invoice.due_date).toLocaleDateString()}</Descriptions.Item>
                        <Descriptions.Item label="Currency">{invoice.currency_code}</Descriptions.Item>
                        <Descriptions.Item label="Subtotal">{invoice.formatted_subtotal}</Descriptions.Item>
                        <Descriptions.Item label="Tax">{invoice.formatted_tax}</Descriptions.Item>
                        <Descriptions.Item label="Total">{invoice.formatted_total}</Descriptions.Item>
                        <Descriptions.Item label="Amount Paid">{invoice.formatted_amount_paid}</Descriptions.Item>
                        <Descriptions.Item label="Balance Due">
                            <span style={{ color: invoice.balance_due > 0 ? token.colorWarning : token.colorSuccess, fontWeight: 600 }}>
                                {invoice.formatted_balance}
                            </span>
                        </Descriptions.Item>
                        {invoice.notes && (
                            <Descriptions.Item label="Internal Notes" span={2}>
                                {invoice.notes}
                            </Descriptions.Item>
                        )}
                    </Descriptions>
                </Card>
            ),
        },
        {
            key: 'payments',
            label: `Payment History (${invoice.payments?.length || 0})`,
            children: (
                <Card>
                    <Table
                        dataSource={invoice.payments || []}
                        columns={paymentColumns}
                        pagination={false}
                        rowKey="id"
                        locale={{
                            emptyText: 'No payments have been recorded yet.',
                        }}
                    />
                </Card>
            ),
        },
    ];

    return (
        <AppLayout
            pageTitle={`Invoice ${invoice.invoice_number}`}
            breadcrumb={[
                { title: 'Invoices', href: index.url() },
                { title: invoice.invoice_number, href: '#' },
            ]}
            actions={
                <Space>
                    <Link href={index.url()}>
                        <Button icon={<ArrowLeftOutlined />}>Back to List</Button>
                    </Link>

                    {invoice.status === 'draft' && (
                        <Link href={edit.url({ id: invoice.id })}>
                            <Button icon={<EditOutlined />}>Edit</Button>
                        </Link>
                    )}

                    <Button icon={<FileTextOutlined />} onClick={() => window.open(pdf.url({ id: invoice.id }), '_blank')}>
                        Download PDF
                    </Button>

                    {invoice.status === 'draft' && (
                        <Button type="primary" icon={<SendOutlined />} onClick={handleSendInvoice}>
                            Send to Client
                        </Button>
                    )}

                    {invoice.is_payable && (
                        <Button type="primary" icon={<DollarOutlined />} onClick={() => setPaymentModalOpen(true)}>
                            Record Payment
                        </Button>
                    )}

                    {invoice.status !== 'void' && invoice.status !== 'paid' && (
                        <Button danger icon={<StopOutlined />} onClick={handleVoidInvoice}>
                            Void Invoice
                        </Button>
                    )}
                </Space>
            }
        >
            <Tabs defaultActiveKey="preview" items={tabItems} />

            <InvoicePaymentModal open={paymentModalOpen} onCancel={() => setPaymentModalOpen(false)} invoice={invoice} accounts={accounts} />
        </AppLayout>
    );
}

InvoiceShow.layout = (page: React.ReactNode) => page;
