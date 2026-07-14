import ActivityTimeline from '@/components/activity-timeline';
import AppLayout from '@/layouts/app-layout';
import { show as employeeShow } from '@/routes/employees';
import { index } from '@/routes/payroll';
import type { Payroll } from '@/types';
import { ArrowLeftOutlined, CheckCircleOutlined, ClockCircleOutlined } from '@ant-design/icons';
import { Link } from '@inertiajs/react';
import { Button, Card, Col, Descriptions, Row, Tag, theme } from 'antd';

const { useToken } = theme;

interface PayrollShowProps {
    payroll: Payroll;
}

export default function PayrollShow({ payroll }: PayrollShowProps) {
    const { token } = useToken();
    const { transaction } = payroll;

    return (
        <AppLayout
            pageTitle={`Payroll — ${payroll.period_label}`}
            actions={
                <Link href={index.url()}>
                    <Button icon={<ArrowLeftOutlined />}>Back to Payroll</Button>
                </Link>
            }
        >
            <Row gutter={[16, 16]}>
                <Col span={24}>
                    <Card title="Payroll Details">
                        <Descriptions bordered column={2}>
                            <Descriptions.Item label="Status">
                                <Tag
                                    icon={payroll.status === 'paid' ? <CheckCircleOutlined /> : <ClockCircleOutlined />}
                                    color={payroll.status === 'paid' ? 'success' : 'warning'}
                                >
                                    {payroll.status.toUpperCase()}
                                </Tag>
                            </Descriptions.Item>
                            <Descriptions.Item label="Employee">
                                {payroll.employee ? (
                                    <Link href={employeeShow.url(payroll.employee_id)}>{payroll.employee.name}</Link>
                                ) : (
                                    '—'
                                )}
                            </Descriptions.Item>
                            <Descriptions.Item label="Period">{payroll.period_label}</Descriptions.Item>
                            <Descriptions.Item label="Payment Method">{payroll.deposit_currency}</Descriptions.Item>
                            <Descriptions.Item label="Base Salary">{payroll.formatted_base_salary}</Descriptions.Item>
                            <Descriptions.Item label="Bonus">{payroll.formatted_bonus}</Descriptions.Item>
                            <Descriptions.Item label="Deductions">{payroll.formatted_deductions}</Descriptions.Item>
                            <Descriptions.Item label="Net Payable">
                                <span style={{ fontWeight: 600, fontSize: '16px', color: token.colorPrimary }}>
                                    {payroll.formatted_net_payable}
                                </span>
                            </Descriptions.Item>
                            {payroll.deposit_currency !== 'PKR' && payroll.formatted_exchange_rate && (
                                <Descriptions.Item label="Exchange Rate">{payroll.formatted_exchange_rate}</Descriptions.Item>
                            )}
                            {payroll.is_paid && payroll.paid_at && <Descriptions.Item label="Paid At">{payroll.paid_at}</Descriptions.Item>}
                            {payroll.notes && (
                                <Descriptions.Item label="Notes" span={2}>
                                    {payroll.notes}
                                </Descriptions.Item>
                            )}
                        </Descriptions>
                    </Card>
                </Col>

                {transaction && (
                    <Col span={24}>
                        <Card title="Transaction">
                            <Descriptions bordered column={2}>
                                <Descriptions.Item label="Type">
                                    <Tag color={transaction.type === 'credit' ? 'success' : 'error'}>{transaction.type.toUpperCase()}</Tag>
                                </Descriptions.Item>
                                <Descriptions.Item label="Amount">{transaction.formatted_amount}</Descriptions.Item>
                                <Descriptions.Item label="Date">{transaction.date}</Descriptions.Item>
                                {transaction.description && (
                                    <Descriptions.Item label="Description">{transaction.description}</Descriptions.Item>
                                )}
                            </Descriptions>
                        </Card>
                    </Col>
                )}

                <Col span={24}>
                    <Card>
                        <ActivityTimeline subjectType="Payroll" subjectId={payroll.id} />
                    </Card>
                </Col>
            </Row>
        </AppLayout>
    );
}
