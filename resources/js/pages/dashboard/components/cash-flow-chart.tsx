import { Card, Segmented, theme } from 'antd';
import { useState } from 'react';
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

interface CashFlowCurrency {
    currency_code: string;
    months: CashFlowMonth[];
}

interface CashFlowTrend {
    currencies: CashFlowCurrency[];
}

interface Props {
    data: CashFlowTrend;
}

function formatAmount(value: number, currency: string): string {
    const absValue = Math.abs(value);
    const sign = value < 0 ? '-' : '';

    if (currency === 'PKR') {
        if (absValue >= 10000000) {
            return `${sign}${(absValue / 10000000).toFixed(1)}Cr`;
        } else if (absValue >= 100000) {
            return `${sign}${(absValue / 100000).toFixed(1)}L`;
        } else if (absValue >= 1000) {
            return `${sign}${(absValue / 1000).toFixed(0)}K`;
        }
        return `${sign}${absValue.toFixed(0)}`;
    } else {
        // USD formatting
        if (absValue >= 1000000) {
            return `${sign}$${(absValue / 1000000).toFixed(1)}M`;
        } else if (absValue >= 1000) {
            return `${sign}$${(absValue / 1000).toFixed(0)}K`;
        }
        return `${sign}$${absValue.toFixed(0)}`;
    }
}

function formatAmountFull(value: number, currency: string): string {
    if (currency === 'PKR') {
        return new Intl.NumberFormat('en-PK', {
            style: 'currency',
            currency: 'PKR',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0,
        }).format(value);
    } else {
        return new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: 'USD',
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        }).format(value);
    }
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
    currency,
}: TooltipProps<number, string> & { token: { colorBgContainer: string; colorBorder: string; colorText: string }; currency: string }) {
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
                    {entry.name}: {formatAmountFull(entry.value as number, currency)}
                </p>
            ))}
        </div>
    );
}

export default function CashFlowChart({ data }: Props) {
    const { token } = useToken();
    const [selectedCurrency, setSelectedCurrency] = useState<string>('USD');

    // Find the selected currency data
    const currencyData = data.currencies.find((c) => c.currency_code === selectedCurrency);

    const chartData: ChartDataPoint[] = currencyData
        ? currencyData.months.map((m) => ({
              month: m.label,
              Income: m.income / 100,
              Expenses: m.expenses / 100,
              Net: m.net / 100,
          }))
        : [];

    // Create currency options from available data
    const currencyOptions = data.currencies.map((c) => ({
        label: c.currency_code,
        value: c.currency_code,
    }));

    return (
        <Card
            title="Cash Flow Trend (6 Months)"
            extra={
                <Segmented
                    options={currencyOptions}
                    value={selectedCurrency}
                    onChange={(value) => setSelectedCurrency(value as string)}
                    size="small"
                />
            }
        >
            <ResponsiveContainer width="100%" height={300}>
                <LineChart data={chartData}>
                    <CartesianGrid strokeDasharray="3 3" stroke={token.colorBorder} />
                    <XAxis dataKey="month" stroke={token.colorText} />
                    <YAxis stroke={token.colorText} tickFormatter={(value) => formatAmount(value, selectedCurrency)} />
                    <Tooltip content={<CustomTooltip token={token} currency={selectedCurrency} />} />
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
