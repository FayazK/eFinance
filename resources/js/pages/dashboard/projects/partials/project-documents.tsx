import { Media, Project } from '@/types';
import {
    DeleteOutlined,
    DownloadOutlined,
    FileExcelOutlined,
    FileOutlined,
    FilePdfOutlined,
    FileWordOutlined,
    UploadOutlined,
} from '@ant-design/icons';
import { router } from '@inertiajs/react';
import { Button, List, Modal, notification, Space, theme, Upload } from 'antd';
import type { RcFile, UploadProps } from 'antd/es/upload';
import { useState } from 'react';

interface ProjectDocumentsProps {
    project: Project;
}

const { useToken } = theme;

export default function ProjectDocuments({ project }: ProjectDocumentsProps) {
    const { token } = useToken();
    const [uploading, setUploading] = useState(false);

    const getFileIcon = (mimeType: string) => {
        if (mimeType.includes('pdf')) return <FilePdfOutlined style={{ fontSize: 24, color: '#ff4d4f' }} />;
        if (mimeType.includes('word')) return <FileWordOutlined style={{ fontSize: 24, color: '#1890ff' }} />;
        if (mimeType.includes('excel') || mimeType.includes('spreadsheet')) return <FileExcelOutlined style={{ fontSize: 24, color: '#52c41a' }} />;
        return <FileOutlined style={{ fontSize: 24 }} />;
    };

    const formatFileSize = (bytes: number): string => {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return Math.round((bytes / Math.pow(k, i)) * 100) / 100 + ' ' + sizes[i];
    };

    const beforeUpload = (file: RcFile): boolean => {
        const allowedTypes = [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ];

        if (!allowedTypes.includes(file.type)) {
            notification.error({ message: 'Only PDF, DOC, DOCX, XLS, and XLSX files are allowed!' });
            return false;
        }

        const isLt10M = file.size / 1024 / 1024 < 10;
        if (!isLt10M) {
            notification.error({ message: 'Document must be smaller than 10MB!' });
            return false;
        }

        return true;
    };

    const customRequest: UploadProps['customRequest'] = async (options) => {
        const { file, onSuccess, onError } = options;
        const formData = new FormData();
        formData.append('document', file as RcFile);

        setUploading(true);
        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            const response = await fetch(`/dashboard/projects/${project.id}/documents`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    Accept: 'application/json',
                },
            });

            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.message || 'Upload failed');
            }

            notification.success({ message: 'Document uploaded successfully!' });
            onSuccess?.({});
            router.reload({ only: ['project'] });
        } catch (error) {
            const errorMessage = error instanceof Error ? error.message : 'Failed to upload document';
            notification.error({ message: errorMessage });
            onError?.(error as Error);
        } finally {
            setUploading(false);
        }
    };

    const handleDelete = (document: Media) => {
        Modal.confirm({
            title: 'Delete Document',
            content: `Are you sure you want to delete "${document.file_name || document.name}"?`,
            okText: 'Delete',
            okType: 'danger',
            cancelText: 'Cancel',
            onOk: async () => {
                try {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                    const response = await fetch(`/dashboard/projects/${project.id}/documents/${document.id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            Accept: 'application/json',
                        },
                    });

                    if (!response.ok) throw new Error('Delete failed');

                    notification.success({ message: 'Document deleted successfully!' });
                    router.reload({ only: ['project'] });
                } catch {
                    notification.error({ message: 'Failed to delete document' });
                }
            },
        });
    };

    return (
        <Space direction="vertical" size="large" style={{ width: '100%' }}>
            <Upload showUploadList={false} beforeUpload={beforeUpload} customRequest={customRequest} accept=".pdf,.doc,.docx,.xls,.xlsx">
                <Button icon={<UploadOutlined />} loading={uploading}>
                    Upload Document
                </Button>
            </Upload>

            {project.documents && project.documents.length > 0 ? (
                <List
                    dataSource={project.documents}
                    renderItem={(doc: Media) => (
                        <List.Item
                            actions={[
                                <Button key="download" type="link" icon={<DownloadOutlined />} href={doc.url} target="_blank">
                                    Download
                                </Button>,
                                <Button key="delete" type="link" danger icon={<DeleteOutlined />} onClick={() => handleDelete(doc)}>
                                    Delete
                                </Button>,
                            ]}
                        >
                            <List.Item.Meta
                                avatar={getFileIcon(doc.mime_type)}
                                title={doc.file_name || doc.name}
                                description={
                                    <Space>
                                        <span>{formatFileSize(doc.size)}</span>
                                        <span style={{ color: token.colorTextSecondary }}>•</span>
                                        <span>{new Date(doc.created_at).toLocaleDateString()}</span>
                                    </Space>
                                }
                            />
                        </List.Item>
                    )}
                />
            ) : (
                <div style={{ textAlign: 'center', padding: '40px', color: token.colorTextSecondary }}>No documents uploaded yet.</div>
            )}
        </Space>
    );
}
