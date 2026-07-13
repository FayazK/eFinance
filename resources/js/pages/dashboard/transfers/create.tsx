import AppLayout from '@/layouts/app-layout';
import { Card } from 'antd';
import TransferForm from './partials/transfer-form';

interface Account {
    id: number;
    name: string;
    currency_code: string;
    current_balance: number;
    formatted_balance: string;
}

interface CreateTransferProps {
    accounts: Account[];
}

export default function CreateTransfer({ accounts }: CreateTransferProps) {
    return (
        <AppLayout pageTitle="New Transfer">
            <Card>
                <TransferForm accounts={accounts} />
            </Card>
        </AppLayout>
    );
}
