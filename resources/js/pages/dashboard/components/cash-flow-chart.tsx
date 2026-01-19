import { Card, theme } from 'antd';
import { CartesianGrid, Legend, Line, LineChart, ResponsiveContainer, Tooltip, TooltipProps, XAxis, YAxis } from 'recharts';

const { useToken } = theme;

interface CashFlowMonth {
    label: string;
    year: number;
    month: number;
    income: number;
    expenses: number;
    net: number;
}

interface CashFlowTrend {
    months: CashFlowMonth[];
}

interface Props {
    data: CashFlowTrend;
}

function formatPKR(value: number): string {
    const absValue = Math.abs(value);
    const sign = value < 0 ? '-' : '';

    if (absValue >= 10000000) {
        return `${sign}${(absValue / 10000000).toFixed(1)}Cr`;
    } else if (absValue >= 100000) {
        return `${sign}${(absValue / 100000).toFixed(1)}L`;
    } else if (absValue >= 1000) {
        return `${sign}${(absValue / 1000).toFixed(0)}K`;
    }
    return `${sign}${absValue.toFixed(0)}`;
}

function formatPKRFull(value: number): string {
    return new Intl.NumberFormat('en-PK', {
        style: 'currency',
        currency: 'PKR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
    }).format(value);
}

interface ChartDataPoint {
    month: string;
    Income: number;
    Expenses: number;
    Net: number;
}

function CustomTooltip({
    active,
    payload,
    label,
    token,
}: TooltipProps<number, string> & { token: { colorBgContainer: string; colorBorder: string; colorText: string } }) {
    if (!active || !payload || payload.length === 0) {
        return null;
    }

    return (
        <div
            style={{
                backgroundColor: token.colorBgContainer,
                border: `1px solid ${token.colorBorder}`,
                borderRadius: 6,
                padding: '8px 12px',
            }}
        >
            <p style={{ margin: 0, marginBottom: 4, fontWeight: 600, color: token.colorText }}>{label}</p>
            {payload.map((entry) => (
                <p key={entry.dataKey} style={{ margin: 0, color: entry.color }}>
                    {entry.name}: {formatPKRFull(entry.value as number)}
                </p>
            ))}
        </div>
    );
}

export default function CashFlowChart({ data }: Props) {
    const { token } = useToken();

    const chartData: ChartDataPoint[] = data.months.map((m) => ({
        month: m.label,
        Income: m.income / 100,
        Expenses: m.expenses / 100,
        Net: m.net / 100,
    }));

    return (
        <Card title="Cash Flow Trend (6 Months)">
            <ResponsiveContainer width="100%" height={300}>
                <LineChart data={chartData}>
                    <CartesianGrid strokeDasharray="3 3" stroke={token.colorBorder} />
                    <XAxis dataKey="month" stroke={token.colorText} />
                    <YAxis stroke={token.colorText} tickFormatter={formatPKR} />
                    <Tooltip content={<CustomTooltip token={token} />} />
                    <Legend />
                    <Line
                        type="linear"
                        dataKey="Income"
                        stroke={token.colorSuccess}
                        strokeWidth={2}
                        dot={{ r: 4 }}
                        activeDot={{ r: 6 }}
                    />
                    <Line
                        type="linear"
                        dataKey="Expenses"
                        stroke={token.colorError}
                        strokeWidth={2}
                        dot={{ r: 4 }}
                        activeDot={{ r: 6 }}
                    />
                    <Line
                        type="linear"
                        dataKey="Net"
                        stroke={token.colorPrimary}
                        strokeWidth={2}
                        dot={{ r: 4 }}
                        activeDot={{ r: 6 }}
                    />
                </LineChart>
            </ResponsiveContainer>
        </Card>
    );
}
