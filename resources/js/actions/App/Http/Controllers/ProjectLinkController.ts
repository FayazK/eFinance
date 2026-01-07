import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\ProjectLinkController::index
* @see app/Http/Controllers/ProjectLinkController.php:21
* @route '/dashboard/projects/{project}/links'
*/
export const index = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(args, options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/dashboard/projects/{project}/links',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\ProjectLinkController::index
* @see app/Http/Controllers/ProjectLinkController.php:21
* @route '/dashboard/projects/{project}/links'
*/
index.url = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { project: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { project: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            project: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        project: typeof args.project === 'object'
        ? args.project.id
        : args.project,
    }

    return index.definition.url
            .replace('{project}', parsedArgs.project.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ProjectLinkController::index
* @see app/Http/Controllers/ProjectLinkController.php:21
* @route '/dashboard/projects/{project}/links'
*/
index.get = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ProjectLinkController::index
* @see app/Http/Controllers/ProjectLinkController.php:21
* @route '/dashboard/projects/{project}/links'
*/
index.head = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\ProjectLinkController::store
* @see app/Http/Controllers/ProjectLinkController.php:30
* @route '/dashboard/projects/{project}/links'
*/
export const store = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/dashboard/projects/{project}/links',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\ProjectLinkController::store
* @see app/Http/Controllers/ProjectLinkController.php:30
* @route '/dashboard/projects/{project}/links'
*/
store.url = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { project: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { project: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            project: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        project: typeof args.project === 'object'
        ? args.project.id
        : args.project,
    }

    return store.definition.url
            .replace('{project}', parsedArgs.project.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ProjectLinkController::store
* @see app/Http/Controllers/ProjectLinkController.php:30
* @route '/dashboard/projects/{project}/links'
*/
store.post = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\ProjectLinkController::update
* @see app/Http/Controllers/ProjectLinkController.php:41
* @route '/dashboard/projects/{project}/links/{link}'
*/
export const update = (args: { project: number | { id: number }, link: number | { id: number } } | [project: number | { id: number }, link: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/dashboard/projects/{project}/links/{link}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\ProjectLinkController::update
* @see app/Http/Controllers/ProjectLinkController.php:41
* @route '/dashboard/projects/{project}/links/{link}'
*/
update.url = (args: { project: number | { id: number }, link: number | { id: number } } | [project: number | { id: number }, link: number | { id: number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            project: args[0],
            link: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        project: typeof args.project === 'object'
        ? args.project.id
        : args.project,
        link: typeof args.link === 'object'
        ? args.link.id
        : args.link,
    }

    return update.definition.url
            .replace('{project}', parsedArgs.project.toString())
            .replace('{link}', parsedArgs.link.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ProjectLinkController::update
* @see app/Http/Controllers/ProjectLinkController.php:41
* @route '/dashboard/projects/{project}/links/{link}'
*/
update.put = (args: { project: number | { id: number }, link: number | { id: number } } | [project: number | { id: number }, link: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\ProjectLinkController::destroy
* @see app/Http/Controllers/ProjectLinkController.php:56
* @route '/dashboard/projects/{project}/links/{link}'
*/
export const destroy = (args: { project: number | { id: number }, link: number | { id: number } } | [project: number | { id: number }, link: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/dashboard/projects/{project}/links/{link}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\ProjectLinkController::destroy
* @see app/Http/Controllers/ProjectLinkController.php:56
* @route '/dashboard/projects/{project}/links/{link}'
*/
destroy.url = (args: { project: number | { id: number }, link: number | { id: number } } | [project: number | { id: number }, link: number | { id: number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            project: args[0],
            link: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        project: typeof args.project === 'object'
        ? args.project.id
        : args.project,
        link: typeof args.link === 'object'
        ? args.link.id
        : args.link,
    }

    return destroy.definition.url
            .replace('{project}', parsedArgs.project.toString())
            .replace('{link}', parsedArgs.link.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ProjectLinkController::destroy
* @see app/Http/Controllers/ProjectLinkController.php:56
* @route '/dashboard/projects/{project}/links/{link}'
*/
destroy.delete = (args: { project: number | { id: number }, link: number | { id: number } } | [project: number | { id: number }, link: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

const ProjectLinkController = { index, store, update, destroy }

export default ProjectLinkController