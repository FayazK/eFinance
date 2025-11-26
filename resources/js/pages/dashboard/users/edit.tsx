import AppLayout from '@/layouts/app-layout';
import UserForm from './partials/user-form';

interface EditUserProps {
    user: {
        id: number;
        first_name: string;
        last_name: string;
        full_name: string;
        email: string;
        phone?: string;
        date_of_birth?: string;
        bio?: string;
        timezone_id?: number;
        language_id?: number;
        is_active: boolean;
    };
}

export default function EditUser({ user }: EditUserProps) {
    return (
        <AppLayout pageTitle={user ? `Edit User: ${user.full_name}` : 'Edit User'}>
            <UserForm user={user} isEdit />
        </AppLayout>
    );
}
