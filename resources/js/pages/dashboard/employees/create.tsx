import AppLayout from '@/layouts/app-layout';
import EmployeeForm from './partials/employee-form';
import { Card } from 'antd';

export default function CreateEmployee() {
    return (
        <AppLayout pageTitle="Create New Employee">
            <Card>
                <EmployeeForm />
            </Card>
        </AppLayout>
    );
}
