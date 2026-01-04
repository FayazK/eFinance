import api from '@/lib/axios';
import type { Shareholder } from '@/types';
import { Checkbox, Form, Input, InputNumber, Modal, notification } from 'antd';
import React, { useEffect } from 'react';

interface ShareholderFormProps {
    visible: boolean;
    shareholder: Shareholder | null;
    onCancel: () => void;
    onSuccess: () => void;
    currentEquityTotal: number;
}

export default function ShareholderForm({ visible, shareholder, onCancel, onSuccess, currentEquityTotal }: ShareholderFormProps) {
    const [form] = Form.useForm();
    const [loading, setLoading] = React.useState(false);

    useEffect(() => {
        if (visible && shareholder) {
            form.setFieldsValue({
                ...shareholder,
                equity_percentage: parseFloat(shareholder.equity_percentage),
            });
        } else if (visible) {
            form.resetFields();
        }
    }, [visible, shareholder, form]);

    const handleSubmit = async (values: Record<string, unknown>) => {
        setLoading(true);
        try {
            if (shareholder) {
                await api.put(`/dashboard/shareholders/${shareholder.id}`, values);
                notification.success({
                    message: 'Shareholder updated successfully',
                });
            } else {
                await api.post('/dashboard/shareholders', values);
                notification.success({
                    message: 'Shareholder created successfully',
                });
            }
            form.resetFields();
            onSuccess();
        } catch (error: unknown) {
            const errorMessage =
                typeof error === 'object' && error !== null && 'response' in error
                    ? (error as { response?: { data?: { message?: string } } }).response?.data?.message
                    : 'An error occurred';

            notification.error({
                message: shareholder ? 'Failed to update shareholder' : 'Failed to create shareholder',
                description: errorMessage,
            });
        } finally {
            setLoading(false);
        }
    };

    const calculateRemainingEquity = (currentValue: number = 0) => {
        const currentEquityExcludingThis = shareholder ? currentEquityTotal - parseFloat(shareholder.equity_percentage) : currentEquityTotal;
        return 100 - currentEquityExcludingThis - currentValue;
    };

    return (
        <Modal
            title={shareholder ? 'Edit Shareholder' : 'Create Shareholder'}
            open={visible}
            onCancel={onCancel}
            onOk={() => form.submit()}
            confirmLoading={loading}
            width={600}
        >
            <Form
                form={form}
                layout="vertical"
                onFinish={handleSubmit}
                initialValues={{
                    is_office_reserve: false,
                    is_active: true,
                }}
            >
                <Form.Item label="Name" name="name" rules={[{ required: true, message: 'Please enter shareholder name' }]}>
                    <Input placeholder="John Doe" />
                </Form.Item>

                <Form.Item label="Email" name="email" rules={[{ type: 'email', message: 'Please enter a valid email' }]}>
                    <Input placeholder="john@example.com" />
                </Form.Item>

                <Form.Item
                    label="Equity Percentage"
                    name="equity_percentage"
                    rules={[
                        { required: true, message: 'Please enter equity percentage' },
                        {
                            type: 'number',
                            min: 0.01,
                            max: 100,
                            message: 'Equity must be between 0.01% and 100%',
                        },
                    ]}
                    help={<span>Remaining equity: {calculateRemainingEquity(form.getFieldValue('equity_percentage')).toFixed(2)}%</span>}
                >
                    <InputNumber min={0.01} max={100} step={0.01} precision={2} placeholder="25.50" style={{ width: '100%' }} addonAfter="%" />
                </Form.Item>

                <Form.Item name="is_office_reserve" valuePropName="checked">
                    <Checkbox>Is Office Reserve</Checkbox>
                </Form.Item>

                <Form.Item name="is_active" valuePropName="checked">
                    <Checkbox>Active</Checkbox>
                </Form.Item>

                <Form.Item label="Notes" name="notes">
                    <Input.TextArea rows={3} placeholder="Additional notes..." />
                </Form.Item>
            </Form>
        </Modal>
    );
}
