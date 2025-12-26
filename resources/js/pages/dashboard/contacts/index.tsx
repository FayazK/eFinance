import React from 'react';
import { Button, Space, Tag, Dropdown, theme, Modal, notification } from 'antd';
import {
    EditOutlined,
    DeleteOutlined,
    PlusOutlined,
    UserOutlined,
    MoreOutlined,
    MailOutlined,
    PhoneOutlined,
    TeamOutlined,
} from '@ant-design/icons';
import { Link } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import DataTable from '@/components/ui/DataTable';
import type { Contact, FilterConfig } from '@/types';
import api from '@/lib/axios';

const { useToken } = theme;

const filters: FilterConfig[] = [
    {
        type: 'dateRange',
        key: 'created_at',
        label: 'Created Date',
    },
];

export default function ContactsIndex() {
    const { token } = useToken();

    const handleDelete = (contact: Contact) => {
        Modal.confirm({
            title: 'Delete Contact',
            content: `Are you sure you want to delete "${contact.full_name}"? This action cannot be undone.`,
            okText: 'Delete',
            okType: 'danger',
            cancelText: 'Cancel',
            onOk: async () => {
                try {
                    await api.delete(`/dashboard/contacts/${contact.id}`);
                    notification.success({
                        message: 'Contact deleted successfully',
                    });
                    window.location.reload();
                } catch {
                    notification.error({
                        message: 'Failed to delete contact',
                        description: 'An error occurred while deleting the contact.',
                    });
                }
            },
        });
    };

    const columns = [
        {
            title: 'Contact',
            dataIndex: 'first_name',
            key: 'name',
            searchable: true,
            sorter: true,
            render: (_: unknown, record: Contact) => (
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
                        <UserOutlined style={{ color: token.colorPrimary }} />
                    </div>
                    <div>
                        <div style={{ fontWeight: 500 }}>{record.full_name}</div>
                        {record.client && (
                            <div style={{ color: token.colorTextSecondary, fontSize: '12px' }}>
                                <TeamOutlined style={{ marginRight: 4 }} />
                                {record.client.name}
                            </div>
                        )}
                    </div>
                </Space>
            ),
        },
        {
            title: 'Email & Phone',
            dataIndex: 'primary_email',
            key: 'contact',
            searchable: true,
            width: 240,
            render: (_: unknown, record: Contact) => (
                <Space direction="vertical" size={0}>
                    <Space size={4}>
                        <MailOutlined style={{ color: token.colorTextSecondary, fontSize: 12 }} />
                        <span style={{ fontSize: '13px' }}>{record.primary_email}</span>
                    </Space>
                    {record.primary_phone && (
                        <Space size={4}>
                            <PhoneOutlined style={{ color: token.colorTextSecondary, fontSize: 12 }} />
                            <span style={{ fontSize: '13px', color: token.colorTextSecondary }}>
                                {record.primary_phone}
                            </span>
                        </Space>
                    )}
                    {record.additional_emails && record.additional_emails.length > 0 && (
                        <Tag color="blue" style={{ fontSize: '11px', marginTop: 4 }}>
                            +{record.additional_emails.length} more email{record.additional_emails.length > 1 ? 's' : ''}
                        </Tag>
                    )}
                </Space>
            ),
        },
        {
            title: 'Location',
            dataIndex: 'city',
            key: 'location',
            width: 180,
            render: (_: unknown, record: Contact) => {
                const parts = [];
                if (record.city) parts.push(record.city);
                if (record.state) parts.push(record.state);
                if (record.country) parts.push(record.country.name);

                return parts.length > 0 ? (
                    <span style={{ fontSize: '13px' }}>{parts.join(', ')}</span>
                ) : (
                    <span style={{ color: token.colorTextDisabled }}>—</span>
                );
            },
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
            render: (_: unknown, record: Contact) => {
                const menuItems = [
                    {
                        key: 'edit',
                        label: (
                            <Link href={`/dashboard/contacts/${record.id}/edit`}>
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
            pageTitle="Contacts"
            actions={
                <Link href="/dashboard/contacts/create">
                    <Button type="primary" icon={<PlusOutlined />}>
                        Add Contact
                    </Button>
                </Link>
            }
        >
            <DataTable<Contact>
                fetchUrl="/dashboard/contacts/data"
                columns={columns}
                filters={filters}
                searchPlaceholder="Search contacts by name, email, or client..."
                defaultPageSize={15}
                emptyMessage="No contacts have been created yet."
                emptyFilterMessage="No contacts match your search criteria."
            />
        </AppLayout>
    );
}

ContactsIndex.layout = (page: React.ReactNode) => page;
