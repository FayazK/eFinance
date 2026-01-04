import AppLayout from '@/layouts/app-layout';
import { Card } from 'antd';
import UserForm from './partials/user-form';

export default function CreateUser() {
    return (
        <AppLayout pageTitle="Create New User">
            <Card>
                <UserForm />
            </Card>
        </AppLayout>
    );
}
