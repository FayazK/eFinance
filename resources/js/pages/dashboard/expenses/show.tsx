import AppLayout from '@/layouts/app-layout';
import api from '@/lib/axios';
import { index } from '@/routes/expenses';
import type { Expense, ExpenseStatus } from '@/types';
import { ArrowLeftOutlined, DeleteOutlined, FileOutlined } from '@ant-design/icons';
import { Link, router } from '@inertiajs/react';
import { Button, Card, Col, Descriptions, Image, Modal, notification, Row, Space, Tag, theme } from 'antd';

const { useToken } = theme;

const STATUS_COLORS: Record<ExpenseStatus, string> = {
    draft: 'default',
    processed: 'green',
    cancelled: 'red',
};

interface ExpenseShowProps {
    expense: Expense;
}

export default function ExpenseShow({ expense }: ExpenseShowProps) {
    const { token } = useToken();

    const handleCancel = () => {
        Modal.confirm({
            title: 'Cancel Expense',
            content: 'Are you sure you want to cancel this expense? This action cannot be undone.',
            okText: 'Cancel Expense',
            okType: 'danger',
            cancelText: 'Go Back',
            onOk: async () => {
                try {
                    await api.delete(`/dashboard/expenses/${expense.id}`);
                    notification.success({ message: 'Expense cancelled successfully' });
                    router.visit(index.url());
                } catch (error: unknown) {
                    const err = error as { response?: { data: { message: string } } };
                    notification.error({
                        message: 'Error',
                        description: err.response?.data.message || 'Failed to cancel expense',
                    });
                }
            },
        });
    };

    return (
        <AppLayout
            pageTitle={`Expense #${expense.id}`}
            actions={
                <Space>
                    <Link href={index.url()}>
                        <Button icon={<ArrowLeftOutlined />}>Back to Expenses</Button>
                    </Link>
                    {expense.status !== 'cancelled' && (
                        <Button danger icon={<DeleteOutlined />} onClick={handleCancel}>
                            Cancel Expense
                        </Button>
                    )}
                </Space>
            }
        >
            <Row gutter={[16, 16]}>
                <Col span={24}>
                    <Card title="Expense Details">
                        <Descriptions bordered column={2}>
                            <Descriptions.Item label="Status">
                                <Tag color={STATUS_COLORS[expense.status]}>{expense.status.toUpperCase()}</Tag>
                            </Descriptions.Item>
                            <Descriptions.Item label="Date">{expense.expense_date}</Descriptions.Item>
                            <Descriptions.Item label="Account">
                                <div>
                                    <div style={{ fontWeight: 500 }}>{expense.account?.name}</div>
                                    <div style={{ color: token.colorTextSecondary, fontSize: '12px' }}>{expense.account?.currency_code}</div>
                                </div>
                            </Descriptions.Item>
                            <Descriptions.Item label="Category">
                                {expense.category ? (
                                    <Tag color={expense.category.color || 'default'}>{expense.category.name}</Tag>
                                ) : (
                                    <span style={{ color: token.colorTextDisabled }}>Uncategorized</span>
                                )}
                            </Descriptions.Item>
                            <Descriptions.Item label="Amount">
                                <span style={{ fontWeight: 600, fontSize: '16px', color: token.colorError }}>{expense.formatted_amount}</span>
                            </Descriptions.Item>
                            {expense.exchange_rate && expense.currency_code !== 'PKR' && (
                                <>
                                    <Descriptions.Item label="Exchange Rate">
                                        1 {expense.currency_code} = {expense.exchange_rate} PKR
                                    </Descriptions.Item>
                                    <Descriptions.Item label="PKR Amount">{expense.formatted_reporting_amount || '—'}</Descriptions.Item>
                                </>
                            )}
                            {expense.vendor && <Descriptions.Item label="Vendor">{expense.vendor}</Descriptions.Item>}
                            {expense.description && (
                                <Descriptions.Item label="Description" span={2}>
                                    {expense.description}
                                </Descriptions.Item>
                            )}
                            {expense.is_recurring && (
                                <>
                                    <Descriptions.Item label="Recurring">
                                        <Tag color="blue">Yes</Tag>
                                    </Descriptions.Item>
                                    <Descriptions.Item label="Frequency">{expense.recurrence_frequency}</Descriptions.Item>
                                    {expense.next_occurrence_date && (
                                        <Descriptions.Item label="Next Occurrence">{expense.next_occurrence_date}</Descriptions.Item>
                                    )}
                                </>
                            )}
                            {expense.transaction_id && <Descriptions.Item label="Transaction ID">#{expense.transaction_id}</Descriptions.Item>}
                        </Descriptions>
                    </Card>
                </Col>

                {expense.receipts && expense.receipts.length > 0 && (
                    <Col span={24}>
                        <Card title={`Receipts (${expense.receipts.length})`}>
                            <Space wrap size="large">
                                {expense.receipts.map((receipt) => (
                                    <div key={receipt.id}>
                                        {receipt.mime_type.startsWith('image/') ? (
                                            <Image
                                                src={receipt.url}
                                                alt={receipt.name}
                                                width={200}
                                                style={{ border: `1px solid ${token.colorBorder}`, borderRadius: 4 }}
                                            />
                                        ) : (
                                            <a href={receipt.url} target="_blank" rel="noopener noreferrer">
                                                <Button icon={<FileOutlined />}>{receipt.name || `Receipt ${receipt.id}`}</Button>
                                            </a>
                                        )}
                                        <div style={{ marginTop: 8, fontSize: '12px', color: token.colorTextSecondary }}>
                                            {(receipt.size / 1024 / 1024).toFixed(2)} MB
                                        </div>
                                    </div>
                                ))}
                            </Space>
                        </Card>
                    </Col>
                )}
            </Row>
        </AppLayout>
    );
}
