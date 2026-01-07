import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\TransferController::index
* @see app/Http/Controllers/TransferController.php:24
* @route '/dashboard/transfers'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/dashboard/transfers',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\TransferController::index
* @see app/Http/Controllers/TransferController.php:24
* @route '/dashboard/transfers'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\TransferController::index
* @see app/Http/Controllers/TransferController.php:24
* @route '/dashboard/transfers'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\TransferController::index
* @see app/Http/Controllers/TransferController.php:24
* @route '/dashboard/transfers'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\TransferController::data
* @see app/Http/Controllers/TransferController.php:29
* @route '/dashboard/transfers/data'
*/
export const data = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: data.url(options),
    method: 'get',
})

data.definition = {
    methods: ["get","head"],
    url: '/dashboard/transfers/data',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\TransferController::data
* @see app/Http/Controllers/TransferController.php:29
* @route '/dashboard/transfers/data'
*/
data.url = (options?: RouteQueryOptions) => {
    return data.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\TransferController::data
* @see app/Http/Controllers/TransferController.php:29
* @route '/dashboard/transfers/data'
*/
data.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: data.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\TransferController::data
* @see app/Http/Controllers/TransferController.php:29
* @route '/dashboard/transfers/data'
*/
data.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: data.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\TransferController::create
* @see app/Http/Controllers/TransferController.php:42
* @route '/dashboard/transfers/create'
*/
export const create = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/dashboard/transfers/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\TransferController::create
* @see app/Http/Controllers/TransferController.php:42
* @route '/dashboard/transfers/create'
*/
create.url = (options?: RouteQueryOptions) => {
    return create.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\TransferController::create
* @see app/Http/Controllers/TransferController.php:42
* @route '/dashboard/transfers/create'
*/
create.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\TransferController::create
* @see app/Http/Controllers/TransferController.php:42
* @route '/dashboard/transfers/create'
*/
create.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\TransferController::show
* @see app/Http/Controllers/TransferController.php:67
* @route '/dashboard/transfers/{transfer}'
*/
export const show = (args: { transfer: string | number } | [transfer: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/dashboard/transfers/{transfer}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\TransferController::show
* @see app/Http/Controllers/TransferController.php:67
* @route '/dashboard/transfers/{transfer}'
*/
show.url = (args: { transfer: string | number } | [transfer: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { transfer: args }
    }

    if (Array.isArray(args)) {
        args = {
            transfer: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        transfer: args.transfer,
    }

    return show.definition.url
            .replace('{transfer}', parsedArgs.transfer.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\TransferController::show
* @see app/Http/Controllers/TransferController.php:67
* @route '/dashboard/transfers/{transfer}'
*/
show.get = (args: { transfer: string | number } | [transfer: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\TransferController::show
* @see app/Http/Controllers/TransferController.php:67
* @route '/dashboard/transfers/{transfer}'
*/
show.head = (args: { transfer: string | number } | [transfer: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\TransferController::store
* @see app/Http/Controllers/TransferController.php:57
* @route '/dashboard/transfers'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/dashboard/transfers',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\TransferController::store
* @see app/Http/Controllers/TransferController.php:57
* @route '/dashboard/transfers'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\TransferController::store
* @see app/Http/Controllers/TransferController.php:57
* @route '/dashboard/transfers'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

const TransferController = { index, data, create, show, store }

export default TransferController