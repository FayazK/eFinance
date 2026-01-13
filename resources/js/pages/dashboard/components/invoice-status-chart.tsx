import { Card, Empty, theme } from 'antd';
import { Cell, Legend, Pie, PieChart, ResponsiveContainer, Tooltip } from 'recharts';

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

export default function InvoiceStatusChart({ data }: Props) {
    const { token } = useToken();

    const COLORS: Record<string, string> = {
        draft: token.colorTextSecondary,
        sent: token.colorInfo,
        partial: token.colorWarning,
        paid: token.colorSuccess,
        overdue: token.colorError,
    };

    const chartData = data.statuses.map((s) => ({
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
                        labelLine={false}
                        label={(entry) => `${entry.name}: ${entry.value}`}
                        outerRadius={80}
                        fill="#8884d8"
                        dataKey="value"
                    >
                        {chartData.map((entry, index) => (
                            <Cell key={`cell-${index}`} fill={COLORS[entry.name.toLowerCase()] || token.colorPrimary} />
                        ))}
                    </Pie>
                    <Tooltip />
                    <Legend />
                </PieChart>
            </ResponsiveContainer>
        </Card>
    );
}
