import AppLayout from '@/layouts/app-layout';
import { Card } from 'antd';
import ClientForm from './partials/client-form';

export default function CreateClient() {
    return (
        <AppLayout pageTitle="Create New Client">
            <Card>
                <ClientForm />
            </Card>
        </AppLayout>
    );
}
