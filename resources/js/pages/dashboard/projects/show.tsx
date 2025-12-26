import React, { useState } from 'react';
import { Card, Tabs, Descriptions, Tag, Button, Space } from 'antd';
import { EditOutlined, ArrowLeftOutlined } from '@ant-design/icons';
import { Link } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { Project } from '@/types';
import ProjectDocuments from './partials/project-documents';
import ProjectLinks from './partials/project-links';
import { edit, index } from '@/routes/projects';

interface ProjectShowProps {
    project: Project;
}

const statusColors = {
    Planning: 'blue',
    Active: 'green',
    Completed: 'default',
    Cancelled: 'red',
};

export default function ProjectShow({ project }: ProjectShowProps) {
    const [activeTab, setActiveTab] = useState('details');

    const tabItems = [
        {
            key: 'details',
            label: 'Project Details',
            children: (
                <Descriptions bordered column={2}>
                    <Descriptions.Item label="Project Name" span={2}>
                        {project.name}
                    </Descriptions.Item>
                    <Descriptions.Item label="Description" span={2}>
                        {project.description || '—'}
                    </Descriptions.Item>
                    <Descriptions.Item label="Client">
                        {project.client?.name || '—'}
                    </Descriptions.Item>
                    <Descriptions.Item label="Status">
                        <Tag color={statusColors[project.status]}>{project.status}</Tag>
                    </Descriptions.Item>
                    <Descriptions.Item label="Start Date">
                        {project.start_date || '—'}
                    </Descriptions.Item>
                    <Descriptions.Item label="Completion Date">
                        {project.completion_date || '—'}
                    </Descriptions.Item>
                    <Descriptions.Item label="Budget">
                        {project.budget
                            ? `${project.client?.currency?.symbol || '$'}${Number(project.budget).toLocaleString()}`
                            : '—'}
                    </Descriptions.Item>
                    <Descriptions.Item label="Actual Cost">
                        {project.actual_cost
                            ? `${project.client?.currency?.symbol || '$'}${Number(project.actual_cost).toLocaleString()}`
                            : '—'}
                    </Descriptions.Item>
                    <Descriptions.Item label="Created At">
                        {new Date(project.created_at).toLocaleString()}
                    </Descriptions.Item>
                    <Descriptions.Item label="Last Updated">
                        {new Date(project.updated_at).toLocaleString()}
                    </Descriptions.Item>
                </Descriptions>
            ),
        },
        {
            key: 'documents',
            label: 'Documents',
            children: <ProjectDocuments project={project} />,
        },
        {
            key: 'links',
            label: 'Reference Links',
            children: <ProjectLinks project={project} />,
        },
    ];

    return (
        <AppLayout
            pageTitle={project.name}
            actions={
                <Space>
                    <Link href={index.url()}>
                        <Button icon={<ArrowLeftOutlined />}>Back to Projects</Button>
                    </Link>
                    <Link href={edit.url(project.id)}>
                        <Button type="primary" icon={<EditOutlined />}>
                            Edit Project
                        </Button>
                    </Link>
                </Space>
            }
        >
            <Card>
                <Tabs activeKey={activeTab} onChange={setActiveTab} items={tabItems} />
            </Card>
        </AppLayout>
    );
}
