import AppLayout from '@/layouts/app-layout';
import api from '@/lib/axios';
import { Client, Contact, LaravelPaginatedResponse } from '@/types';
import { DeleteOutlined, EditOutlined, MailOutlined, PhoneOutlined, PlusOutlined } from '@ant-design/icons';
import { Link } from '@inertiajs/react';
import { Button, Card, Modal, notification, Space, Table, Tabs, theme } from 'antd';
import { useEffect, useState } from 'react';
import ClientForm from './partials/client-form';

const { useToken } = theme;

interface EditClientProps {
    client: Client;
}

export default function EditClient({ client }: EditClientProps) {
    const [contacts, setContacts] = useState<Contact[]>([]);
    const [loadingContacts, setLoadingContacts] = useState(false);
    const [total, setTotal] = useState(0);
    const [currentPage, setCurrentPage] = useState(1);
    const [pageSize, setPageSize] = useState(15);
    const { token } = useToken();

    const fetchContacts = async (page: number, perPage: number) => {
        setLoadingContacts(true);
        try {
            const response = await api.get<LaravelPaginatedResponse<Contact>>(
                `/dashboard/contacts/data?client_id=${client.id}&page=${page}&per_page=${perPage}`,
            );
            setContacts(response.data.data);
            setTotal(response.data.meta.total);
        } catch {
            notification.error({
                message: 'Failed to load contacts',
            });
        } finally {
            setLoadingContacts(false);
        }
    };

    useEffect(() => {
        setCurrentPage(1);
        fetchContacts(1, pageSize);
    }, [client.id]);

    const handleDeleteContact = (contact: Contact) => {
        Modal.confirm({
            title: 'Delete Contact',
            content: `Are you sure you want to delete "${contact.full_name}"?`,
            okText: 'Delete',
            okType: 'danger',
            onOk: async () => {
                try {
                    await api.delete(`/dashboard/contacts/${contact.id}`);
                    notification.success({
                        message: 'Contact deleted successfully',
                    });
                    fetchContacts(currentPage, pageSize);
                } catch {
                    notification.error({
                        message: 'Failed to delete contact',
                    });
                }
            },
        });
    };

    const contactColumns = [
        {
            title: 'Name',
            dataIndex: 'full_name',
            key: 'full_name',
        },
        {
            title: 'Email',
            dataIndex: 'primary_email',
            key: 'primary_email',
            render: (email: string) => (
                <Space>
                    <MailOutlined style={{ color: token.colorTextSecondary }} />
                    {email}
                </Space>
            ),
        },
        {
            title: 'Phone',
            dataIndex: 'primary_phone',
            key: 'primary_phone',
            render: (phone: string) =>
                phone ? (
                    <Space>
                        <PhoneOutlined style={{ color: token.colorTextSecondary }} />
                        {phone}
                    </Space>
                ) : (
                    '—'
                ),
        },
        {
            title: 'Actions',
            key: 'actions',
            render: (_: unknown, record: Contact) => (
                <Space>
                    <Link href={`/dashboard/contacts/${record.id}/edit`}>
                        <Button type="link" icon={<EditOutlined />}>
                            Edit
                        </Button>
                    </Link>
                    <Button type="link" danger icon={<DeleteOutlined />} onClick={() => handleDeleteContact(record)}>
                        Delete
                    </Button>
                </Space>
            ),
        },
    ];

    const tabItems = [
        {
            key: '1',
            label: 'Client Details',
            children: <ClientForm client={client} isEdit />,
        },
        {
            key: '2',
            label: `Contacts (${total})`,
            children: (
                <div>
                    <div style={{ marginBottom: 16, display: 'flex', justifyContent: 'flex-end' }}>
                        <Link href={`/dashboard/contacts/create?client_id=${client.id}`}>
                            <Button type="primary" icon={<PlusOutlined />}>
                                Add Contact
                            </Button>
                        </Link>
                    </div>
                    <Table
                        dataSource={contacts}
                        columns={contactColumns}
                        rowKey="id"
                        loading={loadingContacts}
                        pagination={{
                            current: currentPage,
                            pageSize,
                            total,
                            showSizeChanger: true,
                            showTotal: (t, range) => `${range[0]}-${range[1]} of ${t} contacts`,
                        }}
                        onChange={(p) => {
                            const nextPage = p.current ?? 1;
                            const nextSize = p.pageSize ?? pageSize;
                            setCurrentPage(nextPage);
                            setPageSize(nextSize);
                            fetchContacts(nextPage, nextSize);
                        }}
                    />
                </div>
            ),
        },
    ];

    return (
        <AppLayout pageTitle={client ? `Edit Client: ${client.name}` : 'Edit Client'}>
            <Card>
                <Tabs defaultActiveKey="1" items={tabItems} />
            </Card>
        </AppLayout>
    );
}
