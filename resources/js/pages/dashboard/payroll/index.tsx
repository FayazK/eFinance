import React, { useState } from 'react';
import { Button, Space, Tag, Select, notification, Statistic, Card, Row, Col, theme } from 'antd';
import {
    PlusOutlined,
    DollarOutlined,
    CheckCircleOutlined,
    ClockCircleOutlined,
} from '@ant-design/icons';
import AppLayout from '@/layouts/app-layout';
import DataTable from '@/components/ui/DataTable';
import type { Payroll, FilterConfig } from '@/types';
import { router } from '@inertiajs/react';
import api from '@/lib/axios';

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

export default function PayrollIndex() {
    const { token } = useToken();
    const currentDate = new Date();
    const [selectedMonth, setSelectedMonth] = useState(currentDate.getMonth() + 1);
    const [selectedYear, setSelectedYear] = useState(currentDate.getFullYear());
    const [tableKey, setTableKey] = useState(0);

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
            />
        </AppLayout>
    );
}
