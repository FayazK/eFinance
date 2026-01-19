import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\InvoiceController::index
* @see app/Http/Controllers/InvoiceController.php:41
* @route '/dashboard/invoices'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/dashboard/invoices',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\InvoiceController::index
* @see app/Http/Controllers/InvoiceController.php:41
* @route '/dashboard/invoices'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\InvoiceController::index
* @see app/Http/Controllers/InvoiceController.php:41
* @route '/dashboard/invoices'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\InvoiceController::index
* @see app/Http/Controllers/InvoiceController.php:41
* @route '/dashboard/invoices'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\InvoiceController::index
* @see app/Http/Controllers/InvoiceController.php:41
* @route '/dashboard/invoices'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\InvoiceController::index
* @see app/Http/Controllers/InvoiceController.php:41
* @route '/dashboard/invoices'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\InvoiceController::index
* @see app/Http/Controllers/InvoiceController.php:41
* @route '/dashboard/invoices'
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
* @see \App\Http\Controllers\InvoiceController::data
* @see app/Http/Controllers/InvoiceController.php:49
* @route '/dashboard/invoices/data'
*/
export const data = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: data.url(options),
    method: 'get',
})

data.definition = {
    methods: ["get","head"],
    url: '/dashboard/invoices/data',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\InvoiceController::data
* @see app/Http/Controllers/InvoiceController.php:49
* @route '/dashboard/invoices/data'
*/
data.url = (options?: RouteQueryOptions) => {
    return data.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\InvoiceController::data
* @see app/Http/Controllers/InvoiceController.php:49
* @route '/dashboard/invoices/data'
*/
data.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: data.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\InvoiceController::data
* @see app/Http/Controllers/InvoiceController.php:49
* @route '/dashboard/invoices/data'
*/
data.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: data.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\InvoiceController::data
* @see app/Http/Controllers/InvoiceController.php:49
* @route '/dashboard/invoices/data'
*/
const dataForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: data.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\InvoiceController::data
* @see app/Http/Controllers/InvoiceController.php:49
* @route '/dashboard/invoices/data'
*/
dataForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: data.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\InvoiceController::data
* @see app/Http/Controllers/InvoiceController.php:49
* @route '/dashboard/invoices/data'
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
* @see \App\Http\Controllers\InvoiceController::create
* @see app/Http/Controllers/InvoiceController.php:65
* @route '/dashboard/invoices/create'
*/
export const create = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/dashboard/invoices/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\InvoiceController::create
* @see app/Http/Controllers/InvoiceController.php:65
* @route '/dashboard/invoices/create'
*/
create.url = (options?: RouteQueryOptions) => {
    return create.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\InvoiceController::create
* @see app/Http/Controllers/InvoiceController.php:65
* @route '/dashboard/invoices/create'
*/
create.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\InvoiceController::create
* @see app/Http/Controllers/InvoiceController.php:65
* @route '/dashboard/invoices/create'
*/
create.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\InvoiceController::create
* @see app/Http/Controllers/InvoiceController.php:65
* @route '/dashboard/invoices/create'
*/
const createForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\InvoiceController::create
* @see app/Http/Controllers/InvoiceController.php:65
* @route '/dashboard/invoices/create'
*/
createForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\InvoiceController::create
* @see app/Http/Controllers/InvoiceController.php:65
* @route '/dashboard/invoices/create'
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
* @see \App\Http\Controllers\InvoiceController::show
* @see app/Http/Controllers/InvoiceController.php:108
* @route '/dashboard/invoices/{id}'
*/
export const show = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/dashboard/invoices/{id}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\InvoiceController::show
* @see app/Http/Controllers/InvoiceController.php:108
* @route '/dashboard/invoices/{id}'
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
* @see \App\Http\Controllers\InvoiceController::show
* @see app/Http/Controllers/InvoiceController.php:108
* @route '/dashboard/invoices/{id}'
*/
show.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\InvoiceController::show
* @see app/Http/Controllers/InvoiceController.php:108
* @route '/dashboard/invoices/{id}'
*/
show.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\InvoiceController::show
* @see app/Http/Controllers/InvoiceController.php:108
* @route '/dashboard/invoices/{id}'
*/
const showForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\InvoiceController::show
* @see app/Http/Controllers/InvoiceController.php:108
* @route '/dashboard/invoices/{id}'
*/
showForm.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\InvoiceController::show
* @see app/Http/Controllers/InvoiceController.php:108
* @route '/dashboard/invoices/{id}'
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
* @see \App\Http\Controllers\InvoiceController::edit
* @see app/Http/Controllers/InvoiceController.php:134
* @route '/dashboard/invoices/{id}/edit'
*/
export const edit = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/dashboard/invoices/{id}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\InvoiceController::edit
* @see app/Http/Controllers/InvoiceController.php:134
* @route '/dashboard/invoices/{id}/edit'
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
* @see \App\Http\Controllers\InvoiceController::edit
* @see app/Http/Controllers/InvoiceController.php:134
* @route '/dashboard/invoices/{id}/edit'
*/
edit.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\InvoiceController::edit
* @see app/Http/Controllers/InvoiceController.php:134
* @route '/dashboard/invoices/{id}/edit'
*/
edit.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\InvoiceController::edit
* @see app/Http/Controllers/InvoiceController.php:134
* @route '/dashboard/invoices/{id}/edit'
*/
const editForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\InvoiceController::edit
* @see app/Http/Controllers/InvoiceController.php:134
* @route '/dashboard/invoices/{id}/edit'
*/
editForm.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\InvoiceController::edit
* @see app/Http/Controllers/InvoiceController.php:134
* @route '/dashboard/invoices/{id}/edit'
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
* @see \App\Http\Controllers\InvoiceController::store
* @see app/Http/Controllers/InvoiceController.php:95
* @route '/dashboard/invoices'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/dashboard/invoices',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\InvoiceController::store
* @see app/Http/Controllers/InvoiceController.php:95
* @route '/dashboard/invoices'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\InvoiceController::store
* @see app/Http/Controllers/InvoiceController.php:95
* @route '/dashboard/invoices'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\InvoiceController::store
* @see app/Http/Controllers/InvoiceController.php:95
* @route '/dashboard/invoices'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\InvoiceController::store
* @see app/Http/Controllers/InvoiceController.php:95
* @route '/dashboard/invoices'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\InvoiceController::update
* @see app/Http/Controllers/InvoiceController.php:175
* @route '/dashboard/invoices/{id}'
*/
export const update = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/dashboard/invoices/{id}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\InvoiceController::update
* @see app/Http/Controllers/InvoiceController.php:175
* @route '/dashboard/invoices/{id}'
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
* @see \App\Http\Controllers\InvoiceController::update
* @see app/Http/Controllers/InvoiceController.php:175
* @route '/dashboard/invoices/{id}'
*/
update.put = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\InvoiceController::update
* @see app/Http/Controllers/InvoiceController.php:175
* @route '/dashboard/invoices/{id}'
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
* @see \App\Http\Controllers\InvoiceController::update
* @see app/Http/Controllers/InvoiceController.php:175
* @route '/dashboard/invoices/{id}'
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
* @see \App\Http\Controllers\InvoiceController::destroy
* @see app/Http/Controllers/InvoiceController.php:188
* @route '/dashboard/invoices/{id}'
*/
export const destroy = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/dashboard/invoices/{id}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\InvoiceController::destroy
* @see app/Http/Controllers/InvoiceController.php:188
* @route '/dashboard/invoices/{id}'
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
* @see \App\Http\Controllers\InvoiceController::destroy
* @see app/Http/Controllers/InvoiceController.php:188
* @route '/dashboard/invoices/{id}'
*/
destroy.delete = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\InvoiceController::destroy
* @see app/Http/Controllers/InvoiceController.php:188
* @route '/dashboard/invoices/{id}'
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
* @see \App\Http\Controllers\InvoiceController::destroy
* @see app/Http/Controllers/InvoiceController.php:188
* @route '/dashboard/invoices/{id}'
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
* @see \App\Http\Controllers\InvoiceController::changeStatus
* @see app/Http/Controllers/InvoiceController.php:212
* @route '/dashboard/invoices/{id}/change-status'
*/
export const changeStatus = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: changeStatus.url(args, options),
    method: 'post',
})

changeStatus.definition = {
    methods: ["post"],
    url: '/dashboard/invoices/{id}/change-status',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\InvoiceController::changeStatus
* @see app/Http/Controllers/InvoiceController.php:212
* @route '/dashboard/invoices/{id}/change-status'
*/
changeStatus.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return changeStatus.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\InvoiceController::changeStatus
* @see app/Http/Controllers/InvoiceController.php:212
* @route '/dashboard/invoices/{id}/change-status'
*/
changeStatus.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: changeStatus.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\InvoiceController::changeStatus
* @see app/Http/Controllers/InvoiceController.php:212
* @route '/dashboard/invoices/{id}/change-status'
*/
const changeStatusForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: changeStatus.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\InvoiceController::changeStatus
* @see app/Http/Controllers/InvoiceController.php:212
* @route '/dashboard/invoices/{id}/change-status'
*/
changeStatusForm.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: changeStatus.url(args, options),
    method: 'post',
})

changeStatus.form = changeStatusForm

/**
* @see \App\Http\Controllers\InvoiceController::recordPayment
* @see app/Http/Controllers/InvoiceController.php:235
* @route '/dashboard/invoices/{id}/record-payment'
*/
export const recordPayment = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: recordPayment.url(args, options),
    method: 'post',
})

recordPayment.definition = {
    methods: ["post"],
    url: '/dashboard/invoices/{id}/record-payment',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\InvoiceController::recordPayment
* @see app/Http/Controllers/InvoiceController.php:235
* @route '/dashboard/invoices/{id}/record-payment'
*/
recordPayment.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return recordPayment.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\InvoiceController::recordPayment
* @see app/Http/Controllers/InvoiceController.php:235
* @route '/dashboard/invoices/{id}/record-payment'
*/
recordPayment.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: recordPayment.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\InvoiceController::recordPayment
* @see app/Http/Controllers/InvoiceController.php:235
* @route '/dashboard/invoices/{id}/record-payment'
*/
const recordPaymentForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: recordPayment.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\InvoiceController::recordPayment
* @see app/Http/Controllers/InvoiceController.php:235
* @route '/dashboard/invoices/{id}/record-payment'
*/
recordPaymentForm.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: recordPayment.url(args, options),
    method: 'post',
})

recordPayment.form = recordPaymentForm

/**
* @see \App\Http\Controllers\InvoiceController::voidMethod
* @see app/Http/Controllers/InvoiceController.php:251
* @route '/dashboard/invoices/{id}/void'
*/
export const voidMethod = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: voidMethod.url(args, options),
    method: 'post',
})

voidMethod.definition = {
    methods: ["post"],
    url: '/dashboard/invoices/{id}/void',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\InvoiceController::voidMethod
* @see app/Http/Controllers/InvoiceController.php:251
* @route '/dashboard/invoices/{id}/void'
*/
voidMethod.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return voidMethod.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\InvoiceController::voidMethod
* @see app/Http/Controllers/InvoiceController.php:251
* @route '/dashboard/invoices/{id}/void'
*/
voidMethod.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: voidMethod.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\InvoiceController::voidMethod
* @see app/Http/Controllers/InvoiceController.php:251
* @route '/dashboard/invoices/{id}/void'
*/
const voidMethodForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: voidMethod.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\InvoiceController::voidMethod
* @see app/Http/Controllers/InvoiceController.php:251
* @route '/dashboard/invoices/{id}/void'
*/
voidMethodForm.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: voidMethod.url(args, options),
    method: 'post',
})

voidMethod.form = voidMethodForm

/**
* @see \App\Http\Controllers\InvoiceController::generatePdf
* @see app/Http/Controllers/InvoiceController.php:270
* @route '/dashboard/invoices/{id}/pdf'
*/
export const generatePdf = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: generatePdf.url(args, options),
    method: 'get',
})

generatePdf.definition = {
    methods: ["get","head"],
    url: '/dashboard/invoices/{id}/pdf',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\InvoiceController::generatePdf
* @see app/Http/Controllers/InvoiceController.php:270
* @route '/dashboard/invoices/{id}/pdf'
*/
generatePdf.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return generatePdf.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\InvoiceController::generatePdf
* @see app/Http/Controllers/InvoiceController.php:270
* @route '/dashboard/invoices/{id}/pdf'
*/
generatePdf.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: generatePdf.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\InvoiceController::generatePdf
* @see app/Http/Controllers/InvoiceController.php:270
* @route '/dashboard/invoices/{id}/pdf'
*/
generatePdf.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: generatePdf.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\InvoiceController::generatePdf
* @see app/Http/Controllers/InvoiceController.php:270
* @route '/dashboard/invoices/{id}/pdf'
*/
const generatePdfForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: generatePdf.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\InvoiceController::generatePdf
* @see app/Http/Controllers/InvoiceController.php:270
* @route '/dashboard/invoices/{id}/pdf'
*/
generatePdfForm.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: generatePdf.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\InvoiceController::generatePdf
* @see app/Http/Controllers/InvoiceController.php:270
* @route '/dashboard/invoices/{id}/pdf'
*/
generatePdfForm.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: generatePdf.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

generatePdf.form = generatePdfForm

/**
* @see \App\Http\Controllers\InvoiceController::sendEmail
* @see app/Http/Controllers/InvoiceController.php:358
* @route '/dashboard/invoices/{id}/send-email'
*/
export const sendEmail = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: sendEmail.url(args, options),
    method: 'post',
})

sendEmail.definition = {
    methods: ["post"],
    url: '/dashboard/invoices/{id}/send-email',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\InvoiceController::sendEmail
* @see app/Http/Controllers/InvoiceController.php:358
* @route '/dashboard/invoices/{id}/send-email'
*/
sendEmail.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return sendEmail.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\InvoiceController::sendEmail
* @see app/Http/Controllers/InvoiceController.php:358
* @route '/dashboard/invoices/{id}/send-email'
*/
sendEmail.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: sendEmail.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\InvoiceController::sendEmail
* @see app/Http/Controllers/InvoiceController.php:358
* @route '/dashboard/invoices/{id}/send-email'
*/
const sendEmailForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: sendEmail.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\InvoiceController::sendEmail
* @see app/Http/Controllers/InvoiceController.php:358
* @route '/dashboard/invoices/{id}/send-email'
*/
sendEmailForm.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: sendEmail.url(args, options),
    method: 'post',
})

sendEmail.form = sendEmailForm

const InvoiceController = { index, data, create, show, edit, store, update, destroy, changeStatus, recordPayment, voidMethod, generatePdf, sendEmail, void: voidMethod }

export default InvoiceController