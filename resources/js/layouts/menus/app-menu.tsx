import { type NavItem } from '@/types';
import { dashboard } from '@/routes';
import {
    DashboardOutlined,
    UsergroupAddOutlined,
    GithubOutlined,
    BookOutlined,
} from '@ant-design/icons';

export const appMainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: DashboardOutlined,
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