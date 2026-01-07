import AppLayout from '@/layouts/app-layout';
import { Card } from 'antd';
import ExpenseForm from './partials/expense-form';

interface CreateExpenseProps {
    accounts: Array<{ id: number; name: string; currency_code: string; formatted_balance: string }>;
    categories: Array<{ id: number; name: string; color?: string }>;
}

export default function CreateExpense({ accounts, categories }: CreateExpenseProps) {
    return (
        <AppLayout pageTitle="Record New Expense">
            <Card>
                <ExpenseForm accounts={accounts} categories={categories} />
            </Card>
        </AppLayout>
    );
}
