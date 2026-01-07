import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\ContactController::index
* @see app/Http/Controllers/ContactController.php:23
* @route '/dashboard/contacts'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/dashboard/contacts',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\ContactController::index
* @see app/Http/Controllers/ContactController.php:23
* @route '/dashboard/contacts'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\ContactController::index
* @see app/Http/Controllers/ContactController.php:23
* @route '/dashboard/contacts'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ContactController::index
* @see app/Http/Controllers/ContactController.php:23
* @route '/dashboard/contacts'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\ContactController::data
* @see app/Http/Controllers/ContactController.php:28
* @route '/dashboard/contacts/data'
*/
export const data = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: data.url(options),
    method: 'get',
})

data.definition = {
    methods: ["get","head"],
    url: '/dashboard/contacts/data',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\ContactController::data
* @see app/Http/Controllers/ContactController.php:28
* @route '/dashboard/contacts/data'
*/
data.url = (options?: RouteQueryOptions) => {
    return data.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\ContactController::data
* @see app/Http/Controllers/ContactController.php:28
* @route '/dashboard/contacts/data'
*/
data.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: data.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ContactController::data
* @see app/Http/Controllers/ContactController.php:28
* @route '/dashboard/contacts/data'
*/
data.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: data.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\ContactController::create
* @see app/Http/Controllers/ContactController.php:41
* @route '/dashboard/contacts/create'
*/
export const create = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/dashboard/contacts/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\ContactController::create
* @see app/Http/Controllers/ContactController.php:41
* @route '/dashboard/contacts/create'
*/
create.url = (options?: RouteQueryOptions) => {
    return create.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\ContactController::create
* @see app/Http/Controllers/ContactController.php:41
* @route '/dashboard/contacts/create'
*/
create.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ContactController::create
* @see app/Http/Controllers/ContactController.php:41
* @route '/dashboard/contacts/create'
*/
create.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\ContactController::show
* @see app/Http/Controllers/ContactController.php:46
* @route '/dashboard/contacts/{contact}'
*/
export const show = (args: { contact: string | number } | [contact: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/dashboard/contacts/{contact}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\ContactController::show
* @see app/Http/Controllers/ContactController.php:46
* @route '/dashboard/contacts/{contact}'
*/
show.url = (args: { contact: string | number } | [contact: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { contact: args }
    }

    if (Array.isArray(args)) {
        args = {
            contact: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        contact: args.contact,
    }

    return show.definition.url
            .replace('{contact}', parsedArgs.contact.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ContactController::show
* @see app/Http/Controllers/ContactController.php:46
* @route '/dashboard/contacts/{contact}'
*/
show.get = (args: { contact: string | number } | [contact: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ContactController::show
* @see app/Http/Controllers/ContactController.php:46
* @route '/dashboard/contacts/{contact}'
*/
show.head = (args: { contact: string | number } | [contact: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\ContactController::edit
* @see app/Http/Controllers/ContactController.php:57
* @route '/dashboard/contacts/{contact}/edit'
*/
export const edit = (args: { contact: number | { id: number } } | [contact: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/dashboard/contacts/{contact}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\ContactController::edit
* @see app/Http/Controllers/ContactController.php:57
* @route '/dashboard/contacts/{contact}/edit'
*/
edit.url = (args: { contact: number | { id: number } } | [contact: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { contact: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { contact: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            contact: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        contact: typeof args.contact === 'object'
        ? args.contact.id
        : args.contact,
    }

    return edit.definition.url
            .replace('{contact}', parsedArgs.contact.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ContactController::edit
* @see app/Http/Controllers/ContactController.php:57
* @route '/dashboard/contacts/{contact}/edit'
*/
edit.get = (args: { contact: number | { id: number } } | [contact: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ContactController::edit
* @see app/Http/Controllers/ContactController.php:57
* @route '/dashboard/contacts/{contact}/edit'
*/
edit.head = (args: { contact: number | { id: number } } | [contact: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\ContactController::store
* @see app/Http/Controllers/ContactController.php:64
* @route '/dashboard/contacts'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/dashboard/contacts',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\ContactController::store
* @see app/Http/Controllers/ContactController.php:64
* @route '/dashboard/contacts'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\ContactController::store
* @see app/Http/Controllers/ContactController.php:64
* @route '/dashboard/contacts'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\ContactController::update
* @see app/Http/Controllers/ContactController.php:74
* @route '/dashboard/contacts/{contact}'
*/
export const update = (args: { contact: number | { id: number } } | [contact: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/dashboard/contacts/{contact}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\ContactController::update
* @see app/Http/Controllers/ContactController.php:74
* @route '/dashboard/contacts/{contact}'
*/
update.url = (args: { contact: number | { id: number } } | [contact: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { contact: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { contact: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            contact: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        contact: typeof args.contact === 'object'
        ? args.contact.id
        : args.contact,
    }

    return update.definition.url
            .replace('{contact}', parsedArgs.contact.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ContactController::update
* @see app/Http/Controllers/ContactController.php:74
* @route '/dashboard/contacts/{contact}'
*/
update.put = (args: { contact: number | { id: number } } | [contact: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\ContactController::destroy
* @see app/Http/Controllers/ContactController.php:84
* @route '/dashboard/contacts/{contact}'
*/
export const destroy = (args: { contact: string | number } | [contact: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/dashboard/contacts/{contact}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\ContactController::destroy
* @see app/Http/Controllers/ContactController.php:84
* @route '/dashboard/contacts/{contact}'
*/
destroy.url = (args: { contact: string | number } | [contact: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { contact: args }
    }

    if (Array.isArray(args)) {
        args = {
            contact: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        contact: args.contact,
    }

    return destroy.definition.url
            .replace('{contact}', parsedArgs.contact.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ContactController::destroy
* @see app/Http/Controllers/ContactController.php:84
* @route '/dashboard/contacts/{contact}'
*/
destroy.delete = (args: { contact: string | number } | [contact: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

const ContactController = { index, data, create, show, edit, store, update, destroy }

export default ContactController