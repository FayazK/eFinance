import { ArrowDownOutlined, ArrowUpOutlined } from '@ant-design/icons';
import { Card, Empty, List, theme, Typography } from 'antd';

const { Text } = Typography;
const { useToken } = theme;

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

interface Props {
    transactions: RecentTransaction[];
}

export default function RecentTransactionsList({ transactions }: Props) {
    const { token } = useToken();

    if (transactions.length === 0) {
        return (
            <Card title="Recent Transactions">
                <Empty description="No recent transactions" image={Empty.PRESENTED_IMAGE_SIMPLE} />
            </Card>
        );
    }

    return (
        <Card title="Recent Transactions">
            <List
                dataSource={transactions}
                renderItem={(transaction) => (
                    <List.Item>
                        <List.Item.Meta
                            avatar={
                                transaction.type === 'credit' ? (
                                    <ArrowUpOutlined style={{ color: token.colorSuccess, fontSize: 20 }} />
                                ) : (
                                    <ArrowDownOutlined style={{ color: token.colorError, fontSize: 20 }} />
                                )
                            }
                            title={transaction.description}
                            description={
                                <>
                                    {transaction.account.name} · {transaction.category.name}
                                    <br />
                                    <Text type="secondary">{transaction.date}</Text>
                                </>
                            }
                        />
                        <Text
                            strong
                            style={{
                                color: transaction.type === 'credit' ? token.colorSuccess : token.colorError,
                            }}
                        >
                            {transaction.type === 'credit' ? '+' : '-'}
                            {transaction.formatted_amount}
                        </Text>
                    </List.Item>
                )}
            />
        </Card>
    );
}
