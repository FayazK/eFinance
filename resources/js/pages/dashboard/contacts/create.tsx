import AppLayout from '@/layouts/app-layout';
import ContactForm from './partials/contact-form';
import { Card } from 'antd';

export default function CreateContact() {
    return (
        <AppLayout pageTitle="Create New Contact">
            <Card>
                <ContactForm />
            </Card>
        </AppLayout>
    );
}
