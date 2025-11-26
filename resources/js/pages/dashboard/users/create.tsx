import AppLayout from '@/layouts/app-layout';
import UserForm from './partials/user-form';
import { Card } from 'antd';

export default function CreateUser() {
    return (
        <AppLayout pageTitle="Create New User">
            <Card>
                <UserForm />
            </Card>
        </AppLayout>
    );
}
