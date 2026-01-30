import { usePermissions } from '@/hooks/use-permissions';
import { ReactNode } from 'react';

interface CanAccessProps {
    children: ReactNode;
    /**
     * Permission string or array of permission strings to check.
     * If array is provided, user needs ANY of the permissions to see the content.
     */
    permission?: string | string[];
    /**
     * Optional fallback content to render when permission check fails.
     */
    fallback?: ReactNode;
    /**
     * If true, user needs ALL permissions instead of ANY.
     * Only applicable when permission is an array.
     */
    requireAll?: boolean;
}

/**
 * Component for conditional rendering based on user permissions.
 * Wraps content that should only be visible to users with specific permissions.
 *
 * @example
 * // Single permission
 * <CanAccess permission="users.create">
 *   <Button>Create User</Button>
 * </CanAccess>
 *
 * @example
 * // Multiple permissions (user needs any one)
 * <CanAccess permission={["users.update", "users.delete"]}>
 *   <ActionsMenu />
 * </CanAccess>
 *
 * @example
 * // Multiple permissions (user needs all)
 * <CanAccess permission={["invoices.create", "invoices.update"]} requireAll>
 *   <InvoiceEditor />
 * </CanAccess>
 *
 * @example
 * // With fallback
 * <CanAccess permission="reports.read" fallback={<UpgradePrompt />}>
 *   <ReportsPanel />
 * </CanAccess>
 */
export function CanAccess({ children, permission, fallback = null, requireAll = false }: CanAccessProps) {
    const { can, canAny, canAll, isSuperAdmin } = usePermissions();

    // Super admin can access everything
    if (isSuperAdmin()) {
        return <>{children}</>;
    }

    // No permission required
    if (!permission) {
        return <>{children}</>;
    }

    // Single permission check
    if (typeof permission === 'string') {
        return can(permission) ? <>{children}</> : <>{fallback}</>;
    }

    // Multiple permissions check
    if (Array.isArray(permission)) {
        const hasAccess = requireAll ? canAll(permission) : canAny(permission);
        return hasAccess ? <>{children}</> : <>{fallback}</>;
    }

    return <>{fallback}</>;
}

/**
 * HOC version of CanAccess for wrapping entire components.
 */
export function withPermission<P extends object>(
    WrappedComponent: React.ComponentType<P>,
    permission: string | string[],
    requireAll = false,
) {
    return function WithPermissionComponent(props: P) {
        return (
            <CanAccess permission={permission} requireAll={requireAll}>
                <WrappedComponent {...props} />
            </CanAccess>
        );
    };
}
