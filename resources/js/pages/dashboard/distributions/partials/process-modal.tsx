import api from '@/lib/axios';
import type { Account, Distribution } from '@/types';
import { WarningOutlined } from '@ant-design/icons';
import { Alert, Col, Descriptions, Form, Modal, notification, Row, Select, Statistic, theme } from 'antd';
import React, { useMemo } from 'react';

const { useToken } = theme;

interface ProcessModalProps {
    visible: boolean;
    distribution: Distribution;
    accounts: Account[];
    onCancel: () => void;
    onSuccess: () => void;
}

export default function ProcessModal({ visible, distribution, accounts, onCancel, onSuccess }: ProcessModalProps) {
    const { token: antToken } = useToken();
    const [form] = Form.useForm();
    const [loading, setLoading] = React.useState(false);
    const [selectedAccountId, setSelectedAccountId] = React.useState<number | null>(null);

    const selectedAccount = useMemo(() => accounts.find((acc) => acc.id === selectedAccountId), [selectedAccountId, accounts]);

    const totalDistributable = useMemo(() => {
        if (!distribution.lines || distribution.lines.length === 0) return 0;
        return distribution.lines
            .filter((line) => line.shareholder && !line.shareholder.is_office_reserve)
            .reduce((sum, line) => sum + (line.allocated_amount_pkr || 0), 0);
    }, [distribution.lines]);

    const officeRetained = useMemo(() => {
        if (!distribution.lines || distribution.lines.length === 0) return 0;
        return distribution.lines
            .filter((line) => line.shareholder && line.shareholder.is_office_reserve)
            .reduce((sum, line) => sum + (line.allocated_amount_pkr || 0), 0);
    }, [distribution.lines]);

    const hasSufficientBalance = useMemo(() => {
        if (!selectedAccount) return false;
        return selectedAccount.current_balance >= totalDistributable;
    }, [selectedAccount, totalDistributable]);

    const handleSubmit = async (values: Record<string, unknown>) => {
        setLoading(true);
        try {
            await api.post(`/dashboard/distributions/${distribution.id}/process`, {
                account_id: values.account_id,
            });
            notification.success({
                message: 'Distribution processed successfully',
                description: 'Withdrawal transactions have been created for human partners.',
            });
            form.resetFields();
            onSuccess();
        } catch (error: unknown) {
            const errorMessage =
                typeof error === 'object' && error !== null && 'response' in error
                    ? (error as { response?: { data?: { message?: string } } }).response?.data?.message
                    : 'An error occurred';

            notification.error({
                message: 'Failed to process distribution',
                description: errorMessage,
            });
        } finally {
            setLoading(false);
        }
    };

    return (
        <Modal
            title="Process Distribution"
            open={visible}
            onCancel={() => {
                onCancel();
                form.resetFields();
                setSelectedAccountId(null);
            }}
            onOk={() => form.submit()}
            confirmLoading={loading}
            okButtonProps={{ disabled: !hasSufficientBalance }}
            width={700}
        >
            <Alert
                message="Processing Distribution"
                description="This will create withdrawal transactions for human partners. Office reserve amount will remain in the company. This action cannot be undone."
                type="warning"
                showIcon
                style={{ marginBottom: 16 }}
            />

            <Form
                form={form}
                layout="vertical"
                onFinish={handleSubmit}
                onValuesChange={(changedValues) => {
                    if (changedValues.account_id) {
                        setSelectedAccountId(changedValues.account_id);
                    }
                }}
            >
                <Form.Item label="Select PKR Account" name="account_id" rules={[{ required: true, message: 'Please select an account' }]}>
                    <Select
                        placeholder="Select account to debit from"
                        options={accounts.map((account) => ({
                            label: `${account.name} (${account.formatted_balance})`,
                            value: account.id,
                        }))}
                    />
                </Form.Item>
            </Form>

            <Descriptions title="Distribution Summary" column={1} bordered style={{ marginTop: 16 }}>
                <Descriptions.Item label="Total Net Profit">Rs {(distribution.final_net_profit || 0).toFixed(2)}</Descriptions.Item>
                <Descriptions.Item label="Human Partner Payouts">
                    <span style={{ color: antToken.colorError }}>Rs {(totalDistributable / 100).toFixed(2)}</span>
                </Descriptions.Item>
                <Descriptions.Item label="Office Reserve (Retained)">
                    <span style={{ color: antToken.colorSuccess }}>Rs {(officeRetained / 100).toFixed(2)}</span>
                </Descriptions.Item>
            </Descriptions>

            {selectedAccount && (
                <div style={{ marginTop: 16 }}>
                    <Row gutter={16}>
                        <Col span={12}>
                            <Statistic title="Account Balance" value={selectedAccount.current_balance / 100} prefix="Rs" precision={2} />
                        </Col>
                        <Col span={12}>
                            <Statistic
                                title="Balance After Processing"
                                value={(selectedAccount.current_balance - totalDistributable) / 100}
                                prefix="Rs"
                                precision={2}
                                valueStyle={{
                                    color: hasSufficientBalance ? antToken.colorSuccess : antToken.colorError,
                                }}
                            />
                        </Col>
                    </Row>

                    {!hasSufficientBalance && (
                        <Alert
                            message="Insufficient Balance"
                            description={`The selected account does not have sufficient balance. Need Rs ${(totalDistributable / 100).toFixed(
                                2,
                            )}, have ${selectedAccount.formatted_balance}`}
                            type="error"
                            showIcon
                            icon={<WarningOutlined />}
                            style={{ marginTop: 16 }}
                        />
                    )}
                </div>
            )}
        </Modal>
    );
}
