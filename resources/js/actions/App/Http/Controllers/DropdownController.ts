import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\DropdownController::__invoke
* @see app/Http/Controllers/DropdownController.php:15
* @route '/dropdown'
*/
const DropdownController = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: DropdownController.url(options),
    method: 'get',
})

DropdownController.definition = {
    methods: ["get","head"],
    url: '/dropdown',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DropdownController::__invoke
* @see app/Http/Controllers/DropdownController.php:15
* @route '/dropdown'
*/
DropdownController.url = (options?: RouteQueryOptions) => {
    return DropdownController.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DropdownController::__invoke
* @see app/Http/Controllers/DropdownController.php:15
* @route '/dropdown'
*/
DropdownController.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: DropdownController.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DropdownController::__invoke
* @see app/Http/Controllers/DropdownController.php:15
* @route '/dropdown'
*/
DropdownController.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: DropdownController.url(options),
    method: 'head',
})

export default DropdownController