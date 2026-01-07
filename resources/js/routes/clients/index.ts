import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../wayfinder'
/**
* @see \App\Http\Controllers\ClientController::index
* @see app/Http/Controllers/ClientController.php:23
* @route '/dashboard/clients'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/dashboard/clients',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\ClientController::index
* @see app/Http/Controllers/ClientController.php:23
* @route '/dashboard/clients'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\ClientController::index
* @see app/Http/Controllers/ClientController.php:23
* @route '/dashboard/clients'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ClientController::index
* @see app/Http/Controllers/ClientController.php:23
* @route '/dashboard/clients'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\ClientController::data
* @see app/Http/Controllers/ClientController.php:28
* @route '/dashboard/clients/data'
*/
export const data = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: data.url(options),
    method: 'get',
})

data.definition = {
    methods: ["get","head"],
    url: '/dashboard/clients/data',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\ClientController::data
* @see app/Http/Controllers/ClientController.php:28
* @route '/dashboard/clients/data'
*/
data.url = (options?: RouteQueryOptions) => {
    return data.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\ClientController::data
* @see app/Http/Controllers/ClientController.php:28
* @route '/dashboard/clients/data'
*/
data.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: data.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ClientController::data
* @see app/Http/Controllers/ClientController.php:28
* @route '/dashboard/clients/data'
*/
data.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: data.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\ClientController::create
* @see app/Http/Controllers/ClientController.php:41
* @route '/dashboard/clients/create'
*/
export const create = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/dashboard/clients/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\ClientController::create
* @see app/Http/Controllers/ClientController.php:41
* @route '/dashboard/clients/create'
*/
create.url = (options?: RouteQueryOptions) => {
    return create.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\ClientController::create
* @see app/Http/Controllers/ClientController.php:41
* @route '/dashboard/clients/create'
*/
create.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ClientController::create
* @see app/Http/Controllers/ClientController.php:41
* @route '/dashboard/clients/create'
*/
create.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\ClientController::show
* @see app/Http/Controllers/ClientController.php:46
* @route '/dashboard/clients/{client}'
*/
export const show = (args: { client: string | number } | [client: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/dashboard/clients/{client}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\ClientController::show
* @see app/Http/Controllers/ClientController.php:46
* @route '/dashboard/clients/{client}'
*/
show.url = (args: { client: string | number } | [client: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { client: args }
    }

    if (Array.isArray(args)) {
        args = {
            client: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        client: args.client,
    }

    return show.definition.url
            .replace('{client}', parsedArgs.client.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ClientController::show
* @see app/Http/Controllers/ClientController.php:46
* @route '/dashboard/clients/{client}'
*/
show.get = (args: { client: string | number } | [client: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ClientController::show
* @see app/Http/Controllers/ClientController.php:46
* @route '/dashboard/clients/{client}'
*/
show.head = (args: { client: string | number } | [client: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\ClientController::edit
* @see app/Http/Controllers/ClientController.php:57
* @route '/dashboard/clients/{client}/edit'
*/
export const edit = (args: { client: number | { id: number } } | [client: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/dashboard/clients/{client}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\ClientController::edit
* @see app/Http/Controllers/ClientController.php:57
* @route '/dashboard/clients/{client}/edit'
*/
edit.url = (args: { client: number | { id: number } } | [client: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { client: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { client: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            client: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        client: typeof args.client === 'object'
        ? args.client.id
        : args.client,
    }

    return edit.definition.url
            .replace('{client}', parsedArgs.client.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ClientController::edit
* @see app/Http/Controllers/ClientController.php:57
* @route '/dashboard/clients/{client}/edit'
*/
edit.get = (args: { client: number | { id: number } } | [client: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ClientController::edit
* @see app/Http/Controllers/ClientController.php:57
* @route '/dashboard/clients/{client}/edit'
*/
edit.head = (args: { client: number | { id: number } } | [client: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\ClientController::store
* @see app/Http/Controllers/ClientController.php:64
* @route '/dashboard/clients'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/dashboard/clients',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\ClientController::store
* @see app/Http/Controllers/ClientController.php:64
* @route '/dashboard/clients'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\ClientController::store
* @see app/Http/Controllers/ClientController.php:64
* @route '/dashboard/clients'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\ClientController::update
* @see app/Http/Controllers/ClientController.php:74
* @route '/dashboard/clients/{client}'
*/
export const update = (args: { client: number | { id: number } } | [client: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/dashboard/clients/{client}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\ClientController::update
* @see app/Http/Controllers/ClientController.php:74
* @route '/dashboard/clients/{client}'
*/
update.url = (args: { client: number | { id: number } } | [client: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { client: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { client: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            client: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        client: typeof args.client === 'object'
        ? args.client.id
        : args.client,
    }

    return update.definition.url
            .replace('{client}', parsedArgs.client.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ClientController::update
* @see app/Http/Controllers/ClientController.php:74
* @route '/dashboard/clients/{client}'
*/
update.put = (args: { client: number | { id: number } } | [client: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\ClientController::destroy
* @see app/Http/Controllers/ClientController.php:84
* @route '/dashboard/clients/{client}'
*/
export const destroy = (args: { client: string | number } | [client: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/dashboard/clients/{client}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\ClientController::destroy
* @see app/Http/Controllers/ClientController.php:84
* @route '/dashboard/clients/{client}'
*/
destroy.url = (args: { client: string | number } | [client: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { client: args }
    }

    if (Array.isArray(args)) {
        args = {
            client: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        client: args.client,
    }

    return destroy.definition.url
            .replace('{client}', parsedArgs.client.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ClientController::destroy
* @see app/Http/Controllers/ClientController.php:84
* @route '/dashboard/clients/{client}'
*/
destroy.delete = (args: { client: string | number } | [client: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

const clients = {
    index,
    data,
    create,
    show,
    edit,
    store,
    update,
    destroy,
}

export default clients