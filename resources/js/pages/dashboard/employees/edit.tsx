import AppLayout from '@/layouts/app-layout';
import { Employee } from '@/types';
import { Card } from 'antd';
import EmployeeForm from './partials/employee-form';

interface EditEmployeeProps {
    employee: Employee;
}

export default function EditEmployee({ employee }: EditEmployeeProps) {
    return (
        <AppLayout pageTitle="Edit Employee">
            <Card>
                <EmployeeForm employee={employee} isEdit={true} />
            </Card>
        </AppLayout>
    );
}
