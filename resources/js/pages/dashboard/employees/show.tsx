import AppLayout from '@/layouts/app-layout';
import { edit, index } from '@/routes/employees';
import { show as payrollShow } from '@/routes/payroll';
import type { Employee, Payroll } from '@/types';
import { ArrowLeftOutlined, CheckCircleOutlined, ClockCircleOutlined, EditOutlined } from '@ant-design/icons';
import { Link } from '@inertiajs/react';
import { Button, Card, Col, Descriptions, Row, Space, Table, Tag } from 'antd';

interface EmployeeShowProps {
    employee: Employee;
}

const payrollColumns = [
    {
        title: 'Period',
        dataIndex: 'period_label',
        key: 'period',
    },
    {
        title: 'Base Salary',
        dataIndex: 'formatted_base_salary',
        key: 'base_salary',
        align: 'right' as const,
    },
    {
        title: 'Bonus',
        dataIndex: 'formatted_bonus',
        key: 'bonus',
        align: 'right' as const,
    },
    {
        title: 'Deductions',
        dataIndex: 'formatted_deductions',
        key: 'deductions',
        align: 'right' as const,
    },
    {
        title: 'Net Payable',
        dataIndex: 'formatted_net_payable',
        key: 'net_payable',
        align: 'right' as const,
    },
    {
        title: 'Status',
        dataIndex: 'status',
        key: 'status',
        render: (status: Payroll['status']) => (
            <Tag
                icon={status === 'paid' ? <CheckCircleOutlined /> : <ClockCircleOutlined />}
                color={status === 'paid' ? 'success' : 'warning'}
            >
                {status.toUpperCase()}
            </Tag>
        ),
    },
    {
        title: 'Action',
        key: 'action',
        align: 'center' as const,
        render: (_: unknown, record: Payroll) => (
            <Link href={payrollShow.url(record.id)}>
                <Button type="link" size="small">
                    View
                </Button>
            </Link>
        ),
    },
];

export default function EmployeeShow({ employee }: EmployeeShowProps) {
    return (
        <AppLayout
            pageTitle={employee.name}
            actions={
                <Space>
                    <Link href={index.url()}>
                        <Button icon={<ArrowLeftOutlined />}>Back to Employees</Button>
                    </Link>
                    <Link href={edit.url(employee.id)}>
                        <Button type="primary" icon={<EditOutlined />}>
                            Edit Employee
                        </Button>
                    </Link>
                </Space>
            }
        >
            <Row gutter={[16, 16]}>
                <Col span={24}>
                    <Card title="Employee Details">
                        <Descriptions bordered column={2}>
                            <Descriptions.Item label="Status">
                                <Tag color={employee.status === 'active' ? 'success' : 'default'}>{employee.status.toUpperCase()}</Tag>
                            </Descriptions.Item>
                            <Descriptions.Item label="Designation">{employee.designation}</Descriptions.Item>
                            <Descriptions.Item label="Email">{employee.email}</Descriptions.Item>
                            <Descriptions.Item label="Joining Date">{employee.joining_date}</Descriptions.Item>
                            <Descriptions.Item label="Base Salary">{employee.formatted_salary}</Descriptions.Item>
                            <Descriptions.Item label="Payment Method">{employee.deposit_currency}</Descriptions.Item>
                            {employee.iban && <Descriptions.Item label="IBAN">{employee.iban}</Descriptions.Item>}
                            {employee.bank_name && <Descriptions.Item label="Bank Name">{employee.bank_name}</Descriptions.Item>}
                            {employee.status === 'terminated' && employee.termination_date && (
                                <Descriptions.Item label="Termination Date">{employee.termination_date}</Descriptions.Item>
                            )}
                        </Descriptions>
                    </Card>
                </Col>

                <Col span={24}>
                    <Card title="Payroll History">
                        <Table columns={payrollColumns} dataSource={employee.payrolls ?? []} rowKey="id" pagination={false} />
                    </Card>
                </Col>
            </Row>
        </AppLayout>
    );
}
