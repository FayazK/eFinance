import AppLayout from '@/layouts/app-layout';
import ClientForm from './partials/client-form';
import { Card } from 'antd';
import { Client } from '@/types';

interface EditClientProps {
    client: Client;
}

export default function EditClient({ client }: EditClientProps) {
    return (
        <AppLayout pageTitle={client ? `Edit Client: ${client.name}` : 'Edit Client'}>
            <Card>
                <ClientForm client={client} isEdit />
            </Card>
        </AppLayout>
    );
}
