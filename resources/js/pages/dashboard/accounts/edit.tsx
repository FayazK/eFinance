import AppLayout from '@/layouts/app-layout';
import AccountForm from './partials/account-form';
import { Card } from 'antd';
import { Account } from '@/types';

interface EditAccountProps {
    account: Account;
}

export default function EditAccount({ account }: EditAccountProps) {
    return (
        <AppLayout pageTitle={`Edit ${account.name}`}>
            <Card>
                <AccountForm account={account} isEdit />
            </Card>
        </AppLayout>
    );
}
