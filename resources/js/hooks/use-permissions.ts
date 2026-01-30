import { usePermissionStore } from '@/stores/permission-store';
import { SharedData } from '@/types';
import { usePage } from '@inertiajs/react';
import { useEffect } from 'react';

/**
 * Hook to access and manage permissions in the application.
 * Automatically syncs with the permission store from Inertia shared data.
 */
export function usePermissions() {
    const { auth } = usePage<SharedData>().props;
    const initialize = usePermissionStore((state) => state.initialize);
    const hasPermission = usePermissionStore((state) => state.hasPermission);
    const hasAnyPermission = usePermissionStore((state) => state.hasAnyPermission);
    const hasAllPermissions = usePermissionStore((state) => state.hasAllPermissions);
    const isSuperAdmin = usePermissionStore((state) => state.isSuperAdmin);
    const permissions = usePermissionStore((state) => state.permissions);
    const initialized = usePermissionStore((state) => state.initialized);

    // Sync permissions from Inertia shared data to the store
    useEffect(() => {
        if (auth?.permissions !== undefined) {
            initialize(auth.permissions, auth.is_super_admin);
        }
    }, [auth?.permissions, auth?.is_super_admin, initialize]);

    return {
        /**
         * Check if the user has a specific permission.
         */
        can: (permission: string) => hasPermission(permission),

        /**
         * Check if the user does not have a specific permission.
         */
        cannot: (permission: string) => !hasPermission(permission),

        /**
         * Check if the user has any of the given permissions.
         */
        canAny: (perms: string[]) => hasAnyPermission(perms),

        /**
         * Check if the user has all of the given permissions.
         */
        canAll: (perms: string[]) => hasAllPermissions(perms),

        /**
         * Check if the user is a super admin.
         */
        isSuperAdmin: () => isSuperAdmin,

        /**
         * Get all permissions as an array.
         */
        permissions: Array.from(permissions),

        /**
         * Check if the store has been initialized.
         */
        isInitialized: initialized,
    };
}
