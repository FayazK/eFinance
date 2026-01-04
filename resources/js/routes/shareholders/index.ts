import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../wayfinder'
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
* @see \App\Http\Controllers\ShareholderController::index
* @see app/Http/Controllers/ShareholderController.php:22
* @route '/dashboard/shareholders'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ShareholderController::index
* @see app/Http/Controllers/ShareholderController.php:22
* @route '/dashboard/shareholders'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ShareholderController::index
* @see app/Http/Controllers/ShareholderController.php:22
* @route '/dashboard/shareholders'
*/
indexForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

index.form = indexForm

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
* @see \App\Http\Controllers\ShareholderController::data
* @see app/Http/Controllers/ShareholderController.php:27
* @route '/dashboard/shareholders/data'
*/
const dataForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: data.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ShareholderController::data
* @see app/Http/Controllers/ShareholderController.php:27
* @route '/dashboard/shareholders/data'
*/
dataForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: data.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ShareholderController::data
* @see app/Http/Controllers/ShareholderController.php:27
* @route '/dashboard/shareholders/data'
*/
dataForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: data.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

data.form = dataForm

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
* @see \App\Http\Controllers\ShareholderController::validateEquity
* @see app/Http/Controllers/ShareholderController.php:69
* @route '/dashboard/shareholders/validate-equity'
*/
const validateEquityForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: validateEquity.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ShareholderController::validateEquity
* @see app/Http/Controllers/ShareholderController.php:69
* @route '/dashboard/shareholders/validate-equity'
*/
validateEquityForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: validateEquity.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ShareholderController::validateEquity
* @see app/Http/Controllers/ShareholderController.php:69
* @route '/dashboard/shareholders/validate-equity'
*/
validateEquityForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: validateEquity.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

validateEquity.form = validateEquityForm

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
* @see \App\Http\Controllers\ShareholderController::store
* @see app/Http/Controllers/ShareholderController.php:40
* @route '/dashboard/shareholders'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\ShareholderController::store
* @see app/Http/Controllers/ShareholderController.php:40
* @route '/dashboard/shareholders'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

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
* @see \App\Http\Controllers\ShareholderController::update
* @see app/Http/Controllers/ShareholderController.php:50
* @route '/dashboard/shareholders/{id}'
*/
const updateForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\ShareholderController::update
* @see app/Http/Controllers/ShareholderController.php:50
* @route '/dashboard/shareholders/{id}'
*/
updateForm.put = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

update.form = updateForm

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

/**
* @see \App\Http\Controllers\ShareholderController::destroy
* @see app/Http/Controllers/ShareholderController.php:60
* @route '/dashboard/shareholders/{id}'
*/
const destroyForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\ShareholderController::destroy
* @see app/Http/Controllers/ShareholderController.php:60
* @route '/dashboard/shareholders/{id}'
*/
destroyForm.delete = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroy.form = destroyForm

const shareholders = {
    index,
    data,
    validateEquity,
    store,
    update,
    destroy,
}

export default shareholders