import AppLayout from '@/layouts/app-layout';
import type { Role } from '@/types';
import { Card } from 'antd';
import RoleForm from './partials/role-form';

interface PermissionModule {
    label: string;
    permissions: string[];
}

interface PermissionModules {
    [key: string]: PermissionModule;
}

interface EditRoleProps {
    role: Role;
    permissionModules: PermissionModules;
}

export default function EditRole({ role, permissionModules }: EditRoleProps) {
    return (
        <AppLayout pageTitle={`Edit Role: ${role.name}`}>
            <Card>
                <RoleForm
                    role={role}
                    permissionModules={permissionModules}
                    isEdit
                />
            </Card>
        </AppLayout>
    );
}
