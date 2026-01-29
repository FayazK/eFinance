import api from '@/lib/axios';
import { index, update } from '@/routes/expenses';
import type { Expense } from '@/types';
import { PlusOutlined, UploadOutlined } from '@ant-design/icons';
import { router } from '@inertiajs/react';
import type { UploadFile } from 'antd';
import { Button, Col, ColorPicker, DatePicker, Divider, Form, Input, InputNumber, Modal, notification, Radio, Row, Select, Space, Upload } from 'antd';
import dayjs from 'dayjs';
import { useEffect, useState } from 'react';

interface ExpenseFormProps {
    accounts?: Array<{ id: number; name: string; currency_code: string; formatted_balance: string }>;
    categories?: Array<{ id: number; name: string; color?: string }>;
    expense?: Expense;
    isEditing?: boolean;
}

export default function ExpenseForm({ accounts = [], categories: initialCategories = [], expense, isEditing = false }: ExpenseFormProps) {
    const [form] = Form.useForm();
    const [categoryForm] = Form.useForm();
    const [loading, setLoading] = useState(false);
    const [workflowType, setWorkflowType] = useState<'one-time' | 'recurring'>('one-time');
    const [selectedAccount, setSelectedAccount] = useState<{ id: number; name: string; currency_code: string } | null>(null);
    const [fileList, setFileList] = useState<UploadFile[]>([]);
    const [fetchingExchangeRate, setFetchingExchangeRate] = useState(false);
    const [categories, setCategories] = useState(initialCategories);
    const [categoryModalOpen, setCategoryModalOpen] = useState(false);
    const [creatingCategory, setCreatingCategory] = useState(false);

    const showExchangeRate = selectedAccount?.currency_code !== 'PKR';
    const showRecurringFields = workflowType === 'recurring' && !isEditing;

    // Initialize form with expense data when editing
    useEffect(() => {
        if (isEditing && expense) {
            // Set the selected account first
            const account = accounts.find((acc) => acc.id === expense.account?.id);
            if (account) {
                setSelectedAccount(account);
            }

            // Set form values
            form.setFieldsValue({
                account_id: expense.account?.id,
                category_id: expense.category?.id,
                amount: expense.amount / 100, // Convert from minor units
                expense_date: expense.expense_date ? dayjs(expense.expense_date) : undefined,
                exchange_rate: expense.exchange_rate,
                vendor: expense.vendor,
                description: expense.description,
            });

            // Initialize fileList with existing receipts
            if (expense.receipts && expense.receipts.length > 0) {
                const existingFiles: UploadFile[] = expense.receipts.map((receipt) => ({
                    uid: String(receipt.id),
                    name: receipt.name,
                    status: 'done',
                    url: receipt.url,
                    type: receipt.mime_type,
                    size: receipt.size,
                }));
                setFileList(existingFiles);
            }
        } else {
            // Set default date to today for new expenses
            form.setFieldValue('expense_date', dayjs());
        }
    }, [form, expense, isEditing, accounts]);

    // Fetch last used exchange rate when account changes and it's a foreign currency
    useEffect(() => {
        if (selectedAccount && selectedAccount.currency_code !== 'PKR') {
            fetchLastExchangeRate(selectedAccount.currency_code);
        } else {
            // Clear exchange rate for PKR accounts
            form.setFieldValue('exchange_rate', undefined);
        }
    }, [selectedAccount, form]);

    const fetchLastExchangeRate = async (currency: string) => {
        setFetchingExchangeRate(true);
        try {
            const response = await api.get(`/dashboard/expenses/last-exchange-rate/${currency}`);
            if (response.data.rate) {
                form.setFieldValue('exchange_rate', response.data.rate);
                notification.info({
                    message: 'Exchange Rate Loaded',
                    description: `Last used exchange rate for ${currency}: ${response.data.rate} PKR`,
                    duration: 3,
                });
            }
        } catch (error) {
            console.error('Failed to fetch exchange rate:', error);
        } finally {
            setFetchingExchangeRate(false);
        }
    };

    const handleAccountChange = (value: number) => {
        const account = accounts.find((acc) => acc.id === value);
        setSelectedAccount(account || null);
    };

    const handleWorkflowChange = (value: 'one-time' | 'recurring') => {
        setWorkflowType(value);
        // Clear recurring fields when switching to one-time
        if (value === 'one-time') {
            form.setFieldsValue({
                recurrence_frequency: undefined,
                recurrence_interval: undefined,
                recurrence_start_date: undefined,
                recurrence_end_date: undefined,
            });
        }
    };

    const handleCreateCategory = async () => {
        try {
            const values = await categoryForm.validateFields();
            setCreatingCategory(true);

            const colorValue = values.color?.toHexString?.() ?? values.color ?? '#6366f1';
            const response = await api.post('/dashboard/transaction-categories', {
                name: values.name,
                type: 'expense',
                color: colorValue,
            });

            const newCategory = response.data.data;
            setCategories((prev) => [...prev, { id: newCategory.id, name: newCategory.name, color: newCategory.color }]);
            form.setFieldValue('category_id', newCategory.id);
            setCategoryModalOpen(false);
            categoryForm.resetFields();

            notification.success({
                message: 'Category Created',
                description: `Category "${newCategory.name}" has been created.`,
            });
        } catch (error: unknown) {
            if (error && typeof error === 'object' && 'response' in error) {
                const axiosError = error as { response?: { data?: { message?: string } } };
                notification.error({
                    message: 'Failed to create category',
                    description: axiosError.response?.data?.message || 'An error occurred',
                });
            }
        } finally {
            setCreatingCategory(false);
        }
    };

    const onFinish = async (values: Record<string, unknown>) => {
        setLoading(true);

        // Format dates and values
        const isRecurring = workflowType === 'recurring' && !isEditing;
        const expenseDate = values.expense_date ? dayjs(values.expense_date as dayjs.Dayjs).format('YYYY-MM-DD') : undefined;

        const formattedValues = {
            ...values,
            expense_date: expenseDate,
            // For recurring expenses, use expense_date as the start date
            recurrence_start_date: isRecurring ? expenseDate : undefined,
            recurrence_end_date: values.recurrence_end_date ? dayjs(values.recurrence_end_date as dayjs.Dayjs).format('YYYY-MM-DD') : undefined,
            is_recurring: isRecurring ? '1' : '0', // Use "1"/"0" for FormData boolean compatibility
            currency_code: selectedAccount?.currency_code,
        };

        if (isEditing && expense) {
            // Update existing expense
            router.put(update.url(expense.id), formattedValues, {
                onSuccess: () => {
                    notification.success({
                        message: 'Expense updated successfully!',
                    });
                },
                onError: (errors) => {
                    const formErrors = Object.keys(errors).map((key) => ({
                        name: key,
                        errors: [errors[key]],
                    }));
                    form.setFields(formErrors);
                    notification.error({
                        message: 'Validation Error',
                        description: Object.values(errors)[0] as string,
                    });
                },
                onFinish: () => {
                    setLoading(false);
                },
            });
        } else {
            // Create FormData for file upload (new expense)
            const formData = new FormData();
            Object.keys(formattedValues).forEach((key) => {
                const value = formattedValues[key as keyof typeof formattedValues];
                if (value !== undefined && value !== null) {
                    formData.append(key, String(value));
                }
            });

            // Append receipts if present
            fileList.forEach((file) => {
                if (file.originFileObj) {
                    formData.append('receipts[]', file.originFileObj);
                }
            });

            // Use Inertia router for proper redirect and error handling
            router.post('/dashboard/expenses', formData, {
                forceFormData: true,
                onSuccess: () => {
                    notification.success({
                        message: isRecurring ? 'Recurring expense template created!' : 'Expense saved as draft!',
                    });
                },
                onError: (errors) => {
                    // Handle validation errors
                    const formErrors = Object.keys(errors).map((key) => ({
                        name: key,
                        errors: [errors[key]],
                    }));
                    form.setFields(formErrors);
                    notification.error({
                        message: 'Validation Error',
                        description: Object.values(errors)[0] as string,
                    });
                },
                onFinish: () => {
                    setLoading(false);
                },
            });
        }
    };

    return (
        <>
        <Form form={form} layout="vertical" onFinish={onFinish}>
            {/* Workflow Selector - hidden when editing */}
            {!isEditing && (
                <>
                    <Form.Item label="Expense Type">
                        <Radio.Group
                            value={workflowType}
                            onChange={(e) => handleWorkflowChange(e.target.value as 'one-time' | 'recurring')}
                            buttonStyle="solid"
                        >
                            <Radio.Button value="one-time">One-Time Expense</Radio.Button>
                            <Radio.Button value="recurring">Recurring Expense</Radio.Button>
                        </Radio.Group>
                    </Form.Item>

                    <Divider />
                </>
            )}

            {/* Core Fields */}
            <Row gutter={16}>
                <Col span={12}>
                    <Form.Item label="Account" name="account_id" rules={[{ required: true, message: 'Please select an account!' }]}>
                        <Select
                            placeholder="Select an account"
                            showSearch
                            onChange={handleAccountChange}
                            filterOption={(input, option) => (option?.label ?? '').toLowerCase().includes(input.toLowerCase())}
                            options={accounts.map((account) => ({
                                label: `${account.name} (${account.currency_code}) - ${account.formatted_balance}`,
                                value: account.id,
                            }))}
                        />
                    </Form.Item>
                </Col>
                <Col span={12}>
                    <Form.Item label="Category" name="category_id">
                        <Select
                            placeholder="Select a category (optional)"
                            allowClear
                            showSearch
                            filterOption={(input, option) => (option?.label ?? '').toLowerCase().includes(input.toLowerCase())}
                            options={categories.map((category) => ({
                                label: category.name,
                                value: category.id,
                            }))}
                            popupRender={(menu) => (
                                <>
                                    {menu}
                                    <Divider style={{ margin: '8px 0' }} />
                                    <Button
                                        type="text"
                                        icon={<PlusOutlined />}
                                        onClick={() => setCategoryModalOpen(true)}
                                        style={{ width: '100%', textAlign: 'left' }}
                                    >
                                        Create new category
                                    </Button>
                                </>
                            )}
                        />
                    </Form.Item>
                </Col>
            </Row>

            <Row gutter={16}>
                <Col span={12}>
                    <Form.Item
                        label="Amount"
                        name="amount"
                        rules={[
                            { required: true, message: 'Please input the amount!' },
                            {
                                type: 'number',
                                min: 0.01,
                                message: 'Amount must be greater than 0',
                            },
                        ]}
                    >
                        <InputNumber
                            style={{ width: '100%' }}
                            placeholder="0.00"
                            precision={2}
                            step={0.01}
                            min={0.01}
                            addonAfter={selectedAccount?.currency_code || 'Currency'}
                        />
                    </Form.Item>
                </Col>
                <Col span={12}>
                    <Form.Item
                        label={workflowType === 'recurring' ? 'Start Date' : 'Expense Date'}
                        name="expense_date"
                        rules={[{ required: true, message: 'Please select a date!' }]}
                    >
                        <DatePicker style={{ width: '100%' }} format="YYYY-MM-DD" />
                    </Form.Item>
                </Col>
            </Row>

            {/* Exchange Rate (International) */}
            {showExchangeRate && (
                <Form.Item
                    label="Exchange Rate (to PKR)"
                    name="exchange_rate"
                    rules={[
                        { required: true, message: 'Please input the exchange rate!' },
                        {
                            type: 'number',
                            min: 0.01,
                            message: 'Exchange rate must be greater than 0',
                        },
                    ]}
                    extra={`1 ${selectedAccount?.currency_code} = X PKR`}
                >
                    <InputNumber
                        style={{ width: '100%' }}
                        placeholder="0.00"
                        precision={4}
                        step={0.01}
                        min={0.01}
                        loading={fetchingExchangeRate}
                        addonAfter="PKR"
                    />
                </Form.Item>
            )}

            <Row gutter={16}>
                <Col span={12}>
                    <Form.Item label="Vendor" name="vendor">
                        <Input placeholder="Enter vendor name (optional)" />
                    </Form.Item>
                </Col>
            </Row>

            <Form.Item label="Description" name="description">
                <Input.TextArea rows={4} placeholder="Enter expense description (optional)" />
            </Form.Item>

            {/* Recurring Fields */}
            {showRecurringFields && (
                <>
                    <Divider orientation="left">Recurring Settings</Divider>
                    <Row gutter={16}>
                        <Col span={12}>
                            <Form.Item
                                label="Frequency"
                                name="recurrence_frequency"
                                rules={[{ required: true, message: 'Please select a frequency!' }]}
                            >
                                <Select
                                    placeholder="Select frequency"
                                    options={[
                                        { label: 'Monthly', value: 'monthly' },
                                        { label: 'Quarterly', value: 'quarterly' },
                                        { label: 'Yearly', value: 'yearly' },
                                    ]}
                                />
                            </Form.Item>
                        </Col>
                        <Col span={12}>
                            <Form.Item label="Interval" name="recurrence_interval" initialValue={1}>
                                <InputNumber style={{ width: '100%' }} placeholder="1" min={1} max={12} addonAfter="period(s)" />
                            </Form.Item>
                        </Col>
                    </Row>
                    <Row gutter={16}>
                        <Col span={12}>
                            <Form.Item
                                label="End Date (Optional)"
                                name="recurrence_end_date"
                                rules={[
                                    {
                                        validator: (_, value) => {
                                            const startDate = form.getFieldValue('expense_date');
                                            if (value && startDate && dayjs(value).isBefore(dayjs(startDate))) {
                                                return Promise.reject('End date must be after start date');
                                            }
                                            return Promise.resolve();
                                        },
                                    },
                                ]}
                            >
                                <DatePicker style={{ width: '100%' }} format="YYYY-MM-DD" />
                            </Form.Item>
                        </Col>
                    </Row>
                </>
            )}

            {/* Receipt Upload (One-time only) */}
            {!showRecurringFields && (
                <>
                    <Divider orientation="left">Receipts</Divider>
                    <Form.Item label="Upload Receipts" extra="Upload receipt images or PDFs (max 5MB each)">
                        <Upload
                            listType="picture"
                            fileList={fileList}
                            onChange={({ fileList: newFileList }) => setFileList(newFileList)}
                            beforeUpload={() => false}
                            multiple
                            accept="image/jpeg,image/png,image/webp,application/pdf"
                            maxCount={10}
                            onPreview={(file) => {
                                if (file.url) {
                                    window.open(file.url, '_blank');
                                }
                            }}
                        >
                            <Button icon={<UploadOutlined />}>Select Files</Button>
                        </Upload>
                    </Form.Item>
                </>
            )}

            <Form.Item>
                <Space>
                    <Button type="primary" htmlType="submit" loading={loading}>
                        {isEditing
                            ? 'Update Expense'
                            : workflowType === 'recurring'
                              ? 'Create Recurring Template'
                              : 'Save as Draft'}
                    </Button>
                    <Button onClick={() => router.visit(index.url())}>Cancel</Button>
                </Space>
            </Form.Item>
        </Form>

        <Modal
            title="Create New Category"
            open={categoryModalOpen}
            onOk={handleCreateCategory}
            onCancel={() => {
                setCategoryModalOpen(false);
                categoryForm.resetFields();
            }}
            confirmLoading={creatingCategory}
            okText="Create"
        >
            <Form form={categoryForm} layout="vertical">
                <Form.Item
                    label="Category Name"
                    name="name"
                    rules={[{ required: true, message: 'Please enter a category name' }]}
                >
                    <Input placeholder="e.g., Office Supplies, Marketing" />
                </Form.Item>
                <Form.Item label="Color" name="color" initialValue="#6366f1">
                    <ColorPicker showText />
                </Form.Item>
            </Form>
        </Modal>
        </>
    );
}
