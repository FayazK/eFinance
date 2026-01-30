import { type NavGroupWithPermission, type NavItemWithPermission } from '@/lib/permissions';
import { dashboard } from '@/routes';
import { index as expenses } from '@/routes/expenses';
import {
    BankOutlined,
    CreditCardOutlined,
    DashboardOutlined,
    DollarOutlined,
    FileTextOutlined,
    IdcardOutlined,
    LockOutlined,
    PieChartOutlined,
    ProjectOutlined,
    ShopOutlined,
    SwapOutlined,
    TeamOutlined,
    UserOutlined,
    WalletOutlined,
} from '@ant-design/icons';

// Dashboard - standalone at top (no permission required)
export const appMainNavItems: NavItemWithPermission[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: DashboardOutlined,
    },
];

// Grouped navigation items with permissions
export const appNavGroups: NavGroupWithPermission[] = [
    {
        title: 'CRM',
        items: [
            {
                title: 'Clients',
                href: '/dashboard/clients',
                icon: TeamOutlined,
                permission: 'clients.read',
            },
            {
                title: 'Contacts',
                href: '/dashboard/contacts',
                icon: UserOutlined,
                permission: 'contacts.read',
            },
            {
                title: 'Projects',
                href: '/dashboard/projects',
                icon: ProjectOutlined,
                permission: 'projects.read',
            },
        ],
    },
    {
        title: 'Billing',
        items: [
            {
                title: 'Invoices',
                href: '/dashboard/invoices',
                icon: FileTextOutlined,
                permission: 'invoices.read',
            },
            {
                title: 'Companies',
                href: '/dashboard/companies',
                icon: ShopOutlined,
                permission: 'companies.read',
            },
        ],
    },
    {
        title: 'Finance',
        items: [
            {
                title: 'Accounts',
                href: '/dashboard/accounts',
                icon: WalletOutlined,
                permission: 'accounts.read',
            },
            {
                title: 'Transfers',
                href: '/dashboard/transfers',
                icon: SwapOutlined,
                permission: 'transfers.read',
            },
            {
                title: 'Expenses',
                href: expenses.url(),
                icon: CreditCardOutlined,
                permission: 'expenses.read',
            },
        ],
    },
    {
        title: 'HR',
        items: [
            {
                title: 'Employees',
                href: '/dashboard/employees',
                icon: IdcardOutlined,
                permission: 'employees.read',
            },
            {
                title: 'Payroll',
                href: '/dashboard/payroll',
                icon: DollarOutlined,
                permission: 'payroll.read',
            },
        ],
    },
    {
        title: 'Equity',
        items: [
            {
                title: 'Shareholders',
                href: '/dashboard/shareholders',
                icon: BankOutlined,
                permission: 'shareholders.read',
            },
            {
                title: 'Distributions',
                href: '/dashboard/distributions',
                icon: PieChartOutlined,
                permission: 'distributions.read',
            },
        ],
    },
    {
        title: 'Administration',
        items: [
            {
                title: 'Users',
                href: '/dashboard/users',
                icon: UserOutlined,
                permission: 'users.read',
            },
            {
                title: 'Roles',
                href: '/dashboard/roles',
                icon: LockOutlined,
                permission: 'roles.read',
            },
        ],
    },
];

export const appFooterNavItems: NavItemWithPermission[] = [];
