import DataTable from '@/components/ui/DataTable';
import AppLayout from '@/layouts/app-layout';
import api from '@/lib/axios';
import { create, data, destroy, edit } from '@/routes/companies';
import type { Company, FilterConfig } from '@/types';
import { BankOutlined, DeleteOutlined, EditOutlined, MailOutlined, MoreOutlined, PhoneOutlined, PlusOutlined } from '@ant-design/icons';
import { Link } from '@inertiajs/react';
import { Button, Dropdown, Modal, notification, Space, theme } from 'antd';
import React from 'react';

const { useToken } = theme;

const filters: FilterConfig[] = [
    {
        type: 'dateRange',
        key: 'created_at',
        label: 'Created Date',
    },
];

export default function CompaniesIndex() {
    const { token } = useToken();

    const handleDelete = (company: Company) => {
        Modal.confirm({
            title: 'Delete Company',
            content: `Are you sure you want to delete "${company.name}"? This action cannot be undone.`,
            okText: 'Delete',
            okType: 'danger',
            cancelText: 'Cancel',
            onOk: async () => {
                try {
                    await api.delete(destroy.url(company.id));
                    notification.success({
                        message: 'Company deleted successfully',
                    });
                    window.location.reload();
                } catch (error: unknown) {
                    const err = error as { response?: { data: { message: string } } };
                    notification.error({
                        message: 'Failed to delete company',
                        description: err.response?.data?.message || 'An error occurred while deleting the company.',
                    });
                }
            },
        });
    };

    const columns = [
        {
            title: 'Company',
            dataIndex: 'name',
            key: 'name',
            searchable: true,
            sorter: true,
            render: (_: unknown, record: Company) => (
                <Space>
                    {record.logo_url ? (
                        <img
                            src={record.logo_url}
                            alt={`${record.name} logo`}
                            style={{
                                width: 32,
                                height: 32,
                                objectFit: 'cover',
                                borderRadius: 4,
                                border: `1px solid ${token.colorBorder}`,
                            }}
                        />
                    ) : (
                        <div
                            style={{
                                width: 32,
                                height: 32,
                                borderRadius: 4,
                                backgroundColor: token.colorPrimaryBg,
                                display: 'flex',
                                alignItems: 'center',
                                justifyContent: 'center',
                                border: `1px solid ${token.colorBorder}`,
                            }}
                        >
                            <BankOutlined style={{ color: token.colorPrimary }} />
                        </div>
                    )}
                    <div>
                        <div style={{ fontWeight: 500 }}>{record.name}</div>
                    </div>
                </Space>
            ),
        },
        {
            title: 'Contact',
            key: 'contact',
            width: 220,
            render: (_: unknown, record: Company) => (
                <Space direction="vertical" size={0}>
                    {record.email && (
                        <Space size={4}>
                            <MailOutlined style={{ color: token.colorTextSecondary, fontSize: 12 }} />
                            <span style={{ fontSize: '13px' }}>{record.email}</span>
                        </Space>
                    )}
                    {record.phone && (
                        <Space size={4}>
                            <PhoneOutlined style={{ color: token.colorTextSecondary, fontSize: 12 }} />
                            <span style={{ fontSize: '13px', color: token.colorTextSecondary }}>{record.phone}</span>
                        </Space>
                    )}
                    {!record.email && !record.phone && <span style={{ color: token.colorTextDisabled }}>-</span>}
                </Space>
            ),
        },
        {
            title: 'Tax Info',
            key: 'tax_info',
            width: 180,
            render: (_: unknown, record: Company) => (
                <Space direction="vertical" size={0}>
                    {record.tax_id && <span style={{ fontSize: '13px' }}>Tax ID: {record.tax_id}</span>}
                    {record.vat_number && <span style={{ fontSize: '13px', color: token.colorTextSecondary }}>VAT: {record.vat_number}</span>}
                    {!record.tax_id && !record.vat_number && <span style={{ color: token.colorTextDisabled }}>-</span>}
                </Space>
            ),
        },
        {
            title: 'Created',
            dataIndex: 'created_at',
            key: 'created_at',
            width: 120,
            filterable: true,
            sorter: true,
            render: (createdAt: unknown) => (createdAt ? new Date(createdAt as string).toLocaleDateString() : '-'),
        },
        {
            title: 'Actions',
            key: 'actions',
            width: 80,
            render: (_: unknown, record: Company) => {
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
                    <Dropdown menu={{ items: menuItems }} trigger={['click']} placement="bottomRight">
                        <Button type="text" icon={<MoreOutlined />} />
                    </Dropdown>
                );
            },
        },
    ];

    return (
        <AppLayout
            pageTitle="Companies"
            actions={
                <Link href={create.url()}>
                    <Button type="primary" icon={<PlusOutlined />}>
                        Add Company
                    </Button>
                </Link>
            }
        >
            <DataTable<Company>
                fetchUrl={data.url()}
                columns={columns}
                filters={filters}
                searchPlaceholder="Search companies by name, email, or phone..."
                defaultPageSize={15}
                emptyMessage="No companies have been created yet."
                emptyFilterMessage="No companies match your search criteria."
            />
        </AppLayout>
    );
}

CompaniesIndex.layout = (page: React.ReactNode) => page;
