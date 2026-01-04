import DataTable from '@/components/ui/DataTable';
import AppLayout from '@/layouts/app-layout';
import api from '@/lib/axios';
import { create, data, destroy, edit, show } from '@/routes/projects';
import type { FilterConfig, Project } from '@/types';
import {
    DeleteOutlined,
    EditOutlined,
    EyeOutlined,
    FileOutlined,
    MoreOutlined,
    PlusOutlined,
    ProjectOutlined,
    TeamOutlined,
} from '@ant-design/icons';
import { Link } from '@inertiajs/react';
import { Badge, Button, Dropdown, Modal, notification, Space, Tag, theme } from 'antd';
import React from 'react';

const { useToken } = theme;

const statusColors = {
    Planning: 'blue',
    Active: 'green',
    Completed: 'default',
    Cancelled: 'red',
};

const filters: FilterConfig[] = [
    {
        type: 'select',
        key: 'status',
        label: 'Status',
        options: [
            { label: 'Planning', value: 'Planning' },
            { label: 'Active', value: 'Active' },
            { label: 'Completed', value: 'Completed' },
            { label: 'Cancelled', value: 'Cancelled' },
        ],
    },
    {
        type: 'dateRange',
        key: 'created_at',
        label: 'Created Date',
    },
];

export default function ProjectsIndex() {
    const { token } = useToken();

    const handleDelete = (project: Project) => {
        Modal.confirm({
            title: 'Delete Project',
            content: `Are you sure you want to delete "${project.name}"? This action cannot be undone.`,
            okText: 'Delete',
            okType: 'danger',
            cancelText: 'Cancel',
            onOk: async () => {
                try {
                    await api.delete(destroy.url(project.id));
                    notification.success({
                        message: 'Project deleted successfully',
                    });
                    window.location.reload();
                } catch {
                    notification.error({
                        message: 'Failed to delete project',
                        description: 'An error occurred while deleting the project.',
                    });
                }
            },
        });
    };

    const columns = [
        {
            title: 'Project',
            dataIndex: 'name',
            key: 'name',
            searchable: true,
            sorter: true,
            render: (_: unknown, record: Project) => (
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
                        <ProjectOutlined style={{ color: token.colorPrimary }} />
                    </div>
                    <div>
                        <div style={{ fontWeight: 500 }}>{record.name}</div>
                        {record.description && (
                            <div style={{ color: token.colorTextSecondary, fontSize: '12px' }}>
                                {record.description.substring(0, 50)}
                                {record.description.length > 50 ? '...' : ''}
                            </div>
                        )}
                    </div>
                </Space>
            ),
        },
        {
            title: 'Client',
            dataIndex: 'client',
            key: 'client',
            width: 200,
            render: (_: unknown, record: Project) =>
                record.client ? (
                    <Space size={4}>
                        <TeamOutlined style={{ color: token.colorTextSecondary }} />
                        <span>{record.client.name}</span>
                    </Space>
                ) : (
                    <span style={{ color: token.colorTextDisabled }}>—</span>
                ),
        },
        {
            title: 'Status',
            dataIndex: 'status',
            key: 'status',
            width: 120,
            filterable: true,
            sorter: true,
            render: (status: string) => <Tag color={statusColors[status as keyof typeof statusColors]}>{status}</Tag>,
        },
        {
            title: 'Budget',
            dataIndex: 'budget',
            key: 'budget',
            width: 120,
            sorter: true,
            render: (budget: number | null, record: Project) =>
                budget ? (
                    <span>
                        {record.client?.currency?.symbol || '$'}
                        {budget.toLocaleString()}
                    </span>
                ) : (
                    <span style={{ color: token.colorTextDisabled }}>—</span>
                ),
        },
        {
            title: 'Documents',
            dataIndex: 'documents_count',
            key: 'documents_count',
            width: 100,
            render: (count: number) =>
                count > 0 ? (
                    <Badge count={count} showZero={false}>
                        <FileOutlined style={{ fontSize: 16 }} />
                    </Badge>
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
            render: (createdAt: unknown) => (createdAt ? new Date(createdAt as string).toLocaleDateString() : '—'),
        },
        {
            title: 'Actions',
            key: 'actions',
            width: 80,
            render: (_: unknown, record: Project) => {
                const menuItems = [
                    {
                        key: 'view',
                        label: (
                            <Link href={show.url(record.id)}>
                                <Space>
                                    <EyeOutlined />
                                    View
                                </Space>
                            </Link>
                        ),
                    },
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
            pageTitle="Projects"
            actions={
                <Link href={create.url()}>
                    <Button type="primary" icon={<PlusOutlined />}>
                        Add Project
                    </Button>
                </Link>
            }
        >
            <DataTable<Project>
                fetchUrl={data.url()}
                columns={columns}
                filters={filters}
                searchPlaceholder="Search projects by name or description..."
                defaultPageSize={15}
                emptyMessage="No projects have been created yet."
                emptyFilterMessage="No projects match your search criteria."
            />
        </AppLayout>
    );
}

ProjectsIndex.layout = (page: React.ReactNode) => page;
