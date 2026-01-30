import { NavGroup, NavItem } from '@/types';

/**
 * Extended NavItem with optional permission requirement.
 */
export interface NavItemWithPermission extends NavItem {
    permission?: string;
}

/**
 * Extended NavGroup with permission-aware items.
 */
export interface NavGroupWithPermission {
    title: string;
    items: NavItemWithPermission[];
    permission?: string;
}

/**
 * Filter navigation items based on user permissions.
 *
 * @param items - Array of navigation items with optional permissions
 * @param hasPermission - Function to check if user has a permission
 * @param isSuperAdmin - Whether the user is a super admin
 * @returns Filtered array of navigation items
 */
export function filterNavItems(
    items: NavItemWithPermission[],
    hasPermission: (permission: string) => boolean,
    isSuperAdmin: boolean,
): NavItem[] {
    if (isSuperAdmin) {
        return items;
    }

    return items.filter((item) => {
        if (!item.permission) {
            return true;
        }
        return hasPermission(item.permission);
    });
}

/**
 * Filter navigation groups based on user permissions.
 * Groups with no visible items after filtering are removed.
 *
 * @param groups - Array of navigation groups with optional permissions
 * @param hasPermission - Function to check if user has a permission
 * @param isSuperAdmin - Whether the user is a super admin
 * @returns Filtered array of navigation groups
 */
export function filterNavGroups(
    groups: NavGroupWithPermission[],
    hasPermission: (permission: string) => boolean,
    isSuperAdmin: boolean,
): NavGroup[] {
    if (isSuperAdmin) {
        return groups;
    }

    return groups
        .map((group) => {
            // Check group-level permission
            if (group.permission && !hasPermission(group.permission)) {
                return null;
            }

            // Filter items within the group
            const filteredItems = filterNavItems(group.items, hasPermission, isSuperAdmin);

            // Only include group if it has visible items
            if (filteredItems.length === 0) {
                return null;
            }

            return {
                title: group.title,
                items: filteredItems,
            };
        })
        .filter((group): group is NavGroup => group !== null);
}

/**
 * Check if a specific permission is for a module.
 *
 * @param permission - The permission string (e.g., "users.create")
 * @param module - The module name (e.g., "users")
 * @returns Whether the permission belongs to the module
 */
export function isModulePermission(permission: string, module: string): boolean {
    return permission.startsWith(`${module}.`);
}

/**
 * Get the action from a permission string.
 *
 * @param permission - The permission string (e.g., "users.create")
 * @returns The action part (e.g., "create")
 */
export function getPermissionAction(permission: string): string {
    const parts = permission.split('.');
    return parts[1] || '';
}

/**
 * Get the module from a permission string.
 *
 * @param permission - The permission string (e.g., "users.create")
 * @returns The module part (e.g., "users")
 */
export function getPermissionModule(permission: string): string {
    const parts = permission.split('.');
    return parts[0] || '';
}

/**
 * Build a permission string from module and action.
 *
 * @param module - The module name (e.g., "users")
 * @param action - The action (e.g., "create")
 * @returns The permission string (e.g., "users.create")
 */
export function buildPermission(module: string, action: string): string {
    return `${module}.${action}`;
}
