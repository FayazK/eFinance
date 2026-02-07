import { usePermissions } from '@/hooks/use-permissions';
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

type MenuItem = NonNullable<MenuProps['items']>[number];

export default function QuickCreate() {
    const { can, isSuperAdmin } = usePermissions();

    const handleClick = (url: string) => {
        router.visit(url);
    };

    const canCreate = (module: string): boolean => isSuperAdmin() || can(`${module}.create`);

    const filterChildren = (children: MenuItem[]): MenuItem[] =>
        children.filter((item): item is MenuItem => item !== null);

    const billingChildren = filterChildren([
        canCreate('invoices')
            ? {
                  key: 'invoice',
                  icon: <FileTextOutlined />,
                  label: 'Invoice',
                  onClick: () => handleClick(createInvoice.url()),
              }
            : null,
        canCreate('expenses')
            ? {
                  key: 'expense',
                  icon: <CreditCardOutlined />,
                  label: 'Expense',
                  onClick: () => handleClick(createExpense.url()),
              }
            : null,
    ]);

    const financeChildren = filterChildren([
        canCreate('accounts')
            ? {
                  key: 'account',
                  icon: <WalletOutlined />,
                  label: 'Account',
                  onClick: () => handleClick(createAccount.url()),
              }
            : null,
        canCreate('transfers')
            ? {
                  key: 'transfer',
                  icon: <SwapOutlined />,
                  label: 'Transfer',
                  onClick: () => handleClick(createTransfer.url()),
              }
            : null,
        canCreate('distributions')
            ? {
                  key: 'distribution',
                  icon: <PieChartOutlined />,
                  label: 'Distribution',
                  onClick: () => handleClick(createDistribution.url()),
              }
            : null,
    ]);

    const crmChildren = filterChildren([
        canCreate('clients')
            ? {
                  key: 'client',
                  icon: <TeamOutlined />,
                  label: 'Client',
                  onClick: () => handleClick(createClient.url()),
              }
            : null,
        canCreate('contacts')
            ? {
                  key: 'contact',
                  icon: <UserOutlined />,
                  label: 'Contact',
                  onClick: () => handleClick(createContact.url()),
              }
            : null,
        canCreate('projects')
            ? {
                  key: 'project',
                  icon: <ProjectOutlined />,
                  label: 'Project',
                  onClick: () => handleClick(createProject.url()),
              }
            : null,
    ]);

    const hrChildren = filterChildren([
        canCreate('employees')
            ? {
                  key: 'employee',
                  icon: <IdcardOutlined />,
                  label: 'Employee',
                  onClick: () => handleClick(createEmployee.url()),
              }
            : null,
    ]);

    const menuItems: MenuProps['items'] = [
        ...(billingChildren.length > 0
            ? [{ key: 'billing', type: 'group' as const, label: 'Billing', children: billingChildren }]
            : []),
        ...(financeChildren.length > 0
            ? [{ key: 'finance', type: 'group' as const, label: 'Finance', children: financeChildren }]
            : []),
        ...(crmChildren.length > 0
            ? [{ key: 'crm', type: 'group' as const, label: 'CRM', children: crmChildren }]
            : []),
        ...(hrChildren.length > 0
            ? [{ key: 'hr', type: 'group' as const, label: 'HR', children: hrChildren }]
            : []),
    ];

    if (menuItems.length === 0) {
        return null;
    }

    return (
        <Dropdown menu={{ items: menuItems }} trigger={['click']} placement="bottomRight">
            <Button type="primary" icon={<PlusOutlined />}>
                New
            </Button>
        </Dropdown>
    );
}
