import AppLayout from '@/layouts/app-layout';
import api from '@/lib/axios';
import { index as distributionsIndex } from '@/routes/distributions';
import type { Account, Shareholder } from '@/types';
import { BankOutlined, CheckCircleOutlined, SaveOutlined } from '@ant-design/icons';
import { router, usePage } from '@inertiajs/react';
import {
    Alert,
    Button,
    Card,
    Col,
    Descriptions,
    Form,
    Input,
    InputNumber,
    notification,
    Row,
    Select,
    Space,
    Statistic,
    Table,
    Tag,
    theme,
} from 'antd';
import { useMemo, useState } from 'react';

const { useToken } = theme;

interface PageProps {
    pkrAccounts: Account[];
    shareholders: Shareholder[];
}

export default function CreateDistribution() {
    const { token: antToken } = useToken();
    const { pkrAccounts, shareholders } = usePage<PageProps>().props;

    const [form] = Form.useForm();
    const [selectedAccountId, setSelectedAccountId] = useState<number | null>(null);
    const [manualAmount, setManualAmount] = useState<number>(0); // In major units (PKR)
    const [loading, setLoading] = useState(false);

    // Find selected account
    const selectedAccount = useMemo(() => pkrAccounts.find((acc) => acc.id === selectedAccountId), [selectedAccountId, pkrAccounts]);

    // Calculate distribution preview
    const distributionPreview = useMemo(() => {
        if (!manualAmount || manualAmount <= 0) return [];

        const amountInPaisa = Math.round(manualAmount * 100);

        return shareholders.map((shareholder) => {
            const equityPercent = parseFloat(shareholder.equity_percentage);
            const allocatedPaisa = Math.round(amountInPaisa * (equityPercent / 100));

            return {
                shareholder,
                equityPercent,
                allocatedPaisa,
                allocatedPkr: allocatedPaisa / 100,
                formattedAmount: `Rs ${(allocatedPaisa / 100).toFixed(2)}`,
            };
        });
    }, [manualAmount, shareholders]);

    // Calculate totals
    const totals = useMemo(() => {
        const humanPartnerTotal = distributionPreview
            .filter((line) => !line.shareholder.is_office_reserve)
            .reduce((sum, line) => sum + line.allocatedPaisa, 0);

        const officeReserveTotal = distributionPreview
            .filter((line) => line.shareholder.is_office_reserve)
            .reduce((sum, line) => sum + line.allocatedPaisa, 0);

        return {
            humanPartnerPaisa: humanPartnerTotal,
            humanPartnerPkr: humanPartnerTotal / 100,
            officeReservePaisa: officeReserveTotal,
            officeReservePkr: officeReserveTotal / 100,
        };
    }, [distributionPreview]);

    // Balance check
    const hasSufficientBalance = useMemo(() => {
        if (!selectedAccount) return false;
        return selectedAccount.current_balance >= totals.humanPartnerPaisa;
    }, [selectedAccount, totals.humanPartnerPaisa]);

    // Handle submission
    const handleSubmit = async (action: 'draft' | 'process') => {
        try {
            await form.validateFields();

            if (action === 'process' && !selectedAccountId) {
                notification.error({
                    message: 'Bank account required',
                    description: 'Please select a bank account to process distribution',
                });
                return;
            }

            if (action === 'process' && !hasSufficientBalance) {
                notification.error({
                    message: 'Insufficient balance',
                    description: 'The selected account does not have sufficient balance',
                });
                return;
            }

            setLoading(true);

            const values = form.getFieldsValue();
            await api.post('/dashboard/distributions', {
                manual_amount_pkr: Math.round(manualAmount * 100), // Convert to paisa
                account_id: action === 'process' ? selectedAccountId : null,
                action,
                notes: values.notes,
            });

            notification.success({
                message: action === 'draft' ? 'Distribution saved as draft' : 'Distribution processed successfully',
            });

            router.visit(distributionsIndex.url());
        } catch (error: unknown) {
            const errorMessage =
                typeof error === 'object' && error !== null && 'response' in error
                    ? (error as { response?: { data?: { message?: string } } }).response?.data?.message
                    : 'An error occurred';

            notification.error({
                message: 'Failed to create distribution',
                description: errorMessage,
            });
        } finally {
            setLoading(false);
        }
    };

    const tableColumns = [
        {
            title: 'Shareholder',
            dataIndex: ['shareholder', 'name'],
            key: 'shareholder',
            render: (text: string, record: (typeof distributionPreview)[0]) => (
                <Space>
                    <BankOutlined />
                    {text}
                    {record.shareholder.is_office_reserve && <Tag color="blue">Retained</Tag>}
                </Space>
            ),
        },
        {
            title: 'Equity %',
            dataIndex: 'equityPercent',
            key: 'equity',
            align: 'right' as const,
            render: (value: number) => `${value.toFixed(2)}%`,
        },
        {
            title: 'Allocated Amount',
            dataIndex: 'formattedAmount',
            key: 'amount',
            align: 'right' as const,
        },
    ];

    return (
        <AppLayout
            title="New Distribution"
            breadcrumbs={[
                { title: 'Distributions', href: distributionsIndex.url() },
                { title: 'New Distribution', href: '' },
            ]}
        >
            <Space direction="vertical" size="large" style={{ width: '100%' }}>
                {/* Form Section */}
                <Card title="Distribution Details">
                    <Form form={form} layout="vertical">
                        <Row gutter={16}>
                            <Col xs={24} md={12}>
                                <Form.Item label="Bank Account" name="account_id" rules={[{ required: true, message: 'Please select bank account' }]}>
                                    <Select
                                        placeholder="Select bank account"
                                        options={pkrAccounts.map((account) => ({
                                            label: `${account.name} (${account.formatted_balance})`,
                                            value: account.id,
                                        }))}
                                        onChange={setSelectedAccountId}
                                    />
                                </Form.Item>
                            </Col>
                            <Col xs={24} md={12}>
                                <Form.Item
                                    label="Distribution Amount (PKR)"
                                    name="manual_amount"
                                    rules={[
                                        { required: true, message: 'Please enter amount' },
                                        {
                                            type: 'number',
                                            min: 0.01,
                                            message: 'Amount must be greater than 0',
                                        },
                                    ]}
                                >
                                    <InputNumber
                                        style={{ width: '100%' }}
                                        placeholder="Enter amount in PKR"
                                        min={0}
                                        precision={2}
                                        onChange={(value) => setManualAmount(value || 0)}
                                        prefix="Rs"
                                    />
                                </Form.Item>
                            </Col>
                        </Row>

                        <Form.Item label="Notes" name="notes">
                            <Input.TextArea rows={3} placeholder="Optional notes..." />
                        </Form.Item>
                    </Form>
                </Card>

                {/* Preview Section */}
                {manualAmount > 0 && (
                    <>
                        {/* Balance Preview */}
                        {selectedAccount && (
                            <Card title="Account Balance Preview">
                                <Row gutter={16}>
                                    <Col xs={24} sm={8}>
                                        <Statistic title="Current Balance" value={selectedAccount.current_balance / 100} prefix="Rs" precision={2} />
                                    </Col>
                                    <Col xs={24} sm={8}>
                                        <Statistic
                                            title="Distribution Amount"
                                            value={totals.humanPartnerPkr}
                                            prefix="Rs"
                                            precision={2}
                                            valueStyle={{ color: antToken.colorError }}
                                        />
                                    </Col>
                                    <Col xs={24} sm={8}>
                                        <Statistic
                                            title="Balance After Distribution"
                                            value={(selectedAccount.current_balance - totals.humanPartnerPaisa) / 100}
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
                                        description={`Need Rs ${totals.humanPartnerPkr.toFixed(2)}, have ${selectedAccount.formatted_balance}`}
                                        type="error"
                                        showIcon
                                        style={{ marginTop: 16 }}
                                    />
                                )}
                            </Card>
                        )}

                        {/* Distribution Preview */}
                        <Card title="Distribution Preview">
                            <Descriptions bordered column={2} style={{ marginBottom: 16 }}>
                                <Descriptions.Item label="Total Amount">Rs {manualAmount.toFixed(2)}</Descriptions.Item>
                                <Descriptions.Item label="Human Partner Payouts">
                                    <span style={{ color: antToken.colorError }}>Rs {totals.humanPartnerPkr.toFixed(2)}</span>
                                </Descriptions.Item>
                                <Descriptions.Item label="Office Reserve (Retained)" span={2}>
                                    <span style={{ color: antToken.colorSuccess }}>Rs {totals.officeReservePkr.toFixed(2)}</span>
                                </Descriptions.Item>
                            </Descriptions>

                            <Table
                                columns={tableColumns}
                                dataSource={distributionPreview}
                                rowKey={(record) => record.shareholder.id}
                                pagination={false}
                            />
                        </Card>
                    </>
                )}

                {/* Action Buttons */}
                <Card>
                    <Space>
                        <Button icon={<SaveOutlined />} onClick={() => handleSubmit('draft')} loading={loading} size="large">
                            Save as Draft
                        </Button>
                        <Button
                            type="primary"
                            icon={<CheckCircleOutlined />}
                            onClick={() => handleSubmit('process')}
                            loading={loading}
                            disabled={!selectedAccount || !hasSufficientBalance || manualAmount <= 0}
                            size="large"
                        >
                            Confirm & Distribute
                        </Button>
                        <Button onClick={() => router.visit(distributionsIndex.url())} disabled={loading} size="large">
                            Cancel
                        </Button>
                    </Space>
                </Card>
            </Space>
        </AppLayout>
    );
}
