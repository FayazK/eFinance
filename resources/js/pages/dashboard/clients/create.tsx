import AppLayout from '@/layouts/app-layout';
import ClientForm from './partials/client-form';
import { Card } from 'antd';

export default function CreateClient() {
    return (
        <AppLayout pageTitle="Create New Client">
            <Card>
                <ClientForm />
            </Card>
        </AppLayout>
    );
}
