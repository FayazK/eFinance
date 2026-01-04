import AppLayout from '@/layouts/app-layout';
import { Account } from '@/types';
import { Card } from 'antd';
import AccountForm from './partials/account-form';

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
