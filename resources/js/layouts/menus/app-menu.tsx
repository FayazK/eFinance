import { dashboard } from '@/routes';
import { type NavItem } from '@/types';
import {
    BankOutlined,
    BookOutlined,
    DashboardOutlined,
    DollarOutlined,
    FileTextOutlined,
    GithubOutlined,
    IdcardOutlined,
    PieChartOutlined,
    ProjectOutlined,
    SwapOutlined,
    TeamOutlined,
    UsergroupAddOutlined,
    UserOutlined,
    WalletOutlined,
} from '@ant-design/icons';

export const appMainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: DashboardOutlined,
    },
    {
        title: 'Clients',
        href: '/dashboard/clients',
        icon: TeamOutlined,
    },
    {
        title: 'Contacts',
        href: '/dashboard/contacts',
        icon: UserOutlined,
    },
    {
        title: 'Projects',
        href: '/dashboard/projects',
        icon: ProjectOutlined,
    },
    {
        title: 'Invoices',
        href: '/dashboard/invoices',
        icon: FileTextOutlined,
    },
    {
        title: 'Accounts',
        href: '/dashboard/accounts',
        icon: WalletOutlined,
    },
    {
        title: 'Transfers',
        href: '/dashboard/transfers',
        icon: SwapOutlined,
    },
    {
        title: 'Employees',
        href: '/dashboard/employees',
        icon: IdcardOutlined,
    },
    {
        title: 'Payroll',
        href: '/dashboard/payroll',
        icon: DollarOutlined,
    },
    {
        title: 'Shareholders',
        href: '/dashboard/shareholders',
        icon: BankOutlined,
    },
    {
        title: 'Distributions',
        href: '/dashboard/distributions',
        icon: PieChartOutlined,
    },
    {
        title: 'User Management',
        href: '/dashboard/users',
        icon: UsergroupAddOutlined,
    },
];

export const appFooterNavItems: NavItem[] = [
    {
        title: 'Repository',
        href: 'https://github.com/laravel/react-starter-kit',
        icon: GithubOutlined,
    },
    {
        title: 'Documentation',
        href: 'https://laravel.com/docs/starter-kits#react',
        icon: BookOutlined,
    },
];
