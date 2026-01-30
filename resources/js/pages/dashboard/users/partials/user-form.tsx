import AdvancedSelect from '@/components/advanced-select';
import { CanAccess } from '@/components/can-access';
import api from '@/lib/axios';
import { Role, User } from '@/types';
import { router } from '@inertiajs/react';
import { Button, Col, DatePicker, Form, Input, notification, Row, Select, Switch } from 'antd';
import dayjs, { Dayjs } from 'dayjs';
import { useEffect, useState } from 'react';

interface UserFormValues {
    first_name: string;
    last_name: string;
    email: string;
    password?: string;
    password_confirmation?: string;
    phone?: string;
    date_of_birth?: Dayjs | null;
    bio?: string;
    timezone_id?: number;
    language_id?: number;
    role_id?: number;
    is_active: boolean;
}

interface UserFormProps {
    user?: User;
    roles?: Role[];
    isEdit?: boolean;
}

export default function UserForm({ user, roles = [], isEdit = false }: UserFormProps) {
    const [form] = Form.useForm<UserFormValues>();
    const [loading, setLoading] = useState(false);

    useEffect(() => {
        form.setFieldsValue({
            first_name: user?.first_name || '',
            last_name: user?.last_name || '',
            email: user?.email || '',
            phone: user?.phone || '',
            date_of_birth: user?.date_of_birth ? dayjs(user.date_of_birth) : null,
            bio: user?.bio || '',
            timezone_id: typeof user?.timezone_id === 'number' ? user.timezone_id : undefined,
            language_id: typeof user?.language_id === 'number' ? user.language_id : undefined,
            role_id: user?.role_id,
            is_active: user?.is_active ?? true,
        });
    }, [user, form]);

    const onFinish = async (values: UserFormValues) => {
        setLoading(true);
        const requestData = {
            ...values,
            date_of_birth: values.date_of_birth ? values.date_of_birth.format('YYYY-MM-DD') : null,
        };

        const method = isEdit ? 'put' : 'post';
        const url = isEdit ? `/dashboard/users/${user!.id}` : '/dashboard/users';

        try {
            const response = await api[method](url, requestData);
            notification.success({
                message: response.data.message || `User ${isEdit ? 'updated' : 'created'} successfully`,
            });
            router.visit('/dashboard/users');
        } catch (error: unknown) {
            const err = error as { response?: { status: number; data: { errors?: Record<string, string[]>; message?: string } } };
            if (err.response && err.response.status === 422) {
                const validationErrors = err.response.data.errors || {};
                const formErrors = Object.keys(validationErrors).map((key) => ({
                    name: key as keyof UserFormValues,
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

            <Form.Item label="Email" name="email" rules={[{ required: true, type: 'email', message: 'Please input a valid email!' }]}>
                <Input type="email" />
            </Form.Item>

            <Row gutter={16}>
                <Col span={12}>
                    <Form.Item
                        label="Password"
                        name="password"
                        rules={[{ required: !isEdit, message: 'Please input a password!' }]}
                        extra={isEdit ? 'Leave blank to keep current password' : ''}
                    >
                        <Input.Password />
                    </Form.Item>
                </Col>
                <Col span={12}>
                    <Form.Item
                        label="Confirm Password"
                        name="password_confirmation"
                        dependencies={['password']}
                        rules={[
                            { required: !isEdit || !!form.getFieldValue('password'), message: 'Please confirm your password!' },
                            ({ getFieldValue }) => ({
                                validator(_, value) {
                                    if (!value || getFieldValue('password') === value) {
                                        return Promise.resolve();
                                    }
                                    return Promise.reject(new Error('The two passwords that you entered do not match!'));
                                },
                            }),
                        ]}
                    >
                        <Input.Password />
                    </Form.Item>
                </Col>
            </Row>

            <Form.Item label="Phone" name="phone">
                <Input />
            </Form.Item>

            <Form.Item label="Date of Birth" name="date_of_birth">
                <DatePicker style={{ width: '100%' }} />
            </Form.Item>

            <Form.Item label="Bio" name="bio">
                <Input.TextArea rows={4} />
            </Form.Item>

            <Row gutter={16}>
                <Col span={12}>
                    <Form.Item label="Timezone" name="timezone_id">
                        <AdvancedSelect type="timezones" id={user?.timezone_id ?? null} />
                    </Form.Item>
                </Col>
                <Col span={12}>
                    <Form.Item label="Language" name="language_id">
                        <AdvancedSelect type="languages" id={user?.language_id ?? null} />
                    </Form.Item>
                </Col>
            </Row>

            <CanAccess permission="roles.read">
                <Form.Item label="Role" name="role_id">
                    <Select
                        placeholder="Select a role"
                        allowClear
                        options={roles
                            .filter((role) => !role.is_super_admin)
                            .map((role) => ({
                                value: role.id,
                                label: role.name,
                            }))}
                    />
                </Form.Item>
            </CanAccess>

            <Form.Item label="Active Status" name="is_active" valuePropName="checked">
                <Switch />
            </Form.Item>

            <Form.Item>
                <Button type="primary" htmlType="submit" loading={loading}>
                    {isEdit ? 'Update User' : 'Create User'}
                </Button>
            </Form.Item>
        </Form>
    );
}
