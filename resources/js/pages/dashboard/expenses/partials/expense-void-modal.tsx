import api from '@/lib/axios';
import type { Expense } from '@/types';
import { ExclamationCircleOutlined, WarningOutlined } from '@ant-design/icons';
import { router } from '@inertiajs/react';
import { Alert, Card, Descriptions, Form, Input, Modal, notification, Tag, theme } from 'antd';
import { useState } from 'react';

const { useToken } = theme;

interface ExpenseVoidModalProps {
    open: boolean;
    onCancel: () => void;
    expense: Expense;
}

export default function ExpenseVoidModal({ open, onCancel, expense }: ExpenseVoidModalProps) {
    const { token } = useToken();
    const [form] = Form.useForm();
    const [loading, setLoading] = useState(false);

    const handleSubmit = async (values: { void_reason: string }) => {
        setLoading(true);

        try {
            const response = await api.post(`/dashboard/expenses/${expense.id}/void`, {
                void_reason: values.void_reason,
            });

            notification.success({
                message: response.data.message || 'Expense voided successfully',
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
                    description: err.response.data.message || 'Unable to void expense.',
                });
            } else {
                notification.error({
                    message: 'Error',
                    description: 'An unexpected error occurred while voiding the expense.',
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
                    Void Expense #{expense.id}
                </span>
            }
            open={open}
            onCancel={onCancel}
            onOk={() => form.submit()}
            confirmLoading={loading}
            okText="Void Expense"
            okButtonProps={{ danger: true }}
            cancelText="Cancel"
            width={600}
        >
            <Form form={form} layout="vertical" onFinish={handleSubmit}>
                <Alert
                    message="This action will void the expense and cannot be undone"
                    description="A reversal transaction will be created to credit the amount back to the account. The original transaction will remain for audit purposes."
                    type="warning"
                    showIcon
                    icon={<ExclamationCircleOutlined />}
                    style={{ marginBottom: 16 }}
                />

                <Card title="Transaction Reversal Preview" size="small" style={{ marginBottom: 16 }}>
                    <Descriptions column={1} size="small" bordered>
                        <Descriptions.Item label="Account">
                            <span>
                                {expense.account?.name} ({expense.account?.currency_code})
                            </span>
                        </Descriptions.Item>
                        <Descriptions.Item label="Original Amount (Debit)">
                            <span style={{ color: token.colorError }}>-{expense.formatted_amount}</span>
                        </Descriptions.Item>
                        <Descriptions.Item label="Reversal Amount (Credit)">
                            <span style={{ color: token.colorSuccess, fontWeight: 600 }}>+{expense.formatted_amount}</span>
                        </Descriptions.Item>
                        {expense.vendor && <Descriptions.Item label="Vendor">{expense.vendor}</Descriptions.Item>}
                        {expense.transaction_id && (
                            <Descriptions.Item label="Original Transaction">
                                <Tag>#{expense.transaction_id}</Tag>
                            </Descriptions.Item>
                        )}
                    </Descriptions>
                </Card>

                <Form.Item
                    label="Void Reason"
                    name="void_reason"
                    rules={[
                        { required: true, message: 'Please provide a reason for voiding this expense.' },
                        { min: 1, message: 'Please provide a reason for voiding this expense.' },
                        { max: 1000, message: 'The void reason cannot exceed 1000 characters.' },
                    ]}
                >
                    <Input.TextArea
                        rows={4}
                        placeholder="Enter the reason for voiding this expense (required for audit purposes)..."
                        showCount
                        maxLength={1000}
                    />
                </Form.Item>
            </Form>
        </Modal>
    );
}
