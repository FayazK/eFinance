import AppLayout from '@/layouts/app-layout';
import { Card } from 'antd';
import RoleForm from './partials/role-form';

interface PermissionModule {
    label: string;
    permissions: string[];
}

interface PermissionModules {
    [key: string]: PermissionModule;
}

interface CreateRoleProps {
    permissionModules: PermissionModules;
}

export default function CreateRole({ permissionModules }: CreateRoleProps) {
    return (
        <AppLayout pageTitle="Create New Role">
            <Card>
                <RoleForm permissionModules={permissionModules} />
            </Card>
        </AppLayout>
    );
}
