import api from '@/lib/axios';
import type { Distribution } from '@/types';
import { Alert, Descriptions, Form, Input, InputNumber, Modal, notification } from 'antd';
import React from 'react';

interface AdjustProfitModalProps {
    visible: boolean;
    distribution: Distribution;
    onCancel: () => void;
    onSuccess: () => void;
}

export default function AdjustProfitModal({ visible, distribution, onCancel, onSuccess }: AdjustProfitModalProps) {
    const [form] = Form.useForm();
    const [loading, setLoading] = React.useState(false);

    const handleSubmit = async (values: Record<string, unknown>) => {
        setLoading(true);
        try {
            await api.put(`/dashboard/distributions/${distribution.id}/adjust-profit`, {
                adjusted_amount: Math.round((values.adjusted_amount as number) * 100), // Convert to minor units
                reason: values.reason,
            });
            notification.success({
                message: 'Net profit adjusted successfully',
            });
            form.resetFields();
            onSuccess();
        } catch (error: unknown) {
            const errorMessage =
                typeof error === 'object' && error !== null && 'response' in error
                    ? (error as { response?: { data?: { message?: string } } }).response?.data?.message
                    : 'An error occurred';

            notification.error({
                message: 'Failed to adjust net profit',
                description: errorMessage,
            });
        } finally {
            setLoading(false);
        }
    };

    return (
        <Modal
            title="Adjust Net Profit"
            open={visible}
            onCancel={() => {
                onCancel();
                form.resetFields();
            }}
            onOk={() => form.submit()}
            confirmLoading={loading}
            width={600}
        >
            <Alert
                message="Manual Adjustment"
                description="You can manually adjust the net profit amount. This will recalculate all distribution lines based on the new amount."
                type="info"
                showIcon
                style={{ marginBottom: 16 }}
            />

            <Descriptions column={1} bordered style={{ marginBottom: 16 }}>
                <Descriptions.Item label="Calculated Net Profit">{distribution.formatted_net_profit}</Descriptions.Item>
            </Descriptions>

            <Form form={form} layout="vertical" onFinish={handleSubmit}>
                <Form.Item
                    label="Adjusted Net Profit (PKR)"
                    name="adjusted_amount"
                    rules={[{ required: true, message: 'Please enter adjusted amount' }]}
                >
                    <InputNumber min={0} precision={2} style={{ width: '100%' }} placeholder="Enter adjusted amount in PKR" prefix="Rs" />
                </Form.Item>

                <Form.Item
                    label="Reason for Adjustment"
                    name="reason"
                    rules={[{ required: true, message: 'Please provide a reason for adjustment' }]}
                >
                    <Input.TextArea rows={3} placeholder="e.g., Holding back extra reserves, accounting correction..." />
                </Form.Item>
            </Form>
        </Modal>
    );
}
