import { Card, Checkbox, Col, Row, Space, theme, Typography } from 'antd';
import { CheckboxChangeEvent } from 'antd/es/checkbox';
import { useMemo } from 'react';

const { Text, Title } = Typography;
const { useToken } = theme;

interface PermissionModule {
    label: string;
    permissions: string[];
}

interface PermissionModules {
    [key: string]: PermissionModule;
}

interface PermissionMatrixProps {
    modules: PermissionModules;
    selectedPermissions: string[];
    onChange: (permissions: string[]) => void;
    disabled?: boolean;
}

/**
 * Permission matrix component for selecting permissions by module.
 * Displays a grid of cards, one per module, with checkboxes for each permission.
 */
export default function PermissionMatrix({ modules, selectedPermissions, onChange, disabled = false }: PermissionMatrixProps) {
    const { token } = useToken();

    // Get all permissions for a module
    const getModulePermissions = (moduleKey: string): string[] => {
        const module = modules[moduleKey];
        return module?.permissions.map((action) => `${moduleKey}.${action}`) || [];
    };

    // Check if a module has all permissions selected
    const isModuleFullySelected = (moduleKey: string): boolean => {
        const modulePermissions = getModulePermissions(moduleKey);
        return modulePermissions.every((perm) => selectedPermissions.includes(perm));
    };

    // Check if a module has some (but not all) permissions selected
    const isModulePartiallySelected = (moduleKey: string): boolean => {
        const modulePermissions = getModulePermissions(moduleKey);
        const selectedCount = modulePermissions.filter((perm) => selectedPermissions.includes(perm)).length;
        return selectedCount > 0 && selectedCount < modulePermissions.length;
    };

    // Handle module-level checkbox change (select/deselect all)
    const handleModuleChange = (moduleKey: string, checked: boolean) => {
        const modulePermissions = getModulePermissions(moduleKey);

        if (checked) {
            // Add all module permissions
            const newPermissions = [...new Set([...selectedPermissions, ...modulePermissions])];
            onChange(newPermissions);
        } else {
            // Remove all module permissions
            const newPermissions = selectedPermissions.filter((perm) => !modulePermissions.includes(perm));
            onChange(newPermissions);
        }
    };

    // Handle individual permission checkbox change
    const handlePermissionChange = (permission: string, e: CheckboxChangeEvent) => {
        if (e.target.checked) {
            onChange([...selectedPermissions, permission]);
        } else {
            onChange(selectedPermissions.filter((perm) => perm !== permission));
        }
    };

    // Format action name for display
    const formatActionLabel = (action: string): string => {
        return action.charAt(0).toUpperCase() + action.slice(1);
    };

    // Memoize sorted module keys
    const moduleKeys = useMemo(() => Object.keys(modules).sort(), [modules]);

    return (
        <Row gutter={[16, 16]}>
            {moduleKeys.map((moduleKey) => {
                const module = modules[moduleKey];
                const isFullySelected = isModuleFullySelected(moduleKey);
                const isPartiallySelected = isModulePartiallySelected(moduleKey);

                return (
                    <Col xs={24} sm={12} md={8} lg={6} key={moduleKey}>
                        <Card
                            size="small"
                            style={{
                                borderColor: isFullySelected
                                    ? token.colorPrimary
                                    : isPartiallySelected
                                      ? token.colorWarningBorder
                                      : token.colorBorderSecondary,
                            }}
                        >
                            <Space direction="vertical" style={{ width: '100%' }}>
                                {/* Module Header with Select All */}
                                <div
                                    style={{
                                        display: 'flex',
                                        alignItems: 'center',
                                        justifyContent: 'space-between',
                                        borderBottom: `1px solid ${token.colorBorderSecondary}`,
                                        paddingBottom: 8,
                                        marginBottom: 4,
                                    }}
                                >
                                    <Title level={5} style={{ margin: 0, fontSize: 14 }}>
                                        {module.label}
                                    </Title>
                                    <Checkbox
                                        checked={isFullySelected}
                                        indeterminate={isPartiallySelected}
                                        onChange={(e) => handleModuleChange(moduleKey, e.target.checked)}
                                        disabled={disabled}
                                    />
                                </div>

                                {/* Individual Permission Checkboxes */}
                                <Space direction="vertical" size={4} style={{ width: '100%' }}>
                                    {module.permissions.map((action) => {
                                        const permission = `${moduleKey}.${action}`;
                                        const isChecked = selectedPermissions.includes(permission);

                                        return (
                                            <div
                                                key={permission}
                                                style={{
                                                    display: 'flex',
                                                    alignItems: 'center',
                                                    justifyContent: 'space-between',
                                                }}
                                            >
                                                <Text type="secondary" style={{ fontSize: 13 }}>
                                                    {formatActionLabel(action)}
                                                </Text>
                                                <Checkbox
                                                    checked={isChecked}
                                                    onChange={(e) => handlePermissionChange(permission, e)}
                                                    disabled={disabled}
                                                />
                                            </div>
                                        );
                                    })}
                                </Space>
                            </Space>
                        </Card>
                    </Col>
                );
            })}
        </Row>
    );
}
