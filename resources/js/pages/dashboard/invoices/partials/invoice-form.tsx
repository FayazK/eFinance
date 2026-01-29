import api from '@/lib/axios';
import { index } from '@/routes/invoices';
import { useInvoiceBuilderStore } from '@/stores/invoice-builder-store';
import type { Invoice, InvoiceItem, InvoiceTemplateOption } from '@/types';
import { router } from '@inertiajs/react';
import { Alert, Button, Card, Col, DatePicker, Form, Input, InputNumber, notification, Row, Select, Space } from 'antd';
import dayjs from 'dayjs';
import { useEffect, useState } from 'react';
import InvoiceLineItemsTable from './invoice-line-items-table';

interface Project {
    id: number;
    name: string;
    client_id: number;
}

interface Company {
    id: number;
    name: string;
}

interface InvoiceFormProps {
    companies: Company[];
    clients: { id: number; name: string; currency_code: string }[];
    projects: Project[];
    templates: InvoiceTemplateOption[];
    invoice?: Invoice;
    isEditing?: boolean;
}

export default function InvoiceForm({ companies, clients, projects, templates, invoice, isEditing = false }: InvoiceFormProps) {
    const [form] = Form.useForm();
    const [loading, setLoading] = useState(false);
    const [clientId, setClientId] = useState<number | undefined>(invoice?.client_id);
    const [items, setItems] = useState<InvoiceItem[]>(invoice?.items || []);
    const [taxRate, setTaxRate] = useState<number>(0);

    const { saveDraft, getDraft, clearDraft } = useInvoiceBuilderStore();
    const draftId = isEditing ? `edit-${invoice?.id}` : 'new';

    // Get selected client
    const selectedClient = clients.find((c) => c.id === clientId);

    // Filter projects by selected client
    const filteredProjects = projects.filter((p) => p.client_id === clientId);

    // Calculate totals
    const subtotal = items.reduce((sum, item) => sum + (item.amount || 0), 0);
    const taxAmount = subtotal * (taxRate / 100);
    const total = subtotal + taxAmount;

    // Load draft on mount
    useEffect(() => {
        if (!isEditing) {
            const draft = getDraft(draftId);
            if (draft) {
                form.setFieldsValue({
                    company_id: draft.company_id,
                    template: draft.template,
                    client_id: draft.client_id,
                    project_id: draft.project_id,
                    issue_date: draft.issue_date ? dayjs(draft.issue_date) : undefined,
                    due_date: draft.due_date ? dayjs(draft.due_date) : undefined,
                    notes: draft.notes,
                    terms: draft.terms,
                    client_notes: draft.client_notes,
                });
                setClientId(draft.client_id);
                setItems(draft.items || []);
                setTaxRate(draft.tax_rate || 0);
            }
        }
    }, []);

    // Set default dates
    useEffect(() => {
        if (!invoice && !getDraft(draftId)) {
            form.setFieldValue('issue_date', dayjs());
            form.setFieldValue('due_date', dayjs().add(30, 'days'));
        }
    }, [form, invoice, draftId, getDraft]);

    // Auto-save draft every 5 seconds
    useEffect(() => {
        if (isEditing) return; // Don't auto-save when editing

        const interval = setInterval(() => {
            const values = form.getFieldsValue();
            if (values.client_id || items.length > 0) {
                saveDraft(draftId, {
                    company_id: values.company_id,
                    template: values.template,
                    client_id: values.client_id,
                    project_id: values.project_id,
                    currency_code: selectedClient?.currency_code || 'USD',
                    issue_date: values.issue_date ? dayjs(values.issue_date).format('YYYY-MM-DD') : undefined,
                    due_date: values.due_date ? dayjs(values.due_date).format('YYYY-MM-DD') : undefined,
                    notes: values.notes,
                    terms: values.terms,
                    client_notes: values.client_notes,
                    items: items,
                    tax_rate: taxRate,
                });
            }
        }, 5000);

        return () => clearInterval(interval);
    }, [form, items, taxRate, clientId, selectedClient, saveDraft, draftId, isEditing]);

    const handleClientChange = (value: number) => {
        setClientId(value);
        // Clear project selection when client changes
        form.setFieldValue('project_id', undefined);
    };

    const onFinish = async (values: Record<string, unknown>) => {
        if (items.length === 0) {
            notification.error({
                message: 'Validation Error',
                description: 'Please add at least one line item to the invoice.',
            });
            return;
        }

        setLoading(true);

        const formattedValues = {
            ...values,
            issue_date: values.issue_date ? dayjs(values.issue_date as dayjs.Dayjs).format('YYYY-MM-DD') : undefined,
            due_date: values.due_date ? dayjs(values.due_date as dayjs.Dayjs).format('YYYY-MM-DD') : undefined,
            currency_code: selectedClient?.currency_code || 'USD',
            tax_rate: taxRate,
            items: items.map((item, index) => ({
                description: item.description,
                quantity: item.quantity,
                unit: item.unit,
                unit_price: item.unit_price,
                sort_order: index,
            })),
        };

        try {
            const url = isEditing && invoice ? `/dashboard/invoices/${invoice.id}` : '/dashboard/invoices';
            const method = isEditing ? 'put' : 'post';
            const response = await api[method](url, formattedValues);

            notification.success({
                message: response.data.message || `Invoice ${isEditing ? 'updated' : 'created'} successfully`,
            });

            // Clear draft on successful submission
            if (!isEditing) {
                clearDraft(draftId);
            }

            router.visit(index.url());
        } catch (error: unknown) {
            const err = error as {
                response?: {
                    status: number;
                    data: { errors: { [key: string]: string[] }; message: string };
                };
            };
            if (err.response && err.response.status === 422) {
                const validationErrors = err.response.data.errors;
                const formErrors = Object.keys(validationErrors).map((key) => ({
                    name: key,
                    errors: validationErrors[key],
                }));
                form.setFields(formErrors);
                notification.error({
                    message: 'Validation Error',
                    description: err.response.data.message,
                });
            } else {
                notification.error({
                    message: 'Error',
                    description: 'An unexpected error occurred.',
                });
            }
        } finally {
            setLoading(false);
        }
    };

    // Convert invoice dates to dayjs objects for the form
    const initialValues = invoice
        ? {
              ...invoice,
              issue_date: invoice.issue_date ? dayjs(invoice.issue_date) : undefined,
              due_date: invoice.due_date ? dayjs(invoice.due_date) : undefined,
          }
        : { template: 'modern' };

    return (
        <Form form={form} layout="vertical" onFinish={onFinish} initialValues={initialValues}>
            <Card title="Invoice Details" style={{ marginBottom: 16 }}>
                <Row gutter={16}>
                    <Col span={12}>
                        <Form.Item label="From Company" name="company_id" rules={[{ required: true, message: 'Please select a company!' }]}>
                            <Select
                                placeholder="Select company"
                                showSearch
                                filterOption={(input, option) => (option?.label ?? '').toLowerCase().includes(input.toLowerCase())}
                                options={companies.map((company) => ({
                                    label: company.name,
                                    value: company.id,
                                }))}
                            />
                        </Form.Item>
                    </Col>
                    <Col span={12}>
                        <Form.Item label="Invoice Template" name="template">
                            <Select
                                placeholder="Select template"
                                options={templates.map((template) => ({
                                    label: template.label,
                                    value: template.value,
                                }))}
                            />
                        </Form.Item>
                    </Col>
                </Row>

                <Row gutter={16}>
                    <Col span={12}>
                        <Form.Item label="Client" name="client_id" rules={[{ required: true, message: 'Please select a client!' }]}>
                            <Select
                                placeholder="Select client"
                                showSearch
                                onChange={handleClientChange}
                                filterOption={(input, option) => (option?.label ?? '').toLowerCase().includes(input.toLowerCase())}
                                options={clients.map((client) => ({
                                    label: `${client.name} (${client.currency_code})`,
                                    value: client.id,
                                }))}
                            />
                        </Form.Item>
                    </Col>
                    <Col span={12}>
                        <Form.Item label="Project" name="project_id">
                            <Select
                                placeholder="Select project (optional)"
                                showSearch
                                allowClear
                                disabled={!clientId}
                                filterOption={(input, option) => (option?.label ?? '').toLowerCase().includes(input.toLowerCase())}
                                options={filteredProjects.map((project) => ({
                                    label: project.name,
                                    value: project.id,
                                }))}
                            />
                        </Form.Item>
                    </Col>
                </Row>

                {selectedClient && (
                    <Alert
                        message={`Invoice Currency: ${selectedClient.currency_code}`}
                        description="The invoice will be created in the client's default currency."
                        type="info"
                        showIcon
                        style={{ marginBottom: 16 }}
                    />
                )}

                <Row gutter={16}>
                    <Col span={12}>
                        <Form.Item label="Issue Date" name="issue_date" rules={[{ required: true, message: 'Please select issue date!' }]}>
                            <DatePicker style={{ width: '100%' }} format="YYYY-MM-DD" />
                        </Form.Item>
                    </Col>
                    <Col span={12}>
                        <Form.Item label="Due Date" name="due_date" rules={[{ required: true, message: 'Please select due date!' }]}>
                            <DatePicker style={{ width: '100%' }} format="YYYY-MM-DD" />
                        </Form.Item>
                    </Col>
                </Row>
            </Card>

            <Card title="Line Items" style={{ marginBottom: 16 }}>
                <InvoiceLineItemsTable items={items} onChange={setItems} currency={selectedClient?.currency_code || 'USD'} />

                <Row justify="end" style={{ marginTop: 16 }}>
                    <Col span={8}>
                        <div style={{ display: 'flex', justifyContent: 'space-between', marginBottom: 8 }}>
                            <strong>Subtotal:</strong>
                            <span>
                                {selectedClient?.currency_code || 'USD'} {subtotal.toFixed(2)}
                            </span>
                        </div>

                        <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: 8 }}>
                            <strong>Tax Rate:</strong>
                            <Space.Compact>
                                <InputNumber
                                    value={taxRate}
                                    onChange={(value) => setTaxRate(value || 0)}
                                    min={0}
                                    max={100}
                                    precision={2}
                                    step={0.5}
                                    style={{ width: 96 }}
                                />
                                <Input value="%" disabled style={{ width: 24, textAlign: 'center', padding: 0 }} />
                            </Space.Compact>
                        </div>

                        <div style={{ display: 'flex', justifyContent: 'space-between', marginBottom: 8 }}>
                            <strong>Tax Amount:</strong>
                            <span>
                                {selectedClient?.currency_code || 'USD'} {taxAmount.toFixed(2)}
                            </span>
                        </div>

                        <div
                            style={{
                                display: 'flex',
                                justifyContent: 'space-between',
                                fontSize: 18,
                                fontWeight: 'bold',
                                borderTop: '2px solid #d9d9d9',
                                paddingTop: 8,
                            }}
                        >
                            <span>Total:</span>
                            <span>
                                {selectedClient?.currency_code || 'USD'} {total.toFixed(2)}
                            </span>
                        </div>
                    </Col>
                </Row>
            </Card>

            <Card title="Additional Information" style={{ marginBottom: 16 }}>
                <Form.Item label="Notes (Internal)" name="notes">
                    <Input.TextArea rows={3} placeholder="Internal notes (not visible to client)" />
                </Form.Item>

                <Form.Item label="Terms & Conditions" name="terms">
                    <Input.TextArea rows={3} placeholder="Payment terms and conditions" />
                </Form.Item>

                <Form.Item label="Notes for Client" name="client_notes">
                    <Input.TextArea rows={3} placeholder="Notes that will appear on the invoice" />
                </Form.Item>
            </Card>

            <Form.Item>
                <Button type="primary" htmlType="submit" loading={loading}>
                    {isEditing ? 'Update Invoice' : 'Create Invoice'}
                </Button>
            </Form.Item>
        </Form>
    );
}
