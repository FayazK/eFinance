import AppLayout from '@/layouts/app-layout';
import type { Role, User } from '@/types';
import { Card } from 'antd';
import UserForm from './partials/user-form';

interface EditUserProps {
    user: User;
    roles: Role[];
}

export default function EditUser({ user, roles }: EditUserProps) {
    return (
        <AppLayout pageTitle={user ? `Edit User: ${user.full_name}` : 'Edit User'}>
            <Card>
                <UserForm user={user} roles={roles} isEdit />
            </Card>
        </AppLayout>
    );
}
