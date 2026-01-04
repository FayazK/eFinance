import AppLayout from '@/layouts/app-layout';
import { index as distributionsIndex, downloadStatement } from '@/routes/distributions';
import type { Account, Distribution, DistributionLine } from '@/types';
import { BankOutlined, CheckCircleOutlined, DownloadOutlined, EditOutlined, LinkOutlined, TeamOutlined } from '@ant-design/icons';
import { router, usePage } from '@inertiajs/react';
import { Alert, Button, Card, Col, Descriptions, Row, Space, Statistic, Table, Tag, theme, Typography } from 'antd';
import { useState } from 'react';
import AdjustProfitModal from './partials/adjust-profit-modal';
import ProcessModal from './partials/process-modal';

const { Title } = Typography;
const { useToken } = theme;

interface PageProps {
    distribution: Distribution;
    pkrAccounts: Account[];
}

export default function DistributionShow() {
    const { token } = useToken();
    const { distribution, pkrAccounts } = usePage<PageProps>().props;
    const [adjustModalVisible, setAdjustModalVisible] = useState(false);
    const [processModalVisible, setProcessModalVisible] = useState(false);

    const lineColumns = [
        {
            title: 'Shareholder',
            dataIndex: ['shareholder', 'name'],
            key: 'shareholder',
            render: (text: string, record: DistributionLine) => (
                <Space>
                    {record.shareholder?.is_office_reserve ? <BankOutlined style={{ color: '#1890ff' }} /> : <TeamOutlined />}
                    {text}
                    {record.shareholder?.is_office_reserve && (
                        <Tag color="blue" style={{ marginLeft: 8 }}>
                            Retained
                        </Tag>
                    )}
                </Space>
            ),
        },
        {
            title: 'Equity %',
            dataIndex: 'formatted_equity',
            key: 'equity',
            align: 'right' as const,
        },
        {
            title: 'Amount',
            dataIndex: 'formatted_allocated_amount',
            key: 'amount',
            align: 'right' as const,
        },
        {
            title: 'Transaction',
            dataIndex: 'transaction_id',
            key: 'transaction',
            align: 'center' as const,
            render: (transactionId: number | undefined) =>
                transactionId ? (
                    <Button type="link" size="small" icon={<LinkOutlined />}>
                        View
                    </Button>
                ) : (
                    <Tag color="default">No Transaction</Tag>
                ),
        },
        {
            title: 'Statement',
            key: 'statement',
            align: 'center' as const,
            render: (_: unknown, record: DistributionLine) => (
                <Button
                    type="link"
                    size="small"
                    icon={<DownloadOutlined />}
                    href={downloadStatement.url({
                        id: distribution.id,
                        shareholderId: record.shareholder_id,
                    })}
                    target="_blank"
                >
                    Download
                </Button>
            ),
        },
    ];

    return (
        <AppLayout
            title={`Distribution ${distribution.distribution_number}`}
            breadcrumbs={[
                { title: 'Distributions', href: distributionsIndex.url() },
                { title: distribution.distribution_number, href: '' },
            ]}
        >
            <Space direction="vertical" size="large" style={{ width: '100%' }}>
                {distribution.is_draft && (
                    <Alert
                        message="Draft Distribution"
                        description="This distribution is in draft status. You can adjust the net profit or process it to create withdrawal transactions."
                        type="info"
                        showIcon
                    />
                )}

                {distribution.is_processed && (
                    <Alert
                        message="Processed Distribution"
                        description={`This distribution was processed on ${distribution.processed_at}. Withdrawal transactions have been created for human partners.`}
                        type="success"
                        showIcon
                        icon={<CheckCircleOutlined />}
                    />
                )}

                <Row gutter={16}>
                    <Col xs={24} sm={12} md={6}>
                        <Card>
                            <Statistic
                                title="Revenue"
                                value={distribution.total_revenue_pkr}
                                prefix="Rs"
                                precision={2}
                                valueStyle={{ color: token.colorSuccess }}
                            />
                        </Card>
                    </Col>
                    <Col xs={24} sm={12} md={6}>
                        <Card>
                            <Statistic
                                title="Expenses"
                                value={distribution.total_expenses_pkr}
                                prefix="Rs"
                                precision={2}
                                valueStyle={{ color: token.colorError }}
                            />
                        </Card>
                    </Col>
                    <Col xs={24} sm={12} md={6}>
                        <Card>
                            <Statistic
                                title="Net Profit"
                                value={distribution.final_net_profit}
                                prefix="Rs"
                                precision={2}
                                valueStyle={{ color: token.colorPrimary }}
                            />
                            {distribution.is_manually_adjusted && (
                                <Tag color="warning" style={{ marginTop: 8 }}>
                                    Adjusted
                                </Tag>
                            )}
                        </Card>
                    </Col>
                    <Col xs={24} sm={12} md={6}>
                        <Card>
                            <Statistic title="Distributed" value={distribution.distributed_amount_pkr} prefix="Rs" precision={2} />
                        </Card>
                    </Col>
                </Row>

                <Card title="Distribution Details">
                    <Descriptions column={{ xs: 1, sm: 2, md: 3 }}>
                        <Descriptions.Item label="Number">{distribution.distribution_number}</Descriptions.Item>
                        <Descriptions.Item label="Period">{distribution.period_label}</Descriptions.Item>
                        <Descriptions.Item label="Status">
                            {distribution.is_draft ? <Tag color="warning">Draft</Tag> : <Tag color="success">Processed</Tag>}
                        </Descriptions.Item>
                        {distribution.is_manually_adjusted && (
                            <>
                                <Descriptions.Item label="Original Profit">{distribution.formatted_revenue}</Descriptions.Item>
                                <Descriptions.Item label="Adjustment Reason" span={2}>
                                    {distribution.adjustment_reason}
                                </Descriptions.Item>
                            </>
                        )}
                        {distribution.notes && (
                            <Descriptions.Item label="Notes" span={3}>
                                {distribution.notes}
                            </Descriptions.Item>
                        )}
                    </Descriptions>

                    {distribution.is_draft && (
                        <Space style={{ marginTop: 16 }}>
                            <Button type="default" icon={<EditOutlined />} onClick={() => setAdjustModalVisible(true)}>
                                Adjust Net Profit
                            </Button>
                            <Button type="primary" icon={<CheckCircleOutlined />} onClick={() => setProcessModalVisible(true)}>
                                Process Distribution
                            </Button>
                        </Space>
                    )}
                </Card>

                <Card title="Distribution Lines">
                    <Table columns={lineColumns} dataSource={distribution.lines} rowKey="id" pagination={false} />
                </Card>
            </Space>

            <AdjustProfitModal
                visible={adjustModalVisible}
                distribution={distribution}
                onCancel={() => setAdjustModalVisible(false)}
                onSuccess={() => {
                    setAdjustModalVisible(false);
                    router.reload();
                }}
            />

            <ProcessModal
                visible={processModalVisible}
                distribution={distribution}
                accounts={pkrAccounts}
                onCancel={() => setProcessModalVisible(false)}
                onSuccess={() => {
                    setProcessModalVisible(false);
                    router.reload();
                }}
            />
        </AppLayout>
    );
}
