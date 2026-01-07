import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../wayfinder'
/**
* @see \App\Http\Controllers\ExpenseController::index
* @see app/Http/Controllers/ExpenseController.php:23
* @route '/dashboard/expenses'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/dashboard/expenses',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\ExpenseController::index
* @see app/Http/Controllers/ExpenseController.php:23
* @route '/dashboard/expenses'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\ExpenseController::index
* @see app/Http/Controllers/ExpenseController.php:23
* @route '/dashboard/expenses'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ExpenseController::index
* @see app/Http/Controllers/ExpenseController.php:23
* @route '/dashboard/expenses'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\ExpenseController::data
* @see app/Http/Controllers/ExpenseController.php:31
* @route '/dashboard/expenses/data'
*/
export const data = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: data.url(options),
    method: 'get',
})

data.definition = {
    methods: ["get","head"],
    url: '/dashboard/expenses/data',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\ExpenseController::data
* @see app/Http/Controllers/ExpenseController.php:31
* @route '/dashboard/expenses/data'
*/
data.url = (options?: RouteQueryOptions) => {
    return data.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\ExpenseController::data
* @see app/Http/Controllers/ExpenseController.php:31
* @route '/dashboard/expenses/data'
*/
data.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: data.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ExpenseController::data
* @see app/Http/Controllers/ExpenseController.php:31
* @route '/dashboard/expenses/data'
*/
data.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: data.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\ExpenseController::create
* @see app/Http/Controllers/ExpenseController.php:50
* @route '/dashboard/expenses/create'
*/
export const create = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/dashboard/expenses/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\ExpenseController::create
* @see app/Http/Controllers/ExpenseController.php:50
* @route '/dashboard/expenses/create'
*/
create.url = (options?: RouteQueryOptions) => {
    return create.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\ExpenseController::create
* @see app/Http/Controllers/ExpenseController.php:50
* @route '/dashboard/expenses/create'
*/
create.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ExpenseController::create
* @see app/Http/Controllers/ExpenseController.php:50
* @route '/dashboard/expenses/create'
*/
create.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\ExpenseController::show
* @see app/Http/Controllers/ExpenseController.php:98
* @route '/dashboard/expenses/{id}'
*/
export const show = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/dashboard/expenses/{id}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\ExpenseController::show
* @see app/Http/Controllers/ExpenseController.php:98
* @route '/dashboard/expenses/{id}'
*/
show.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { id: args }
    }

    if (Array.isArray(args)) {
        args = {
            id: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        id: args.id,
    }

    return show.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ExpenseController::show
* @see app/Http/Controllers/ExpenseController.php:98
* @route '/dashboard/expenses/{id}'
*/
show.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ExpenseController::show
* @see app/Http/Controllers/ExpenseController.php:98
* @route '/dashboard/expenses/{id}'
*/
show.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\ExpenseController::store
* @see app/Http/Controllers/ExpenseController.php:71
* @route '/dashboard/expenses'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/dashboard/expenses',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\ExpenseController::store
* @see app/Http/Controllers/ExpenseController.php:71
* @route '/dashboard/expenses'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\ExpenseController::store
* @see app/Http/Controllers/ExpenseController.php:71
* @route '/dashboard/expenses'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\ExpenseController::destroy
* @see app/Http/Controllers/ExpenseController.php:111
* @route '/dashboard/expenses/{id}'
*/
export const destroy = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/dashboard/expenses/{id}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\ExpenseController::destroy
* @see app/Http/Controllers/ExpenseController.php:111
* @route '/dashboard/expenses/{id}'
*/
destroy.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { id: args }
    }

    if (Array.isArray(args)) {
        args = {
            id: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        id: args.id,
    }

    return destroy.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ExpenseController::destroy
* @see app/Http/Controllers/ExpenseController.php:111
* @route '/dashboard/expenses/{id}'
*/
destroy.delete = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\ExpenseController::lastExchangeRate
* @see app/Http/Controllers/ExpenseController.php:123
* @route '/dashboard/expenses/last-exchange-rate/{currency}'
*/
export const lastExchangeRate = (args: { currency: string | number } | [currency: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: lastExchangeRate.url(args, options),
    method: 'get',
})

lastExchangeRate.definition = {
    methods: ["get","head"],
    url: '/dashboard/expenses/last-exchange-rate/{currency}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\ExpenseController::lastExchangeRate
* @see app/Http/Controllers/ExpenseController.php:123
* @route '/dashboard/expenses/last-exchange-rate/{currency}'
*/
lastExchangeRate.url = (args: { currency: string | number } | [currency: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { currency: args }
    }

    if (Array.isArray(args)) {
        args = {
            currency: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        currency: args.currency,
    }

    return lastExchangeRate.definition.url
            .replace('{currency}', parsedArgs.currency.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ExpenseController::lastExchangeRate
* @see app/Http/Controllers/ExpenseController.php:123
* @route '/dashboard/expenses/last-exchange-rate/{currency}'
*/
lastExchangeRate.get = (args: { currency: string | number } | [currency: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: lastExchangeRate.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ExpenseController::lastExchangeRate
* @see app/Http/Controllers/ExpenseController.php:123
* @route '/dashboard/expenses/last-exchange-rate/{currency}'
*/
lastExchangeRate.head = (args: { currency: string | number } | [currency: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: lastExchangeRate.url(args, options),
    method: 'head',
})

const expenses = {
    index,
    data,
    create,
    show,
    store,
    destroy,
    lastExchangeRate,
}

export default expenses