import api from '@/lib/axios';
import { SUPPORTED_CURRENCIES } from '@/lib/currencies';
import { index } from '@/routes/accounts';
import { Account } from '@/types';
import { router } from '@inertiajs/react';
import { Button, Col, Form, Input, InputNumber, notification, Row, Select, Switch } from 'antd';
import { useEffect, useState } from 'react';

interface AccountFormProps {
    account?: Account;
    isEdit?: boolean;
}

const accountTypes = [
    { label: 'Bank Account', value: 'bank' },
    { label: 'Wallet', value: 'wallet' },
    { label: 'Cash', value: 'cash' },
];

const currencies = SUPPORTED_CURRENCIES.map((currency) => ({
    label: `${currency.symbol} ${currency.code} - ${currency.name}`,
    value: currency.code,
}));

export default function AccountForm({ account, isEdit = false }: AccountFormProps) {
    const [form] = Form.useForm();
    const [loading, setLoading] = useState(false);

    useEffect(() => {
        form.setFieldsValue({
            name: account?.name || '',
            type: account?.type || 'bank',
            currency_code: account?.currency_code || 'USD',
            current_balance: account?.current_balance || 0,
            account_number: account?.account_number || '',
            bank_name: account?.bank_name || '',
            is_active: account?.is_active !== undefined ? account.is_active : true,
        });
    }, [account, form]);

    const onFinish = async (values: Record<string, unknown>) => {
        setLoading(true);

        const method = isEdit ? 'put' : 'post';
        const url = isEdit ? `/dashboard/accounts/${account!.id}` : '/dashboard/accounts';

        try {
            const response = await api[method](url, values);
            notification.success({
                message: response.data.message || `Account ${isEdit ? 'updated' : 'created'} successfully`,
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
                    <Form.Item label="Account Name" name="name" rules={[{ required: true, message: 'Please input the account name!' }]}>
                        <Input placeholder="e.g., Payoneer USD Account" />
                    </Form.Item>
                </Col>
                <Col span={12}>
                    <Form.Item label="Account Type" name="type" rules={[{ required: true, message: 'Please select an account type!' }]}>
                        <Select options={accountTypes} />
                    </Form.Item>
                </Col>
            </Row>

            <Row gutter={16}>
                <Col span={12}>
                    <Form.Item label="Currency" name="currency_code" rules={[{ required: true, message: 'Please select a currency!' }]}>
                        <Select
                            options={currencies}
                            showSearch
                            filterOption={(input, option) => (option?.label ?? '').toLowerCase().includes(input.toLowerCase())}
                        />
                    </Form.Item>
                </Col>
                <Col span={12}>
                    <Form.Item
                        label="Initial Balance"
                        name="current_balance"
                        tooltip="Enter the current balance in major units (e.g., dollars, not cents)"
                    >
                        <InputNumber style={{ width: '100%' }} placeholder="0.00" precision={2} step={0.01} />
                    </Form.Item>
                </Col>
            </Row>

            <Row gutter={16}>
                <Col span={12}>
                    <Form.Item label="Bank Name" name="bank_name">
                        <Input placeholder="e.g., Chase Bank" />
                    </Form.Item>
                </Col>
                <Col span={12}>
                    <Form.Item label="Account Number" name="account_number">
                        <Input placeholder="e.g., 1234567890" />
                    </Form.Item>
                </Col>
            </Row>

            <Form.Item label="Active" name="is_active" valuePropName="checked">
                <Switch />
            </Form.Item>

            <Form.Item>
                <Button type="primary" htmlType="submit" loading={loading}>
                    {isEdit ? 'Update Account' : 'Create Account'}
                </Button>
            </Form.Item>
        </Form>
    );
}
