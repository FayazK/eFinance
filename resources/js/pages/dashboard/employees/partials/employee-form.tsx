import { Form, Input, Button, Row, Col, notification, DatePicker, InputNumber } from 'antd';
import { Employee } from '@/types';
import { router } from '@inertiajs/react';
import api from '@/lib/axios';
import { useEffect, useState } from 'react';
import { index } from '@/routes/employees';
import dayjs from 'dayjs';

interface EmployeeFormProps {
    employee?: Employee;
    isEdit?: boolean;
}

export default function EmployeeForm({ employee, isEdit = false }: EmployeeFormProps) {
    const [form] = Form.useForm();
    const [loading, setLoading] = useState(false);

    useEffect(() => {
        form.setFieldsValue({
            name: employee?.name || '',
            email: employee?.email || '',
            designation: employee?.designation || '',
            joining_date: employee?.joining_date ? dayjs(employee.joining_date) : undefined,
            base_salary: employee?.base_salary_pkr || 0,
            iban: employee?.iban || '',
            bank_name: employee?.bank_name || '',
        });
    }, [employee, form]);

    const onFinish = async (values: Record<string, unknown>) => {
        setLoading(true);

        // Format the joining_date
        const formattedValues = {
            ...values,
            joining_date: values.joining_date ? dayjs(values.joining_date as dayjs.Dayjs).format('YYYY-MM-DD') : null,
        };

        const method = isEdit ? 'put' : 'post';
        const url = isEdit ? `/dashboard/employees/${employee!.id}` : '/dashboard/employees';

        try {
            const response = await api[method](url, formattedValues);
            notification.success({
                message: response.data.message || `Employee ${isEdit ? 'updated' : 'created'} successfully`,
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
                        label="Name"
                        name="name"
                        rules={[{ required: true, message: 'Please input the employee name!' }]}
                    >
                        <Input />
                    </Form.Item>
                </Col>
                <Col span={12}>
                    <Form.Item
                        label="Email"
                        name="email"
                        rules={[{ required: true, type: 'email', message: 'Please input a valid email!' }]}
                    >
                        <Input type="email" />
                    </Form.Item>
                </Col>
            </Row>

            <Row gutter={16}>
                <Col span={12}>
                    <Form.Item
                        label="Designation"
                        name="designation"
                        rules={[{ required: true, message: 'Please input the designation!' }]}
                    >
                        <Input />
                    </Form.Item>
                </Col>
                <Col span={12}>
                    <Form.Item
                        label="Joining Date"
                        name="joining_date"
                        rules={[{ required: true, message: 'Please select the joining date!' }]}
                    >
                        <DatePicker style={{ width: '100%' }} format="YYYY-MM-DD" />
                    </Form.Item>
                </Col>
            </Row>

            <Row gutter={16}>
                <Col span={12}>
                    <Form.Item
                        label="Base Salary (PKR)"
                        name="base_salary"
                        rules={[{ required: true, message: 'Please input the base salary!' }]}
                    >
                        <InputNumber
                            style={{ width: '100%' }}
                            min={0}
                            precision={2}
                            formatter={(value) => `${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')}
                        />
                    </Form.Item>
                </Col>
            </Row>

            <Row gutter={16}>
                <Col span={12}>
                    <Form.Item
                        label="IBAN"
                        name="iban"
                    >
                        <Input />
                    </Form.Item>
                </Col>
                <Col span={12}>
                    <Form.Item
                        label="Bank Name"
                        name="bank_name"
                    >
                        <Input />
                    </Form.Item>
                </Col>
            </Row>

            <Form.Item>
                <Button type="primary" htmlType="submit" loading={loading}>
                    {isEdit ? 'Update' : 'Create'} Employee
                </Button>
            </Form.Item>
        </Form>
    );
}
