import React from 'react';
import { Row, Col, Card, Statistic, Button, Space, Tag, theme, Empty } from 'antd';
import {
    PlusOutlined,
    WalletOutlined,
    BankOutlined,
    DollarOutlined,
    CheckCircleOutlined,
    StopOutlined,
} from '@ant-design/icons';
import { Link, usePage } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import type { Account, LaravelPaginatedResponse, SharedData } from '@/types';
import { create } from '@/routes/accounts';

const { useToken } = theme;

interface NetWorthData {
    total_pkr: number;
    formatted_total_pkr: string;
    currency_breakdown: Array<{
        currency_code: string;
        currency_symbol: string;
        currency_name: string;
        balance: number;
        pkr_value: number;
        formatted_balance: string;
        formatted_pkr_value: string;
    }>;
}

interface AccountsIndexProps extends SharedData {
    accounts: LaravelPaginatedResponse<Account>;
    netWorth: string;
    netWorthData: NetWorthData;
}

const accountTypeIcons = {
    bank: <BankOutlined />,
    wallet: <WalletOutlined />,
    cash: <DollarOutlined />,
};

export default function AccountsIndex() {
    const { accounts, netWorth, netWorthData } = usePage<AccountsIndexProps>().props;
    const { token } = useToken();

    const accountsData = accounts.data || [];

    return (
        <AppLayout
            pageTitle="Accounts"
            actions={
                <Link href={create.url()}>
                    <Button type="primary" icon={<PlusOutlined />}>
                        Add Account
                    </Button>
                </Link>
            }
        >
            <Space direction="vertical" size="large" style={{ width: '100%' }}>
                {/* Net Worth Summary */}
                <Row gutter={[16, 16]}>
                    <Col xs={24} sm={12} lg={8}>
                        <Card>
                            <Statistic
                                title="Total Net Worth"
                                value={netWorth}
                                prefix={<DollarOutlined style={{ color: token.colorSuccess }} />}
                                valueStyle={{ color: token.colorSuccess }}
                            />
                        </Card>
                    </Col>
                    <Col xs={24} sm={12} lg={8}>
                        <Card>
                            <Statistic
                                title="Total Accounts"
                                value={accountsData.length}
                                prefix={<WalletOutlined style={{ color: token.colorPrimary }} />}
                            />
                        </Card>
                    </Col>
                    <Col xs={24} sm={12} lg={8}>
                        <Card>
                            <Statistic
                                title="Active Accounts"
                                value={accountsData.filter((acc) => acc.is_active).length}
                                prefix={<CheckCircleOutlined style={{ color: token.colorInfo }} />}
                            />
                        </Card>
                    </Col>
                </Row>

                {/* Currency Breakdown */}
                {netWorthData?.currency_breakdown && netWorthData.currency_breakdown.length > 0 && (
                    <Card title="Net Worth by Currency">
                        <Row gutter={[16, 16]}>
                            {netWorthData.currency_breakdown.map((currency) => (
                                <Col xs={24} sm={12} lg={8} key={currency.currency_code}>
                                    <Card
                                        size="small"
                                        style={{
                                            backgroundColor: token.colorBgContainer,
                                            borderColor: token.colorBorder,
                                        }}
                                    >
                                        <Space direction="vertical" size="small" style={{ width: '100%' }}>
                                            <div style={{ display: 'flex', alignItems: 'center', gap: 8 }}>
                                                <Tag color="blue">{currency.currency_symbol}</Tag>
                                                <span style={{ fontSize: 14, fontWeight: 600 }}>
                                                    {currency.currency_name}
                                                </span>
                                            </div>
                                            <div>
                                                <div
                                                    style={{
                                                        fontSize: 20,
                                                        fontWeight: 700,
                                                        color: token.colorPrimary,
                                                    }}
                                                >
                                                    {currency.formatted_balance}
                                                </div>
                                                {currency.currency_code !== 'PKR' && currency.pkr_value > 0 && (
                                                    <div
                                                        style={{
                                                            fontSize: 12,
                                                            color: token.colorTextSecondary,
                                                        }}
                                                    >
                                                        ≈ {currency.formatted_pkr_value}
                                                    </div>
                                                )}
                                            </div>
                                        </Space>
                                    </Card>
                                </Col>
                            ))}
                        </Row>
                    </Card>
                )}

                {/* Accounts Grid */}
                {accountsData.length > 0 ? (
                    <Row gutter={[16, 16]}>
                        {accountsData.map((account) => (
                            <Col xs={24} sm={12} lg={8} key={account.id}>
                                <Link href={`/dashboard/accounts/${account.id}`}>
                                    <Card
                                        hoverable
                                        style={{
                                            height: '100%',
                                            borderColor: account.is_active
                                                ? token.colorBorder
                                                : token.colorBorderSecondary,
                                        }}
                                    >
                                        <Space
                                            direction="vertical"
                                            size="middle"
                                            style={{ width: '100%' }}
                                        >
                                            {/* Account Header */}
                                            <Space style={{ width: '100%', justifyContent: 'space-between' }}>
                                                <Space>
                                                    <div
                                                        style={{
                                                            width: 40,
                                                            height: 40,
                                                            borderRadius: '50%',
                                                            backgroundColor: token.colorPrimaryBg,
                                                            display: 'flex',
                                                            alignItems: 'center',
                                                            justifyContent: 'center',
                                                        }}
                                                    >
                                                        {React.cloneElement(accountTypeIcons[account.type], {
                                                            style: { color: token.colorPrimary, fontSize: 20 },
                                                        })}
                                                    </div>
                                                    <div>
                                                        <div style={{ fontWeight: 600, fontSize: 16 }}>
                                                            {account.name}
                                                        </div>
                                                        {account.bank_name && (
                                                            <div
                                                                style={{
                                                                    color: token.colorTextSecondary,
                                                                    fontSize: 12,
                                                                }}
                                                            >
                                                                {account.bank_name}
                                                            </div>
                                                        )}
                                                    </div>
                                                </Space>
                                                {account.is_active ? (
                                                    <Tag color="green" icon={<CheckCircleOutlined />}>
                                                        Active
                                                    </Tag>
                                                ) : (
                                                    <Tag color="default" icon={<StopOutlined />}>
                                                        Inactive
                                                    </Tag>
                                                )}
                                            </Space>

                                            {/* Balance */}
                                            <div>
                                                <div style={{ color: token.colorTextSecondary, fontSize: 12 }}>
                                                    Current Balance
                                                </div>
                                                <div
                                                    style={{
                                                        fontSize: 24,
                                                        fontWeight: 700,
                                                        color:
                                                            account.current_balance >= 0
                                                                ? token.colorSuccess
                                                                : token.colorError,
                                                    }}
                                                >
                                                    {account.formatted_balance}
                                                </div>
                                            </div>

                                            {/* Account Details */}
                                            {account.account_number && (
                                                <div>
                                                    <div
                                                        style={{
                                                            color: token.colorTextSecondary,
                                                            fontSize: 12,
                                                        }}
                                                    >
                                                        Account Number
                                                    </div>
                                                    <div style={{ fontSize: 13 }}>
                                                        {account.account_number}
                                                    </div>
                                                </div>
                                            )}
                                        </Space>
                                    </Card>
                                </Link>
                            </Col>
                        ))}
                    </Row>
                ) : (
                    <Card>
                        <Empty
                            image={Empty.PRESENTED_IMAGE_SIMPLE}
                            description="No accounts have been created yet."
                        >
                            <Link href={create.url()}>
                                <Button type="primary" icon={<PlusOutlined />}>
                                    Create First Account
                                </Button>
                            </Link>
                        </Empty>
                    </Card>
                )}
            </Space>
        </AppLayout>
    );
}

AccountsIndex.layout = (page: React.ReactNode) => page;
