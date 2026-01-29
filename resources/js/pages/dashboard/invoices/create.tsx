import AppLayout from '@/layouts/app-layout';
import { index } from '@/routes/invoices';
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

interface InvoiceCreateProps {
    companies: { id: number; name: string }[];
    clients: { id: number; name: string; currency_code: string }[];
    projects: { id: number; name: string; client_id: number }[];
    templates: TemplateOption[];
}

export default function InvoiceCreate({ companies, clients, projects, templates }: InvoiceCreateProps) {
    return (
        <AppLayout
            pageTitle="Create Invoice"
            breadcrumb={[
                { title: 'Invoices', href: index.url() },
                { title: 'Create', href: '#' },
            ]}
            actions={
                <Link href={index.url()}>
                    <Button icon={<ArrowLeftOutlined />}>Back to Invoices</Button>
                </Link>
            }
        >
            <Card>
                <InvoiceForm companies={companies} clients={clients} projects={projects} templates={templates} />
            </Card>
        </AppLayout>
    );
}

InvoiceCreate.layout = (page: React.ReactNode) => page;
