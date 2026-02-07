import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../wayfinder'
/**
* @see \App\Http\Controllers\RoleController::index
* @see app/Http/Controllers/RoleController.php:27
* @route '/dashboard/roles'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/dashboard/roles',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\RoleController::index
* @see app/Http/Controllers/RoleController.php:27
* @route '/dashboard/roles'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\RoleController::index
* @see app/Http/Controllers/RoleController.php:27
* @route '/dashboard/roles'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\RoleController::index
* @see app/Http/Controllers/RoleController.php:27
* @route '/dashboard/roles'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\RoleController::index
* @see app/Http/Controllers/RoleController.php:27
* @route '/dashboard/roles'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\RoleController::index
* @see app/Http/Controllers/RoleController.php:27
* @route '/dashboard/roles'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\RoleController::index
* @see app/Http/Controllers/RoleController.php:27
* @route '/dashboard/roles'
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
* @see \App\Http\Controllers\RoleController::data
* @see app/Http/Controllers/RoleController.php:35
* @route '/dashboard/roles/data'
*/
export const data = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: data.url(options),
    method: 'get',
})

data.definition = {
    methods: ["get","head"],
    url: '/dashboard/roles/data',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\RoleController::data
* @see app/Http/Controllers/RoleController.php:35
* @route '/dashboard/roles/data'
*/
data.url = (options?: RouteQueryOptions) => {
    return data.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\RoleController::data
* @see app/Http/Controllers/RoleController.php:35
* @route '/dashboard/roles/data'
*/
data.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: data.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\RoleController::data
* @see app/Http/Controllers/RoleController.php:35
* @route '/dashboard/roles/data'
*/
data.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: data.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\RoleController::data
* @see app/Http/Controllers/RoleController.php:35
* @route '/dashboard/roles/data'
*/
const dataForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: data.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\RoleController::data
* @see app/Http/Controllers/RoleController.php:35
* @route '/dashboard/roles/data'
*/
dataForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: data.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\RoleController::data
* @see app/Http/Controllers/RoleController.php:35
* @route '/dashboard/roles/data'
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
* @see \App\Http\Controllers\RoleController::assignable
* @see app/Http/Controllers/RoleController.php:112
* @route '/dashboard/roles/assignable'
*/
export const assignable = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: assignable.url(options),
    method: 'get',
})

assignable.definition = {
    methods: ["get","head"],
    url: '/dashboard/roles/assignable',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\RoleController::assignable
* @see app/Http/Controllers/RoleController.php:112
* @route '/dashboard/roles/assignable'
*/
assignable.url = (options?: RouteQueryOptions) => {
    return assignable.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\RoleController::assignable
* @see app/Http/Controllers/RoleController.php:112
* @route '/dashboard/roles/assignable'
*/
assignable.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: assignable.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\RoleController::assignable
* @see app/Http/Controllers/RoleController.php:112
* @route '/dashboard/roles/assignable'
*/
assignable.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: assignable.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\RoleController::assignable
* @see app/Http/Controllers/RoleController.php:112
* @route '/dashboard/roles/assignable'
*/
const assignableForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: assignable.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\RoleController::assignable
* @see app/Http/Controllers/RoleController.php:112
* @route '/dashboard/roles/assignable'
*/
assignableForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: assignable.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\RoleController::assignable
* @see app/Http/Controllers/RoleController.php:112
* @route '/dashboard/roles/assignable'
*/
assignableForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: assignable.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

assignable.form = assignableForm

/**
* @see \App\Http\Controllers\RoleController::create
* @see app/Http/Controllers/RoleController.php:50
* @route '/dashboard/roles/create'
*/
export const create = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/dashboard/roles/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\RoleController::create
* @see app/Http/Controllers/RoleController.php:50
* @route '/dashboard/roles/create'
*/
create.url = (options?: RouteQueryOptions) => {
    return create.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\RoleController::create
* @see app/Http/Controllers/RoleController.php:50
* @route '/dashboard/roles/create'
*/
create.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\RoleController::create
* @see app/Http/Controllers/RoleController.php:50
* @route '/dashboard/roles/create'
*/
create.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\RoleController::create
* @see app/Http/Controllers/RoleController.php:50
* @route '/dashboard/roles/create'
*/
const createForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\RoleController::create
* @see app/Http/Controllers/RoleController.php:50
* @route '/dashboard/roles/create'
*/
createForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\RoleController::create
* @see app/Http/Controllers/RoleController.php:50
* @route '/dashboard/roles/create'
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
* @see \App\Http\Controllers\RoleController::edit
* @see app/Http/Controllers/RoleController.php:73
* @route '/dashboard/roles/{role}/edit'
*/
export const edit = (args: { role: number | { id: number } } | [role: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/dashboard/roles/{role}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\RoleController::edit
* @see app/Http/Controllers/RoleController.php:73
* @route '/dashboard/roles/{role}/edit'
*/
edit.url = (args: { role: number | { id: number } } | [role: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { role: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { role: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            role: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        role: typeof args.role === 'object'
        ? args.role.id
        : args.role,
    }

    return edit.definition.url
            .replace('{role}', parsedArgs.role.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\RoleController::edit
* @see app/Http/Controllers/RoleController.php:73
* @route '/dashboard/roles/{role}/edit'
*/
edit.get = (args: { role: number | { id: number } } | [role: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\RoleController::edit
* @see app/Http/Controllers/RoleController.php:73
* @route '/dashboard/roles/{role}/edit'
*/
edit.head = (args: { role: number | { id: number } } | [role: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\RoleController::edit
* @see app/Http/Controllers/RoleController.php:73
* @route '/dashboard/roles/{role}/edit'
*/
const editForm = (args: { role: number | { id: number } } | [role: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\RoleController::edit
* @see app/Http/Controllers/RoleController.php:73
* @route '/dashboard/roles/{role}/edit'
*/
editForm.get = (args: { role: number | { id: number } } | [role: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\RoleController::edit
* @see app/Http/Controllers/RoleController.php:73
* @route '/dashboard/roles/{role}/edit'
*/
editForm.head = (args: { role: number | { id: number } } | [role: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
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
* @see \App\Http\Controllers\RoleController::store
* @see app/Http/Controllers/RoleController.php:60
* @route '/dashboard/roles'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/dashboard/roles',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\RoleController::store
* @see app/Http/Controllers/RoleController.php:60
* @route '/dashboard/roles'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\RoleController::store
* @see app/Http/Controllers/RoleController.php:60
* @route '/dashboard/roles'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\RoleController::store
* @see app/Http/Controllers/RoleController.php:60
* @route '/dashboard/roles'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\RoleController::store
* @see app/Http/Controllers/RoleController.php:60
* @route '/dashboard/roles'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\RoleController::update
* @see app/Http/Controllers/RoleController.php:89
* @route '/dashboard/roles/{role}'
*/
export const update = (args: { role: number | { id: number } } | [role: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/dashboard/roles/{role}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\RoleController::update
* @see app/Http/Controllers/RoleController.php:89
* @route '/dashboard/roles/{role}'
*/
update.url = (args: { role: number | { id: number } } | [role: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { role: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { role: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            role: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        role: typeof args.role === 'object'
        ? args.role.id
        : args.role,
    }

    return update.definition.url
            .replace('{role}', parsedArgs.role.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\RoleController::update
* @see app/Http/Controllers/RoleController.php:89
* @route '/dashboard/roles/{role}'
*/
update.put = (args: { role: number | { id: number } } | [role: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\RoleController::update
* @see app/Http/Controllers/RoleController.php:89
* @route '/dashboard/roles/{role}'
*/
const updateForm = (args: { role: number | { id: number } } | [role: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\RoleController::update
* @see app/Http/Controllers/RoleController.php:89
* @route '/dashboard/roles/{role}'
*/
updateForm.put = (args: { role: number | { id: number } } | [role: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
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
* @see \App\Http\Controllers\RoleController::destroy
* @see app/Http/Controllers/RoleController.php:102
* @route '/dashboard/roles/{role}'
*/
export const destroy = (args: { role: number | { id: number } } | [role: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/dashboard/roles/{role}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\RoleController::destroy
* @see app/Http/Controllers/RoleController.php:102
* @route '/dashboard/roles/{role}'
*/
destroy.url = (args: { role: number | { id: number } } | [role: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { role: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { role: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            role: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        role: typeof args.role === 'object'
        ? args.role.id
        : args.role,
    }

    return destroy.definition.url
            .replace('{role}', parsedArgs.role.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\RoleController::destroy
* @see app/Http/Controllers/RoleController.php:102
* @route '/dashboard/roles/{role}'
*/
destroy.delete = (args: { role: number | { id: number } } | [role: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\RoleController::destroy
* @see app/Http/Controllers/RoleController.php:102
* @route '/dashboard/roles/{role}'
*/
const destroyForm = (args: { role: number | { id: number } } | [role: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\RoleController::destroy
* @see app/Http/Controllers/RoleController.php:102
* @route '/dashboard/roles/{role}'
*/
destroyForm.delete = (args: { role: number | { id: number } } | [role: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroy.form = destroyForm

const roles = {
    index,
    data,
    assignable,
    create,
    edit,
    store,
    update,
    destroy,
}

export default roles