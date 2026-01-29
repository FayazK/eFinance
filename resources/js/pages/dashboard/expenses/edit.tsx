import { destroy, index } from '@/actions/App/Http/Controllers/ExpenseController';
import AppLayout from '@/layouts/app-layout';
import api from '@/lib/axios';
import type { Expense } from '@/types';
import { DeleteOutlined } from '@ant-design/icons';
import { router } from '@inertiajs/react';
import { Button, Card, Modal, notification, Space } from 'antd';
import { useState } from 'react';
import ExpenseForm from './partials/expense-form';

interface EditExpenseProps {
    expense: Expense;
    accounts: Array<{ id: number; name: string; currency_code: string; formatted_balance: string }>;
    categories: Array<{ id: number; name: string; color?: string }>;
}

export default function EditExpense({ expense, accounts, categories }: EditExpenseProps) {
    const [discarding, setDiscarding] = useState(false);

    const handleDiscard = () => {
        Modal.confirm({
            title: 'Discard Draft Expense?',
            content: 'This will permanently delete this draft expense. This action cannot be undone.',
            okText: 'Discard',
            okType: 'danger',
            cancelText: 'Cancel',
            onOk: async () => {
                setDiscarding(true);
                try {
                    await api.delete(destroy.url(expense.id));
                    notification.success({ message: 'Draft expense discarded' });
                    router.visit(index.url());
                } catch (error: unknown) {
                    setDiscarding(false);
                    const err = error as { response?: { data: { message: string } } };
                    notification.error({
                        message: 'Error',
                        description: err.response?.data.message || 'Failed to discard expense',
                    });
                }
            },
        });
    };

    return (
        <AppLayout pageTitle="Edit Expense">
            <Card
                title="Edit Draft Expense"
                extra={
                    <Space>
                        <Button danger icon={<DeleteOutlined />} onClick={handleDiscard} loading={discarding}>
                            Discard Draft
                        </Button>
                    </Space>
                }
            >
                <ExpenseForm accounts={accounts} categories={categories} expense={expense} isEditing />
            </Card>
        </AppLayout>
    );
}
