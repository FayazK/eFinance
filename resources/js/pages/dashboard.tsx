import AppLayout from '@/layouts/app-layout';
import type { SharedData } from '@/types';
import { BankOutlined, CalendarOutlined, DollarOutlined, FileTextOutlined, TeamOutlined } from '@ant-design/icons';
import { Head, usePage } from '@inertiajs/react';
import { Card, Col, Row, Space, Statistic, Tag, theme, Tooltip, Typography } from 'antd';
import CashFlowChart from './dashboard/components/cash-flow-chart';
import InvoiceStatusChart from './dashboard/components/invoice-status-chart';
import RecentTransactionsList from './dashboard/components/recent-transactions-list';

const { Title, Paragraph, Text } = Typography;
const { useToken } = theme;

interface FinancialOverview {
    accounts_by_currency: Array<{
        currency_code: string;
        total_balance: number;
        formatted_balance: string;
    }>;
    total_active_accounts: number;
}

interface RevenueMetrics {
    total_receivables: number;
    formatted_receivables: string;
    invoice_counts: {
        draft: number;
        sent: number;
        partial: number;
        paid: number;
        overdue: number;
    };
    overdue_count: number;
}

interface PayrollSummary {
    active_employees: number;
    last_month_expense: number;
    formatted_last_month: string;
    pending_payrolls: number;
}

interface CashFlowMonth {
    label: string;
    year: number;
    month: number;
    income: number;
    expenses: number;
    net: number;
}

interface CashFlowCurrency {
    currency_code: string;
    months: CashFlowMonth[];
}

interface CashFlowTrend {
    currencies: CashFlowCurrency[];
}

interface InvoiceStatusBreakdown {
    statuses: Array<{
        status: string;
        count: number;
        total_amount: number;
        formatted_amount: string;
    }>;
}

interface RecentTransaction {
    id: number;
    date: string;
    description: string;
    amount: number;
    formatted_amount: string;
    type: 'credit' | 'debit';
    account: {
        name: string;
        currency_code: string;
    };
    category: {
        name: string;
        color: string;
    };
}

interface PageProps extends SharedData {
    distributableProfit: {
        amount_pkr: number;
        formatted_amount: string;
    };
    runway: {
        runway_months: number;
        office_balance_pkr: number;
        formatted_office_balance: string;
        avg_monthly_expenses_pkr: number;
        formatted_avg_monthly_expenses: string;
    };
    financialOverview: FinancialOverview;
    revenueMetrics: RevenueMetrics;
    payrollSummary: PayrollSummary;
    cashFlowTrend: CashFlowTrend;
    invoiceStatusBreakdown: InvoiceStatusBreakdown;
    recentTransactions: RecentTransaction[];
}

export default function Dashboard() {
    const {
        auth,
        distributableProfit,
        runway,
        financialOverview,
        revenueMetrics,
        payrollSummary,
        cashFlowTrend,
        invoiceStatusBreakdown,
        recentTransactions,
    } = usePage<PageProps>().props;
    const { token } = useToken();

    return (
        <AppLayout pageTitle="Dashboard">
            <Head title="Dashboard" />

            <Space direction="vertical" size="large" style={{ width: '100%' }}>
                <div>
                    <Title level={2} style={{ marginBottom: token.marginXS }}>
                        Welcome back, {auth.user.full_name}!
                    </Title>
                    <Paragraph type="secondary">Here's your financial overview for the last 6 months.</Paragraph>
                </div>

                {/* Financial Overview - Dynamic columns based on currencies */}
                <Row gutter={[16, 16]}>
                    {financialOverview.accounts_by_currency.map((currency) => (
                        <Col xs={24} sm={12} lg={6} key={currency.currency_code}>
                            <Card>
                                <Statistic
                                    title={`Total Cash (${currency.currency_code})`}
                                    value={currency.total_balance / 100}
                                    precision={2}
                                    prefix={<DollarOutlined style={{ color: token.colorSuccess }} />}
                                    formatter={() => currency.formatted_balance}
                                />
                            </Card>
                        </Col>
                    ))}
                    <Col xs={24} sm={12} lg={6}>
                        <Card>
                            <Statistic
                                title={<Tooltip title="Undistributed profit available for distribution">Distributable Profit</Tooltip>}
                                value={distributableProfit.amount_pkr / 100}
                                precision={2}
                                prefix={<DollarOutlined style={{ color: token.colorPrimary }} />}
                                formatter={() => distributableProfit.formatted_amount}
                            />
                        </Card>
                    </Col>
                </Row>

                {/* Key Metrics - 3 columns */}
                <Row gutter={[16, 16]}>
                    <Col xs={24} sm={12} lg={8}>
                        <Card>
                            <Statistic
                                title="Total Receivables"
                                value={revenueMetrics.total_receivables / 100}
                                precision={2}
                                prefix={<FileTextOutlined style={{ color: token.colorInfo }} />}
                                formatter={() => revenueMetrics.formatted_receivables}
                            />
                            {revenueMetrics.overdue_count > 0 && (
                                <Tag color="red" style={{ marginTop: 8 }}>
                                    {revenueMetrics.overdue_count} Overdue
                                </Tag>
                            )}
                        </Card>
                    </Col>
                    <Col xs={24} sm={12} lg={8}>
                        <Card>
                            <Statistic
                                title={
                                    <Tooltip title="Months the company can operate on Office reserves based on average monthly expenses">
                                        Runway
                                    </Tooltip>
                                }
                                value={runway.runway_months}
                                precision={1}
                                prefix={<CalendarOutlined style={{ color: token.colorWarning }} />}
                                suffix={runway.runway_months === 1 ? 'month' : 'months'}
                            />
                        </Card>
                    </Col>
                    <Col xs={24} sm={12} lg={8}>
                        <Card>
                            <Statistic
                                title="Active Employees"
                                value={payrollSummary.active_employees}
                                prefix={<TeamOutlined style={{ color: token.colorSuccess }} />}
                            />
                            <Text type="secondary" style={{ fontSize: 12 }}>
                                Last month: {payrollSummary.formatted_last_month}
                            </Text>
                        </Card>
                    </Col>
                </Row>

                {/* Charts Section */}
                <Row gutter={[16, 16]}>
                    <Col xs={24} lg={16}>
                        <CashFlowChart data={cashFlowTrend} />
                    </Col>
                    <Col xs={24} lg={8}>
                        <InvoiceStatusChart data={invoiceStatusBreakdown} />
                    </Col>
                </Row>

                {/* Recent Transactions & Office Reserve */}
                <Row gutter={[16, 16]}>
                    <Col xs={24} lg={16}>
                        <RecentTransactionsList transactions={recentTransactions} />
                    </Col>
                    <Col xs={24} lg={8}>
                        <Card title="Office Reserve">
                            <Statistic
                                title={<Tooltip title="Total retained earnings in Office Reserve">Office Reserve Balance</Tooltip>}
                                value={runway.office_balance_pkr / 100}
                                precision={2}
                                prefix={<BankOutlined style={{ color: token.colorPrimary }} />}
                                formatter={() => runway.formatted_office_balance}
                            />
                        </Card>
                    </Col>
                </Row>
            </Space>
        </AppLayout>
    );
}
