import { applyUrlDefaults, queryParams, type RouteDefinition, type RouteFormDefinition, type RouteQueryOptions } from './../../wayfinder';
/**
 * @see \App\Http\Controllers\DistributionController::index
 * @see app/Http/Controllers/DistributionController.php:27
 * @route '/dashboard/distributions'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
});

index.definition = {
    methods: ['get', 'head'],
    url: '/dashboard/distributions',
} satisfies RouteDefinition<['get', 'head']>;

/**
 * @see \App\Http\Controllers\DistributionController::index
 * @see app/Http/Controllers/DistributionController.php:27
 * @route '/dashboard/distributions'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options);
};

/**
 * @see \App\Http\Controllers\DistributionController::index
 * @see app/Http/Controllers/DistributionController.php:27
 * @route '/dashboard/distributions'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\DistributionController::index
 * @see app/Http/Controllers/DistributionController.php:27
 * @route '/dashboard/distributions'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
});

/**
 * @see \App\Http\Controllers\DistributionController::index
 * @see app/Http/Controllers/DistributionController.php:27
 * @route '/dashboard/distributions'
 */
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\DistributionController::index
 * @see app/Http/Controllers/DistributionController.php:27
 * @route '/dashboard/distributions'
 */
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\DistributionController::index
 * @see app/Http/Controllers/DistributionController.php:27
 * @route '/dashboard/distributions'
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
 * @see \App\Http\Controllers\DistributionController::create
 * @see app/Http/Controllers/DistributionController.php:36
 * @route '/dashboard/distributions/create'
 */
export const create = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
});

create.definition = {
    methods: ['get', 'head'],
    url: '/dashboard/distributions/create',
} satisfies RouteDefinition<['get', 'head']>;

/**
 * @see \App\Http\Controllers\DistributionController::create
 * @see app/Http/Controllers/DistributionController.php:36
 * @route '/dashboard/distributions/create'
 */
create.url = (options?: RouteQueryOptions) => {
    return create.definition.url + queryParams(options);
};

/**
 * @see \App\Http\Controllers\DistributionController::create
 * @see app/Http/Controllers/DistributionController.php:36
 * @route '/dashboard/distributions/create'
 */
create.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\DistributionController::create
 * @see app/Http/Controllers/DistributionController.php:36
 * @route '/dashboard/distributions/create'
 */
create.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(options),
    method: 'head',
});

/**
 * @see \App\Http\Controllers\DistributionController::create
 * @see app/Http/Controllers/DistributionController.php:36
 * @route '/dashboard/distributions/create'
 */
const createForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\DistributionController::create
 * @see app/Http/Controllers/DistributionController.php:36
 * @route '/dashboard/distributions/create'
 */
createForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\DistributionController::create
 * @see app/Http/Controllers/DistributionController.php:36
 * @route '/dashboard/distributions/create'
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
 * @see \App\Http\Controllers\DistributionController::data
 * @see app/Http/Controllers/DistributionController.php:54
 * @route '/dashboard/distributions/data'
 */
export const data = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: data.url(options),
    method: 'get',
});

data.definition = {
    methods: ['get', 'head'],
    url: '/dashboard/distributions/data',
} satisfies RouteDefinition<['get', 'head']>;

/**
 * @see \App\Http\Controllers\DistributionController::data
 * @see app/Http/Controllers/DistributionController.php:54
 * @route '/dashboard/distributions/data'
 */
data.url = (options?: RouteQueryOptions) => {
    return data.definition.url + queryParams(options);
};

/**
 * @see \App\Http\Controllers\DistributionController::data
 * @see app/Http/Controllers/DistributionController.php:54
 * @route '/dashboard/distributions/data'
 */
data.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: data.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\DistributionController::data
 * @see app/Http/Controllers/DistributionController.php:54
 * @route '/dashboard/distributions/data'
 */
data.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: data.url(options),
    method: 'head',
});

/**
 * @see \App\Http\Controllers\DistributionController::data
 * @see app/Http/Controllers/DistributionController.php:54
 * @route '/dashboard/distributions/data'
 */
const dataForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: data.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\DistributionController::data
 * @see app/Http/Controllers/DistributionController.php:54
 * @route '/dashboard/distributions/data'
 */
dataForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: data.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\DistributionController::data
 * @see app/Http/Controllers/DistributionController.php:54
 * @route '/dashboard/distributions/data'
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
 * @see \App\Http\Controllers\DistributionController::show
 * @see app/Http/Controllers/DistributionController.php:81
 * @route '/dashboard/distributions/{id}'
 */
export const show = (
    args: { id: string | number } | [id: string | number] | string | number,
    options?: RouteQueryOptions,
): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
});

show.definition = {
    methods: ['get', 'head'],
    url: '/dashboard/distributions/{id}',
} satisfies RouteDefinition<['get', 'head']>;

/**
 * @see \App\Http\Controllers\DistributionController::show
 * @see app/Http/Controllers/DistributionController.php:81
 * @route '/dashboard/distributions/{id}'
 */
show.url = (args: { id: string | number } | [id: string | number] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { id: args };
    }

    if (Array.isArray(args)) {
        args = {
            id: args[0],
        };
    }

    args = applyUrlDefaults(args);

    const parsedArgs = {
        id: args.id,
    };

    return show.definition.url.replace('{id}', parsedArgs.id.toString()).replace(/\/+$/, '') + queryParams(options);
};

/**
 * @see \App\Http\Controllers\DistributionController::show
 * @see app/Http/Controllers/DistributionController.php:81
 * @route '/dashboard/distributions/{id}'
 */
show.get = (args: { id: string | number } | [id: string | number] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\DistributionController::show
 * @see app/Http/Controllers/DistributionController.php:81
 * @route '/dashboard/distributions/{id}'
 */
show.head = (args: { id: string | number } | [id: string | number] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
});

/**
 * @see \App\Http\Controllers\DistributionController::show
 * @see app/Http/Controllers/DistributionController.php:81
 * @route '/dashboard/distributions/{id}'
 */
const showForm = (
    args: { id: string | number } | [id: string | number] | string | number,
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\DistributionController::show
 * @see app/Http/Controllers/DistributionController.php:81
 * @route '/dashboard/distributions/{id}'
 */
showForm.get = (
    args: { id: string | number } | [id: string | number] | string | number,
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\DistributionController::show
 * @see app/Http/Controllers/DistributionController.php:81
 * @route '/dashboard/distributions/{id}'
 */
showForm.head = (
    args: { id: string | number } | [id: string | number] | string | number,
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
 * @see \App\Http\Controllers\DistributionController::store
 * @see app/Http/Controllers/DistributionController.php:67
 * @route '/dashboard/distributions'
 */
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
});

store.definition = {
    methods: ['post'],
    url: '/dashboard/distributions',
} satisfies RouteDefinition<['post']>;

/**
 * @see \App\Http\Controllers\DistributionController::store
 * @see app/Http/Controllers/DistributionController.php:67
 * @route '/dashboard/distributions'
 */
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options);
};

/**
 * @see \App\Http\Controllers\DistributionController::store
 * @see app/Http/Controllers/DistributionController.php:67
 * @route '/dashboard/distributions'
 */
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
});

/**
 * @see \App\Http\Controllers\DistributionController::store
 * @see app/Http/Controllers/DistributionController.php:67
 * @route '/dashboard/distributions'
 */
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
});

/**
 * @see \App\Http\Controllers\DistributionController::store
 * @see app/Http/Controllers/DistributionController.php:67
 * @route '/dashboard/distributions'
 */
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
});

store.form = storeForm;

/**
 * @see \App\Http\Controllers\DistributionController::update
 * @see app/Http/Controllers/DistributionController.php:97
 * @route '/dashboard/distributions/{id}'
 */
export const update = (
    args: { id: string | number } | [id: string | number] | string | number,
    options?: RouteQueryOptions,
): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
});

update.definition = {
    methods: ['put'],
    url: '/dashboard/distributions/{id}',
} satisfies RouteDefinition<['put']>;

/**
 * @see \App\Http\Controllers\DistributionController::update
 * @see app/Http/Controllers/DistributionController.php:97
 * @route '/dashboard/distributions/{id}'
 */
update.url = (args: { id: string | number } | [id: string | number] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { id: args };
    }

    if (Array.isArray(args)) {
        args = {
            id: args[0],
        };
    }

    args = applyUrlDefaults(args);

    const parsedArgs = {
        id: args.id,
    };

    return update.definition.url.replace('{id}', parsedArgs.id.toString()).replace(/\/+$/, '') + queryParams(options);
};

/**
 * @see \App\Http\Controllers\DistributionController::update
 * @see app/Http/Controllers/DistributionController.php:97
 * @route '/dashboard/distributions/{id}'
 */
update.put = (args: { id: string | number } | [id: string | number] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
});

/**
 * @see \App\Http\Controllers\DistributionController::update
 * @see app/Http/Controllers/DistributionController.php:97
 * @route '/dashboard/distributions/{id}'
 */
const updateForm = (
    args: { id: string | number } | [id: string | number] | string | number,
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
 * @see \App\Http\Controllers\DistributionController::update
 * @see app/Http/Controllers/DistributionController.php:97
 * @route '/dashboard/distributions/{id}'
 */
updateForm.put = (
    args: { id: string | number } | [id: string | number] | string | number,
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
 * @see \App\Http\Controllers\DistributionController::destroy
 * @see app/Http/Controllers/DistributionController.php:107
 * @route '/dashboard/distributions/{id}'
 */
export const destroy = (
    args: { id: string | number } | [id: string | number] | string | number,
    options?: RouteQueryOptions,
): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
});

destroy.definition = {
    methods: ['delete'],
    url: '/dashboard/distributions/{id}',
} satisfies RouteDefinition<['delete']>;

/**
 * @see \App\Http\Controllers\DistributionController::destroy
 * @see app/Http/Controllers/DistributionController.php:107
 * @route '/dashboard/distributions/{id}'
 */
destroy.url = (args: { id: string | number } | [id: string | number] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { id: args };
    }

    if (Array.isArray(args)) {
        args = {
            id: args[0],
        };
    }

    args = applyUrlDefaults(args);

    const parsedArgs = {
        id: args.id,
    };

    return destroy.definition.url.replace('{id}', parsedArgs.id.toString()).replace(/\/+$/, '') + queryParams(options);
};

/**
 * @see \App\Http\Controllers\DistributionController::destroy
 * @see app/Http/Controllers/DistributionController.php:107
 * @route '/dashboard/distributions/{id}'
 */
destroy.delete = (
    args: { id: string | number } | [id: string | number] | string | number,
    options?: RouteQueryOptions,
): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
});

/**
 * @see \App\Http\Controllers\DistributionController::destroy
 * @see app/Http/Controllers/DistributionController.php:107
 * @route '/dashboard/distributions/{id}'
 */
const destroyForm = (
    args: { id: string | number } | [id: string | number] | string | number,
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
 * @see \App\Http\Controllers\DistributionController::destroy
 * @see app/Http/Controllers/DistributionController.php:107
 * @route '/dashboard/distributions/{id}'
 */
destroyForm.delete = (
    args: { id: string | number } | [id: string | number] | string | number,
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

/**
 * @see \App\Http\Controllers\DistributionController::adjustProfit
 * @see app/Http/Controllers/DistributionController.php:116
 * @route '/dashboard/distributions/{id}/adjust-profit'
 */
export const adjustProfit = (
    args: { id: string | number } | [id: string | number] | string | number,
    options?: RouteQueryOptions,
): RouteDefinition<'put'> => ({
    url: adjustProfit.url(args, options),
    method: 'put',
});

adjustProfit.definition = {
    methods: ['put'],
    url: '/dashboard/distributions/{id}/adjust-profit',
} satisfies RouteDefinition<['put']>;

/**
 * @see \App\Http\Controllers\DistributionController::adjustProfit
 * @see app/Http/Controllers/DistributionController.php:116
 * @route '/dashboard/distributions/{id}/adjust-profit'
 */
adjustProfit.url = (args: { id: string | number } | [id: string | number] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { id: args };
    }

    if (Array.isArray(args)) {
        args = {
            id: args[0],
        };
    }

    args = applyUrlDefaults(args);

    const parsedArgs = {
        id: args.id,
    };

    return adjustProfit.definition.url.replace('{id}', parsedArgs.id.toString()).replace(/\/+$/, '') + queryParams(options);
};

/**
 * @see \App\Http\Controllers\DistributionController::adjustProfit
 * @see app/Http/Controllers/DistributionController.php:116
 * @route '/dashboard/distributions/{id}/adjust-profit'
 */
adjustProfit.put = (
    args: { id: string | number } | [id: string | number] | string | number,
    options?: RouteQueryOptions,
): RouteDefinition<'put'> => ({
    url: adjustProfit.url(args, options),
    method: 'put',
});

/**
 * @see \App\Http\Controllers\DistributionController::adjustProfit
 * @see app/Http/Controllers/DistributionController.php:116
 * @route '/dashboard/distributions/{id}/adjust-profit'
 */
const adjustProfitForm = (
    args: { id: string | number } | [id: string | number] | string | number,
    options?: RouteQueryOptions,
): RouteFormDefinition<'post'> => ({
    action: adjustProfit.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        },
    }),
    method: 'post',
});

/**
 * @see \App\Http\Controllers\DistributionController::adjustProfit
 * @see app/Http/Controllers/DistributionController.php:116
 * @route '/dashboard/distributions/{id}/adjust-profit'
 */
adjustProfitForm.put = (
    args: { id: string | number } | [id: string | number] | string | number,
    options?: RouteQueryOptions,
): RouteFormDefinition<'post'> => ({
    action: adjustProfit.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        },
    }),
    method: 'post',
});

adjustProfit.form = adjustProfitForm;

/**
 * @see \App\Http\Controllers\DistributionController::process
 * @see app/Http/Controllers/DistributionController.php:130
 * @route '/dashboard/distributions/{id}/process'
 */
export const process = (
    args: { id: string | number } | [id: string | number] | string | number,
    options?: RouteQueryOptions,
): RouteDefinition<'post'> => ({
    url: process.url(args, options),
    method: 'post',
});

process.definition = {
    methods: ['post'],
    url: '/dashboard/distributions/{id}/process',
} satisfies RouteDefinition<['post']>;

/**
 * @see \App\Http\Controllers\DistributionController::process
 * @see app/Http/Controllers/DistributionController.php:130
 * @route '/dashboard/distributions/{id}/process'
 */
process.url = (args: { id: string | number } | [id: string | number] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { id: args };
    }

    if (Array.isArray(args)) {
        args = {
            id: args[0],
        };
    }

    args = applyUrlDefaults(args);

    const parsedArgs = {
        id: args.id,
    };

    return process.definition.url.replace('{id}', parsedArgs.id.toString()).replace(/\/+$/, '') + queryParams(options);
};

/**
 * @see \App\Http\Controllers\DistributionController::process
 * @see app/Http/Controllers/DistributionController.php:130
 * @route '/dashboard/distributions/{id}/process'
 */
process.post = (args: { id: string | number } | [id: string | number] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: process.url(args, options),
    method: 'post',
});

/**
 * @see \App\Http\Controllers\DistributionController::process
 * @see app/Http/Controllers/DistributionController.php:130
 * @route '/dashboard/distributions/{id}/process'
 */
const processForm = (
    args: { id: string | number } | [id: string | number] | string | number,
    options?: RouteQueryOptions,
): RouteFormDefinition<'post'> => ({
    action: process.url(args, options),
    method: 'post',
});

/**
 * @see \App\Http\Controllers\DistributionController::process
 * @see app/Http/Controllers/DistributionController.php:130
 * @route '/dashboard/distributions/{id}/process'
 */
processForm.post = (
    args: { id: string | number } | [id: string | number] | string | number,
    options?: RouteQueryOptions,
): RouteFormDefinition<'post'> => ({
    action: process.url(args, options),
    method: 'post',
});

process.form = processForm;

/**
 * @see \App\Http\Controllers\DistributionController::downloadStatement
 * @see app/Http/Controllers/DistributionController.php:143
 * @route '/dashboard/distributions/{id}/statements/{shareholderId}'
 */
export const downloadStatement = (
    args: { id: string | number; shareholderId: string | number } | [id: string | number, shareholderId: string | number],
    options?: RouteQueryOptions,
): RouteDefinition<'get'> => ({
    url: downloadStatement.url(args, options),
    method: 'get',
});

downloadStatement.definition = {
    methods: ['get', 'head'],
    url: '/dashboard/distributions/{id}/statements/{shareholderId}',
} satisfies RouteDefinition<['get', 'head']>;

/**
 * @see \App\Http\Controllers\DistributionController::downloadStatement
 * @see app/Http/Controllers/DistributionController.php:143
 * @route '/dashboard/distributions/{id}/statements/{shareholderId}'
 */
downloadStatement.url = (
    args: { id: string | number; shareholderId: string | number } | [id: string | number, shareholderId: string | number],
    options?: RouteQueryOptions,
) => {
    if (Array.isArray(args)) {
        args = {
            id: args[0],
            shareholderId: args[1],
        };
    }

    args = applyUrlDefaults(args);

    const parsedArgs = {
        id: args.id,
        shareholderId: args.shareholderId,
    };

    return (
        downloadStatement.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace('{shareholderId}', parsedArgs.shareholderId.toString())
            .replace(/\/+$/, '') + queryParams(options)
    );
};

/**
 * @see \App\Http\Controllers\DistributionController::downloadStatement
 * @see app/Http/Controllers/DistributionController.php:143
 * @route '/dashboard/distributions/{id}/statements/{shareholderId}'
 */
downloadStatement.get = (
    args: { id: string | number; shareholderId: string | number } | [id: string | number, shareholderId: string | number],
    options?: RouteQueryOptions,
): RouteDefinition<'get'> => ({
    url: downloadStatement.url(args, options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\DistributionController::downloadStatement
 * @see app/Http/Controllers/DistributionController.php:143
 * @route '/dashboard/distributions/{id}/statements/{shareholderId}'
 */
downloadStatement.head = (
    args: { id: string | number; shareholderId: string | number } | [id: string | number, shareholderId: string | number],
    options?: RouteQueryOptions,
): RouteDefinition<'head'> => ({
    url: downloadStatement.url(args, options),
    method: 'head',
});

/**
 * @see \App\Http\Controllers\DistributionController::downloadStatement
 * @see app/Http/Controllers/DistributionController.php:143
 * @route '/dashboard/distributions/{id}/statements/{shareholderId}'
 */
const downloadStatementForm = (
    args: { id: string | number; shareholderId: string | number } | [id: string | number, shareholderId: string | number],
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: downloadStatement.url(args, options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\DistributionController::downloadStatement
 * @see app/Http/Controllers/DistributionController.php:143
 * @route '/dashboard/distributions/{id}/statements/{shareholderId}'
 */
downloadStatementForm.get = (
    args: { id: string | number; shareholderId: string | number } | [id: string | number, shareholderId: string | number],
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: downloadStatement.url(args, options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\DistributionController::downloadStatement
 * @see app/Http/Controllers/DistributionController.php:143
 * @route '/dashboard/distributions/{id}/statements/{shareholderId}'
 */
downloadStatementForm.head = (
    args: { id: string | number; shareholderId: string | number } | [id: string | number, shareholderId: string | number],
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: downloadStatement.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        },
    }),
    method: 'get',
});

downloadStatement.form = downloadStatementForm;

const distributions = {
    index,
    create,
    data,
    show,
    store,
    update,
    destroy,
    adjustProfit,
    process,
    downloadStatement,
};

export default distributions;
