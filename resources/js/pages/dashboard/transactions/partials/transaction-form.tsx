import api from '@/lib/axios';
import { index } from '@/routes/transactions';
import { router } from '@inertiajs/react';
import { Button, Col, DatePicker, Form, Input, InputNumber, notification, Row, Select } from 'antd';
import dayjs from 'dayjs';
import { useEffect, useState } from 'react';

interface TransactionFormProps {
    accounts?: Array<{ id: number; name: string; currency_code: string }>;
    categories?: Array<{ id: number; name: string; type: string; color?: string }>;
}

export default function TransactionForm({ accounts = [], categories = [] }: TransactionFormProps) {
    const [form] = Form.useForm();
    const [loading, setLoading] = useState(false);
    const [selectedType, setSelectedType] = useState<'income' | 'expense' | undefined>(undefined);

    // Filter categories based on transaction type
    const filteredCategories = selectedType ? categories.filter((cat) => cat.type === selectedType) : categories;

    const handleTypeChange = (value: 'credit' | 'debit') => {
        // Map credit -> income, debit -> expense for category filtering
        setSelectedType(value === 'credit' ? 'income' : 'expense');
        // Reset category when type changes
        form.setFieldValue('category_id', undefined);
    };

    useEffect(() => {
        // Set default date to today
        form.setFieldValue('date', dayjs());
    }, [form]);

    const onFinish = async (values: Record<string, unknown>) => {
        setLoading(true);

        // Format date
        const formattedValues = {
            ...values,
            date: values.date ? dayjs(values.date as dayjs.Dayjs).format('YYYY-MM-DD') : undefined,
        };

        try {
            const response = await api.post('/dashboard/transactions', formattedValues);
            notification.success({
                message: response.data.message || 'Transaction recorded successfully',
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
                    <Form.Item label="Account" name="account_id" rules={[{ required: true, message: 'Please select an account!' }]}>
                        <Select
                            placeholder="Select an account"
                            showSearch
                            filterOption={(input, option) => (option?.label ?? '').toLowerCase().includes(input.toLowerCase())}
                            options={accounts.map((account) => ({
                                label: `${account.name} (${account.currency_code})`,
                                value: account.id,
                            }))}
                        />
                    </Form.Item>
                </Col>
                <Col span={12}>
                    <Form.Item label="Type" name="type" rules={[{ required: true, message: 'Please select a transaction type!' }]}>
                        <Select
                            placeholder="Select type"
                            onChange={handleTypeChange}
                            options={[
                                { label: 'Credit (Money In)', value: 'credit' },
                                { label: 'Debit (Money Out)', value: 'debit' },
                            ]}
                        />
                    </Form.Item>
                </Col>
            </Row>

            <Row gutter={16}>
                <Col span={12}>
                    <Form.Item
                        label="Amount"
                        name="amount"
                        rules={[
                            { required: true, message: 'Please input the amount!' },
                            {
                                type: 'number',
                                min: 0.01,
                                message: 'Amount must be greater than 0',
                            },
                        ]}
                    >
                        <InputNumber style={{ width: '100%' }} placeholder="0.00" precision={2} step={0.01} min={0.01} />
                    </Form.Item>
                </Col>
                <Col span={12}>
                    <Form.Item label="Date" name="date" rules={[{ required: true, message: 'Please select a date!' }]}>
                        <DatePicker style={{ width: '100%' }} format="YYYY-MM-DD" />
                    </Form.Item>
                </Col>
            </Row>

            <Form.Item label="Category" name="category_id">
                <Select
                    placeholder="Select a category (optional)"
                    allowClear
                    showSearch
                    filterOption={(input, option) => (option?.label ?? '').toLowerCase().includes(input.toLowerCase())}
                    options={filteredCategories.map((category) => ({
                        label: category.name,
                        value: category.id,
                    }))}
                />
            </Form.Item>

            <Form.Item label="Description" name="description">
                <Input.TextArea rows={4} placeholder="Enter transaction description (optional)" />
            </Form.Item>

            <Form.Item>
                <Button type="primary" htmlType="submit" loading={loading}>
                    Record Transaction
                </Button>
            </Form.Item>
        </Form>
    );
}
