import AppLayout from '@/layouts/app-layout';
import AccountForm from './partials/account-form';
import { Card } from 'antd';

export default function CreateAccount() {
    return (
        <AppLayout pageTitle="Create New Account">
            <Card>
                <AccountForm />
            </Card>
        </AppLayout>
    );
}
