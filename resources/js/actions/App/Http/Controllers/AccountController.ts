import { applyUrlDefaults, queryParams, type RouteDefinition, type RouteFormDefinition, type RouteQueryOptions } from './../../../../wayfinder';
/**
 * @see \App\Http\Controllers\AccountController::index
 * @see app/Http/Controllers/AccountController.php:27
 * @route '/dashboard/accounts'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
});

index.definition = {
    methods: ['get', 'head'],
    url: '/dashboard/accounts',
} satisfies RouteDefinition<['get', 'head']>;

/**
 * @see \App\Http\Controllers\AccountController::index
 * @see app/Http/Controllers/AccountController.php:27
 * @route '/dashboard/accounts'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options);
};

/**
 * @see \App\Http\Controllers\AccountController::index
 * @see app/Http/Controllers/AccountController.php:27
 * @route '/dashboard/accounts'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\AccountController::index
 * @see app/Http/Controllers/AccountController.php:27
 * @route '/dashboard/accounts'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
});

/**
 * @see \App\Http\Controllers\AccountController::index
 * @see app/Http/Controllers/AccountController.php:27
 * @route '/dashboard/accounts'
 */
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\AccountController::index
 * @see app/Http/Controllers/AccountController.php:27
 * @route '/dashboard/accounts'
 */
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\AccountController::index
 * @see app/Http/Controllers/AccountController.php:27
 * @route '/dashboard/accounts'
 */
indexForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        },
    }),
    method: 'get',
});

index.form = indexForm;

/**
 * @see \App\Http\Controllers\AccountController::create
 * @see app/Http/Controllers/AccountController.php:39
 * @route '/dashboard/accounts/create'
 */
export const create = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
});

create.definition = {
    methods: ['get', 'head'],
    url: '/dashboard/accounts/create',
} satisfies RouteDefinition<['get', 'head']>;

/**
 * @see \App\Http\Controllers\AccountController::create
 * @see app/Http/Controllers/AccountController.php:39
 * @route '/dashboard/accounts/create'
 */
create.url = (options?: RouteQueryOptions) => {
    return create.definition.url + queryParams(options);
};

/**
 * @see \App\Http\Controllers\AccountController::create
 * @see app/Http/Controllers/AccountController.php:39
 * @route '/dashboard/accounts/create'
 */
create.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\AccountController::create
 * @see app/Http/Controllers/AccountController.php:39
 * @route '/dashboard/accounts/create'
 */
create.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(options),
    method: 'head',
});

/**
 * @see \App\Http\Controllers\AccountController::create
 * @see app/Http/Controllers/AccountController.php:39
 * @route '/dashboard/accounts/create'
 */
const createForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\AccountController::create
 * @see app/Http/Controllers/AccountController.php:39
 * @route '/dashboard/accounts/create'
 */
createForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\AccountController::create
 * @see app/Http/Controllers/AccountController.php:39
 * @route '/dashboard/accounts/create'
 */
createForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        },
    }),
    method: 'get',
});

create.form = createForm;

/**
 * @see \App\Http\Controllers\AccountController::show
 * @see app/Http/Controllers/AccountController.php:54
 * @route '/dashboard/accounts/{account}'
 */
export const show = (
    args: { account: number | { id: number } } | [account: number | { id: number }] | number | { id: number },
    options?: RouteQueryOptions,
): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
});

show.definition = {
    methods: ['get', 'head'],
    url: '/dashboard/accounts/{account}',
} satisfies RouteDefinition<['get', 'head']>;

/**
 * @see \App\Http\Controllers\AccountController::show
 * @see app/Http/Controllers/AccountController.php:54
 * @route '/dashboard/accounts/{account}'
 */
show.url = (
    args: { account: number | { id: number } } | [account: number | { id: number }] | number | { id: number },
    options?: RouteQueryOptions,
) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { account: args };
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { account: args.id };
    }

    if (Array.isArray(args)) {
        args = {
            account: args[0],
        };
    }

    args = applyUrlDefaults(args);

    const parsedArgs = {
        account: typeof args.account === 'object' ? args.account.id : args.account,
    };

    return show.definition.url.replace('{account}', parsedArgs.account.toString()).replace(/\/+$/, '') + queryParams(options);
};

/**
 * @see \App\Http\Controllers\AccountController::show
 * @see app/Http/Controllers/AccountController.php:54
 * @route '/dashboard/accounts/{account}'
 */
show.get = (
    args: { account: number | { id: number } } | [account: number | { id: number }] | number | { id: number },
    options?: RouteQueryOptions,
): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\AccountController::show
 * @see app/Http/Controllers/AccountController.php:54
 * @route '/dashboard/accounts/{account}'
 */
show.head = (
    args: { account: number | { id: number } } | [account: number | { id: number }] | number | { id: number },
    options?: RouteQueryOptions,
): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
});

/**
 * @see \App\Http\Controllers\AccountController::show
 * @see app/Http/Controllers/AccountController.php:54
 * @route '/dashboard/accounts/{account}'
 */
const showForm = (
    args: { account: number | { id: number } } | [account: number | { id: number }] | number | { id: number },
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\AccountController::show
 * @see app/Http/Controllers/AccountController.php:54
 * @route '/dashboard/accounts/{account}'
 */
showForm.get = (
    args: { account: number | { id: number } } | [account: number | { id: number }] | number | { id: number },
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\AccountController::show
 * @see app/Http/Controllers/AccountController.php:54
 * @route '/dashboard/accounts/{account}'
 */
showForm.head = (
    args: { account: number | { id: number } } | [account: number | { id: number }] | number | { id: number },
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: show.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        },
    }),
    method: 'get',
});

show.form = showForm;

/**
 * @see \App\Http\Controllers\AccountController::edit
 * @see app/Http/Controllers/AccountController.php:61
 * @route '/dashboard/accounts/{account}/edit'
 */
export const edit = (
    args: { account: number | { id: number } } | [account: number | { id: number }] | number | { id: number },
    options?: RouteQueryOptions,
): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
});

edit.definition = {
    methods: ['get', 'head'],
    url: '/dashboard/accounts/{account}/edit',
} satisfies RouteDefinition<['get', 'head']>;

/**
 * @see \App\Http\Controllers\AccountController::edit
 * @see app/Http/Controllers/AccountController.php:61
 * @route '/dashboard/accounts/{account}/edit'
 */
edit.url = (
    args: { account: number | { id: number } } | [account: number | { id: number }] | number | { id: number },
    options?: RouteQueryOptions,
) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { account: args };
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { account: args.id };
    }

    if (Array.isArray(args)) {
        args = {
            account: args[0],
        };
    }

    args = applyUrlDefaults(args);

    const parsedArgs = {
        account: typeof args.account === 'object' ? args.account.id : args.account,
    };

    return edit.definition.url.replace('{account}', parsedArgs.account.toString()).replace(/\/+$/, '') + queryParams(options);
};

/**
 * @see \App\Http\Controllers\AccountController::edit
 * @see app/Http/Controllers/AccountController.php:61
 * @route '/dashboard/accounts/{account}/edit'
 */
edit.get = (
    args: { account: number | { id: number } } | [account: number | { id: number }] | number | { id: number },
    options?: RouteQueryOptions,
): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\AccountController::edit
 * @see app/Http/Controllers/AccountController.php:61
 * @route '/dashboard/accounts/{account}/edit'
 */
edit.head = (
    args: { account: number | { id: number } } | [account: number | { id: number }] | number | { id: number },
    options?: RouteQueryOptions,
): RouteDefinition<'head'> => ({
    url: edit.url(args, options),
    method: 'head',
});

/**
 * @see \App\Http\Controllers\AccountController::edit
 * @see app/Http/Controllers/AccountController.php:61
 * @route '/dashboard/accounts/{account}/edit'
 */
const editForm = (
    args: { account: number | { id: number } } | [account: number | { id: number }] | number | { id: number },
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: edit.url(args, options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\AccountController::edit
 * @see app/Http/Controllers/AccountController.php:61
 * @route '/dashboard/accounts/{account}/edit'
 */
editForm.get = (
    args: { account: number | { id: number } } | [account: number | { id: number }] | number | { id: number },
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: edit.url(args, options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\AccountController::edit
 * @see app/Http/Controllers/AccountController.php:61
 * @route '/dashboard/accounts/{account}/edit'
 */
editForm.head = (
    args: { account: number | { id: number } } | [account: number | { id: number }] | number | { id: number },
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: edit.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        },
    }),
    method: 'get',
});

edit.form = editForm;

/**
 * @see \App\Http\Controllers\AccountController::transactions
 * @see app/Http/Controllers/AccountController.php:93
 * @route '/dashboard/accounts/{account}/transactions'
 */
export const transactions = (
    args: { account: number | { id: number } } | [account: number | { id: number }] | number | { id: number },
    options?: RouteQueryOptions,
): RouteDefinition<'get'> => ({
    url: transactions.url(args, options),
    method: 'get',
});

transactions.definition = {
    methods: ['get', 'head'],
    url: '/dashboard/accounts/{account}/transactions',
} satisfies RouteDefinition<['get', 'head']>;

/**
 * @see \App\Http\Controllers\AccountController::transactions
 * @see app/Http/Controllers/AccountController.php:93
 * @route '/dashboard/accounts/{account}/transactions'
 */
transactions.url = (
    args: { account: number | { id: number } } | [account: number | { id: number }] | number | { id: number },
    options?: RouteQueryOptions,
) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { account: args };
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { account: args.id };
    }

    if (Array.isArray(args)) {
        args = {
            account: args[0],
        };
    }

    args = applyUrlDefaults(args);

    const parsedArgs = {
        account: typeof args.account === 'object' ? args.account.id : args.account,
    };

    return transactions.definition.url.replace('{account}', parsedArgs.account.toString()).replace(/\/+$/, '') + queryParams(options);
};

/**
 * @see \App\Http\Controllers\AccountController::transactions
 * @see app/Http/Controllers/AccountController.php:93
 * @route '/dashboard/accounts/{account}/transactions'
 */
transactions.get = (
    args: { account: number | { id: number } } | [account: number | { id: number }] | number | { id: number },
    options?: RouteQueryOptions,
): RouteDefinition<'get'> => ({
    url: transactions.url(args, options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\AccountController::transactions
 * @see app/Http/Controllers/AccountController.php:93
 * @route '/dashboard/accounts/{account}/transactions'
 */
transactions.head = (
    args: { account: number | { id: number } } | [account: number | { id: number }] | number | { id: number },
    options?: RouteQueryOptions,
): RouteDefinition<'head'> => ({
    url: transactions.url(args, options),
    method: 'head',
});

/**
 * @see \App\Http\Controllers\AccountController::transactions
 * @see app/Http/Controllers/AccountController.php:93
 * @route '/dashboard/accounts/{account}/transactions'
 */
const transactionsForm = (
    args: { account: number | { id: number } } | [account: number | { id: number }] | number | { id: number },
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: transactions.url(args, options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\AccountController::transactions
 * @see app/Http/Controllers/AccountController.php:93
 * @route '/dashboard/accounts/{account}/transactions'
 */
transactionsForm.get = (
    args: { account: number | { id: number } } | [account: number | { id: number }] | number | { id: number },
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: transactions.url(args, options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\AccountController::transactions
 * @see app/Http/Controllers/AccountController.php:93
 * @route '/dashboard/accounts/{account}/transactions'
 */
transactionsForm.head = (
    args: { account: number | { id: number } } | [account: number | { id: number }] | number | { id: number },
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: transactions.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        },
    }),
    method: 'get',
});

transactions.form = transactionsForm;

/**
 * @see \App\Http\Controllers\AccountController::store
 * @see app/Http/Controllers/AccountController.php:44
 * @route '/dashboard/accounts'
 */
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
});

store.definition = {
    methods: ['post'],
    url: '/dashboard/accounts',
} satisfies RouteDefinition<['post']>;

/**
 * @see \App\Http\Controllers\AccountController::store
 * @see app/Http/Controllers/AccountController.php:44
 * @route '/dashboard/accounts'
 */
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options);
};

/**
 * @see \App\Http\Controllers\AccountController::store
 * @see app/Http/Controllers/AccountController.php:44
 * @route '/dashboard/accounts'
 */
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
});

/**
 * @see \App\Http\Controllers\AccountController::store
 * @see app/Http/Controllers/AccountController.php:44
 * @route '/dashboard/accounts'
 */
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
});

/**
 * @see \App\Http\Controllers\AccountController::store
 * @see app/Http/Controllers/AccountController.php:44
 * @route '/dashboard/accounts'
 */
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
});

store.form = storeForm;

/**
 * @see \App\Http\Controllers\AccountController::update
 * @see app/Http/Controllers/AccountController.php:68
 * @route '/dashboard/accounts/{account}'
 */
export const update = (
    args: { account: number | { id: number } } | [account: number | { id: number }] | number | { id: number },
    options?: RouteQueryOptions,
): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
});

update.definition = {
    methods: ['put'],
    url: '/dashboard/accounts/{account}',
} satisfies RouteDefinition<['put']>;

/**
 * @see \App\Http\Controllers\AccountController::update
 * @see app/Http/Controllers/AccountController.php:68
 * @route '/dashboard/accounts/{account}'
 */
update.url = (
    args: { account: number | { id: number } } | [account: number | { id: number }] | number | { id: number },
    options?: RouteQueryOptions,
) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { account: args };
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { account: args.id };
    }

    if (Array.isArray(args)) {
        args = {
            account: args[0],
        };
    }

    args = applyUrlDefaults(args);

    const parsedArgs = {
        account: typeof args.account === 'object' ? args.account.id : args.account,
    };

    return update.definition.url.replace('{account}', parsedArgs.account.toString()).replace(/\/+$/, '') + queryParams(options);
};

/**
 * @see \App\Http\Controllers\AccountController::update
 * @see app/Http/Controllers/AccountController.php:68
 * @route '/dashboard/accounts/{account}'
 */
update.put = (
    args: { account: number | { id: number } } | [account: number | { id: number }] | number | { id: number },
    options?: RouteQueryOptions,
): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
});

/**
 * @see \App\Http\Controllers\AccountController::update
 * @see app/Http/Controllers/AccountController.php:68
 * @route '/dashboard/accounts/{account}'
 */
const updateForm = (
    args: { account: number | { id: number } } | [account: number | { id: number }] | number | { id: number },
    options?: RouteQueryOptions,
): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        },
    }),
    method: 'post',
});

/**
 * @see \App\Http\Controllers\AccountController::update
 * @see app/Http/Controllers/AccountController.php:68
 * @route '/dashboard/accounts/{account}'
 */
updateForm.put = (
    args: { account: number | { id: number } } | [account: number | { id: number }] | number | { id: number },
    options?: RouteQueryOptions,
): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        },
    }),
    method: 'post',
});

update.form = updateForm;

/**
 * @see \App\Http\Controllers\AccountController::destroy
 * @see app/Http/Controllers/AccountController.php:78
 * @route '/dashboard/accounts/{account}'
 */
export const destroy = (
    args: { account: string | number } | [account: string | number] | string | number,
    options?: RouteQueryOptions,
): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
});

destroy.definition = {
    methods: ['delete'],
    url: '/dashboard/accounts/{account}',
} satisfies RouteDefinition<['delete']>;

/**
 * @see \App\Http\Controllers\AccountController::destroy
 * @see app/Http/Controllers/AccountController.php:78
 * @route '/dashboard/accounts/{account}'
 */
destroy.url = (args: { account: string | number } | [account: string | number] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { account: args };
    }

    if (Array.isArray(args)) {
        args = {
            account: args[0],
        };
    }

    args = applyUrlDefaults(args);

    const parsedArgs = {
        account: args.account,
    };

    return destroy.definition.url.replace('{account}', parsedArgs.account.toString()).replace(/\/+$/, '') + queryParams(options);
};

/**
 * @see \App\Http\Controllers\AccountController::destroy
 * @see app/Http/Controllers/AccountController.php:78
 * @route '/dashboard/accounts/{account}'
 */
destroy.delete = (
    args: { account: string | number } | [account: string | number] | string | number,
    options?: RouteQueryOptions,
): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
});

/**
 * @see \App\Http\Controllers\AccountController::destroy
 * @see app/Http/Controllers/AccountController.php:78
 * @route '/dashboard/accounts/{account}'
 */
const destroyForm = (
    args: { account: string | number } | [account: string | number] | string | number,
    options?: RouteQueryOptions,
): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        },
    }),
    method: 'post',
});

/**
 * @see \App\Http\Controllers\AccountController::destroy
 * @see app/Http/Controllers/AccountController.php:78
 * @route '/dashboard/accounts/{account}'
 */
destroyForm.delete = (
    args: { account: string | number } | [account: string | number] | string | number,
    options?: RouteQueryOptions,
): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        },
    }),
    method: 'post',
});

destroy.form = destroyForm;

const AccountController = { index, create, show, edit, transactions, store, update, destroy };

export default AccountController;
