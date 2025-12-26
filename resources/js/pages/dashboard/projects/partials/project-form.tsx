import { Form, Input, Button, Row, Col, notification, Select, DatePicker } from 'antd';
import { Project } from '@/types';
import { router } from '@inertiajs/react';
import api from '@/lib/axios';
import { useEffect, useState } from 'react';
import AdvancedSelect from '@/components/advanced-select';
import { index } from '@/routes/projects';
import dayjs from 'dayjs';

interface ProjectFormProps {
    project?: Project;
    isEdit?: boolean;
}

const { TextArea } = Input;
const { Option } = Select;

export default function ProjectForm({ project, isEdit = false }: ProjectFormProps) {
    const [form] = Form.useForm();
    const [loading, setLoading] = useState(false);

    useEffect(() => {
        form.setFieldsValue({
            name: project?.name || '',
            description: project?.description || '',
            client_id: project?.client?.id,
            start_date: project?.start_date ? dayjs(project.start_date) : null,
            completion_date: project?.completion_date ? dayjs(project.completion_date) : null,
            status: project?.status || 'Planning',
            budget: project?.budget || null,
            actual_cost: project?.actual_cost || null,
        });
    }, [project, form]);

    const onFinish = async (values: Record<string, unknown>) => {
        setLoading(true);

        // Convert dayjs to string
        const submitData = {
            ...values,
            start_date: values.start_date ? dayjs(values.start_date as dayjs.Dayjs).format('YYYY-MM-DD') : null,
            completion_date: values.completion_date ? dayjs(values.completion_date as dayjs.Dayjs).format('YYYY-MM-DD') : null,
        };

        const method = isEdit ? 'put' : 'post';
        const url = isEdit ? `/dashboard/projects/${project!.id}` : '/dashboard/projects';

        try {
            const response = await api[method](url, submitData);
            notification.success({
                message: response.data.message || `Project ${isEdit ? 'updated' : 'created'} successfully`,
            });
            router.visit(index.url());
        } catch (error: unknown) {
            const err = error as { response?: { status: number; data: { errors: { [key: string]: string[] }; message: string; }; }; };
            if (err.response && err.response.status === 422) {
                const validationErrors = err.response.data.errors;
                const formErrors = Object.keys(validationErrors).map(key => ({
                    name: key,
                    errors: validationErrors[key],
                }));
                form.setFields(formErrors);
                notification.error({
                    message: 'Validation Error',
                    description: err.response.data.message,
                });
            } else {
                notification.error({
                    message: 'Error',
                    description: 'An unexpected error occurred.',
                });
            }
        } finally {
            setLoading(false);
        }
    };

    return (
        <Form
            form={form}
            layout="vertical"
            onFinish={onFinish}
        >
            <Row gutter={16}>
                <Col span={12}>
                    <Form.Item
                        label="Project Name"
                        name="name"
                        rules={[{ required: true, message: 'Please input the project name!' }]}
                    >
                        <Input />
                    </Form.Item>
                </Col>
                <Col span={12}>
                    <Form.Item
                        label="Client"
                        name="client_id"
                        rules={[{ required: true, message: 'Please select a client!' }]}
                    >
                        <AdvancedSelect type="clients" id={project?.client?.id} />
                    </Form.Item>
                </Col>
            </Row>

            <Form.Item
                label="Description"
                name="description"
            >
                <TextArea rows={3} />
            </Form.Item>

            <Row gutter={16}>
                <Col span={8}>
                    <Form.Item
                        label="Start Date"
                        name="start_date"
                    >
                        <DatePicker style={{ width: '100%' }} />
                    </Form.Item>
                </Col>
                <Col span={8}>
                    <Form.Item
                        label="Completion Date"
                        name="completion_date"
                    >
                        <DatePicker style={{ width: '100%' }} />
                    </Form.Item>
                </Col>
                <Col span={8}>
                    <Form.Item
                        label="Status"
                        name="status"
                        rules={[{ required: true, message: 'Please select a status!' }]}
                    >
                        <Select>
                            <Option value="Planning">Planning</Option>
                            <Option value="Active">Active</Option>
                            <Option value="Completed">Completed</Option>
                            <Option value="Cancelled">Cancelled</Option>
                        </Select>
                    </Form.Item>
                </Col>
            </Row>

            <Row gutter={16}>
                <Col span={12}>
                    <Form.Item
                        label="Budget"
                        name="budget"
                    >
                        <Input type="number" min={0} step={0.01} />
                    </Form.Item>
                </Col>
                <Col span={12}>
                    <Form.Item
                        label="Actual Cost"
                        name="actual_cost"
                    >
                        <Input type="number" min={0} step={0.01} />
                    </Form.Item>
                </Col>
            </Row>

            <Form.Item>
                <Button type="primary" htmlType="submit" loading={loading}>
                    {isEdit ? 'Update Project' : 'Create Project'}
                </Button>
            </Form.Item>
        </Form>
    );
}
