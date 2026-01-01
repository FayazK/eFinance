import React from 'react';
import { Button, Space, Tag, Dropdown, Modal, notification, theme } from 'antd';
import {
    EditOutlined,
    DeleteOutlined,
    PlusOutlined,
    IdcardOutlined,
    MoreOutlined,
    MailOutlined,
} from '@ant-design/icons';
import { Link } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import DataTable from '@/components/ui/DataTable';
import type { Employee, FilterConfig } from '@/types';
import api from '@/lib/axios';

const { useToken } = theme;

const filters: FilterConfig[] = [
    {
        type: 'select',
        key: 'status',
        label: 'Status',
        options: [
            { label: 'Active', value: 'active' },
            { label: 'Terminated', value: 'terminated' },
        ],
    },
    {
        type: 'dateRange',
        key: 'created_at',
        label: 'Created Date',
    },
];

export default function EmployeesIndex() {
    const { token } = useToken();

    const handleDelete = (employee: Employee) => {
        Modal.confirm({
            title: 'Delete Employee',
            content: `Are you sure you want to delete "${employee.name}"? This action cannot be undone.`,
            okText: 'Delete',
            okType: 'danger',
            cancelText: 'Cancel',
            onOk: async () => {
                try {
                    await api.delete(`/dashboard/employees/${employee.id}`);
                    notification.success({
                        message: 'Employee deleted successfully',
                    });
                    window.location.reload();
                } catch {
                    notification.error({
                        message: 'Failed to delete employee',
                        description: 'An error occurred while deleting the employee.',
                    });
                }
            },
        });
    };

    const columns = [
        {
            title: 'Employee',
            dataIndex: 'name',
            key: 'name',
            searchable: true,
            sorter: true,
            render: (_: unknown, record: Employee) => (
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
                        <IdcardOutlined style={{ color: token.colorPrimary }} />
                    </div>
                    <div>
                        <div style={{ fontWeight: 500 }}>{record.name}</div>
                        <div style={{ fontSize: 12, color: token.colorTextSecondary }}>
                            {record.designation}
                        </div>
                    </div>
                </Space>
            ),
        },
        {
            title: 'Email',
            dataIndex: 'email',
            key: 'email',
            render: (email: string) => (
                <Space>
                    <MailOutlined style={{ color: token.colorTextSecondary }} />
                    <span>{email}</span>
                </Space>
            ),
        },
        {
            title: 'Salary',
            dataIndex: 'formatted_salary',
            key: 'base_salary_pkr',
            sorter: true,
            align: 'right' as const,
        },
        {
            title: 'Status',
            dataIndex: 'status',
            key: 'status',
            filterable: true,
            render: (status: string) => (
                <Tag color={status === 'active' ? 'success' : 'default'}>
                    {status.toUpperCase()}
                </Tag>
            ),
        },
        {
            title: 'Joining Date',
            dataIndex: 'joining_date',
            key: 'joining_date',
            sorter: true,
        },
        {
            title: 'Actions',
            key: 'actions',
            align: 'right' as const,
            width: 100,
            render: (_: unknown, record: Employee) => (
                <Dropdown
                    menu={{
                        items: [
                            {
                                key: 'edit',
                                label: (
                                    <Link href={`/dashboard/employees/${record.id}/edit`}>
                                        Edit Employee
                                    </Link>
                                ),
                                icon: <EditOutlined />,
                            },
                            {
                                key: 'delete',
                                label: 'Delete',
                                icon: <DeleteOutlined />,
                                danger: true,
                                onClick: () => handleDelete(record),
                            },
                        ],
                    }}
                    trigger={['click']}
                >
                    <Button type="text" icon={<MoreOutlined />} />
                </Dropdown>
            ),
        },
    ];

    return (
        <AppLayout
            breadcrumbs={[
                { title: 'Dashboard', href: '/dashboard' },
                { title: 'Employees', href: '/dashboard/employees' },
            ]}
            title="Employees"
            actions={
                <Link href="/dashboard/employees/create">
                    <Button type="primary" icon={<PlusOutlined />}>
                        Add Employee
                    </Button>
                </Link>
            }
        >
            <DataTable<Employee>
                columns={columns}
                dataUrl="/dashboard/employees/data"
                filters={filters}
                defaultSortBy="name"
                defaultSortDirection="asc"
            />
        </AppLayout>
    );
}
