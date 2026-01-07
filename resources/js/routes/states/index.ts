import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults, validateParameters } from './../../wayfinder'
/**
* @see \Nnjeim\World\Http\Controllers\State\StateController::index
* @see vendor/nnjeim/world/src/Http/Controllers/State/StateController.php:0
* @route '/{prefix?}/states'
*/
export const index = (args?: { prefix?: string | number } | [prefix: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(args, options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/{prefix?}/states',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Nnjeim\World\Http\Controllers\State\StateController::index
* @see vendor/nnjeim/world/src/Http/Controllers/State/StateController.php:0
* @route '/{prefix?}/states'
*/
index.url = (args?: { prefix?: string | number } | [prefix: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { prefix: args }
    }

    if (Array.isArray(args)) {
        args = {
            prefix: args[0],
        }
    }

    args = applyUrlDefaults(args)

    validateParameters(args, [
        "prefix",
    ])

    const parsedArgs = {
        prefix: args?.prefix,
    }

    return index.definition.url
            .replace('{prefix?}', parsedArgs.prefix?.toString() ?? '')
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Nnjeim\World\Http\Controllers\State\StateController::index
* @see vendor/nnjeim/world/src/Http/Controllers/State/StateController.php:0
* @route '/{prefix?}/states'
*/
index.get = (args?: { prefix?: string | number } | [prefix: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(args, options),
    method: 'get',
})

/**
* @see \Nnjeim\World\Http\Controllers\State\StateController::index
* @see vendor/nnjeim/world/src/Http/Controllers/State/StateController.php:0
* @route '/{prefix?}/states'
*/
index.head = (args?: { prefix?: string | number } | [prefix: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(args, options),
    method: 'head',
})

const states = {
    index,
}

export default states