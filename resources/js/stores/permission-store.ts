import { create } from 'zustand';

interface PermissionState {
    permissions: Set<string>;
    isSuperAdmin: boolean;
    initialized: boolean;
    initialize: (permissions: string[], isSuperAdmin: boolean) => void;
    hasPermission: (permission: string) => boolean;
    hasAnyPermission: (permissions: string[]) => boolean;
    hasAllPermissions: (permissions: string[]) => boolean;
    reset: () => void;
}

export const usePermissionStore = create<PermissionState>()((set, get) => ({
    permissions: new Set<string>(),
    isSuperAdmin: false,
    initialized: false,

    initialize: (permissions: string[], isSuperAdmin: boolean) => {
        set({
            permissions: new Set(permissions),
            isSuperAdmin,
            initialized: true,
        });
    },

    hasPermission: (permission: string) => {
        const state = get();
        if (state.isSuperAdmin) {
            return true;
        }
        return state.permissions.has(permission);
    },

    hasAnyPermission: (permissions: string[]) => {
        const state = get();
        if (state.isSuperAdmin) {
            return true;
        }
        return permissions.some((permission) => state.permissions.has(permission));
    },

    hasAllPermissions: (permissions: string[]) => {
        const state = get();
        if (state.isSuperAdmin) {
            return true;
        }
        return permissions.every((permission) => state.permissions.has(permission));
    },

    reset: () => {
        set({
            permissions: new Set<string>(),
            isSuperAdmin: false,
            initialized: false,
        });
    },
}));
