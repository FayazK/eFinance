<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Permission Modules
    |--------------------------------------------------------------------------
    |
    | Define all modules and their available permissions. Each module has a
    | human-readable label and a list of permissions (actions) available.
    |
    */
    'modules' => [
        'users' => [
            'label' => 'Users',
            'permissions' => ['create', 'read', 'update', 'delete'],
        ],
        'clients' => [
            'label' => 'Clients',
            'permissions' => ['create', 'read', 'update', 'delete'],
        ],
        'contacts' => [
            'label' => 'Contacts',
            'permissions' => ['create', 'read', 'update', 'delete'],
        ],
        'companies' => [
            'label' => 'Companies',
            'permissions' => ['create', 'read', 'update', 'delete'],
        ],
        'projects' => [
            'label' => 'Projects',
            'permissions' => ['create', 'read', 'update', 'delete'],
        ],
        'accounts' => [
            'label' => 'Accounts',
            'permissions' => ['create', 'read', 'update', 'delete'],
        ],
        'transactions' => [
            'label' => 'Transactions',
            'permissions' => ['create', 'read', 'update', 'delete'],
        ],
        'transaction_categories' => [
            'label' => 'Transaction Categories',
            'permissions' => ['create', 'read', 'update', 'delete'],
        ],
        'transfers' => [
            'label' => 'Transfers',
            'permissions' => ['create', 'read', 'update', 'delete'],
        ],
        'expenses' => [
            'label' => 'Expenses',
            'permissions' => ['create', 'read', 'update', 'delete'],
        ],
        'invoices' => [
            'label' => 'Invoices',
            'permissions' => ['create', 'read', 'update', 'delete'],
        ],
        'employees' => [
            'label' => 'Employees',
            'permissions' => ['create', 'read', 'update', 'delete'],
        ],
        'payroll' => [
            'label' => 'Payroll',
            'permissions' => ['create', 'read', 'update', 'delete'],
        ],
        'shareholders' => [
            'label' => 'Shareholders',
            'permissions' => ['create', 'read', 'update', 'delete'],
        ],
        'distributions' => [
            'label' => 'Distributions',
            'permissions' => ['create', 'read', 'update', 'delete'],
        ],
        'roles' => [
            'label' => 'Roles',
            'permissions' => ['create', 'read', 'update', 'delete'],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Super Admin Configuration
    |--------------------------------------------------------------------------
    |
    | The slug used to identify the super admin role. Users with this role
    | bypass all permission checks.
    |
    */
    'super_admin_slug' => 'super-admin',

    /*
    |--------------------------------------------------------------------------
    | Cache Configuration
    |--------------------------------------------------------------------------
    |
    | Configure how permissions are cached to improve performance.
    |
    */
    'cache' => [
        'enabled' => true,
        'ttl' => 3600, // Time to live in seconds (1 hour)
        'prefix' => 'permissions:',
    ],
];
