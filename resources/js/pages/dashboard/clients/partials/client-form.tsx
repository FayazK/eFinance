import AdvancedSelect from '@/components/advanced-select';
import api from '@/lib/axios';
import { index } from '@/routes/clients';
import { Client } from '@/types';
import { router } from '@inertiajs/react';
import { Button, Col, Form, Input, notification, Row } from 'antd';
import { useEffect, useState } from 'react';

interface ClientFormProps {
    client?: Client;
    isEdit?: boolean;
}

export default function ClientForm({ client, isEdit = false }: ClientFormProps) {
    const [form] = Form.useForm();
    const [loading, setLoading] = useState(false);

    useEffect(() => {
        form.setFieldsValue({
            name: client?.name || '',
            email: client?.email || '',
            country_id: client?.country?.id,
            city_id: client?.city?.id,
            currency_id: client?.currency?.id,
            address: client?.address || '',
            phone: client?.phone || '',
            company: client?.company || '',
            tax_id: client?.tax_id || '',
            website: client?.website || '',
            notes: client?.notes || '',
        });
    }, [client, form]);

    const onFinish = async (values: Record<string, unknown>) => {
        setLoading(true);

        const method = isEdit ? 'put' : 'post';
        const url = isEdit ? `/dashboard/clients/${client!.id}` : '/dashboard/clients';

        try {
            const response = await api[method](url, values);
            notification.success({
                message: response.data.message || `Client ${isEdit ? 'updated' : 'created'} successfully`,
            });
            router.visit(index.url());
        } catch (error: unknown) {
            const err = error as { response?: { status: number; data: { errors: { [key: string]: string[] }; message: string } } };
            if (err.response && err.response.status === 422) {
                const validationErrors = err.response.data.errors;
                const formErrors = Object.keys(validationErrors).map((key) => ({
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
        <Form form={form} layout="vertical" onFinish={onFinish}>
            <Row gutter={16}>
                <Col span={12}>
                    <Form.Item label="Name" name="name" rules={[{ required: true, message: 'Please input the client name!' }]}>
                        <Input />
                    </Form.Item>
                </Col>
                <Col span={12}>
                    <Form.Item label="Email" name="email" rules={[{ required: true, type: 'email', message: 'Please input a valid email!' }]}>
                        <Input type="email" />
                    </Form.Item>
                </Col>
            </Row>

            <Row gutter={16}>
                <Col span={12}>
                    <Form.Item label="Company" name="company">
                        <Input />
                    </Form.Item>
                </Col>
                <Col span={12}>
                    <Form.Item label="Phone" name="phone">
                        <Input />
                    </Form.Item>
                </Col>
            </Row>

            <Row gutter={16}>
                <Col span={8}>
                    <Form.Item label="Country" name="country_id" rules={[{ required: true, message: 'Please select a country!' }]}>
                        <AdvancedSelect type="countries" id={client?.country?.id} />
                    </Form.Item>
                </Col>
                <Col span={8}>
                    <Form.Item label="City" name="city_id">
                        <AdvancedSelect type="cities" id={client?.city?.id} />
                    </Form.Item>
                </Col>
                <Col span={8}>
                    <Form.Item label="Currency" name="currency_id" rules={[{ required: true, message: 'Please select a currency!' }]}>
                        <AdvancedSelect type="currencies" id={client?.currency?.id} />
                    </Form.Item>
                </Col>
            </Row>

            <Form.Item label="Address" name="address">
                <Input.TextArea rows={2} />
            </Form.Item>

            <Row gutter={16}>
                <Col span={12}>
                    <Form.Item label="Tax ID" name="tax_id">
                        <Input />
                    </Form.Item>
                </Col>
                <Col span={12}>
                    <Form.Item label="Website" name="website">
                        <Input type="url" placeholder="https://" />
                    </Form.Item>
                </Col>
            </Row>

            <Form.Item label="Notes" name="notes">
                <Input.TextArea rows={4} />
            </Form.Item>

            <Form.Item>
                <Button type="primary" htmlType="submit" loading={loading}>
                    {isEdit ? 'Update Client' : 'Create Client'}
                </Button>
            </Form.Item>
        </Form>
    );
}
