import { usePermissions } from '@/hooks/use-permissions';
import { filterNavGroups, filterNavItems } from '@/lib/permissions';
import { useMemo, type ReactNode } from 'react';
import MasterLayout from './master-layout';
import { appFooterNavItems, appMainNavItems, appNavGroups } from './menus/app-menu';

interface AppLayoutProps {
    children: ReactNode;
    pageTitle?: string;
    actions?: ReactNode;
}

export default function AppLayout({ children, pageTitle, actions }: AppLayoutProps) {
    const { can, isSuperAdmin } = usePermissions();

    // Filter navigation items based on user permissions
    const filteredMainNavItems = useMemo(
        () => filterNavItems(appMainNavItems, can, isSuperAdmin()),
        [can, isSuperAdmin],
    );

    const filteredNavGroups = useMemo(
        () => filterNavGroups(appNavGroups, can, isSuperAdmin()),
        [can, isSuperAdmin],
    );

    const filteredFooterNavItems = useMemo(
        () => filterNavItems(appFooterNavItems, can, isSuperAdmin()),
        [can, isSuperAdmin],
    );

    return (
        <MasterLayout
            pageTitle={pageTitle}
            actions={actions}
            mainNavItems={filteredMainNavItems}
            navGroups={filteredNavGroups}
            footerNavItems={filteredFooterNavItems}
        >
            {children}
        </MasterLayout>
    );
}
