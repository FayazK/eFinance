import api from '@/lib/axios';
import { index } from '@/routes/transfers';
import { router } from '@inertiajs/react';
import { Alert, Button, Col, DatePicker, Form, Input, InputNumber, notification, Row, Select } from 'antd';
import dayjs from 'dayjs';
import { useEffect, useState } from 'react';

interface Account {
    id: number;
    name: string;
    currency_code: string;
    current_balance: number;
    formatted_balance: string;
}

interface TransferFormProps {
    accounts: Account[];
}

export default function TransferForm({ accounts }: TransferFormProps) {
    const [form] = Form.useForm();
    const [loading, setLoading] = useState(false);
    const [sourceAccountId, setSourceAccountId] = useState<number | undefined>(undefined);
    const [destinationAccountId, setDestinationAccountId] = useState<number | undefined>(undefined);
    const [sourceAmount, setSourceAmount] = useState<number | undefined>(undefined);
    const [destinationAmount, setDestinationAmount] = useState<number | undefined>(undefined);

    // Get selected account objects
    const sourceAccount = accounts.find((acc) => acc.id === sourceAccountId);
    const destinationAccount = accounts.find((acc) => acc.id === destinationAccountId);

    // Check if same currency
    const sameCurrency = sourceAccount && destinationAccount ? sourceAccount.currency_code === destinationAccount.currency_code : false;

    // Calculate exchange rate
    const exchangeRate = sourceAmount && destinationAmount && sourceAmount > 0 ? destinationAmount / sourceAmount : undefined;

    // Calculate implicit fee for same-currency transfers
    const calculatedFee =
        sameCurrency && sourceAmount && destinationAmount && sourceAmount > destinationAmount ? sourceAmount - destinationAmount : 0;

    // Set default date to today
    useEffect(() => {
        form.setFieldValue('date', dayjs());
    }, [form]);

    const onFinish = async (values: Record<string, unknown>) => {
        setLoading(true);

        const formattedValues = {
            ...values,
            date: values.date ? dayjs(values.date as dayjs.Dayjs).format('YYYY-MM-DD') : undefined,
        };

        try {
            const response = await api.post('/dashboard/transfers', formattedValues);
            notification.success({
                message: response.data.message || 'Transfer completed successfully',
            });
            router.visit(index.url());
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
                    description: 'An unexpected error occurred.',
                });
            }
        } finally {
            setLoading(false);
        }
    };

    return (
        <Form form={form} layout="vertical" onFinish={onFinish}>
            <Row gutter={16}>
                <Col span={12}>
                    <Form.Item label="From Account" name="source_account_id" rules={[{ required: true, message: 'Please select source account!' }]}>
                        <Select
                            placeholder="Select source account"
                            showSearch
                            onChange={(value) => setSourceAccountId(value)}
                            filterOption={(input, option) => (option?.label ?? '').toLowerCase().includes(input.toLowerCase())}
                            options={accounts.map((account) => ({
                                label: `${account.name} (${account.currency_code}) - ${account.formatted_balance}`,
                                value: account.id,
                            }))}
                        />
                    </Form.Item>
                </Col>
                <Col span={12}>
                    <Form.Item
                        label="To Account"
                        name="destination_account_id"
                        rules={[{ required: true, message: 'Please select destination account!' }]}
                    >
                        <Select
                            placeholder="Select destination account"
                            showSearch
                            onChange={(value) => setDestinationAccountId(value)}
                            filterOption={(input, option) => (option?.label ?? '').toLowerCase().includes(input.toLowerCase())}
                            options={accounts.map((account) => ({
                                label: `${account.name} (${account.currency_code}) - ${account.formatted_balance}`,
                                value: account.id,
                                disabled: account.id === sourceAccountId,
                            }))}
                        />
                    </Form.Item>
                </Col>
            </Row>

            {sameCurrency && (
                <Alert
                    message="Same Currency Transfer"
                    description="Both accounts use the same currency. The destination amount will match the source amount."
                    type="info"
                    showIcon
                    style={{ marginBottom: 16 }}
                />
            )}

            <Row gutter={16}>
                <Col span={12}>
                    <Form.Item
                        label={`Amount to Send${sourceAccount ? ` (${sourceAccount.currency_code})` : ''}`}
                        name="source_amount"
                        rules={[
                            { required: true, message: 'Please input the amount!' },
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
                            onChange={(value) => setSourceAmount(value || undefined)}
                        />
                    </Form.Item>
                </Col>
                <Col span={12}>
                    <Form.Item
                        label={`Amount to Receive${destinationAccount ? ` (${destinationAccount.currency_code})` : ''}`}
                        name="destination_amount"
                        rules={[
                            { required: true, message: 'Please input the amount!' },
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
                            onChange={(value) => setDestinationAmount(value || undefined)}
                        />
                    </Form.Item>
                </Col>
            </Row>

            {sameCurrency && calculatedFee > 0 && (
                <Alert
                    message={`Transfer Fee Detected: ${sourceAccount?.currency_code} ${calculatedFee.toFixed(2)}`}
                    description={`Amount sent: ${sourceAmount?.toFixed(2)}, Amount received: ${destinationAmount?.toFixed(2)}`}
                    type="info"
                    showIcon
                    style={{ marginBottom: 16 }}
                />
            )}

            {sameCurrency && sourceAmount && calculatedFee > 0 && (
                <Alert
                    message={`Total Deduction from Source: ${sourceAccount?.currency_code} ${sourceAmount.toFixed(2)}`}
                    description={`Transfer: ${destinationAmount?.toFixed(2)}, Fee: ${calculatedFee.toFixed(2)}`}
                    type="warning"
                    showIcon
                    style={{ marginBottom: 16 }}
                />
            )}

            {exchangeRate && !sameCurrency && (
                <Alert
                    message={`Exchange Rate: ${exchangeRate.toFixed(4)}`}
                    description={`1 ${sourceAccount?.currency_code} = ${exchangeRate.toFixed(4)} ${destinationAccount?.currency_code}`}
                    type="info"
                    showIcon
                    style={{ marginBottom: 16 }}
                />
            )}

            <Form.Item label="Date" name="date" rules={[{ required: true, message: 'Please select a date!' }]}>
                <DatePicker style={{ width: '100%' }} format="YYYY-MM-DD" />
            </Form.Item>

            <Form.Item label="Description" name="description">
                <Input.TextArea rows={4} placeholder="Enter transfer description (optional)" />
            </Form.Item>

            <Form.Item>
                <Button type="primary" htmlType="submit" loading={loading}>
                    Complete Transfer
                </Button>
            </Form.Item>
        </Form>
    );
}
