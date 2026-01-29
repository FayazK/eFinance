import AppLayout from '@/layouts/app-layout';
import { Account } from '@/types';
import { Card } from 'antd';
import AccountForm from './partials/account-form';

interface EditAccountProps {
    account: Account | { data: Account };
}

export default function EditAccount({ account: accountProp }: EditAccountProps) {
    // Unwrap the account data if it's wrapped in a data property
    const account = 'data' in accountProp ? accountProp.data : accountProp;

    return (
        <AppLayout pageTitle={`Edit ${account.name}`}>
            <Card>
                <AccountForm account={account} isEdit />
            </Card>
        </AppLayout>
    );
}
