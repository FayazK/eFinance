import api from '@/lib/axios';
import type { Role } from '@/types';
import { router } from '@inertiajs/react';
import { Button, Col, Divider, Form, Input, notification, Row, Switch, Typography } from 'antd';
import { useEffect, useState } from 'react';
import PermissionMatrix from './permission-matrix';

const { Title, Text } = Typography;

interface PermissionModule {
    label: string;
    permissions: string[];
}

interface PermissionModules {
    [key: string]: PermissionModule;
}

interface RoleFormProps {
    role?: Role;
    permissionModules: PermissionModules;
    isEdit?: boolean;
}

interface FormValues {
    name: string;
    slug?: string;
    description?: string;
    permissions: string[];
    is_default: boolean;
}

export default function RoleForm({ role, permissionModules, isEdit = false }: RoleFormProps) {
    const [form] = Form.useForm<FormValues>();
    const [loading, setLoading] = useState(false);
    const [selectedPermissions, setSelectedPermissions] = useState<string[]>(role?.permissions || []);

    useEffect(() => {
        form.setFieldsValue({
            name: role?.name || '',
            slug: role?.slug || '',
            description: role?.description || '',
            permissions: role?.permissions || [],
            is_default: role?.is_default ?? false,
        });
        setSelectedPermissions(role?.permissions || []);
    }, [role, form]);

    const handlePermissionsChange = (permissions: string[]) => {
        setSelectedPermissions(permissions);
        form.setFieldValue('permissions', permissions);
    };

    const selectAllPermissions = () => {
        const allPermissions: string[] = [];
        Object.keys(permissionModules).forEach((moduleKey) => {
            const module = permissionModules[moduleKey];
            module.permissions.forEach((action) => {
                allPermissions.push(`${moduleKey}.${action}`);
            });
        });
        handlePermissionsChange(allPermissions);
    };

    const clearAllPermissions = () => {
        handlePermissionsChange([]);
    };

    const onFinish = async (values: FormValues) => {
        setLoading(true);

        const requestData = {
            ...values,
            permissions: selectedPermissions,
        };

        const method = isEdit ? 'put' : 'post';
        const url = isEdit ? `/dashboard/roles/${role!.id}` : '/dashboard/roles';

        try {
            const response = await api[method](url, requestData);
            notification.success({
                message: response.data.message || `Role ${isEdit ? 'updated' : 'created'} successfully`,
            });
            router.visit('/dashboard/roles');
        } catch (error: unknown) {
            const err = error as { response?: { status: number; data: { errors?: Record<string, string[]>; message?: string } } };
            if (err.response && err.response.status === 422) {
                const validationErrors = err.response.data.errors || {};
                const formErrors = Object.keys(validationErrors).map((key) => ({
                    name: key as keyof FormValues,
                    errors: validationErrors[key],
                }));
                form.setFields(formErrors);
                notification.error({
                    message: 'Validation Error',
                    description: err.response.data.message,
                });
            } else if (err.response && err.response.status === 403) {
                notification.error({
                    message: 'Forbidden',
                    description: err.response.data.message || 'You do not have permission to perform this action.',
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
                    <Form.Item
                        label="Role Name"
                        name="name"
                        rules={[{ required: true, message: 'Please enter a role name' }]}
                    >
                        <Input placeholder="e.g., Editor, Accountant" />
                    </Form.Item>
                </Col>
                <Col span={12}>
                    <Form.Item
                        label="Slug"
                        name="slug"
                        extra="Leave blank to auto-generate from name"
                    >
                        <Input placeholder="e.g., editor, accountant" />
                    </Form.Item>
                </Col>
            </Row>

            <Form.Item
                label="Description"
                name="description"
            >
                <Input.TextArea
                    rows={2}
                    placeholder="Describe what this role is for..."
                />
            </Form.Item>

            <Form.Item
                name="is_default"
                valuePropName="checked"
            >
                <Switch />
                <Text style={{ marginLeft: 8 }}>
                    Set as default role for new users
                </Text>
            </Form.Item>

            <Divider />

            {/* Permissions Section */}
            <div style={{ marginBottom: 16 }}>
                <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: 16 }}>
                    <div>
                        <Title level={5} style={{ margin: 0 }}>Permissions</Title>
                        <Text type="secondary">
                            Selected: {selectedPermissions.length} permissions
                        </Text>
                    </div>
                    <div>
                        <Button size="small" onClick={selectAllPermissions} style={{ marginRight: 8 }}>
                            Select All
                        </Button>
                        <Button size="small" onClick={clearAllPermissions}>
                            Clear All
                        </Button>
                    </div>
                </div>

                <Form.Item
                    name="permissions"
                    rules={[
                        {
                            validator: () => {
                                if (selectedPermissions.length === 0) {
                                    return Promise.reject('At least one permission must be selected');
                                }
                                return Promise.resolve();
                            },
                        },
                    ]}
                >
                    <PermissionMatrix
                        modules={permissionModules}
                        selectedPermissions={selectedPermissions}
                        onChange={handlePermissionsChange}
                    />
                </Form.Item>
            </div>

            <Divider />

            <Form.Item style={{ marginBottom: 0 }}>
                <Button type="primary" htmlType="submit" loading={loading}>
                    {isEdit ? 'Update Role' : 'Create Role'}
                </Button>
                <Button style={{ marginLeft: 8 }} onClick={() => router.visit('/dashboard/roles')}>
                    Cancel
                </Button>
            </Form.Item>
        </Form>
    );
}
