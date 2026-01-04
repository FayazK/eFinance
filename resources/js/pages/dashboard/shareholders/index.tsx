import DataTable from '@/components/ui/DataTable';
import AppLayout from '@/layouts/app-layout';
import api from '@/lib/axios';
import { data as shareholdersData } from '@/routes/shareholders';
import type { DataTableColumn, FilterConfig, Shareholder } from '@/types';
import { BankOutlined, DeleteOutlined, EditOutlined, PlusOutlined, TeamOutlined } from '@ant-design/icons';
import { Alert, Button, Popconfirm, Space, Tag, notification } from 'antd';
import { useEffect, useState } from 'react';
import ShareholderForm from './partials/shareholder-form';

const filters: FilterConfig[] = [
    {
        type: 'boolean',
        key: 'is_active',
        label: 'Status',
        trueLabel: 'Active',
        falseLabel: 'Inactive',
    },
    {
        type: 'boolean',
        key: 'is_office_reserve',
        label: 'Type',
        trueLabel: 'Office Reserve',
        falseLabel: 'Human Partner',
    },
];

export default function ShareholdersIndex() {
    const [modalVisible, setModalVisible] = useState(false);
    const [selectedShareholder, setSelectedShareholder] = useState<Shareholder | null>(null);
    const [tableKey, setTableKey] = useState(0);
    const [equityValidation, setEquityValidation] = useState<{
        total: number;
        is_valid: boolean;
        message: string;
    } | null>(null);

    const fetchEquityValidation = async () => {
        try {
            const response = await api.get('/dashboard/shareholders/validate-equity');
            setEquityValidation(response.data);
        } catch (error) {
            console.error('Failed to fetch equity validation:', error);
        }
    };

    useEffect(() => {
        fetchEquityValidation();
    }, [tableKey]);

    const handleCreate = () => {
        setSelectedShareholder(null);
        setModalVisible(true);
    };

    const handleEdit = (shareholder: Shareholder) => {
        setSelectedShareholder(shareholder);
        setModalVisible(true);
    };

    const handleDelete = async (id: number) => {
        try {
            await api.delete(`/dashboard/shareholders/${id}`);
            notification.success({
                message: 'Shareholder deleted successfully',
            });
            setTableKey((prev) => prev + 1);
        } catch (error: unknown) {
            const errorMessage =
                typeof error === 'object' && error !== null && 'response' in error
                    ? (error as { response?: { data?: { message?: string } } }).response?.data?.message
                    : 'An error occurred';

            notification.error({
                message: 'Failed to delete shareholder',
                description: errorMessage,
            });
        }
    };

    const handleFormSuccess = () => {
        setModalVisible(false);
        setSelectedShareholder(null);
        setTableKey((prev) => prev + 1);
    };

    const columns: DataTableColumn<Shareholder>[] = [
        {
            title: 'Name',
            dataIndex: 'name',
            key: 'name',
            sorter: true,
            render: (text: string, record: Shareholder) => (
                <Space>
                    {record.is_office_reserve ? <BankOutlined style={{ color: '#1890ff' }} /> : <TeamOutlined />}
                    {text}
                </Space>
            ),
        },
        {
            title: 'Email',
            dataIndex: 'email',
            key: 'email',
            render: (text: string) => text || '-',
        },
        {
            title: 'Equity %',
            dataIndex: 'formatted_equity',
            key: 'equity_percentage',
            sorter: true,
            align: 'right',
        },
        {
            title: 'Type',
            dataIndex: 'is_office_reserve',
            key: 'is_office_reserve',
            render: (isOffice: boolean) => (isOffice ? <Tag color="blue">Office Reserve</Tag> : <Tag color="green">Human Partner</Tag>),
        },
        {
            title: 'Status',
            dataIndex: 'is_active',
            key: 'is_active',
            render: (isActive: boolean) => (isActive ? <Tag color="success">Active</Tag> : <Tag color="default">Inactive</Tag>),
        },
        {
            title: 'Actions',
            key: 'actions',
            align: 'right',
            render: (_: unknown, record: Shareholder) => (
                <Space>
                    <Button type="text" size="small" icon={<EditOutlined />} onClick={() => handleEdit(record)}>
                        Edit
                    </Button>
                    <Popconfirm
                        title="Delete Shareholder"
                        description="Are you sure you want to delete this shareholder?"
                        onConfirm={() => handleDelete(record.id)}
                        okText="Yes"
                        cancelText="No"
                    >
                        <Button type="text" size="small" danger icon={<DeleteOutlined />}>
                            Delete
                        </Button>
                    </Popconfirm>
                </Space>
            ),
        },
    ];

    return (
        <AppLayout
            title="Shareholders"
            actions={
                <Button type="primary" icon={<PlusOutlined />} onClick={handleCreate}>
                    Create Shareholder
                </Button>
            }
        >
            <Space direction="vertical" size="middle" style={{ width: '100%' }}>
                {equityValidation && !equityValidation.is_valid && (
                    <Alert message="Equity Validation" description={equityValidation.message} type="error" showIcon />
                )}
                {equityValidation && equityValidation.is_valid && (
                    <Alert message="Equity Validation" description={equityValidation.message} type="success" showIcon />
                )}

                <DataTable<Shareholder> key={tableKey} columns={columns} fetchUrl={shareholdersData.url()} filters={filters} />
            </Space>

            <ShareholderForm
                visible={modalVisible}
                shareholder={selectedShareholder}
                onCancel={() => {
                    setModalVisible(false);
                    setSelectedShareholder(null);
                }}
                onSuccess={handleFormSuccess}
                currentEquityTotal={equityValidation?.total || 0}
            />
        </AppLayout>
    );
}
