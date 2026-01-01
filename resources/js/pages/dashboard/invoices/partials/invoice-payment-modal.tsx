import { Modal, Form, Select, InputNumber, DatePicker, Input, notification, Alert, Card, Radio } from 'antd';
import { useState, useEffect } from 'react';
import api from '@/lib/axios';
import dayjs from 'dayjs';
import type { Invoice, Account } from '@/types';
import { router } from '@inertiajs/react';

interface InvoicePaymentModalProps {
    open: boolean;
    onCancel: () => void;
    invoice: Invoice;
    accounts: Account[];
}

export default function InvoicePaymentModal({ open, onCancel, invoice, accounts }: InvoicePaymentModalProps) {
    const [form] = Form.useForm();
    const [loading, setLoading] = useState(false);
    const [amountReceived, setAmountReceived] = useState<number | undefined>(invoice.balance_due);
    const [paymentClassification, setPaymentClassification] = useState<'full' | 'fee' | 'partial'>('full');

    // Calculate shortfall
    const shortfall = invoice.balance_due - (amountReceived || 0);
    const hasShortfall = shortfall > 0;

    // Set default values on open
    useEffect(() => {
        if (open) {
            form.setFieldsValue({
                payment_amount: invoice.balance_due,
                amount_received: invoice.balance_due,
                payment_date: dayjs(),
            });
            setAmountReceived(invoice.balance_due);
            setPaymentClassification('full');
        }
    }, [open, invoice, form]);

    const handleAmountReceivedChange = (value: number | null) => {
        const received = value || 0;
        setAmountReceived(received);

        // Auto-classify based on amount received
        if (received >= invoice.balance_due) {
            setPaymentClassification('full');
        } else if (received > 0) {
            setPaymentClassification('partial');
        }
    };

    const handleSubmit = async (values: Record<string, unknown>) => {
        setLoading(true);

        try {
            const payload = {
                account_id: values.account_id,
                payment_amount: invoice.balance_due,
                amount_received: values.amount_received,
                payment_date: dayjs(values.payment_date as dayjs.Dayjs).format('YYYY-MM-DD'),
                notes: values.notes,
            };

            const response = await api.post(`/dashboard/invoices/${invoice.id}/record-payment`, payload);

            notification.success({
                message: response.data.message || 'Payment recorded successfully',
            });

            form.resetFields();
            onCancel();
            router.reload();
        } catch (error: unknown) {
            const err = error as {
                response?: {
                    status: number;
                    data: { errors: { [key: string]: string[] }; message: string };
                };
            };

            if (err.response && err.response.status === 422) {
                const validationErrors = err.response.data.errors;
                const formErrors = Object.keys(validationErrors).map((key) => ({
                    name: key,
                    errors: validationErrors[key],
                }));
                form.setFields(formErrors);
                notification.error({
                    message: 'Validation Error',
                    description: err.response.data.message,
                });
            } else {
                notification.error({
                    message: 'Error',
                    description: 'An unexpected error occurred while recording payment.',
                });
            }
        } finally {
            setLoading(false);
        }
    };

    return (
        <Modal
            title="Record Payment"
            open={open}
            onCancel={onCancel}
            onOk={() => form.submit()}
            confirmLoading={loading}
            okText="Record Payment"
            cancelText="Cancel"
            width={700}
        >
            <Form form={form} layout="vertical" onFinish={handleSubmit}>
                <Alert
                    message={`Invoice Balance Due: ${invoice.formatted_balance}`}
                    type="info"
                    showIcon
                    style={{ marginBottom: 16 }}
                />

                <Form.Item
                    label="Account"
                    name="account_id"
                    rules={[{ required: true, message: 'Please select an account!' }]}
                >
                    <Select
                        placeholder="Select account"
                        showSearch
                        filterOption={(input, option) =>
                            (option?.label ?? '').toLowerCase().includes(input.toLowerCase())
                        }
                        options={accounts.map((account) => ({
                            label: `${account.name} (${account.currency_code}) - ${account.formatted_balance}`,
                            value: account.id,
                        }))}
                    />
                </Form.Item>

                <Form.Item
                    label={`Amount Received (${invoice.currency_code})`}
                    name="amount_received"
                    rules={[
                        { required: true, message: 'Please enter the amount received!' },
                        {
                            type: 'number',
                            min: 0.01,
                            message: 'Amount must be greater than 0',
                        },
                    ]}
                >
                    <InputNumber
                        style={{ width: '100%' }}
                        placeholder="0.00"
                        precision={2}
                        step={0.01}
                        min={0.01}
                        onChange={handleAmountReceivedChange}
                    />
                </Form.Item>

                {hasShortfall && (
                    <Card
                        title={`Shortfall Detected: ${invoice.currency_code} ${shortfall.toFixed(2)}`}
                        size="small"
                        style={{ marginBottom: 16 }}
                    >
                        <p>
                            The amount received is less than the invoice balance. How would you like to classify this
                            shortfall?
                        </p>

                        <Radio.Group
                            value={paymentClassification}
                            onChange={(e) => setPaymentClassification(e.target.value)}
                            style={{ width: '100%' }}
                        >
                            <Radio value="fee" style={{ display: 'block', marginBottom: 8 }}>
                                <strong>Transaction Fee / Bank Charges</strong>
                                <div style={{ fontSize: 12, color: '#666', marginLeft: 24 }}>
                                    Record full invoice amount ({invoice.formatted_balance}) as revenue + {invoice.currency_code}{' '}
                                    {shortfall.toFixed(2)} as expense
                                </div>
                            </Radio>
                            <Radio value="partial" style={{ display: 'block' }}>
                                <strong>Partial Payment</strong>
                                <div style={{ fontSize: 12, color: '#666', marginLeft: 24 }}>
                                    Record only the amount received and keep invoice open for remaining balance
                                </div>
                            </Radio>
                        </Radio.Group>
                    </Card>
                )}

                <Form.Item
                    label="Payment Date"
                    name="payment_date"
                    rules={[{ required: true, message: 'Please select payment date!' }]}
                >
                    <DatePicker style={{ width: '100%' }} format="YYYY-MM-DD" />
                </Form.Item>

                <Form.Item label="Notes" name="notes">
                    <Input.TextArea rows={3} placeholder="Payment notes (optional)" />
                </Form.Item>

                {/* Transaction Preview */}
                <Card title="Transaction Preview" size="small" type="inner">
                    {paymentClassification === 'fee' && hasShortfall ? (
                        <>
                            <div style={{ marginBottom: 8 }}>
                                <strong>Income Transaction:</strong>
                                <div style={{ fontSize: 12, color: '#52c41a', marginLeft: 16 }}>
                                    + {invoice.formatted_balance} (Full invoice amount as revenue)
                                </div>
                            </div>
                            <div>
                                <strong>Expense Transaction:</strong>
                                <div style={{ fontSize: 12, color: '#ff4d4f', marginLeft: 16 }}>
                                    - {invoice.currency_code} {shortfall.toFixed(2)} (Bank charges & fees)
                                </div>
                            </div>
                            <Alert
                                message={`Net Account Balance Change: +${invoice.currency_code} ${amountReceived?.toFixed(2)}`}
                                type="success"
                                showIcon
                                style={{ marginTop: 12 }}
                            />
                        </>
                    ) : (
                        <div>
                            <strong>Income Transaction:</strong>
                            <div style={{ fontSize: 12, color: '#52c41a', marginLeft: 16 }}>
                                + {invoice.currency_code} {amountReceived?.toFixed(2)}
                            </div>
                            {paymentClassification === 'partial' && (
                                <Alert
                                    message={`Remaining Balance: ${invoice.currency_code} ${shortfall.toFixed(2)}`}
                                    type="warning"
                                    showIcon
                                    style={{ marginTop: 12 }}
                                />
                            )}
                        </div>
                    )}
                </Card>
            </Form>
        </Modal>
    );
}
