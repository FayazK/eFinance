import React, { useState } from 'react';
import { Button, Space, Tag, Select, notification, Statistic, Card, Row, Col, theme, Modal, Form, DatePicker, Input } from 'antd';
import {
    PlusOutlined,
    DollarOutlined,
    CheckCircleOutlined,
    ClockCircleOutlined,
} from '@ant-design/icons';
import AppLayout from '@/layouts/app-layout';
import DataTable from '@/components/ui/DataTable';
import type { Payroll, FilterConfig, Account } from '@/types';
import { router, usePage } from '@inertiajs/react';
import api from '@/lib/axios';
import dayjs from 'dayjs';

const { useToken } = theme;

const MONTHS = [
    { label: 'January', value: 1 },
    { label: 'February', value: 2 },
    { label: 'March', value: 3 },
    { label: 'April', value: 4 },
    { label: 'May', value: 5 },
    { label: 'June', value: 6 },
    { label: 'July', value: 7 },
    { label: 'August', value: 8 },
    { label: 'September', value: 9 },
    { label: 'October', value: 10 },
    { label: 'November', value: 11 },
    { label: 'December', value: 12 },
];

const YEARS = Array.from({ length: 5 }, (_, i) => {
    const year = new Date().getFullYear() - 2 + i;
    return { label: year.toString(), value: year };
});

const filters: FilterConfig[] = [
    {
        type: 'select',
        key: 'status',
        label: 'Status',
        options: [
            { label: 'Pending', value: 'pending' },
            { label: 'Paid', value: 'paid' },
        ],
    },
];

interface PageProps {
    pkrAccounts: Account[];
}

export default function PayrollIndex() {
    const { token } = useToken();
    const { pkrAccounts } = usePage<PageProps>().props;
    const currentDate = new Date();
    const [selectedMonth, setSelectedMonth] = useState(currentDate.getMonth() + 1);
    const [selectedYear, setSelectedYear] = useState(currentDate.getFullYear());
    const [tableKey, setTableKey] = useState(0);
    const [selectedPayrollIds, setSelectedPayrollIds] = useState<number[]>([]);
    const [paymentModalVisible, setPaymentModalVisible] = useState(false);
    const [paymentForm] = Form.useForm();
    const [paymentLoading, setPaymentLoading] = useState(false);

    const handleGenerate = async () => {
        try {
            await api.post('/dashboard/payroll/generate', {
                month: selectedMonth,
                year: selectedYear,
            });
            notification.success({
                message: 'Payroll generated successfully',
            });
            setTableKey((prev) => prev + 1); // Force table reload
        } catch (error: unknown) {
            const errorMessage =
                typeof error === 'object' && error !== null && 'response' in error
                    ? (error as { response?: { data?: { message?: string } } }).response?.data
                          ?.message
                    : 'An error occurred while generating payroll';

            notification.error({
                message: 'Failed to generate payroll',
                description: errorMessage,
            });
        }
    };

    const handlePayment = async (values: Record<string, unknown>) => {
        setPaymentLoading(true);
        try {
            const formattedValues = {
                ...values,
                payroll_ids: selectedPayrollIds,
                payment_date: values.payment_date ? dayjs(values.payment_date as dayjs.Dayjs).format('YYYY-MM-DD') : null,
            };

            await api.post('/dashboard/payroll/pay', formattedValues);
            notification.success({
                message: 'Payroll paid successfully',
            });
            setPaymentModalVisible(false);
            setSelectedPayrollIds([]);
            paymentForm.resetFields();
            setTableKey((prev) => prev + 1); // Force table reload
        } catch (error: unknown) {
            const err = error as { response?: { status: number; data: { errors: { [key: string]: string[] }; message: string; }; }; };
            if (err.response && err.response.status === 422) {
                const validationErrors = err.response.data.errors;
                const formErrors = Object.keys(validationErrors).map(key => ({
                    name: key,
                    errors: validationErrors[key],
                }));
                paymentForm.setFields(formErrors);
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
            setPaymentLoading(false);
        }
    };

    const openPaymentModal = () => {
        if (selectedPayrollIds.length === 0) {
            notification.warning({
                message: 'No payrolls selected',
                description: 'Please select at least one payroll entry to pay.',
            });
            return;
        }
        paymentForm.setFieldsValue({
            payment_date: dayjs(),
        });
        setPaymentModalVisible(true);
    };

    const columns = [
        {
            title: 'Employee',
            dataIndex: ['employee', 'name'],
            key: 'employee_name',
            searchable: true,
            render: (_: unknown, record: Payroll) => (
                <div>
                    <div style={{ fontWeight: 500 }}>{record.employee?.name}</div>
                    <div style={{ fontSize: 12, color: token.colorTextSecondary }}>
                        {record.employee?.designation}
                    </div>
                </div>
            ),
        },
        {
            title: 'Period',
            dataIndex: 'period_label',
            key: 'period_label',
        },
        {
            title: 'Base Salary',
            dataIndex: 'formatted_base_salary',
            key: 'base_salary',
            align: 'right' as const,
        },
        {
            title: 'Bonus',
            dataIndex: 'formatted_bonus',
            key: 'bonus',
            align: 'right' as const,
        },
        {
            title: 'Deductions',
            dataIndex: 'formatted_deductions',
            key: 'deductions',
            align: 'right' as const,
        },
        {
            title: 'Net Payable',
            dataIndex: 'formatted_net_payable',
            key: 'net_payable',
            align: 'right' as const,
            render: (value: string) => (
                <span style={{ fontWeight: 600, color: token.colorPrimary }}>{value}</span>
            ),
        },
        {
            title: 'Status',
            dataIndex: 'status',
            key: 'status',
            filterable: true,
            render: (status: string) => (
                <Tag
                    icon={
                        status === 'paid' ? <CheckCircleOutlined /> : <ClockCircleOutlined />
                    }
                    color={status === 'paid' ? 'success' : 'warning'}
                >
                    {status.toUpperCase()}
                </Tag>
            ),
        },
        {
            title: 'Paid At',
            dataIndex: 'paid_at',
            key: 'paid_at',
            render: (date: string | undefined) => date || '-',
        },
    ];

    return (
        <AppLayout
            breadcrumbs={[
                { title: 'Dashboard', href: '/dashboard' },
                { title: 'Payroll', href: '/dashboard/payroll' },
            ]}
            title="Payroll Management"
            actions={
                <Space>
                    <Select
                        value={selectedMonth}
                        onChange={(value) => {
                            setSelectedMonth(value);
                            setTableKey((prev) => prev + 1);
                        }}
                        options={MONTHS}
                        style={{ width: 140 }}
                    />
                    <Select
                        value={selectedYear}
                        onChange={(value) => {
                            setSelectedYear(value);
                            setTableKey((prev) => prev + 1);
                        }}
                        options={YEARS}
                        style={{ width: 100 }}
                    />
                    <Button
                        icon={<DollarOutlined />}
                        onClick={openPaymentModal}
                        disabled={selectedPayrollIds.length === 0}
                    >
                        Pay Selected ({selectedPayrollIds.length})
                    </Button>
                    <Button type="primary" icon={<PlusOutlined />} onClick={handleGenerate}>
                        Generate Payroll
                    </Button>
                </Space>
            }
        >
            <DataTable<Payroll>
                key={tableKey}
                columns={columns}
                fetchUrl={`/dashboard/payroll/data?month=${selectedMonth}&year=${selectedYear}`}
                filters={filters}
                rowSelection={{
                    selectedRowKeys: selectedPayrollIds,
                    onChange: (selectedKeys) => {
                        setSelectedPayrollIds(selectedKeys as number[]);
                    },
                    getCheckboxProps: (record: Payroll) => ({
                        disabled: record.status === 'paid',
                    }),
                }}
            />

            <Modal
                title="Pay Payroll"
                open={paymentModalVisible}
                onCancel={() => {
                    setPaymentModalVisible(false);
                    paymentForm.resetFields();
                }}
                footer={null}
                destroyOnClose
            >
                <Form
                    form={paymentForm}
                    layout="vertical"
                    onFinish={handlePayment}
                >
                    <Form.Item
                        label="Account"
                        name="account_id"
                        rules={[{ required: true, message: 'Please select an account!' }]}
                    >
                        <Select
                            placeholder="Select PKR account"
                            options={pkrAccounts.map(account => ({
                                label: `${account.name} (${account.formatted_current_balance})`,
                                value: account.id,
                            }))}
                        />
                    </Form.Item>

                    <Form.Item
                        label="Payment Date"
                        name="payment_date"
                        rules={[{ required: true, message: 'Please select payment date!' }]}
                    >
                        <DatePicker style={{ width: '100%' }} format="YYYY-MM-DD" />
                    </Form.Item>

                    <Form.Item
                        label="Notes"
                        name="notes"
                    >
                        <Input.TextArea rows={3} placeholder="Optional payment notes" />
                    </Form.Item>

                    <Form.Item>
                        <Space>
                            <Button type="primary" htmlType="submit" loading={paymentLoading}>
                                Pay {selectedPayrollIds.length} Payroll{selectedPayrollIds.length > 1 ? 's' : ''}
                            </Button>
                            <Button onClick={() => setPaymentModalVisible(false)}>
                                Cancel
                            </Button>
                        </Space>
                    </Form.Item>
                </Form>
            </Modal>
        </AppLayout>
    );
}
