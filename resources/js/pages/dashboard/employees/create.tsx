import AppLayout from '@/layouts/app-layout';
import { Card } from 'antd';
import EmployeeForm from './partials/employee-form';

export default function CreateEmployee() {
    return (
        <AppLayout pageTitle="Create New Employee">
            <Card>
                <EmployeeForm />
            </Card>
        </AppLayout>
    );
}
