import { queryParams, type RouteDefinition, type RouteFormDefinition, type RouteQueryOptions } from './../../../../wayfinder';
/**
 * @see \App\Http\Controllers\DropdownController::__invoke
 * @see app/Http/Controllers/DropdownController.php:15
 * @route '/dropdown'
 */
const DropdownController = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: DropdownController.url(options),
    method: 'get',
});

DropdownController.definition = {
    methods: ['get', 'head'],
    url: '/dropdown',
} satisfies RouteDefinition<['get', 'head']>;

/**
 * @see \App\Http\Controllers\DropdownController::__invoke
 * @see app/Http/Controllers/DropdownController.php:15
 * @route '/dropdown'
 */
DropdownController.url = (options?: RouteQueryOptions) => {
    return DropdownController.definition.url + queryParams(options);
};

/**
 * @see \App\Http\Controllers\DropdownController::__invoke
 * @see app/Http/Controllers/DropdownController.php:15
 * @route '/dropdown'
 */
DropdownController.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: DropdownController.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\DropdownController::__invoke
 * @see app/Http/Controllers/DropdownController.php:15
 * @route '/dropdown'
 */
DropdownController.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: DropdownController.url(options),
    method: 'head',
});

/**
 * @see \App\Http\Controllers\DropdownController::__invoke
 * @see app/Http/Controllers/DropdownController.php:15
 * @route '/dropdown'
 */
const DropdownControllerForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: DropdownController.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\DropdownController::__invoke
 * @see app/Http/Controllers/DropdownController.php:15
 * @route '/dropdown'
 */
DropdownControllerForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: DropdownController.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\DropdownController::__invoke
 * @see app/Http/Controllers/DropdownController.php:15
 * @route '/dropdown'
 */
DropdownControllerForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: DropdownController.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        },
    }),
    method: 'get',
});

DropdownController.form = DropdownControllerForm;

export default DropdownController;
