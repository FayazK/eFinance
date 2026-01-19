import { Card, Empty, theme } from 'antd';
import { Cell, Legend, Pie, PieChart, ResponsiveContainer, Tooltip, TooltipProps } from 'recharts';

const { useToken } = theme;

interface InvoiceStatus {
    status: string;
    count: number;
    total_amount: number;
    formatted_amount: string;
}

interface InvoiceStatusBreakdown {
    statuses: InvoiceStatus[];
}

interface Props {
    data: InvoiceStatusBreakdown;
}

interface ChartDataPoint {
    name: string;
    value: number;
    amount: string;
}

function CustomTooltip({
    active,
    payload,
    token,
}: TooltipProps<number, string> & { token: { colorBgContainer: string; colorBorder: string; colorText: string } }) {
    if (!active || !payload || payload.length === 0) {
        return null;
    }

    const entry = payload[0].payload as ChartDataPoint;

    return (
        <div
            style={{
                backgroundColor: token.colorBgContainer,
                border: `1px solid ${token.colorBorder}`,
                borderRadius: 6,
                padding: '8px 12px',
            }}
        >
            <p style={{ margin: 0, marginBottom: 4, fontWeight: 600, color: token.colorText }}>{entry.name}</p>
            <p style={{ margin: 0, color: token.colorText }}>
                Count: {entry.value} invoice{entry.value !== 1 ? 's' : ''}
            </p>
            <p style={{ margin: 0, color: token.colorText }}>Amount: {entry.amount}</p>
        </div>
    );
}

export default function InvoiceStatusChart({ data }: Props) {
    const { token } = useToken();

    const COLORS: Record<string, string> = {
        draft: token.colorTextSecondary,
        sent: token.colorInfo,
        partial: token.colorWarning,
        paid: token.colorSuccess,
        overdue: token.colorError,
    };

    const chartData: ChartDataPoint[] = data.statuses.map((s) => ({
        name: s.status.charAt(0).toUpperCase() + s.status.slice(1),
        value: s.count,
        amount: s.formatted_amount,
    }));

    if (chartData.length === 0) {
        return (
            <Card title="Invoice Status Breakdown">
                <Empty description="No invoices found" image={Empty.PRESENTED_IMAGE_SIMPLE} />
            </Card>
        );
    }

    return (
        <Card title="Invoice Status Breakdown">
            <ResponsiveContainer width="100%" height={300}>
                <PieChart>
                    <Pie
                        data={chartData}
                        cx="50%"
                        cy="50%"
                        innerRadius={50}
                        outerRadius={90}
                        fill="#8884d8"
                        dataKey="value"
                        paddingAngle={2}
                    >
                        {chartData.map((entry, index) => (
                            <Cell key={`cell-${index}`} fill={COLORS[entry.name.toLowerCase()] || token.colorPrimary} />
                        ))}
                    </Pie>
                    <Tooltip content={<CustomTooltip token={token} />} />
                    <Legend />
                </PieChart>
            </ResponsiveContainer>
        </Card>
    );
}
