import AppLayout from '@/layouts/app-layout';
import { Company } from '@/types';
import { Card } from 'antd';
import CompanyForm from './partials/company-form';

interface EditCompanyProps {
    company: Company;
}

export default function EditCompany({ company }: EditCompanyProps) {
    return (
        <AppLayout pageTitle={company ? `Edit Company: ${company.name}` : 'Edit Company'}>
            <Card>
                <CompanyForm company={company} isEdit />
            </Card>
        </AppLayout>
    );
}
