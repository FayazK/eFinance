import DataTable from '@/components/ui/DataTable';
import AppLayout from '@/layouts/app-layout';
import { create, data } from '@/routes/transfers';
import type { DataTableColumn } from '@/types';
import { Transfer } from '@/types';
import { PlusOutlined } from '@ant-design/icons';
import { Button, Card } from 'antd';

export default function TransfersIndex() {
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
                        {record.has_fee && <span className="ml-1 text-xs text-orange-600">(+{record.formatted_fee} fee)</span>}
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
                    <div className="text-sm text-gray-500">{record.formatted_destination_amount}</div>
                </div>
            ),
        },
        {
            title: 'Exchange Rate',
            dataIndex: 'formatted_exchange_rate',
            key: 'exchange_rate',
            sorter: true,
            render: (rate, record) => {
                const isSameCurrency = record.source_account?.currency_code === record.destination_account?.currency_code;
                return isSameCurrency ? '—' : rate;
            },
        },
        {
            title: 'Description',
            dataIndex: 'description',
            key: 'description',
            ellipsis: true,
            searchable: true,
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
                    fetchUrl={data.url()}
                    columns={columns}
                    searchPlaceholder="Search transfers..."
                    defaultPageSize={15}
                    emptyMessage="No transfers have been recorded yet."
                    emptyFilterMessage="No transfers match your search criteria."
                />
            </Card>
        </AppLayout>
    );
}
