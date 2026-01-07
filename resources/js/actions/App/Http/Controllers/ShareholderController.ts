import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\ShareholderController::index
* @see app/Http/Controllers/ShareholderController.php:22
* @route '/dashboard/shareholders'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/dashboard/shareholders',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\ShareholderController::index
* @see app/Http/Controllers/ShareholderController.php:22
* @route '/dashboard/shareholders'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\ShareholderController::index
* @see app/Http/Controllers/ShareholderController.php:22
* @route '/dashboard/shareholders'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ShareholderController::index
* @see app/Http/Controllers/ShareholderController.php:22
* @route '/dashboard/shareholders'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\ShareholderController::data
* @see app/Http/Controllers/ShareholderController.php:27
* @route '/dashboard/shareholders/data'
*/
export const data = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: data.url(options),
    method: 'get',
})

data.definition = {
    methods: ["get","head"],
    url: '/dashboard/shareholders/data',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\ShareholderController::data
* @see app/Http/Controllers/ShareholderController.php:27
* @route '/dashboard/shareholders/data'
*/
data.url = (options?: RouteQueryOptions) => {
    return data.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\ShareholderController::data
* @see app/Http/Controllers/ShareholderController.php:27
* @route '/dashboard/shareholders/data'
*/
data.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: data.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ShareholderController::data
* @see app/Http/Controllers/ShareholderController.php:27
* @route '/dashboard/shareholders/data'
*/
data.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: data.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\ShareholderController::validateEquity
* @see app/Http/Controllers/ShareholderController.php:69
* @route '/dashboard/shareholders/validate-equity'
*/
export const validateEquity = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: validateEquity.url(options),
    method: 'get',
})

validateEquity.definition = {
    methods: ["get","head"],
    url: '/dashboard/shareholders/validate-equity',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\ShareholderController::validateEquity
* @see app/Http/Controllers/ShareholderController.php:69
* @route '/dashboard/shareholders/validate-equity'
*/
validateEquity.url = (options?: RouteQueryOptions) => {
    return validateEquity.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\ShareholderController::validateEquity
* @see app/Http/Controllers/ShareholderController.php:69
* @route '/dashboard/shareholders/validate-equity'
*/
validateEquity.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: validateEquity.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ShareholderController::validateEquity
* @see app/Http/Controllers/ShareholderController.php:69
* @route '/dashboard/shareholders/validate-equity'
*/
validateEquity.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: validateEquity.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\ShareholderController::store
* @see app/Http/Controllers/ShareholderController.php:40
* @route '/dashboard/shareholders'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/dashboard/shareholders',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\ShareholderController::store
* @see app/Http/Controllers/ShareholderController.php:40
* @route '/dashboard/shareholders'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\ShareholderController::store
* @see app/Http/Controllers/ShareholderController.php:40
* @route '/dashboard/shareholders'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\ShareholderController::update
* @see app/Http/Controllers/ShareholderController.php:50
* @route '/dashboard/shareholders/{id}'
*/
export const update = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/dashboard/shareholders/{id}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\ShareholderController::update
* @see app/Http/Controllers/ShareholderController.php:50
* @route '/dashboard/shareholders/{id}'
*/
update.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return update.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ShareholderController::update
* @see app/Http/Controllers/ShareholderController.php:50
* @route '/dashboard/shareholders/{id}'
*/
update.put = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\ShareholderController::destroy
* @see app/Http/Controllers/ShareholderController.php:60
* @route '/dashboard/shareholders/{id}'
*/
export const destroy = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/dashboard/shareholders/{id}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\ShareholderController::destroy
* @see app/Http/Controllers/ShareholderController.php:60
* @route '/dashboard/shareholders/{id}'
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
* @see \App\Http\Controllers\ShareholderController::destroy
* @see app/Http/Controllers/ShareholderController.php:60
* @route '/dashboard/shareholders/{id}'
*/
destroy.delete = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

const ShareholderController = { index, data, validateEquity, store, update, destroy }

export default ShareholderController