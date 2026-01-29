import api from '@/lib/axios';
import type { Account, Invoice, InvoicePayment } from '@/types';
import { ExclamationCircleOutlined, WarningOutlined } from '@ant-design/icons';
import { router } from '@inertiajs/react';
import { Alert, Card, Descriptions, Form, Input, Modal, notification, Table, Tag, theme } from 'antd';
import { useState } from 'react';

const { useToken } = theme;

interface InvoiceVoidModalProps {
    open: boolean;
    onCancel: () => void;
    invoice: Invoice;
    accounts: Account[];
}

interface AccountBalance {
    accountId: number;
    accountName: string;
    currencyCode: string;
    currentBalance: number;
    formattedBalance: string;
    requiredAmount: number;
    hasSufficientBalance: boolean;
}

export default function InvoiceVoidModal({ open, onCancel, invoice, accounts }: InvoiceVoidModalProps) {
    const { token } = useToken();
    const [form] = Form.useForm();
    const [loading, setLoading] = useState(false);

    // Get active (non-voided) payments
    const activePayments = invoice.payments?.filter((p) => !p.is_voided) || [];
    const hasPayments = activePayments.length > 0;

    // Calculate balance requirements per account
    const accountBalances: AccountBalance[] = [];
    if (hasPayments) {
        const balanceByAccount = new Map<number, number>();

        activePayments.forEach((payment) => {
            const current = balanceByAccount.get(payment.account_id) || 0;
            // Net reversal amount = amount_received (income - fee)
            balanceByAccount.set(payment.account_id, current + payment.amount_received);
        });

        balanceByAccount.forEach((requiredAmount, accountId) => {
            const account = accounts.find((a) => a.id === accountId);
            if (account) {
                accountBalances.push({
                    accountId,
                    accountName: account.name,
                    currencyCode: account.currency_code,
                    currentBalance: account.current_balance,
                    formattedBalance: account.formatted_balance,
                    requiredAmount,
                    hasSufficientBalance: account.current_balance >= requiredAmount,
                });
            }
        });
    }

    const hasInsufficientBalance = accountBalances.some((ab) => !ab.hasSufficientBalance);

    // Payment table columns
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
            title: 'Income (Credit)',
            dataIndex: 'formatted_payment_amount',
            key: 'income',
            render: (text: string) => <span style={{ color: token.colorSuccess }}>+{text}</span>,
        },
        {
            title: 'Fee (Debit)',
            key: 'fee',
            render: (_: unknown, record: InvoicePayment) =>
                record.has_fee ? <span style={{ color: token.colorError }}>-{record.formatted_fee}</span> : <span>—</span>,
        },
        {
            title: 'Net Reversal',
            key: 'net',
            render: (_: unknown, record: InvoicePayment) => (
                <span style={{ color: token.colorError, fontWeight: 600 }}>
                    -{record.account?.currency_code} {record.amount_received.toFixed(2)}
                </span>
            ),
        },
    ];

    const handleSubmit = async (values: { void_reason: string }) => {
        setLoading(true);

        try {
            const response = await api.post(`/dashboard/invoices/${invoice.id}/void`, {
                void_reason: values.void_reason,
            });

            notification.success({
                message: response.data.message || 'Invoice voided successfully',
            });

            form.resetFields();
            onCancel();
            router.reload();
        } catch (error: unknown) {
            const err = error as {
                response?: {
                    status: number;
                    data: { errors?: { [key: string]: string[] }; message?: string };
                };
            };

            if (err.response && err.response.status === 422) {
                const validationErrors = err.response.data.errors;
                if (validationErrors) {
                    const formErrors = Object.keys(validationErrors).map((key) => ({
                        name: key,
                        errors: validationErrors[key],
                    }));
                    form.setFields(formErrors);
                }
                notification.error({
                    message: 'Validation Error',
                    description: err.response.data.message || 'Please correct the errors below.',
                });
            } else if (err.response && err.response.status === 403) {
                notification.error({
                    message: 'Error',
                    description: err.response.data.message || 'Unable to void invoice.',
                });
            } else {
                notification.error({
                    message: 'Error',
                    description: 'An unexpected error occurred while voiding the invoice.',
                });
            }
        } finally {
            setLoading(false);
        }
    };

    return (
        <Modal
            title={
                <span>
                    <WarningOutlined style={{ color: token.colorError, marginRight: 8 }} />
                    Void Invoice {invoice.invoice_number}
                </span>
            }
            open={open}
            onCancel={onCancel}
            onOk={() => form.submit()}
            confirmLoading={loading}
            okText="Void Invoice"
            okButtonProps={{ danger: true, disabled: hasInsufficientBalance }}
            cancelText="Cancel"
            width={800}
        >
            <Form form={form} layout="vertical" onFinish={handleSubmit}>
                {/* Warning Alert */}
                <Alert
                    message="This action will void the invoice and cannot be undone"
                    description={
                        hasPayments
                            ? 'All payment transactions will be reversed and account balances will be restored. Payment records will be kept for audit purposes.'
                            : 'The invoice status will be changed to void.'
                    }
                    type="warning"
                    showIcon
                    icon={<ExclamationCircleOutlined />}
                    style={{ marginBottom: 16 }}
                />

                {/* Balance Requirements Card (only if there are payments) */}
                {hasPayments && (
                    <Card title="Balance Requirements" size="small" style={{ marginBottom: 16 }}>
                        <Descriptions column={1} size="small" bordered>
                            {accountBalances.map((ab) => (
                                <Descriptions.Item
                                    key={ab.accountId}
                                    label={
                                        <span>
                                            {ab.accountName} ({ab.currencyCode})
                                        </span>
                                    }
                                >
                                    <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
                                        <span>
                                            Current: {ab.formattedBalance} | Required: {ab.currencyCode} {ab.requiredAmount.toFixed(2)}
                                        </span>
                                        {ab.hasSufficientBalance ? (
                                            <Tag color="success">Sufficient</Tag>
                                        ) : (
                                            <Tag color="error">Insufficient</Tag>
                                        )}
                                    </div>
                                </Descriptions.Item>
                            ))}
                        </Descriptions>

                        {hasInsufficientBalance && (
                            <Alert
                                message="Insufficient balance in one or more accounts"
                                description="Cannot void this invoice because the account(s) do not have enough balance to reverse the payment transactions."
                                type="error"
                                showIcon
                                style={{ marginTop: 12 }}
                            />
                        )}
                    </Card>
                )}

                {/* Transaction Reversal Preview (only if there are payments) */}
                {hasPayments && (
                    <Card title="Payment Reversal Preview" size="small" style={{ marginBottom: 16 }}>
                        <Table
                            dataSource={activePayments}
                            columns={paymentColumns}
                            pagination={false}
                            rowKey="id"
                            size="small"
                            footer={() => (
                                <div style={{ textAlign: 'right', fontWeight: 600 }}>
                                    Total Net Reversal:{' '}
                                    <span style={{ color: token.colorError }}>
                                        -{invoice.currency_code} {activePayments.reduce((sum, p) => sum + p.amount_received, 0).toFixed(2)}
                                    </span>
                                </div>
                            )}
                        />
                    </Card>
                )}

                {/* Void Reason (Required) */}
                <Form.Item
                    label="Void Reason"
                    name="void_reason"
                    rules={[
                        { required: true, message: 'Please provide a reason for voiding this invoice.' },
                        { min: 1, message: 'Please provide a reason for voiding this invoice.' },
                        { max: 1000, message: 'The void reason cannot exceed 1000 characters.' },
                    ]}
                >
                    <Input.TextArea
                        rows={4}
                        placeholder="Enter the reason for voiding this invoice (required for audit purposes)..."
                        showCount
                        maxLength={1000}
                    />
                </Form.Item>
            </Form>
        </Modal>
    );
}
