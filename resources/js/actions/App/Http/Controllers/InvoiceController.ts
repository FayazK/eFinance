import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\InvoiceController::index
* @see app/Http/Controllers/InvoiceController.php:37
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
* @see app/Http/Controllers/InvoiceController.php:37
* @route '/dashboard/invoices'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\InvoiceController::index
* @see app/Http/Controllers/InvoiceController.php:37
* @route '/dashboard/invoices'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\InvoiceController::index
* @see app/Http/Controllers/InvoiceController.php:37
* @route '/dashboard/invoices'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\InvoiceController::data
* @see app/Http/Controllers/InvoiceController.php:45
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
* @see app/Http/Controllers/InvoiceController.php:45
* @route '/dashboard/invoices/data'
*/
data.url = (options?: RouteQueryOptions) => {
    return data.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\InvoiceController::data
* @see app/Http/Controllers/InvoiceController.php:45
* @route '/dashboard/invoices/data'
*/
data.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: data.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\InvoiceController::data
* @see app/Http/Controllers/InvoiceController.php:45
* @route '/dashboard/invoices/data'
*/
data.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: data.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\InvoiceController::create
* @see app/Http/Controllers/InvoiceController.php:61
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
* @see app/Http/Controllers/InvoiceController.php:61
* @route '/dashboard/invoices/create'
*/
create.url = (options?: RouteQueryOptions) => {
    return create.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\InvoiceController::create
* @see app/Http/Controllers/InvoiceController.php:61
* @route '/dashboard/invoices/create'
*/
create.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\InvoiceController::create
* @see app/Http/Controllers/InvoiceController.php:61
* @route '/dashboard/invoices/create'
*/
create.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\InvoiceController::show
* @see app/Http/Controllers/InvoiceController.php:97
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
* @see app/Http/Controllers/InvoiceController.php:97
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
* @see app/Http/Controllers/InvoiceController.php:97
* @route '/dashboard/invoices/{id}'
*/
show.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\InvoiceController::show
* @see app/Http/Controllers/InvoiceController.php:97
* @route '/dashboard/invoices/{id}'
*/
show.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\InvoiceController::edit
* @see app/Http/Controllers/InvoiceController.php:123
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
* @see app/Http/Controllers/InvoiceController.php:123
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
* @see app/Http/Controllers/InvoiceController.php:123
* @route '/dashboard/invoices/{id}/edit'
*/
edit.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\InvoiceController::edit
* @see app/Http/Controllers/InvoiceController.php:123
* @route '/dashboard/invoices/{id}/edit'
*/
edit.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\InvoiceController::store
* @see app/Http/Controllers/InvoiceController.php:84
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
* @see app/Http/Controllers/InvoiceController.php:84
* @route '/dashboard/invoices'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\InvoiceController::store
* @see app/Http/Controllers/InvoiceController.php:84
* @route '/dashboard/invoices'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\InvoiceController::update
* @see app/Http/Controllers/InvoiceController.php:157
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
* @see app/Http/Controllers/InvoiceController.php:157
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
* @see app/Http/Controllers/InvoiceController.php:157
* @route '/dashboard/invoices/{id}'
*/
update.put = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\InvoiceController::destroy
* @see app/Http/Controllers/InvoiceController.php:170
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
* @see app/Http/Controllers/InvoiceController.php:170
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
* @see app/Http/Controllers/InvoiceController.php:170
* @route '/dashboard/invoices/{id}'
*/
destroy.delete = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\InvoiceController::changeStatus
* @see app/Http/Controllers/InvoiceController.php:194
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
* @see app/Http/Controllers/InvoiceController.php:194
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
* @see app/Http/Controllers/InvoiceController.php:194
* @route '/dashboard/invoices/{id}/change-status'
*/
changeStatus.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: changeStatus.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\InvoiceController::recordPayment
* @see app/Http/Controllers/InvoiceController.php:211
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
* @see app/Http/Controllers/InvoiceController.php:211
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
* @see app/Http/Controllers/InvoiceController.php:211
* @route '/dashboard/invoices/{id}/record-payment'
*/
recordPayment.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: recordPayment.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\InvoiceController::voidMethod
* @see app/Http/Controllers/InvoiceController.php:227
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
* @see app/Http/Controllers/InvoiceController.php:227
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
* @see app/Http/Controllers/InvoiceController.php:227
* @route '/dashboard/invoices/{id}/void'
*/
voidMethod.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: voidMethod.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\InvoiceController::generatePdf
* @see app/Http/Controllers/InvoiceController.php:240
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
* @see app/Http/Controllers/InvoiceController.php:240
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
* @see app/Http/Controllers/InvoiceController.php:240
* @route '/dashboard/invoices/{id}/pdf'
*/
generatePdf.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: generatePdf.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\InvoiceController::generatePdf
* @see app/Http/Controllers/InvoiceController.php:240
* @route '/dashboard/invoices/{id}/pdf'
*/
generatePdf.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: generatePdf.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\InvoiceController::sendEmail
* @see app/Http/Controllers/InvoiceController.php:286
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
* @see app/Http/Controllers/InvoiceController.php:286
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
* @see app/Http/Controllers/InvoiceController.php:286
* @route '/dashboard/invoices/{id}/send-email'
*/
sendEmail.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: sendEmail.url(args, options),
    method: 'post',
})

const InvoiceController = { index, data, create, show, edit, store, update, destroy, changeStatus, recordPayment, voidMethod, generatePdf, sendEmail, void: voidMethod }

export default InvoiceController