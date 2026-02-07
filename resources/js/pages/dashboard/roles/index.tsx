import { CanAccess } from '@/components/can-access';
import DataTable from '@/components/ui/DataTable';
import AppLayout from '@/layouts/app-layout';
import type { Role } from '@/types';
import { DeleteOutlined, EditOutlined, LockOutlined, MoreOutlined, PlusOutlined, TeamOutlined } from '@ant-design/icons';
import { Link, router } from '@inertiajs/react';
import { Button, Dropdown, Modal, notification, Space, Tag, theme } from 'antd';
import React from 'react';

const { useToken } = theme;

export default function RolesIndex() {
    const { token } = useToken();

    const handleDelete = (role: Role) => {
        if (role.is_super_admin) {
            notification.error({
                message: 'Cannot Delete',
                description: 'The super admin role cannot be deleted.',
            });
            return;
        }

        if (role.users_count && role.users_count > 0) {
            notification.error({
                message: 'Cannot Delete',
                description: 'Cannot delete a role that has users assigned. Please reassign users first.',
            });
            return;
        }

        Modal.confirm({
            title: 'Delete Role',
            content: `Are you sure you want to delete the role "${role.name}"? This action cannot be undone.`,
            okText: 'Delete',
            okButtonProps: { danger: true },
            onOk: async () => {
                try {
                    await router.delete(`/dashboard/roles/${role.id}`, {
                        preserveScroll: true,
                        onSuccess: () => {
                            notification.success({
                                message: 'Role deleted successfully',
                            });
                        },
                        onError: (errors) => {
                            notification.error({
                                message: 'Failed to delete role',
                                description: errors.role || 'An error occurred',
                            });
                        },
                    });
                } catch {
                    notification.error({
                        message: 'Failed to delete role',
                    });
                }
            },
        });
    };

    const columns = [
        {
            title: 'Role',
            dataIndex: 'name',
            key: 'name',
            searchable: true,
            sorter: true,
            render: (_: unknown, record: Role) => (
                <Space>
                    <div>
                        <div style={{ fontWeight: 500 }}>
                            {record.name}
                            {record.is_super_admin && (
                                <Tag color="gold" style={{ marginLeft: 8 }}>
                                    <LockOutlined /> Super Admin
                                </Tag>
                            )}
                            {record.is_default && (
                                <Tag color="blue" style={{ marginLeft: 8 }}>
                                    Default
                                </Tag>
                            )}
                        </div>
                        {record.description && (
                            <div style={{ color: token.colorTextSecondary, fontSize: '12px' }}>{record.description}</div>
                        )}
                    </div>
                </Space>
            ),
        },
        {
            title: 'Slug',
            dataIndex: 'slug',
            key: 'slug',
            width: 150,
            render: (slug: unknown) => <code style={{ fontSize: '12px' }}>{String(slug)}</code>,
        },
        {
            title: 'Permissions',
            dataIndex: 'permissions',
            key: 'permissions',
            width: 120,
            render: (_: unknown, record: Role) => (
                <Tag color={record.is_super_admin ? 'gold' : 'default'}>
                    {record.is_super_admin ? 'All' : record.permissions?.length || 0}
                </Tag>
            ),
        },
        {
            title: 'Users',
            dataIndex: 'users_count',
            key: 'users_count',
            width: 100,
            sorter: true,
            render: (count: unknown) => (
                <Space>
                    <TeamOutlined />
                    {(count as number) || 0}
                </Space>
            ),
        },
        {
            title: 'Created',
            dataIndex: 'created_at',
            key: 'created_at',
            width: 120,
            sorter: true,
            render: (createdAt: unknown) => new Date(createdAt as string).toLocaleDateString(),
        },
        {
            title: 'Actions',
            key: 'actions',
            width: 100,
            render: (_: unknown, record: Role) => {
                if (record.is_super_admin) {
                    return (
                        <Tag color="default">
                            <LockOutlined /> Protected
                        </Tag>
                    );
                }

                const menuItems = [
                    {
                        key: 'edit',
                        label: (
                            <CanAccess permission="roles.update">
                                <Space>
                                    <EditOutlined />
                                    Edit
                                </Space>
                            </CanAccess>
                        ),
                        onClick: () => router.visit(`/dashboard/roles/${record.id}/edit`),
                    },
                    {
                        type: 'divider' as const,
                    },
                    {
                        key: 'delete',
                        label: (
                            <CanAccess permission="roles.delete">
                                <Space>
                                    <DeleteOutlined />
                                    Delete
                                </Space>
                            </CanAccess>
                        ),
                        danger: true,
                        disabled: (record.users_count ?? 0) > 0,
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
            pageTitle="Roles"
            actions={
                <CanAccess permission="roles.create">
                    <Link href="/dashboard/roles/create">
                        <Button type="primary" icon={<PlusOutlined />}>
                            Add Role
                        </Button>
                    </Link>
                </CanAccess>
            }
        >
            <DataTable<Role>
                fetchUrl="/dashboard/roles/data"
                columns={columns}
                searchPlaceholder="Search roles by name or description..."
                defaultPageSize={15}
                emptyMessage="No roles have been created yet."
                emptyFilterMessage="No roles match your search criteria."
            />
        </AppLayout>
    );
}

RolesIndex.layout = (page: React.ReactNode) => page;
