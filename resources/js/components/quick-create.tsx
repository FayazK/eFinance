import { create as createAccount } from '@/routes/accounts';
import { create as createClient } from '@/routes/clients';
import { create as createContact } from '@/routes/contacts';
import { create as createDistribution } from '@/routes/distributions';
import { create as createEmployee } from '@/routes/employees';
import { create as createExpense } from '@/routes/expenses';
import { create as createInvoice } from '@/routes/invoices';
import { create as createProject } from '@/routes/projects';
import { create as createTransfer } from '@/routes/transfers';
import {
    CreditCardOutlined,
    FileTextOutlined,
    IdcardOutlined,
    PieChartOutlined,
    PlusOutlined,
    ProjectOutlined,
    SwapOutlined,
    TeamOutlined,
    UserOutlined,
    WalletOutlined,
} from '@ant-design/icons';
import { router } from '@inertiajs/react';
import type { MenuProps } from 'antd';
import { Button, Dropdown } from 'antd';

export default function QuickCreate() {
    const handleClick = (url: string) => {
        router.visit(url);
    };

    const menuItems: MenuProps['items'] = [
        {
            key: 'billing',
            type: 'group',
            label: 'Billing',
            children: [
                {
                    key: 'invoice',
                    icon: <FileTextOutlined />,
                    label: 'Invoice',
                    onClick: () => handleClick(createInvoice.url()),
                },
                {
                    key: 'expense',
                    icon: <CreditCardOutlined />,
                    label: 'Expense',
                    onClick: () => handleClick(createExpense.url()),
                },
            ],
        },
        {
            key: 'finance',
            type: 'group',
            label: 'Finance',
            children: [
                {
                    key: 'account',
                    icon: <WalletOutlined />,
                    label: 'Account',
                    onClick: () => handleClick(createAccount.url()),
                },
                {
                    key: 'transfer',
                    icon: <SwapOutlined />,
                    label: 'Transfer',
                    onClick: () => handleClick(createTransfer.url()),
                },
                {
                    key: 'distribution',
                    icon: <PieChartOutlined />,
                    label: 'Distribution',
                    onClick: () => handleClick(createDistribution.url()),
                },
            ],
        },
        {
            key: 'crm',
            type: 'group',
            label: 'CRM',
            children: [
                {
                    key: 'client',
                    icon: <TeamOutlined />,
                    label: 'Client',
                    onClick: () => handleClick(createClient.url()),
                },
                {
                    key: 'contact',
                    icon: <UserOutlined />,
                    label: 'Contact',
                    onClick: () => handleClick(createContact.url()),
                },
                {
                    key: 'project',
                    icon: <ProjectOutlined />,
                    label: 'Project',
                    onClick: () => handleClick(createProject.url()),
                },
            ],
        },
        {
            key: 'hr',
            type: 'group',
            label: 'HR',
            children: [
                {
                    key: 'employee',
                    icon: <IdcardOutlined />,
                    label: 'Employee',
                    onClick: () => handleClick(createEmployee.url()),
                },
            ],
        },
    ];

    return (
        <Dropdown menu={{ items: menuItems }} trigger={['click']} placement="bottomRight">
            <Button type="primary" icon={<PlusOutlined />}>
                New
            </Button>
        </Dropdown>
    );
}
