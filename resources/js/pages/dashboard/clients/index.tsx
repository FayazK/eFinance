import React from 'react';
import { Button, Space, Tag, Dropdown, theme, Modal, notification } from 'antd';
import {
    EditOutlined,
    DeleteOutlined,
    PlusOutlined,
    TeamOutlined,
    MoreOutlined,
    GlobalOutlined,
    MailOutlined,
    PhoneOutlined,
} from '@ant-design/icons';
import { Link } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import DataTable from '@/components/ui/DataTable';
import type { Client, FilterConfig } from '@/types';
import { data, create, edit, destroy } from '@/routes/clients';
import api from '@/lib/axios';

const { useToken } = theme;

// Filter configurations for the DataTable
const filters: FilterConfig[] = [
    {
        type: 'dateRange',
        key: 'created_at',
        label: 'Created Date',
    },
];

export default function ClientsIndex() {
    const { token } = useToken();

    const handleDelete = (client: Client) => {
        Modal.confirm({
            title: 'Delete Client',
            content: `Are you sure you want to delete "${client.name}"? This action cannot be undone.`,
            okText: 'Delete',
            okType: 'danger',
            cancelText: 'Cancel',
            onOk: async () => {
                try {
                    await api.delete(destroy.url(client.id));
                    notification.success({
                        message: 'Client deleted successfully',
                    });
                    // Reload the page to refresh the table
                    window.location.reload();
                } catch {
                    notification.error({
                        message: 'Failed to delete client',
                        description: 'An error occurred while deleting the client.',
                    });
                }
            },
        });
    };

    const columns = [
        {
            title: 'Client',
            dataIndex: 'name',
            key: 'name',
            searchable: true,
            sorter: true,
            render: (_: unknown, record: Client) => (
                <Space>
                    <div
                        style={{
                            width: 32,
                            height: 32,
                            borderRadius: '50%',
                            backgroundColor: token.colorPrimaryBg,
                            display: 'flex',
                            alignItems: 'center',
                            justifyContent: 'center',
                        }}
                    >
                        <TeamOutlined style={{ color: token.colorPrimary }} />
                    </div>
                    <div>
                        <div style={{ fontWeight: 500 }}>{record.name}</div>
                        {record.company && (
                            <div style={{ color: token.colorTextSecondary, fontSize: '12px' }}>
                                {record.company}
                            </div>
                        )}
                    </div>
                </Space>
            ),
        },
        {
            title: 'Contact',
            dataIndex: 'email',
            key: 'contact',
            searchable: true,
            width: 220,
            render: (_: unknown, record: Client) => (
                <Space direction="vertical" size={0}>
                    <Space size={4}>
                        <MailOutlined style={{ color: token.colorTextSecondary, fontSize: 12 }} />
                        <span style={{ fontSize: '13px' }}>{record.email}</span>
                    </Space>
                    {record.phone && (
                        <Space size={4}>
                            <PhoneOutlined style={{ color: token.colorTextSecondary, fontSize: 12 }} />
                            <span style={{ fontSize: '13px', color: token.colorTextSecondary }}>
                                {record.phone}
                            </span>
                        </Space>
                    )}
                </Space>
            ),
        },
        {
            title: 'Country',
            dataIndex: 'country',
            key: 'country',
            width: 150,
            sorter: true,
            render: (_: unknown, record: Client) =>
                record.country ? (
                    <Space size={4}>
                        <GlobalOutlined style={{ color: token.colorTextSecondary }} />
                        <span>{record.country.name}</span>
                    </Space>
                ) : (
                    <span style={{ color: token.colorTextDisabled }}>—</span>
                ),
        },
        {
            title: 'Currency',
            dataIndex: 'currency',
            key: 'currency',
            width: 100,
            render: (_: unknown, record: Client) =>
                record.currency ? (
                    <Tag color="blue">{record.currency.code}</Tag>
                ) : (
                    <span style={{ color: token.colorTextDisabled }}>—</span>
                ),
        },
        {
            title: 'Created',
            dataIndex: 'created_at',
            key: 'created_at',
            width: 120,
            filterable: true,
            sorter: true,
            render: (createdAt: unknown) =>
                createdAt ? new Date(createdAt as string).toLocaleDateString() : '—',
        },
        {
            title: 'Actions',
            key: 'actions',
            width: 80,
            render: (_: unknown, record: Client) => {
                const menuItems = [
                    {
                        key: 'edit',
                        label: (
                            <Link href={edit.url(record.id)}>
                                <Space>
                                    <EditOutlined />
                                    Edit
                                </Space>
                            </Link>
                        ),
                    },
                    {
                        type: 'divider' as const,
                    },
                    {
                        key: 'delete',
                        label: (
                            <Space>
                                <DeleteOutlined />
                                Delete
                            </Space>
                        ),
                        danger: true,
                        onClick: () => handleDelete(record),
                    },
                ];

                return (
                    <Dropdown
                        menu={{ items: menuItems }}
                        trigger={['click']}
                        placement="bottomRight"
                    >
                        <Button type="text" icon={<MoreOutlined />} />
                    </Dropdown>
                );
            },
        },
    ];

    return (
        <AppLayout
            pageTitle="Clients"
            actions={
                <Link href={create.url()}>
                    <Button type="primary" icon={<PlusOutlined />}>
                        Add Client
                    </Button>
                </Link>
            }
        >
            <DataTable<Client>
                fetchUrl={data.url()}
                columns={columns}
                filters={filters}
                searchPlaceholder="Search clients by name, email, or company..."
                defaultPageSize={15}
                emptyMessage="No clients have been created yet."
                emptyFilterMessage="No clients match your search criteria."
            />
        </AppLayout>
    );
}

ClientsIndex.layout = (page: React.ReactNode) => page;
