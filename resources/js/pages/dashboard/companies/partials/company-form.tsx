import api from '@/lib/axios';
import { index } from '@/routes/companies';
import { Company } from '@/types';
import { InboxOutlined } from '@ant-design/icons';
import { router } from '@inertiajs/react';
import { Button, Col, Form, Input, notification, Row, Space, Upload } from 'antd';
import type { UploadChangeParam, UploadFile } from 'antd/es/upload';
import { useEffect, useState } from 'react';

interface CompanyFormProps {
    company?: Company;
    isEdit?: boolean;
}

export default function CompanyForm({ company, isEdit = false }: CompanyFormProps) {
    const [form] = Form.useForm();
    const [loading, setLoading] = useState(false);
    const [logoFile, setLogoFile] = useState<UploadFile | null>(null);
    const [previewUrl, setPreviewUrl] = useState<string | null>(company?.logo_url || null);
    const [deleteLogo, setDeleteLogo] = useState(false);

    useEffect(() => {
        form.setFieldsValue({
            name: company?.name || '',
            address: company?.address || '',
            phone: company?.phone || '',
            email: company?.email || '',
            tax_id: company?.tax_id || '',
            vat_number: company?.vat_number || '',
        });
        setPreviewUrl(company?.logo_url || null);
    }, [company, form]);

    const handleLogoChange = (info: UploadChangeParam<UploadFile>) => {
        const file = info.fileList[0];
        if (file && file.originFileObj) {
            setLogoFile(file);
            const reader = new FileReader();
            reader.onload = (e) => setPreviewUrl(e.target?.result as string);
            reader.readAsDataURL(file.originFileObj);
            setDeleteLogo(false);
        }
    };

    const handleRemoveLogo = () => {
        setLogoFile(null);
        setPreviewUrl(null);
        setDeleteLogo(true);
    };

    const onFinish = async (values: Record<string, unknown>) => {
        setLoading(true);

        const formData = new FormData();

        Object.keys(values).forEach((key) => {
            if (values[key] !== undefined && values[key] !== null && values[key] !== '') {
                formData.append(key, String(values[key]));
            }
        });

        if (logoFile?.originFileObj) {
            formData.append('logo', logoFile.originFileObj);
        }

        if (deleteLogo) {
            formData.append('delete_logo', '1');
        }

        try {
            const url = isEdit ? `/dashboard/companies/${company!.id}` : '/dashboard/companies';

            const response = await api.post(url, formData, {
                headers: { 'Content-Type': 'multipart/form-data' },
                params: isEdit ? { _method: 'PUT' } : undefined,
            });

            notification.success({
                message: response.data.message || `Company ${isEdit ? 'updated' : 'created'} successfully`,
            });
            router.visit(index.url());
        } catch (error: unknown) {
            const err = error as { response?: { status: number; data: { errors: { [key: string]: string[] }; message: string } } };
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

    return (
        <Form form={form} layout="vertical" onFinish={onFinish}>
            <Row gutter={16}>
                <Col span={12}>
                    <Form.Item label="Company Name" name="name" rules={[{ required: true, message: 'Please input the company name!' }]}>
                        <Input />
                    </Form.Item>
                </Col>
                <Col span={12}>
                    <Form.Item label="Email" name="email" rules={[{ type: 'email', message: 'Please input a valid email!' }]}>
                        <Input type="email" />
                    </Form.Item>
                </Col>
            </Row>

            <Row gutter={16}>
                <Col span={12}>
                    <Form.Item label="Phone" name="phone">
                        <Input />
                    </Form.Item>
                </Col>
                <Col span={12}>
                    <Row gutter={16}>
                        <Col span={12}>
                            <Form.Item label="Tax ID" name="tax_id">
                                <Input />
                            </Form.Item>
                        </Col>
                        <Col span={12}>
                            <Form.Item label="VAT Number" name="vat_number">
                                <Input />
                            </Form.Item>
                        </Col>
                    </Row>
                </Col>
            </Row>

            <Form.Item label="Address" name="address">
                <Input.TextArea rows={3} />
            </Form.Item>

            <Form.Item label="Logo">
                <Space direction="vertical" style={{ width: '100%' }}>
                    {previewUrl && (
                        <Space align="start">
                            <img
                                src={previewUrl}
                                alt="Logo preview"
                                style={{
                                    width: 100,
                                    height: 100,
                                    objectFit: 'contain',
                                    border: '1px solid #d9d9d9',
                                    borderRadius: 4,
                                }}
                            />
                            <Button danger onClick={handleRemoveLogo}>
                                Remove Logo
                            </Button>
                        </Space>
                    )}

                    {!previewUrl && (
                        <Upload.Dragger
                            name="logo"
                            listType="picture"
                            maxCount={1}
                            beforeUpload={() => false}
                            onChange={handleLogoChange}
                            accept="image/jpeg,image/png,image/jpg,image/svg+xml"
                            showUploadList={false}
                        >
                            <p className="ant-upload-drag-icon">
                                <InboxOutlined />
                            </p>
                            <p className="ant-upload-text">Click or drag logo to upload</p>
                            <p className="ant-upload-hint">Supports JPEG, PNG, JPG, SVG up to 2MB</p>
                        </Upload.Dragger>
                    )}
                </Space>
            </Form.Item>

            <Form.Item>
                <Button type="primary" htmlType="submit" loading={loading}>
                    {isEdit ? 'Update Company' : 'Create Company'}
                </Button>
            </Form.Item>
        </Form>
    );
}
