import React from 'react';
import { Card, Button } from 'antd';
import { ArrowLeftOutlined } from '@ant-design/icons';
import { Link } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import InvoiceForm from './partials/invoice-form';
import { index } from '@/routes/invoices';

interface InvoiceCreateProps {
    clients: { id: number; name: string; currency_code: string }[];
    projects: { id: number; name: string; client_id: number }[];
}

export default function InvoiceCreate({ clients, projects }: InvoiceCreateProps) {
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
                <InvoiceForm clients={clients} projects={projects} />
            </Card>
        </AppLayout>
    );
}

InvoiceCreate.layout = (page: React.ReactNode) => page;
