import AppLayout from '@/layouts/app-layout';
import type { Role } from '@/types';
import { Card } from 'antd';
import UserForm from './partials/user-form';

interface CreateUserProps {
    roles: Role[];
}

export default function CreateUser({ roles }: CreateUserProps) {
    return (
        <AppLayout pageTitle="Create New User">
            <Card>
                <UserForm roles={roles} />
            </Card>
        </AppLayout>
    );
}
