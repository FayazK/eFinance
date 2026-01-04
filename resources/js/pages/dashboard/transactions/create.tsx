import AppLayout from '@/layouts/app-layout';
import { Card } from 'antd';
import TransactionForm from './partials/transaction-form';

interface CreateTransactionProps {
    accounts: Array<{ id: number; name: string; currency_code: string }>;
    categories: Array<{ id: number; name: string; type: string; color?: string }>;
}

export default function CreateTransaction({ accounts, categories }: CreateTransactionProps) {
    return (
        <AppLayout pageTitle="Record New Transaction">
            <Card>
                <TransactionForm accounts={accounts} categories={categories} />
            </Card>
        </AppLayout>
    );
}
