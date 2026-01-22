import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../wayfinder'
/**
* @see \App\Http\Controllers\ExpenseController::index
* @see app/Http/Controllers/ExpenseController.php:27
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
* @see app/Http/Controllers/ExpenseController.php:27
* @route '/dashboard/expenses'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\ExpenseController::index
* @see app/Http/Controllers/ExpenseController.php:27
* @route '/dashboard/expenses'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ExpenseController::index
* @see app/Http/Controllers/ExpenseController.php:27
* @route '/dashboard/expenses'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\ExpenseController::index
* @see app/Http/Controllers/ExpenseController.php:27
* @route '/dashboard/expenses'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ExpenseController::index
* @see app/Http/Controllers/ExpenseController.php:27
* @route '/dashboard/expenses'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ExpenseController::index
* @see app/Http/Controllers/ExpenseController.php:27
* @route '/dashboard/expenses'
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
* @see \App\Http\Controllers\ExpenseController::data
* @see app/Http/Controllers/ExpenseController.php:35
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
* @see app/Http/Controllers/ExpenseController.php:35
* @route '/dashboard/expenses/data'
*/
data.url = (options?: RouteQueryOptions) => {
    return data.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\ExpenseController::data
* @see app/Http/Controllers/ExpenseController.php:35
* @route '/dashboard/expenses/data'
*/
data.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: data.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ExpenseController::data
* @see app/Http/Controllers/ExpenseController.php:35
* @route '/dashboard/expenses/data'
*/
data.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: data.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\ExpenseController::data
* @see app/Http/Controllers/ExpenseController.php:35
* @route '/dashboard/expenses/data'
*/
const dataForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: data.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ExpenseController::data
* @see app/Http/Controllers/ExpenseController.php:35
* @route '/dashboard/expenses/data'
*/
dataForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: data.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ExpenseController::data
* @see app/Http/Controllers/ExpenseController.php:35
* @route '/dashboard/expenses/data'
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
* @see \App\Http\Controllers\ExpenseController::create
* @see app/Http/Controllers/ExpenseController.php:54
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
* @see app/Http/Controllers/ExpenseController.php:54
* @route '/dashboard/expenses/create'
*/
create.url = (options?: RouteQueryOptions) => {
    return create.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\ExpenseController::create
* @see app/Http/Controllers/ExpenseController.php:54
* @route '/dashboard/expenses/create'
*/
create.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ExpenseController::create
* @see app/Http/Controllers/ExpenseController.php:54
* @route '/dashboard/expenses/create'
*/
create.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\ExpenseController::create
* @see app/Http/Controllers/ExpenseController.php:54
* @route '/dashboard/expenses/create'
*/
const createForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ExpenseController::create
* @see app/Http/Controllers/ExpenseController.php:54
* @route '/dashboard/expenses/create'
*/
createForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ExpenseController::create
* @see app/Http/Controllers/ExpenseController.php:54
* @route '/dashboard/expenses/create'
*/
createForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

create.form = createForm

/**
* @see \App\Http\Controllers\ExpenseController::show
* @see app/Http/Controllers/ExpenseController.php:160
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
* @see app/Http/Controllers/ExpenseController.php:160
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
* @see app/Http/Controllers/ExpenseController.php:160
* @route '/dashboard/expenses/{id}'
*/
show.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ExpenseController::show
* @see app/Http/Controllers/ExpenseController.php:160
* @route '/dashboard/expenses/{id}'
*/
show.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\ExpenseController::show
* @see app/Http/Controllers/ExpenseController.php:160
* @route '/dashboard/expenses/{id}'
*/
const showForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ExpenseController::show
* @see app/Http/Controllers/ExpenseController.php:160
* @route '/dashboard/expenses/{id}'
*/
showForm.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ExpenseController::show
* @see app/Http/Controllers/ExpenseController.php:160
* @route '/dashboard/expenses/{id}'
*/
showForm.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

show.form = showForm

/**
* @see \App\Http\Controllers\ExpenseController::edit
* @see app/Http/Controllers/ExpenseController.php:103
* @route '/dashboard/expenses/{id}/edit'
*/
export const edit = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/dashboard/expenses/{id}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\ExpenseController::edit
* @see app/Http/Controllers/ExpenseController.php:103
* @route '/dashboard/expenses/{id}/edit'
*/
edit.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return edit.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ExpenseController::edit
* @see app/Http/Controllers/ExpenseController.php:103
* @route '/dashboard/expenses/{id}/edit'
*/
edit.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ExpenseController::edit
* @see app/Http/Controllers/ExpenseController.php:103
* @route '/dashboard/expenses/{id}/edit'
*/
edit.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\ExpenseController::edit
* @see app/Http/Controllers/ExpenseController.php:103
* @route '/dashboard/expenses/{id}/edit'
*/
const editForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ExpenseController::edit
* @see app/Http/Controllers/ExpenseController.php:103
* @route '/dashboard/expenses/{id}/edit'
*/
editForm.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ExpenseController::edit
* @see app/Http/Controllers/ExpenseController.php:103
* @route '/dashboard/expenses/{id}/edit'
*/
editForm.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

edit.form = editForm

/**
* @see \App\Http\Controllers\ExpenseController::store
* @see app/Http/Controllers/ExpenseController.php:75
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
* @see app/Http/Controllers/ExpenseController.php:75
* @route '/dashboard/expenses'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\ExpenseController::store
* @see app/Http/Controllers/ExpenseController.php:75
* @route '/dashboard/expenses'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\ExpenseController::store
* @see app/Http/Controllers/ExpenseController.php:75
* @route '/dashboard/expenses'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\ExpenseController::store
* @see app/Http/Controllers/ExpenseController.php:75
* @route '/dashboard/expenses'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\ExpenseController::update
* @see app/Http/Controllers/ExpenseController.php:136
* @route '/dashboard/expenses/{id}'
*/
export const update = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/dashboard/expenses/{id}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\ExpenseController::update
* @see app/Http/Controllers/ExpenseController.php:136
* @route '/dashboard/expenses/{id}'
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
* @see \App\Http\Controllers\ExpenseController::update
* @see app/Http/Controllers/ExpenseController.php:136
* @route '/dashboard/expenses/{id}'
*/
update.put = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\ExpenseController::update
* @see app/Http/Controllers/ExpenseController.php:136
* @route '/dashboard/expenses/{id}'
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
* @see \App\Http\Controllers\ExpenseController::update
* @see app/Http/Controllers/ExpenseController.php:136
* @route '/dashboard/expenses/{id}'
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
* @see \App\Http\Controllers\ExpenseController::process
* @see app/Http/Controllers/ExpenseController.php:148
* @route '/dashboard/expenses/{id}/process'
*/
export const process = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: process.url(args, options),
    method: 'post',
})

process.definition = {
    methods: ["post"],
    url: '/dashboard/expenses/{id}/process',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\ExpenseController::process
* @see app/Http/Controllers/ExpenseController.php:148
* @route '/dashboard/expenses/{id}/process'
*/
process.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return process.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ExpenseController::process
* @see app/Http/Controllers/ExpenseController.php:148
* @route '/dashboard/expenses/{id}/process'
*/
process.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: process.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\ExpenseController::process
* @see app/Http/Controllers/ExpenseController.php:148
* @route '/dashboard/expenses/{id}/process'
*/
const processForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: process.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\ExpenseController::process
* @see app/Http/Controllers/ExpenseController.php:148
* @route '/dashboard/expenses/{id}/process'
*/
processForm.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: process.url(args, options),
    method: 'post',
})

process.form = processForm

/**
* @see \App\Http\Controllers\ExpenseController::destroy
* @see app/Http/Controllers/ExpenseController.php:173
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
* @see app/Http/Controllers/ExpenseController.php:173
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
* @see app/Http/Controllers/ExpenseController.php:173
* @route '/dashboard/expenses/{id}'
*/
destroy.delete = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\ExpenseController::destroy
* @see app/Http/Controllers/ExpenseController.php:173
* @route '/dashboard/expenses/{id}'
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
* @see \App\Http\Controllers\ExpenseController::destroy
* @see app/Http/Controllers/ExpenseController.php:173
* @route '/dashboard/expenses/{id}'
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

/**
* @see \App\Http\Controllers\ExpenseController::lastExchangeRate
* @see app/Http/Controllers/ExpenseController.php:194
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
* @see app/Http/Controllers/ExpenseController.php:194
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
* @see app/Http/Controllers/ExpenseController.php:194
* @route '/dashboard/expenses/last-exchange-rate/{currency}'
*/
lastExchangeRate.get = (args: { currency: string | number } | [currency: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: lastExchangeRate.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ExpenseController::lastExchangeRate
* @see app/Http/Controllers/ExpenseController.php:194
* @route '/dashboard/expenses/last-exchange-rate/{currency}'
*/
lastExchangeRate.head = (args: { currency: string | number } | [currency: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: lastExchangeRate.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\ExpenseController::lastExchangeRate
* @see app/Http/Controllers/ExpenseController.php:194
* @route '/dashboard/expenses/last-exchange-rate/{currency}'
*/
const lastExchangeRateForm = (args: { currency: string | number } | [currency: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: lastExchangeRate.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ExpenseController::lastExchangeRate
* @see app/Http/Controllers/ExpenseController.php:194
* @route '/dashboard/expenses/last-exchange-rate/{currency}'
*/
lastExchangeRateForm.get = (args: { currency: string | number } | [currency: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: lastExchangeRate.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ExpenseController::lastExchangeRate
* @see app/Http/Controllers/ExpenseController.php:194
* @route '/dashboard/expenses/last-exchange-rate/{currency}'
*/
lastExchangeRateForm.head = (args: { currency: string | number } | [currency: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: lastExchangeRate.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

lastExchangeRate.form = lastExchangeRateForm

const expenses = {
    index,
    data,
    create,
    show,
    edit,
    store,
    update,
    process,
    destroy,
    lastExchangeRate,
}

export default expenses