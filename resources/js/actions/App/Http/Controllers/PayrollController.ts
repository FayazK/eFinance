import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\PayrollController::index
* @see app/Http/Controllers/PayrollController.php:25
* @route '/dashboard/payroll'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/dashboard/payroll',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\PayrollController::index
* @see app/Http/Controllers/PayrollController.php:25
* @route '/dashboard/payroll'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\PayrollController::index
* @see app/Http/Controllers/PayrollController.php:25
* @route '/dashboard/payroll'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\PayrollController::index
* @see app/Http/Controllers/PayrollController.php:25
* @route '/dashboard/payroll'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\PayrollController::data
* @see app/Http/Controllers/PayrollController.php:41
* @route '/dashboard/payroll/data'
*/
export const data = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: data.url(options),
    method: 'get',
})

data.definition = {
    methods: ["get","head"],
    url: '/dashboard/payroll/data',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\PayrollController::data
* @see app/Http/Controllers/PayrollController.php:41
* @route '/dashboard/payroll/data'
*/
data.url = (options?: RouteQueryOptions) => {
    return data.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\PayrollController::data
* @see app/Http/Controllers/PayrollController.php:41
* @route '/dashboard/payroll/data'
*/
data.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: data.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\PayrollController::data
* @see app/Http/Controllers/PayrollController.php:41
* @route '/dashboard/payroll/data'
*/
data.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: data.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\PayrollController::show
* @see app/Http/Controllers/PayrollController.php:96
* @route '/dashboard/payroll/{payroll}'
*/
export const show = (args: { payroll: number | { id: number } } | [payroll: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/dashboard/payroll/{payroll}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\PayrollController::show
* @see app/Http/Controllers/PayrollController.php:96
* @route '/dashboard/payroll/{payroll}'
*/
show.url = (args: { payroll: number | { id: number } } | [payroll: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { payroll: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { payroll: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            payroll: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        payroll: typeof args.payroll === 'object'
        ? args.payroll.id
        : args.payroll,
    }

    return show.definition.url
            .replace('{payroll}', parsedArgs.payroll.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\PayrollController::show
* @see app/Http/Controllers/PayrollController.php:96
* @route '/dashboard/payroll/{payroll}'
*/
show.get = (args: { payroll: number | { id: number } } | [payroll: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\PayrollController::show
* @see app/Http/Controllers/PayrollController.php:96
* @route '/dashboard/payroll/{payroll}'
*/
show.head = (args: { payroll: number | { id: number } } | [payroll: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\PayrollController::generate
* @see app/Http/Controllers/PayrollController.php:60
* @route '/dashboard/payroll/generate'
*/
export const generate = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: generate.url(options),
    method: 'post',
})

generate.definition = {
    methods: ["post"],
    url: '/dashboard/payroll/generate',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\PayrollController::generate
* @see app/Http/Controllers/PayrollController.php:60
* @route '/dashboard/payroll/generate'
*/
generate.url = (options?: RouteQueryOptions) => {
    return generate.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\PayrollController::generate
* @see app/Http/Controllers/PayrollController.php:60
* @route '/dashboard/payroll/generate'
*/
generate.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: generate.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\PayrollController::updateAdjustments
* @see app/Http/Controllers/PayrollController.php:73
* @route '/dashboard/payroll/{id}/adjustments'
*/
export const updateAdjustments = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: updateAdjustments.url(args, options),
    method: 'put',
})

updateAdjustments.definition = {
    methods: ["put"],
    url: '/dashboard/payroll/{id}/adjustments',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\PayrollController::updateAdjustments
* @see app/Http/Controllers/PayrollController.php:73
* @route '/dashboard/payroll/{id}/adjustments'
*/
updateAdjustments.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return updateAdjustments.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\PayrollController::updateAdjustments
* @see app/Http/Controllers/PayrollController.php:73
* @route '/dashboard/payroll/{id}/adjustments'
*/
updateAdjustments.put = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: updateAdjustments.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\PayrollController::pay
* @see app/Http/Controllers/PayrollController.php:83
* @route '/dashboard/payroll/pay'
*/
export const pay = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: pay.url(options),
    method: 'post',
})

pay.definition = {
    methods: ["post"],
    url: '/dashboard/payroll/pay',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\PayrollController::pay
* @see app/Http/Controllers/PayrollController.php:83
* @route '/dashboard/payroll/pay'
*/
pay.url = (options?: RouteQueryOptions) => {
    return pay.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\PayrollController::pay
* @see app/Http/Controllers/PayrollController.php:83
* @route '/dashboard/payroll/pay'
*/
pay.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: pay.url(options),
    method: 'post',
})

const PayrollController = { index, data, show, generate, updateAdjustments, pay }

export default PayrollController