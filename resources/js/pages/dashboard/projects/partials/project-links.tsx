import { Project, ProjectLink } from '@/types';
import { DeleteOutlined, EditOutlined, LinkOutlined, PlusOutlined } from '@ant-design/icons';
import { router } from '@inertiajs/react';
import { Button, Form, Input, List, Modal, notification, Space, theme } from 'antd';
import { useState } from 'react';

interface ProjectLinksProps {
    project: Project;
}

const { useToken } = theme;
const { TextArea } = Input;

export default function ProjectLinks({ project }: ProjectLinksProps) {
    const { token } = useToken();
    const [isModalOpen, setIsModalOpen] = useState(false);
    const [editingLink, setEditingLink] = useState<ProjectLink | null>(null);
    const [form] = Form.useForm();
    const [loading, setLoading] = useState(false);

    const handleAdd = () => {
        form.resetFields();
        setEditingLink(null);
        setIsModalOpen(true);
    };

    const handleEdit = (link: ProjectLink) => {
        form.setFieldsValue(link);
        setEditingLink(link);
        setIsModalOpen(true);
    };

    const handleSubmit = async (values: Record<string, unknown>) => {
        setLoading(true);
        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            const url = editingLink ? `/dashboard/projects/${project.id}/links/${editingLink.id}` : `/dashboard/projects/${project.id}/links`;
            const method = editingLink ? 'PUT' : 'POST';

            const response = await fetch(url, {
                method,
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                },
                body: JSON.stringify(values),
            });

            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.message || 'Operation failed');
            }

            notification.success({
                message: `Link ${editingLink ? 'updated' : 'created'} successfully!`,
            });
            setIsModalOpen(false);
            router.reload({ only: ['project'] });
        } catch (error) {
            notification.error({
                message: error instanceof Error ? error.message : 'An error occurred',
            });
        } finally {
            setLoading(false);
        }
    };

    const handleDelete = (link: ProjectLink) => {
        Modal.confirm({
            title: 'Delete Link',
            content: `Are you sure you want to delete "${link.title}"?`,
            okText: 'Delete',
            okType: 'danger',
            cancelText: 'Cancel',
            onOk: async () => {
                try {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                    const response = await fetch(`/dashboard/projects/${project.id}/links/${link.id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            Accept: 'application/json',
                        },
                    });

                    if (!response.ok) throw new Error('Delete failed');

                    notification.success({ message: 'Link deleted successfully!' });
                    router.reload({ only: ['project'] });
                } catch {
                    notification.error({ message: 'Failed to delete link' });
                }
            },
        });
    };

    return (
        <>
            <Space direction="vertical" size="large" style={{ width: '100%' }}>
                <Button type="primary" icon={<PlusOutlined />} onClick={handleAdd}>
                    Add Link
                </Button>

                {project.links && project.links.length > 0 ? (
                    <List
                        dataSource={project.links}
                        renderItem={(link: ProjectLink) => (
                            <List.Item
                                actions={[
                                    <Button key="edit" type="link" icon={<EditOutlined />} onClick={() => handleEdit(link)}>
                                        Edit
                                    </Button>,
                                    <Button key="delete" type="link" danger icon={<DeleteOutlined />} onClick={() => handleDelete(link)}>
                                        Delete
                                    </Button>,
                                ]}
                            >
                                <List.Item.Meta
                                    avatar={<LinkOutlined style={{ fontSize: 20, color: token.colorPrimary }} />}
                                    title={
                                        <a href={link.url} target="_blank" rel="noopener noreferrer">
                                            {link.title}
                                        </a>
                                    }
                                    description={
                                        <Space direction="vertical" size={0}>
                                            <span style={{ fontSize: '12px', color: token.colorTextSecondary }}>{link.url}</span>
                                            {link.description && <span style={{ fontSize: '13px' }}>{link.description}</span>}
                                        </Space>
                                    }
                                />
                            </List.Item>
                        )}
                    />
                ) : (
                    <div style={{ textAlign: 'center', padding: '40px', color: token.colorTextSecondary }}>No links added yet.</div>
                )}
            </Space>

            <Modal title={editingLink ? 'Edit Link' : 'Add Link'} open={isModalOpen} onCancel={() => setIsModalOpen(false)} footer={null}>
                <Form form={form} layout="vertical" onFinish={handleSubmit}>
                    <Form.Item label="Title" name="title" rules={[{ required: true, message: 'Please input the title!' }]}>
                        <Input />
                    </Form.Item>

                    <Form.Item
                        label="URL"
                        name="url"
                        rules={[
                            { required: true, message: 'Please input the URL!' },
                            { type: 'url', message: 'Please enter a valid URL!' },
                        ]}
                    >
                        <Input placeholder="https://example.com" />
                    </Form.Item>

                    <Form.Item label="Description" name="description">
                        <TextArea rows={3} />
                    </Form.Item>

                    <Form.Item>
                        <Space>
                            <Button type="primary" htmlType="submit" loading={loading}>
                                {editingLink ? 'Update' : 'Create'}
                            </Button>
                            <Button onClick={() => setIsModalOpen(false)}>Cancel</Button>
                        </Space>
                    </Form.Item>
                </Form>
            </Modal>
        </>
    );
}
