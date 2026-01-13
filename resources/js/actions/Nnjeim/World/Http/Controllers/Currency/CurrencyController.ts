import {
    applyUrlDefaults,
    queryParams,
    validateParameters,
    type RouteDefinition,
    type RouteFormDefinition,
    type RouteQueryOptions,
} from './../../../../../../wayfinder';
/**
 * @see \Nnjeim\World\Http\Controllers\Currency\CurrencyController::index
 * @see vendor/nnjeim/world/src/Http/Controllers/Currency/CurrencyController.php:0
 * @route '/{prefix?}/currencies'
 */
export const index = (
    args?: { prefix?: string | number } | [prefix: string | number] | string | number,
    options?: RouteQueryOptions,
): RouteDefinition<'get'> => ({
    url: index.url(args, options),
    method: 'get',
});

index.definition = {
    methods: ['get', 'head'],
    url: '/{prefix?}/currencies',
} satisfies RouteDefinition<['get', 'head']>;

/**
 * @see \Nnjeim\World\Http\Controllers\Currency\CurrencyController::index
 * @see vendor/nnjeim/world/src/Http/Controllers/Currency/CurrencyController.php:0
 * @route '/{prefix?}/currencies'
 */
index.url = (args?: { prefix?: string | number } | [prefix: string | number] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { prefix: args };
    }

    if (Array.isArray(args)) {
        args = {
            prefix: args[0],
        };
    }

    args = applyUrlDefaults(args);

    validateParameters(args, ['prefix']);

    const parsedArgs = {
        prefix: args?.prefix,
    };

    return index.definition.url.replace('{prefix?}', parsedArgs.prefix?.toString() ?? '').replace(/\/+$/, '') + queryParams(options);
};

/**
 * @see \Nnjeim\World\Http\Controllers\Currency\CurrencyController::index
 * @see vendor/nnjeim/world/src/Http/Controllers/Currency/CurrencyController.php:0
 * @route '/{prefix?}/currencies'
 */
index.get = (
    args?: { prefix?: string | number } | [prefix: string | number] | string | number,
    options?: RouteQueryOptions,
): RouteDefinition<'get'> => ({
    url: index.url(args, options),
    method: 'get',
});

/**
 * @see \Nnjeim\World\Http\Controllers\Currency\CurrencyController::index
 * @see vendor/nnjeim/world/src/Http/Controllers/Currency/CurrencyController.php:0
 * @route '/{prefix?}/currencies'
 */
index.head = (
    args?: { prefix?: string | number } | [prefix: string | number] | string | number,
    options?: RouteQueryOptions,
): RouteDefinition<'head'> => ({
    url: index.url(args, options),
    method: 'head',
});

/**
 * @see \Nnjeim\World\Http\Controllers\Currency\CurrencyController::index
 * @see vendor/nnjeim/world/src/Http/Controllers/Currency/CurrencyController.php:0
 * @route '/{prefix?}/currencies'
 */
const indexForm = (
    args?: { prefix?: string | number } | [prefix: string | number] | string | number,
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: index.url(args, options),
    method: 'get',
});

/**
 * @see \Nnjeim\World\Http\Controllers\Currency\CurrencyController::index
 * @see vendor/nnjeim/world/src/Http/Controllers/Currency/CurrencyController.php:0
 * @route '/{prefix?}/currencies'
 */
indexForm.get = (
    args?: { prefix?: string | number } | [prefix: string | number] | string | number,
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: index.url(args, options),
    method: 'get',
});

/**
 * @see \Nnjeim\World\Http\Controllers\Currency\CurrencyController::index
 * @see vendor/nnjeim/world/src/Http/Controllers/Currency/CurrencyController.php:0
 * @route '/{prefix?}/currencies'
 */
indexForm.head = (
    args?: { prefix?: string | number } | [prefix: string | number] | string | number,
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: index.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        },
    }),
    method: 'get',
});

index.form = indexForm;

const CurrencyController = { index };

export default CurrencyController;
