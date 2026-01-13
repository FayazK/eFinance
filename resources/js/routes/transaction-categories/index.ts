import { applyUrlDefaults, queryParams, type RouteDefinition, type RouteFormDefinition, type RouteQueryOptions } from './../../wayfinder';
/**
 * @see \App\Http\Controllers\TransactionCategoryController::index
 * @see app/Http/Controllers/TransactionCategoryController.php:23
 * @route '/dashboard/transaction-categories'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
});

index.definition = {
    methods: ['get', 'head'],
    url: '/dashboard/transaction-categories',
} satisfies RouteDefinition<['get', 'head']>;

/**
 * @see \App\Http\Controllers\TransactionCategoryController::index
 * @see app/Http/Controllers/TransactionCategoryController.php:23
 * @route '/dashboard/transaction-categories'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options);
};

/**
 * @see \App\Http\Controllers\TransactionCategoryController::index
 * @see app/Http/Controllers/TransactionCategoryController.php:23
 * @route '/dashboard/transaction-categories'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\TransactionCategoryController::index
 * @see app/Http/Controllers/TransactionCategoryController.php:23
 * @route '/dashboard/transaction-categories'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
});

/**
 * @see \App\Http\Controllers\TransactionCategoryController::index
 * @see app/Http/Controllers/TransactionCategoryController.php:23
 * @route '/dashboard/transaction-categories'
 */
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\TransactionCategoryController::index
 * @see app/Http/Controllers/TransactionCategoryController.php:23
 * @route '/dashboard/transaction-categories'
 */
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\TransactionCategoryController::index
 * @see app/Http/Controllers/TransactionCategoryController.php:23
 * @route '/dashboard/transaction-categories'
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
 * @see \App\Http\Controllers\TransactionCategoryController::data
 * @see app/Http/Controllers/TransactionCategoryController.php:28
 * @route '/dashboard/transaction-categories/data'
 */
export const data = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: data.url(options),
    method: 'get',
});

data.definition = {
    methods: ['get', 'head'],
    url: '/dashboard/transaction-categories/data',
} satisfies RouteDefinition<['get', 'head']>;

/**
 * @see \App\Http\Controllers\TransactionCategoryController::data
 * @see app/Http/Controllers/TransactionCategoryController.php:28
 * @route '/dashboard/transaction-categories/data'
 */
data.url = (options?: RouteQueryOptions) => {
    return data.definition.url + queryParams(options);
};

/**
 * @see \App\Http\Controllers\TransactionCategoryController::data
 * @see app/Http/Controllers/TransactionCategoryController.php:28
 * @route '/dashboard/transaction-categories/data'
 */
data.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: data.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\TransactionCategoryController::data
 * @see app/Http/Controllers/TransactionCategoryController.php:28
 * @route '/dashboard/transaction-categories/data'
 */
data.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: data.url(options),
    method: 'head',
});

/**
 * @see \App\Http\Controllers\TransactionCategoryController::data
 * @see app/Http/Controllers/TransactionCategoryController.php:28
 * @route '/dashboard/transaction-categories/data'
 */
const dataForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: data.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\TransactionCategoryController::data
 * @see app/Http/Controllers/TransactionCategoryController.php:28
 * @route '/dashboard/transaction-categories/data'
 */
dataForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: data.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\TransactionCategoryController::data
 * @see app/Http/Controllers/TransactionCategoryController.php:28
 * @route '/dashboard/transaction-categories/data'
 */
dataForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: data.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        },
    }),
    method: 'get',
});

data.form = dataForm;

/**
 * @see \App\Http\Controllers\TransactionCategoryController::store
 * @see app/Http/Controllers/TransactionCategoryController.php:40
 * @route '/dashboard/transaction-categories'
 */
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
});

store.definition = {
    methods: ['post'],
    url: '/dashboard/transaction-categories',
} satisfies RouteDefinition<['post']>;

/**
 * @see \App\Http\Controllers\TransactionCategoryController::store
 * @see app/Http/Controllers/TransactionCategoryController.php:40
 * @route '/dashboard/transaction-categories'
 */
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options);
};

/**
 * @see \App\Http\Controllers\TransactionCategoryController::store
 * @see app/Http/Controllers/TransactionCategoryController.php:40
 * @route '/dashboard/transaction-categories'
 */
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
});

/**
 * @see \App\Http\Controllers\TransactionCategoryController::store
 * @see app/Http/Controllers/TransactionCategoryController.php:40
 * @route '/dashboard/transaction-categories'
 */
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
});

/**
 * @see \App\Http\Controllers\TransactionCategoryController::store
 * @see app/Http/Controllers/TransactionCategoryController.php:40
 * @route '/dashboard/transaction-categories'
 */
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
});

store.form = storeForm;

/**
 * @see \App\Http\Controllers\TransactionCategoryController::update
 * @see app/Http/Controllers/TransactionCategoryController.php:50
 * @route '/dashboard/transaction-categories/{category}'
 */
export const update = (
    args: { category: number | { id: number } } | [category: number | { id: number }] | number | { id: number },
    options?: RouteQueryOptions,
): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
});

update.definition = {
    methods: ['put'],
    url: '/dashboard/transaction-categories/{category}',
} satisfies RouteDefinition<['put']>;

/**
 * @see \App\Http\Controllers\TransactionCategoryController::update
 * @see app/Http/Controllers/TransactionCategoryController.php:50
 * @route '/dashboard/transaction-categories/{category}'
 */
update.url = (
    args: { category: number | { id: number } } | [category: number | { id: number }] | number | { id: number },
    options?: RouteQueryOptions,
) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { category: args };
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { category: args.id };
    }

    if (Array.isArray(args)) {
        args = {
            category: args[0],
        };
    }

    args = applyUrlDefaults(args);

    const parsedArgs = {
        category: typeof args.category === 'object' ? args.category.id : args.category,
    };

    return update.definition.url.replace('{category}', parsedArgs.category.toString()).replace(/\/+$/, '') + queryParams(options);
};

/**
 * @see \App\Http\Controllers\TransactionCategoryController::update
 * @see app/Http/Controllers/TransactionCategoryController.php:50
 * @route '/dashboard/transaction-categories/{category}'
 */
update.put = (
    args: { category: number | { id: number } } | [category: number | { id: number }] | number | { id: number },
    options?: RouteQueryOptions,
): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
});

/**
 * @see \App\Http\Controllers\TransactionCategoryController::update
 * @see app/Http/Controllers/TransactionCategoryController.php:50
 * @route '/dashboard/transaction-categories/{category}'
 */
const updateForm = (
    args: { category: number | { id: number } } | [category: number | { id: number }] | number | { id: number },
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
 * @see \App\Http\Controllers\TransactionCategoryController::update
 * @see app/Http/Controllers/TransactionCategoryController.php:50
 * @route '/dashboard/transaction-categories/{category}'
 */
updateForm.put = (
    args: { category: number | { id: number } } | [category: number | { id: number }] | number | { id: number },
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
 * @see \App\Http\Controllers\TransactionCategoryController::destroy
 * @see app/Http/Controllers/TransactionCategoryController.php:60
 * @route '/dashboard/transaction-categories/{category}'
 */
export const destroy = (
    args: { category: string | number } | [category: string | number] | string | number,
    options?: RouteQueryOptions,
): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
});

destroy.definition = {
    methods: ['delete'],
    url: '/dashboard/transaction-categories/{category}',
} satisfies RouteDefinition<['delete']>;

/**
 * @see \App\Http\Controllers\TransactionCategoryController::destroy
 * @see app/Http/Controllers/TransactionCategoryController.php:60
 * @route '/dashboard/transaction-categories/{category}'
 */
destroy.url = (args: { category: string | number } | [category: string | number] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { category: args };
    }

    if (Array.isArray(args)) {
        args = {
            category: args[0],
        };
    }

    args = applyUrlDefaults(args);

    const parsedArgs = {
        category: args.category,
    };

    return destroy.definition.url.replace('{category}', parsedArgs.category.toString()).replace(/\/+$/, '') + queryParams(options);
};

/**
 * @see \App\Http\Controllers\TransactionCategoryController::destroy
 * @see app/Http/Controllers/TransactionCategoryController.php:60
 * @route '/dashboard/transaction-categories/{category}'
 */
destroy.delete = (
    args: { category: string | number } | [category: string | number] | string | number,
    options?: RouteQueryOptions,
): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
});

/**
 * @see \App\Http\Controllers\TransactionCategoryController::destroy
 * @see app/Http/Controllers/TransactionCategoryController.php:60
 * @route '/dashboard/transaction-categories/{category}'
 */
const destroyForm = (
    args: { category: string | number } | [category: string | number] | string | number,
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
 * @see \App\Http\Controllers\TransactionCategoryController::destroy
 * @see app/Http/Controllers/TransactionCategoryController.php:60
 * @route '/dashboard/transaction-categories/{category}'
 */
destroyForm.delete = (
    args: { category: string | number } | [category: string | number] | string | number,
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

const transactionCategories = {
    index,
    data,
    store,
    update,
    destroy,
};

export default transactionCategories;
