import AppLayout from '@/layouts/app-layout';
import { Contact } from '@/types';
import { Card } from 'antd';
import ContactForm from './partials/contact-form';

interface EditContactProps {
    contact: Contact;
}

export default function EditContact({ contact }: EditContactProps) {
    return (
        <AppLayout pageTitle={contact ? `Edit Contact: ${contact.full_name}` : 'Edit Contact'}>
            <Card>
                <ContactForm contact={contact} isEdit />
            </Card>
        </AppLayout>
    );
}
