import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\ProjectDocumentController::store
* @see app/Http/Controllers/ProjectDocumentController.php:19
* @route '/dashboard/projects/{project}/documents'
*/
export const store = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/dashboard/projects/{project}/documents',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\ProjectDocumentController::store
* @see app/Http/Controllers/ProjectDocumentController.php:19
* @route '/dashboard/projects/{project}/documents'
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
* @see \App\Http\Controllers\ProjectDocumentController::store
* @see app/Http/Controllers/ProjectDocumentController.php:19
* @route '/dashboard/projects/{project}/documents'
*/
store.post = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\ProjectDocumentController::store
* @see app/Http/Controllers/ProjectDocumentController.php:19
* @route '/dashboard/projects/{project}/documents'
*/
const storeForm = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\ProjectDocumentController::store
* @see app/Http/Controllers/ProjectDocumentController.php:19
* @route '/dashboard/projects/{project}/documents'
*/
storeForm.post = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(args, options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\ProjectDocumentController::destroy
* @see app/Http/Controllers/ProjectDocumentController.php:38
* @route '/dashboard/projects/{project}/documents/{media}'
*/
export const destroy = (args: { project: number | { id: number }, media: number | { id: number } } | [project: number | { id: number }, media: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/dashboard/projects/{project}/documents/{media}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\ProjectDocumentController::destroy
* @see app/Http/Controllers/ProjectDocumentController.php:38
* @route '/dashboard/projects/{project}/documents/{media}'
*/
destroy.url = (args: { project: number | { id: number }, media: number | { id: number } } | [project: number | { id: number }, media: number | { id: number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            project: args[0],
            media: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        project: typeof args.project === 'object'
        ? args.project.id
        : args.project,
        media: typeof args.media === 'object'
        ? args.media.id
        : args.media,
    }

    return destroy.definition.url
            .replace('{project}', parsedArgs.project.toString())
            .replace('{media}', parsedArgs.media.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ProjectDocumentController::destroy
* @see app/Http/Controllers/ProjectDocumentController.php:38
* @route '/dashboard/projects/{project}/documents/{media}'
*/
destroy.delete = (args: { project: number | { id: number }, media: number | { id: number } } | [project: number | { id: number }, media: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\ProjectDocumentController::destroy
* @see app/Http/Controllers/ProjectDocumentController.php:38
* @route '/dashboard/projects/{project}/documents/{media}'
*/
const destroyForm = (args: { project: number | { id: number }, media: number | { id: number } } | [project: number | { id: number }, media: number | { id: number } ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\ProjectDocumentController::destroy
* @see app/Http/Controllers/ProjectDocumentController.php:38
* @route '/dashboard/projects/{project}/documents/{media}'
*/
destroyForm.delete = (args: { project: number | { id: number }, media: number | { id: number } } | [project: number | { id: number }, media: number | { id: number } ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroy.form = destroyForm

const ProjectDocumentController = { store, destroy }

export default ProjectDocumentController