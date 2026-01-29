import DataTable from '@/components/ui/DataTable';
import AppLayout from '@/layouts/app-layout';
import api from '@/lib/axios';
import type { Account, FilterConfig, Payroll } from '@/types';
import { CheckCircleOutlined, ClockCircleOutlined, DollarOutlined, PlusOutlined } from '@ant-design/icons';
import { usePage } from '@inertiajs/react';
import { Button, notification, Select, Space, Tag, theme } from 'antd';
import { useState } from 'react';
import PaymentModal from './partials/payment-modal';

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
    usdAccounts: Account[];
}

export default function PayrollIndex() {
    const { token } = useToken();
    const { pkrAccounts, usdAccounts } = usePage<PageProps>().props;
    const currentDate = new Date();
    const [selectedMonth, setSelectedMonth] = useState(currentDate.getMonth() + 1);
    const [selectedYear, setSelectedYear] = useState(currentDate.getFullYear());
    const [tableKey, setTableKey] = useState(0);
    const [selectedPayrolls, setSelectedPayrolls] = useState<Payroll[]>([]);
    const [paymentModalVisible, setPaymentModalVisible] = useState(false);

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
                    ? (error as { response?: { data?: { message?: string } } }).response?.data?.message
                    : 'An error occurred while generating payroll';

            notification.error({
                message: 'Failed to generate payroll',
                description: errorMessage,
            });
        }
    };

    const openPaymentModal = () => {
        if (selectedPayrolls.length === 0) {
            notification.warning({
                message: 'No payrolls selected',
                description: 'Please select at least one payroll entry to pay.',
            });
            return;
        }

        // Check for available accounts based on selected currencies
        const currencies = [...new Set(selectedPayrolls.map((p) => p.deposit_currency))];
        const hasPkr = currencies.includes('PKR');
        const hasUsd = currencies.includes('USD');

        if (hasPkr && pkrAccounts.length === 0) {
            notification.error({
                message: 'No PKR accounts available',
                description: 'Please create a PKR account first.',
            });
            return;
        }

        if (hasUsd && usdAccounts.length === 0) {
            notification.error({
                message: 'No USD accounts available',
                description: 'Please create a USD account first.',
            });
            return;
        }

        setPaymentModalVisible(true);
    };

    const handlePaymentSuccess = () => {
        setPaymentModalVisible(false);
        setSelectedPayrolls([]);
        setTableKey((prev) => prev + 1);
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
                    <div style={{ fontSize: 12, color: token.colorTextSecondary }}>{record.employee?.designation}</div>
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
            render: (value: string) => <span style={{ fontWeight: 600, color: token.colorPrimary }}>{value}</span>,
        },
        {
            title: 'Status',
            dataIndex: 'status',
            key: 'status',
            filterable: true,
            render: (status: string) => (
                <Tag icon={status === 'paid' ? <CheckCircleOutlined /> : <ClockCircleOutlined />} color={status === 'paid' ? 'success' : 'warning'}>
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
                    <Button icon={<DollarOutlined />} onClick={openPaymentModal} disabled={selectedPayrolls.length === 0}>
                        Pay Selected ({selectedPayrolls.length})
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
                    selectedRowKeys: selectedPayrolls.map((p) => p.id),
                    onChange: (_selectedKeys, selectedRows: Payroll[]) => {
                        setSelectedPayrolls(selectedRows);
                    },
                    getCheckboxProps: (record: Payroll) => ({
                        disabled: record.status === 'paid',
                    }),
                }}
            />

            <PaymentModal
                visible={paymentModalVisible}
                payrolls={selectedPayrolls}
                pkrAccounts={pkrAccounts}
                usdAccounts={usdAccounts}
                onCancel={() => setPaymentModalVisible(false)}
                onSuccess={handlePaymentSuccess}
            />
        </AppLayout>
    );
}
