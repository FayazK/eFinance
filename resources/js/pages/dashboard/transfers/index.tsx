import { useState } from 'react';
import { Button, Card } from 'antd';
import { PlusOutlined } from '@ant-design/icons';
import AppLayout from '@/layouts/app-layout';
import DataTable from '@/components/ui/DataTable';
import { Transfer } from '@/types';
import { create } from '@/routes/transfers';
import type { DataTableColumn } from '@/types';

export default function TransfersIndex() {
    const [filters, setFilters] = useState({});

    const columns: DataTableColumn<Transfer>[] = [
        {
            title: 'Date',
            dataIndex: 'date',
            key: 'date',
            sorter: true,
        },
        {
            title: 'From',
            key: 'source',
            render: (_, record) => (
                <div>
                    <div className="font-medium">{record.source_account?.name}</div>
                    <div className="text-sm text-gray-500">
                        {record.formatted_source_amount}
                    </div>
                </div>
            ),
        },
        {
            title: 'To',
            key: 'destination',
            render: (_, record) => (
                <div>
                    <div className="font-medium">{record.destination_account?.name}</div>
                    <div className="text-sm text-gray-500">
                        {record.formatted_destination_amount}
                    </div>
                </div>
            ),
        },
        {
            title: 'Exchange Rate',
            dataIndex: 'formatted_exchange_rate',
            key: 'exchange_rate',
            sorter: true,
            render: (rate, record) => {
                const isSameCurrency =
                    record.source_account?.currency_code ===
                    record.destination_account?.currency_code;
                return isSameCurrency ? '—' : rate;
            },
        },
        {
            title: 'Description',
            dataIndex: 'description',
            key: 'description',
            ellipsis: true,
        },
    ];

    return (
        <AppLayout
            pageTitle="Transfers"
            breadcrumbs={[
                { title: 'Dashboard', href: '/dashboard' },
                { title: 'Transfers', href: '/dashboard/transfers' },
            ]}
        >
            <Card
                extra={
                    <Button type="primary" icon={<PlusOutlined />} href={create.url()}>
                        New Transfer
                    </Button>
                }
            >
                <DataTable<Transfer>
                    apiUrl="/dashboard/transfers/data"
                    columns={columns}
                    rowKey="id"
                    filters={filters}
                    onFiltersChange={setFilters}
                />
            </Card>
        </AppLayout>
    );
}
