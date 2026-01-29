import AppLayout from '@/layouts/app-layout';
import { index } from '@/routes/invoices';
import type { Invoice } from '@/types';
import { ArrowLeftOutlined } from '@ant-design/icons';
import { Link } from '@inertiajs/react';
import { Button, Card } from 'antd';
import React from 'react';
import InvoiceForm from './partials/invoice-form';

interface TemplateOption {
    value: string;
    label: string;
    description: string;
}

interface InvoiceEditProps {
    companies: { id: number; name: string }[];
    invoice: Invoice;
    clients: { id: number; name: string; currency_code: string }[];
    projects: { id: number; name: string; client_id: number }[];
    templates: TemplateOption[];
}

export default function InvoiceEdit({ companies, invoice, clients, projects, templates }: InvoiceEditProps) {
    return (
        <AppLayout
            pageTitle={`Edit Invoice ${invoice.invoice_number}`}
            breadcrumb={[
                { title: 'Invoices', href: index.url() },
                { title: invoice.invoice_number, href: '#' },
                { title: 'Edit', href: '#' },
            ]}
            actions={
                <Link href={index.url()}>
                    <Button icon={<ArrowLeftOutlined />}>Back to Invoices</Button>
                </Link>
            }
        >
            <Card>
                <InvoiceForm companies={companies} clients={clients} projects={projects} templates={templates} invoice={invoice} isEditing={true} />
            </Card>
        </AppLayout>
    );
}

InvoiceEdit.layout = (page: React.ReactNode) => page;
