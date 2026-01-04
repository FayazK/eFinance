import type { InvoiceItem } from '@/types';
import { DeleteOutlined, PlusOutlined } from '@ant-design/icons';
import { Button, Input, InputNumber, Popconfirm, Select, Table } from 'antd';
import { useState } from 'react';

interface InvoiceLineItemsTableProps {
    items: InvoiceItem[];
    onChange: (items: InvoiceItem[]) => void;
    currency: string;
}

const UNIT_OPTIONS = [
    { value: 'hour', label: 'Hour' },
    { value: 'day', label: 'Day' },
    { value: 'unit', label: 'Unit' },
    { value: 'item', label: 'Item' },
];

export default function InvoiceLineItemsTable({ items, onChange, currency }: InvoiceLineItemsTableProps) {
    const [editingKey, setEditingKey] = useState<number | null>(null);

    const addNewItem = () => {
        const newItem: InvoiceItem = {
            id: Date.now(), // Temporary ID for new items
            description: '',
            quantity: 1,
            unit: 'unit',
            unit_price: 0,
            amount: 0,
            formatted_unit_price: `${currency} 0.00`,
            formatted_amount: `${currency} 0.00`,
            sort_order: items.length,
        };
        onChange([...items, newItem]);
        setEditingKey(newItem.id);
    };

    const removeItem = (id: number) => {
        onChange(items.filter((item) => item.id !== id));
    };

    const updateItem = (id: number, field: keyof InvoiceItem, value: string | number) => {
        const updatedItems = items.map((item) => {
            if (item.id === id) {
                const updated = { ...item, [field]: value };

                // Auto-calculate amount when quantity or unit_price changes
                if (field === 'quantity' || field === 'unit_price') {
                    const quantity = field === 'quantity' ? (value as number) : item.quantity;
                    const unitPrice = field === 'unit_price' ? (value as number) : item.unit_price;
                    updated.amount = quantity * unitPrice;
                    updated.formatted_amount = `${currency} ${updated.amount.toFixed(2)}`;
                }

                if (field === 'unit_price') {
                    updated.formatted_unit_price = `${currency} ${(value as number).toFixed(2)}`;
                }

                return updated;
            }
            return item;
        });
        onChange(updatedItems);
    };

    const columns = [
        {
            title: 'Description',
            dataIndex: 'description',
            key: 'description',
            width: '35%',
            render: (text: string, record: InvoiceItem) => (
                <Input value={text} onChange={(e) => updateItem(record.id, 'description', e.target.value)} placeholder="Enter description" />
            ),
        },
        {
            title: 'Quantity',
            dataIndex: 'quantity',
            key: 'quantity',
            width: '12%',
            render: (value: number, record: InvoiceItem) => (
                <InputNumber
                    value={value}
                    onChange={(val) => updateItem(record.id, 'quantity', val || 1)}
                    min={1}
                    precision={0}
                    style={{ width: '100%' }}
                />
            ),
        },
        {
            title: 'Unit',
            dataIndex: 'unit',
            key: 'unit',
            width: '12%',
            render: (value: string, record: InvoiceItem) => (
                <Select value={value} onChange={(val) => updateItem(record.id, 'unit', val)} options={UNIT_OPTIONS} style={{ width: '100%' }} />
            ),
        },
        {
            title: `Rate (${currency})`,
            dataIndex: 'unit_price',
            key: 'unit_price',
            width: '15%',
            render: (value: number, record: InvoiceItem) => (
                <InputNumber
                    value={value}
                    onChange={(val) => updateItem(record.id, 'unit_price', val || 0)}
                    min={0}
                    precision={2}
                    step={0.01}
                    style={{ width: '100%' }}
                />
            ),
        },
        {
            title: `Amount (${currency})`,
            dataIndex: 'amount',
            key: 'amount',
            width: '15%',
            render: (value: number) => <span style={{ fontWeight: 500 }}>{value.toFixed(2)}</span>,
        },
        {
            title: 'Action',
            key: 'action',
            width: '11%',
            render: (_: unknown, record: InvoiceItem) => (
                <Popconfirm title="Delete this item?" onConfirm={() => removeItem(record.id)} okText="Yes" cancelText="No">
                    <Button type="text" danger icon={<DeleteOutlined />} />
                </Popconfirm>
            ),
        },
    ];

    return (
        <div>
            <Table
                columns={columns}
                dataSource={items}
                rowKey="id"
                pagination={false}
                size="small"
                bordered
                locale={{
                    emptyText: 'No line items added yet. Click "Add Line Item" to start.',
                }}
            />
            <Button type="dashed" onClick={addNewItem} icon={<PlusOutlined />} style={{ width: '100%', marginTop: 16 }}>
                Add Line Item
            </Button>
        </div>
    );
}
