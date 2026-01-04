import AppLayout from '@/layouts/app-layout';
import { Card } from 'antd';
import ContactForm from './partials/contact-form';

export default function CreateContact() {
    return (
        <AppLayout pageTitle="Create New Contact">
            <Card>
                <ContactForm />
            </Card>
        </AppLayout>
    );
}
