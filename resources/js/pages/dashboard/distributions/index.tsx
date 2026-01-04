import DataTable from '@/components/ui/DataTable';
import AppLayout from '@/layouts/app-layout';
import api from '@/lib/axios';
import { create as distributionsCreate, data as distributionsData, show as distributionsShow } from '@/routes/distributions';
import type { DataTableColumn, Distribution, FilterConfig } from '@/types';
import { CheckCircleOutlined, DeleteOutlined, EyeOutlined, PlusOutlined } from '@ant-design/icons';
import { router } from '@inertiajs/react';
import { Button, Popconfirm, Space, Tag, notification } from 'antd';
import { useState } from 'react';

const filters: FilterConfig[] = [
    {
        type: 'select',
        key: 'status',
        label: 'Status',
        options: [
            { label: 'Draft', value: 'draft' },
            { label: 'Processed', value: 'processed' },
        ],
    },
];

export default function DistributionsIndex() {
    const [tableKey, setTableKey] = useState(0);

    const handleDelete = async (id: number) => {
        try {
            await api.delete(`/dashboard/distributions/${id}`);
            notification.success({
                message: 'Distribution deleted successfully',
            });
            setTableKey((prev) => prev + 1);
        } catch (error: unknown) {
            const errorMessage =
                typeof error === 'object' && error !== null && 'response' in error
                    ? (error as { response?: { data?: { message?: string } } }).response?.data?.message
                    : 'An error occurred';

            notification.error({
                message: 'Failed to delete distribution',
                description: errorMessage,
            });
        }
    };

    const columns: DataTableColumn<Distribution>[] = [
        {
            title: 'Number',
            dataIndex: 'distribution_number',
            key: 'distribution_number',
            sorter: true,
        },
        {
            title: 'Period',
            dataIndex: 'period_label',
            key: 'period_start',
            sorter: true,
        },
        {
            title: 'Revenue',
            dataIndex: 'formatted_revenue',
            key: 'total_revenue_pkr',
            align: 'right',
            sorter: true,
        },
        {
            title: 'Expenses',
            dataIndex: 'formatted_expenses',
            key: 'total_expenses_pkr',
            align: 'right',
            sorter: true,
        },
        {
            title: 'Net Profit',
            dataIndex: 'formatted_net_profit',
            key: 'calculated_net_profit_pkr',
            align: 'right',
            sorter: true,
        },
        {
            title: 'Status',
            dataIndex: 'status',
            key: 'status',
            render: (status: string) =>
                status === 'draft' ? (
                    <Tag color="warning">Draft</Tag>
                ) : (
                    <Tag color="success" icon={<CheckCircleOutlined />}>
                        Processed
                    </Tag>
                ),
        },
        {
            title: 'Actions',
            key: 'actions',
            align: 'right',
            render: (_: unknown, record: Distribution) => (
                <Space>
                    <Button type="text" size="small" icon={<EyeOutlined />} onClick={() => router.visit(distributionsShow.url({ id: record.id }))}>
                        View
                    </Button>
                    {record.is_draft && (
                        <Popconfirm
                            title="Delete Distribution"
                            description="Are you sure you want to delete this distribution?"
                            onConfirm={() => handleDelete(record.id)}
                            okText="Yes"
                            cancelText="No"
                        >
                            <Button type="text" size="small" danger icon={<DeleteOutlined />}>
                                Delete
                            </Button>
                        </Popconfirm>
                    )}
                </Space>
            ),
        },
    ];

    return (
        <AppLayout
            title="Distributions"
            actions={
                <Button type="primary" icon={<PlusOutlined />} onClick={() => router.visit(distributionsCreate.url())}>
                    New Distribution
                </Button>
            }
        >
            <DataTable<Distribution> key={tableKey} columns={columns} fetchUrl={distributionsData.url()} filters={filters} />
        </AppLayout>
    );
}
