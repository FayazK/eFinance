import AppLayout from '@/layouts/app-layout';
import { Card } from 'antd';
import AccountForm from './partials/account-form';

export default function CreateAccount() {
    return (
        <AppLayout pageTitle="Create New Account">
            <Card>
                <AccountForm />
            </Card>
        </AppLayout>
    );
}
