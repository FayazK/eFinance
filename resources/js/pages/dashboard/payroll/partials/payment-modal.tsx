import api from '@/lib/axios';
import type { Account, Payroll } from '@/types';
import { WarningOutlined } from '@ant-design/icons';
import { Alert, Col, DatePicker, Form, Input, InputNumber, Modal, notification, Row, Select, Statistic, Table, theme, Typography } from 'antd';
import type { ColumnsType } from 'antd/es/table';
import dayjs from 'dayjs';
import { useMemo, useState } from 'react';

const { useToken } = theme;
const { Text } = Typography;

interface PaymentModalProps {
    visible: boolean;
    payrolls: Payroll[];
    pkrAccounts: Account[];
    usdAccounts: Account[];
    onCancel: () => void;
    onSuccess: () => void;
}

export default function PaymentModal({ visible, payrolls, pkrAccounts, usdAccounts, onCancel, onSuccess }: PaymentModalProps) {
    const { token: antToken } = useToken();
    const [form] = Form.useForm();
    const [loading, setLoading] = useState(false);
    const [selectedPkrAccountId, setSelectedPkrAccountId] = useState<number | null>(null);
    const [selectedUsdAccountId, setSelectedUsdAccountId] = useState<number | null>(null);
    const [exchangeRate, setExchangeRate] = useState<number | null>(null);

    // Group payrolls by currency
    const pkrPayrolls = useMemo(() => payrolls.filter((p) => p.deposit_currency === 'PKR'), [payrolls]);
    const usdPayrolls = useMemo(() => payrolls.filter((p) => p.deposit_currency === 'USD'), [payrolls]);

    const hasPkr = pkrPayrolls.length > 0;
    const hasUsd = usdPayrolls.length > 0;

    // Calculate totals
    const pkrTotal = useMemo(() => pkrPayrolls.reduce((sum, p) => sum + p.net_payable, 0), [pkrPayrolls]);
    const usdTotalPkr = useMemo(() => usdPayrolls.reduce((sum, p) => sum + p.net_payable, 0), [usdPayrolls]);
    const usdTotalUsd = useMemo(() => {
        if (!exchangeRate || exchangeRate <= 0) return 0;
        return usdTotalPkr / exchangeRate;
    }, [usdTotalPkr, exchangeRate]);

    // Get selected accounts
    const selectedPkrAccount = useMemo(() => pkrAccounts.find((a) => a.id === selectedPkrAccountId), [pkrAccounts, selectedPkrAccountId]);
    const selectedUsdAccount = useMemo(() => usdAccounts.find((a) => a.id === selectedUsdAccountId), [usdAccounts, selectedUsdAccountId]);

    // Balance validation
    const hasSufficientPkrBalance = useMemo(() => {
        if (!hasPkr) return true;
        if (!selectedPkrAccount) return false;
        return selectedPkrAccount.current_balance >= pkrTotal;
    }, [hasPkr, selectedPkrAccount, pkrTotal]);

    const hasSufficientUsdBalance = useMemo(() => {
        if (!hasUsd) return true;
        if (!selectedUsdAccount || !exchangeRate) return false;
        return selectedUsdAccount.current_balance >= usdTotalUsd;
    }, [hasUsd, selectedUsdAccount, usdTotalUsd, exchangeRate]);

    const canSubmit = useMemo(() => {
        const pkrReady = !hasPkr || (selectedPkrAccountId && hasSufficientPkrBalance);
        const usdReady = !hasUsd || (selectedUsdAccountId && exchangeRate && exchangeRate > 0 && hasSufficientUsdBalance);
        return pkrReady && usdReady;
    }, [hasPkr, hasUsd, selectedPkrAccountId, selectedUsdAccountId, exchangeRate, hasSufficientPkrBalance, hasSufficientUsdBalance]);

    const handleSubmit = async (values: Record<string, unknown>) => {
        setLoading(true);
        try {
            const payload = {
                payroll_ids: payrolls.map((p) => p.id),
                pkr_account_id: values.pkr_account_id || null,
                usd_account_id: values.usd_account_id || null,
                exchange_rate: values.exchange_rate || null,
                payment_date: values.payment_date ? dayjs(values.payment_date as dayjs.Dayjs).format('YYYY-MM-DD') : null,
                notes: values.notes || null,
            };

            await api.post('/dashboard/payroll/pay', payload);
            notification.success({
                message: 'Payroll paid successfully',
                description: `${payrolls.length} payroll(s) have been processed.`,
            });
            form.resetFields();
            setSelectedPkrAccountId(null);
            setSelectedUsdAccountId(null);
            setExchangeRate(null);
            onSuccess();
        } catch (error: unknown) {
            const err = error as { response?: { status: number; data: { errors: { [key: string]: string[] }; message: string } } };
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
                    description: 'An unexpected error occurred while processing payment.',
                });
            }
        } finally {
            setLoading(false);
        }
    };

    const handleCancel = () => {
        form.resetFields();
        setSelectedPkrAccountId(null);
        setSelectedUsdAccountId(null);
        setExchangeRate(null);
        onCancel();
    };

    const columns: ColumnsType<Payroll> = [
        {
            title: 'Employee',
            dataIndex: ['employee', 'name'],
            key: 'employee',
            render: (_, record) => (
                <div>
                    <div style={{ fontWeight: 500 }}>{record.employee?.name}</div>
                    <Text type="secondary" style={{ fontSize: 12 }}>
                        {record.employee?.designation}
                    </Text>
                </div>
            ),
        },
        {
            title: 'Period',
            dataIndex: 'period_label',
            key: 'period',
        },
        {
            title: 'Currency',
            dataIndex: 'deposit_currency',
            key: 'currency',
            render: (currency: string) => <Text strong>{currency}</Text>,
        },
        {
            title: 'Net Payable (PKR)',
            dataIndex: 'formatted_net_payable',
            key: 'net_payable_pkr',
            align: 'right',
        },
        {
            title: 'Payment Amount',
            key: 'payment_amount',
            align: 'right',
            render: (_, record) => {
                if (record.deposit_currency === 'PKR') {
                    return <Text strong>{record.formatted_net_payable}</Text>;
                }
                // USD: calculate from exchange rate
                if (!exchangeRate || exchangeRate <= 0) {
                    return <Text type="secondary">Enter rate</Text>;
                }
                const usdAmount = record.net_payable / exchangeRate;
                return (
                    <Text strong style={{ color: antToken.colorPrimary }}>
                        ${usdAmount.toFixed(2)}
                    </Text>
                );
            },
        },
    ];

    return (
        <Modal title="Pay Payroll" open={visible} onCancel={handleCancel} onOk={() => form.submit()} confirmLoading={loading} okButtonProps={{ disabled: !canSubmit }} width={900} destroyOnClose>
            {/* Employee Table */}
            <Table<Payroll>
                columns={columns}
                dataSource={payrolls}
                rowKey="id"
                pagination={false}
                size="small"
                style={{ marginBottom: 24 }}
                summary={() => (
                    <Table.Summary fixed>
                        <Table.Summary.Row>
                            <Table.Summary.Cell index={0} colSpan={3}>
                                <Text strong>Total</Text>
                            </Table.Summary.Cell>
                            <Table.Summary.Cell index={1} align="right">
                                <Text strong>Rs {(pkrTotal + usdTotalPkr).toLocaleString('en-US', { minimumFractionDigits: 2 })}</Text>
                            </Table.Summary.Cell>
                            <Table.Summary.Cell index={2} align="right">
                                {hasPkr && hasUsd ? (
                                    <div>
                                        <div>
                                            <Text strong>PKR: Rs {pkrTotal.toLocaleString('en-US', { minimumFractionDigits: 2 })}</Text>
                                        </div>
                                        <div>
                                            <Text strong style={{ color: antToken.colorPrimary }}>
                                                USD: ${usdTotalUsd.toLocaleString('en-US', { minimumFractionDigits: 2 })}
                                            </Text>
                                        </div>
                                    </div>
                                ) : hasPkr ? (
                                    <Text strong>Rs {pkrTotal.toLocaleString('en-US', { minimumFractionDigits: 2 })}</Text>
                                ) : (
                                    <Text strong style={{ color: antToken.colorPrimary }}>
                                        ${usdTotalUsd.toLocaleString('en-US', { minimumFractionDigits: 2 })}
                                    </Text>
                                )}
                            </Table.Summary.Cell>
                        </Table.Summary.Row>
                    </Table.Summary>
                )}
            />

            <Form
                form={form}
                layout="vertical"
                onFinish={handleSubmit}
                initialValues={{ payment_date: dayjs() }}
                onValuesChange={(changedValues) => {
                    if ('pkr_account_id' in changedValues) {
                        setSelectedPkrAccountId(changedValues.pkr_account_id);
                    }
                    if ('usd_account_id' in changedValues) {
                        setSelectedUsdAccountId(changedValues.usd_account_id);
                    }
                    if ('exchange_rate' in changedValues) {
                        setExchangeRate(changedValues.exchange_rate);
                    }
                }}
            >
                <Row gutter={16}>
                    {/* PKR Account Selector */}
                    {hasPkr && (
                        <Col span={hasUsd ? 12 : 24}>
                            <Form.Item label="PKR Account" name="pkr_account_id" rules={[{ required: true, message: 'Select PKR account' }]}>
                                <Select
                                    placeholder="Select PKR account"
                                    options={pkrAccounts.map((account) => ({
                                        label: `${account.name} (${account.formatted_balance})`,
                                        value: account.id,
                                    }))}
                                />
                            </Form.Item>
                            {selectedPkrAccount && (
                                <Row gutter={16} style={{ marginBottom: 16 }}>
                                    <Col span={12}>
                                        <Statistic title="PKR Balance" value={selectedPkrAccount.current_balance} prefix="Rs" precision={2} valueStyle={{ fontSize: 16 }} />
                                    </Col>
                                    <Col span={12}>
                                        <Statistic
                                            title="After Payment"
                                            value={selectedPkrAccount.current_balance - pkrTotal}
                                            prefix="Rs"
                                            precision={2}
                                            valueStyle={{
                                                fontSize: 16,
                                                color: hasSufficientPkrBalance ? antToken.colorSuccess : antToken.colorError,
                                            }}
                                        />
                                    </Col>
                                </Row>
                            )}
                            {selectedPkrAccount && !hasSufficientPkrBalance && (
                                <Alert message="Insufficient PKR balance" type="error" showIcon icon={<WarningOutlined />} style={{ marginBottom: 16 }} />
                            )}
                        </Col>
                    )}

                    {/* USD Account Selector */}
                    {hasUsd && (
                        <Col span={hasPkr ? 12 : 24}>
                            <Form.Item label="USD Account" name="usd_account_id" rules={[{ required: true, message: 'Select USD account' }]}>
                                <Select
                                    placeholder="Select USD account"
                                    options={usdAccounts.map((account) => ({
                                        label: `${account.name} (${account.formatted_balance})`,
                                        value: account.id,
                                    }))}
                                />
                            </Form.Item>
                            <Form.Item
                                label="Exchange Rate (PKR per USD)"
                                name="exchange_rate"
                                rules={[{ required: true, message: 'Enter exchange rate' }]}
                                extra="Used to calculate USD amount from PKR salary"
                            >
                                <InputNumber min={0.0001} max={99999} precision={4} style={{ width: '100%' }} placeholder="e.g., 280" />
                            </Form.Item>
                            {selectedUsdAccount && exchangeRate && exchangeRate > 0 && (
                                <Row gutter={16} style={{ marginBottom: 16 }}>
                                    <Col span={12}>
                                        <Statistic title="USD Balance" value={selectedUsdAccount.current_balance} prefix="$" precision={2} valueStyle={{ fontSize: 16 }} />
                                    </Col>
                                    <Col span={12}>
                                        <Statistic
                                            title="After Payment"
                                            value={selectedUsdAccount.current_balance - usdTotalUsd}
                                            prefix="$"
                                            precision={2}
                                            valueStyle={{
                                                fontSize: 16,
                                                color: hasSufficientUsdBalance ? antToken.colorSuccess : antToken.colorError,
                                            }}
                                        />
                                    </Col>
                                </Row>
                            )}
                            {selectedUsdAccount && exchangeRate && !hasSufficientUsdBalance && (
                                <Alert message="Insufficient USD balance" type="error" showIcon icon={<WarningOutlined />} style={{ marginBottom: 16 }} />
                            )}
                        </Col>
                    )}
                </Row>

                <Row gutter={16}>
                    <Col span={12}>
                        <Form.Item label="Payment Date" name="payment_date" rules={[{ required: true, message: 'Select payment date' }]}>
                            <DatePicker style={{ width: '100%' }} format="YYYY-MM-DD" />
                        </Form.Item>
                    </Col>
                    <Col span={12}>
                        <Form.Item label="Notes" name="notes">
                            <Input.TextArea rows={1} placeholder="Optional payment notes" />
                        </Form.Item>
                    </Col>
                </Row>
            </Form>
        </Modal>
    );
}
