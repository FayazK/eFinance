import AdvancedSelect from '@/components/advanced-select';
import api from '@/lib/axios';
import { Contact } from '@/types';
import { MinusCircleOutlined, PlusOutlined } from '@ant-design/icons';
import { router } from '@inertiajs/react';
import { Button, Card, Col, Form, Input, notification, Row, Space } from 'antd';
import { useEffect, useRef, useState } from 'react';

interface ContactFormProps {
    contact?: Contact;
    isEdit?: boolean;
}

export default function ContactForm({ contact, isEdit = false }: ContactFormProps) {
    const [form] = Form.useForm();
    const [loading, setLoading] = useState(false);

    // Watch country_id and state_id for dependent dropdowns
    // Use contact values as fallback for initial render before form is populated
    const formCountryId = Form.useWatch('country_id', form);
    const formStateId = Form.useWatch('state_id', form);
    const countryId = formCountryId ?? contact?.country?.id;
    const stateId = formStateId ?? contact?.state?.id;

    // Track previous values to avoid resetting on initial load
    const prevCountryId = useRef<number | undefined>(contact?.country?.id);
    const prevStateId = useRef<number | undefined>(contact?.state?.id);
    const isInitialized = useRef(false);

    useEffect(() => {
        // Check for client_id in URL query params (for pre-filling from Client page)
        const searchParams = new URLSearchParams(window.location.search);
        const clientIdFromUrl = searchParams.get('client_id');

        form.setFieldsValue({
            first_name: contact?.first_name || '',
            last_name: contact?.last_name || '',
            client_id: contact?.client?.id || (clientIdFromUrl ? parseInt(clientIdFromUrl) : undefined),
            address: contact?.address || '',
            country_id: contact?.country?.id,
            state_id: contact?.state?.id,
            city_id: contact?.city?.id,
            primary_phone: contact?.primary_phone || '',
            primary_email: contact?.primary_email || '',
            additional_phones: contact?.additional_phones || [],
            additional_emails: contact?.additional_emails || [],
        });
        isInitialized.current = true;
    }, [contact, form]);

    // Reset state and city when country changes (only after initialization)
    useEffect(() => {
        if (isInitialized.current && prevCountryId.current !== countryId) {
            form.setFieldsValue({ state_id: undefined, city_id: undefined });
        }
        prevCountryId.current = countryId;
    }, [countryId, form]);

    // Reset city when state changes (only after initialization)
    useEffect(() => {
        if (isInitialized.current && prevStateId.current !== stateId) {
            form.setFieldsValue({ city_id: undefined });
        }
        prevStateId.current = stateId;
    }, [stateId, form]);

    const onFinish = async (values: Record<string, unknown>) => {
        setLoading(true);

        const method = isEdit ? 'put' : 'post';
        const url = isEdit ? `/dashboard/contacts/${contact!.id}` : '/dashboard/contacts';

        try {
            const response = await api[method](url, values);
            notification.success({
                message: response.data.message || `Contact ${isEdit ? 'updated' : 'created'} successfully`,
            });
            router.visit('/dashboard/contacts');
        } catch (error: unknown) {
            const err = error as {
                response?: {
                    status: number;
                    data: { errors: { [key: string]: string[] }; message: string };
                };
            };
            if (err.response && err.response.status === 422) {
                const validationErrors = err.response.data.errors;
                const formErrors = Object.keys(validationErrors).map((key) => ({
                    name: key.split('.'), // Converts 'additional_emails.0' to ['additional_emails', 0]
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
            <Card title="Basic Information" style={{ marginBottom: 16 }}>
                <Row gutter={16}>
                    <Col span={12}>
                        <Form.Item label="First Name" name="first_name" rules={[{ required: true, message: 'Please input the first name!' }]}>
                            <Input />
                        </Form.Item>
                    </Col>
                    <Col span={12}>
                        <Form.Item label="Last Name" name="last_name" rules={[{ required: true, message: 'Please input the last name!' }]}>
                            <Input />
                        </Form.Item>
                    </Col>
                </Row>

                <Form.Item label="Client" name="client_id" rules={[{ required: true, message: 'Please select a client!' }]}>
                    <AdvancedSelect type="clients" id={contact?.client?.id} />
                </Form.Item>
            </Card>

            <Card title="Primary Contact Information" style={{ marginBottom: 16 }}>
                <Row gutter={16}>
                    <Col span={12}>
                        <Form.Item
                            label="Primary Email"
                            name="primary_email"
                            rules={[{ required: true, type: 'email', message: 'Please input a valid email!' }]}
                        >
                            <Input type="email" />
                        </Form.Item>
                    </Col>
                    <Col span={12}>
                        <Form.Item label="Primary Phone" name="primary_phone">
                            <Input />
                        </Form.Item>
                    </Col>
                </Row>
            </Card>

            <Card title="Additional Contact Methods" style={{ marginBottom: 16 }}>
                <Form.List name="additional_emails">
                    {(fields, { add, remove }) => (
                        <>
                            <div style={{ marginBottom: 8, fontWeight: 500 }}>Additional Emails</div>
                            {fields.map((field) => (
                                <Space key={field.key} style={{ display: 'flex', marginBottom: 8 }} align="baseline">
                                    <Form.Item
                                        {...field}
                                        rules={[{ type: 'email', message: 'Please enter a valid email!' }]}
                                        style={{ marginBottom: 0, flex: 1 }}
                                    >
                                        <Input placeholder="email@example.com" style={{ width: 300 }} />
                                    </Form.Item>
                                    <MinusCircleOutlined onClick={() => remove(field.name)} />
                                </Space>
                            ))}
                            <Form.Item>
                                <Button type="dashed" onClick={() => add()} block icon={<PlusOutlined />}>
                                    Add Email
                                </Button>
                            </Form.Item>
                        </>
                    )}
                </Form.List>

                <Form.List name="additional_phones">
                    {(fields, { add, remove }) => (
                        <>
                            <div style={{ marginBottom: 8, fontWeight: 500 }}>Additional Phone Numbers</div>
                            {fields.map((field) => (
                                <Space key={field.key} style={{ display: 'flex', marginBottom: 8 }} align="baseline">
                                    <Form.Item {...field} style={{ marginBottom: 0, flex: 1 }}>
                                        <Input placeholder="+1234567890" style={{ width: 300 }} />
                                    </Form.Item>
                                    <MinusCircleOutlined onClick={() => remove(field.name)} />
                                </Space>
                            ))}
                            <Form.Item>
                                <Button type="dashed" onClick={() => add()} block icon={<PlusOutlined />}>
                                    Add Phone Number
                                </Button>
                            </Form.Item>
                        </>
                    )}
                </Form.List>
            </Card>

            <Card title="Address Information" style={{ marginBottom: 16 }}>
                <Form.Item label="Address" name="address">
                    <Input.TextArea rows={2} />
                </Form.Item>

                <Row gutter={16}>
                    <Col span={8}>
                        <Form.Item label="Country" name="country_id">
                            <AdvancedSelect type="countries" id={contact?.country?.id} />
                        </Form.Item>
                    </Col>
                    <Col span={8}>
                        <Form.Item label="State" name="state_id">
                            <AdvancedSelect
                                type="states"
                                id={contact?.state?.id}
                                params={{ country_id: countryId }}
                                disabled={!countryId}
                            />
                        </Form.Item>
                    </Col>
                    <Col span={8}>
                        <Form.Item label="City" name="city_id">
                            <AdvancedSelect
                                type="cities"
                                id={contact?.city?.id}
                                params={{ state_id: stateId }}
                                disabled={!stateId}
                            />
                        </Form.Item>
                    </Col>
                </Row>
            </Card>

            <Form.Item>
                <Button type="primary" htmlType="submit" loading={loading}>
                    {isEdit ? 'Update Contact' : 'Create Contact'}
                </Button>
            </Form.Item>
        </Form>
    );
}
