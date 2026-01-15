import AppLayout from '@/layouts/app-layout';
import { Card } from 'antd';
import CompanyForm from './partials/company-form';

export default function CreateCompany() {
    return (
        <AppLayout pageTitle="Create New Company">
            <Card>
                <CompanyForm />
            </Card>
        </AppLayout>
    );
}
