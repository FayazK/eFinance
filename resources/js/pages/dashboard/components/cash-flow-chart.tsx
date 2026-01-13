import { Card, theme } from 'antd';
import { CartesianGrid, Legend, Line, LineChart, ResponsiveContainer, Tooltip, XAxis, YAxis } from 'recharts';

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

export default function CashFlowChart({ data }: Props) {
    const { token } = useToken();

    const chartData = data.months.map((m) => ({
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
                    <YAxis stroke={token.colorText} />
                    <Tooltip
                        contentStyle={{
                            backgroundColor: token.colorBgContainer,
                            borderColor: token.colorBorder,
                        }}
                    />
                    <Legend />
                    <Line type="monotone" dataKey="Income" stroke={token.colorSuccess} strokeWidth={2} />
                    <Line type="monotone" dataKey="Expenses" stroke={token.colorError} strokeWidth={2} />
                    <Line type="monotone" dataKey="Net" stroke={token.colorPrimary} strokeWidth={2} />
                </LineChart>
            </ResponsiveContainer>
        </Card>
    );
}
