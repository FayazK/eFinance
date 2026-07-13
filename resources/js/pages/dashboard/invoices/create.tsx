import AppLayout from '@/layouts/app-layout';
import { index } from '@/routes/invoices';
import type { InvoiceTemplateOption } from '@/types';
import { ArrowLeftOutlined } from '@ant-design/icons';
import { Link } from '@inertiajs/react';
import { Button, Card } from 'antd';
import React from 'react';
import InvoiceForm from './partials/invoice-form';

interface InvoiceCreateProps {
    companies: { id: number; name: string }[];
    clients: { id: number; name: string; currency_code: string }[];
    projects: { id: number; name: string; client_id: number }[];
    templates: InvoiceTemplateOption[];
}

export default function InvoiceCreate({ companies, clients, projects, templates }: InvoiceCreateProps) {
    return (
        <AppLayout
            pageTitle="Create Invoice"
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
