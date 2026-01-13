import { applyUrlDefaults, queryParams, type RouteDefinition, type RouteFormDefinition, type RouteQueryOptions } from './../../../../wayfinder';
/**
 * @see \App\Http\Controllers\ProjectController::index
 * @see app/Http/Controllers/ProjectController.php:23
 * @route '/dashboard/projects'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
});

index.definition = {
    methods: ['get', 'head'],
    url: '/dashboard/projects',
} satisfies RouteDefinition<['get', 'head']>;

/**
 * @see \App\Http\Controllers\ProjectController::index
 * @see app/Http/Controllers/ProjectController.php:23
 * @route '/dashboard/projects'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options);
};

/**
 * @see \App\Http\Controllers\ProjectController::index
 * @see app/Http/Controllers/ProjectController.php:23
 * @route '/dashboard/projects'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\ProjectController::index
 * @see app/Http/Controllers/ProjectController.php:23
 * @route '/dashboard/projects'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
});

/**
 * @see \App\Http\Controllers\ProjectController::index
 * @see app/Http/Controllers/ProjectController.php:23
 * @route '/dashboard/projects'
 */
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\ProjectController::index
 * @see app/Http/Controllers/ProjectController.php:23
 * @route '/dashboard/projects'
 */
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\ProjectController::index
 * @see app/Http/Controllers/ProjectController.php:23
 * @route '/dashboard/projects'
 */
indexForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        },
    }),
    method: 'get',
});

index.form = indexForm;

/**
 * @see \App\Http\Controllers\ProjectController::data
 * @see app/Http/Controllers/ProjectController.php:28
 * @route '/dashboard/projects/data'
 */
export const data = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: data.url(options),
    method: 'get',
});

data.definition = {
    methods: ['get', 'head'],
    url: '/dashboard/projects/data',
} satisfies RouteDefinition<['get', 'head']>;

/**
 * @see \App\Http\Controllers\ProjectController::data
 * @see app/Http/Controllers/ProjectController.php:28
 * @route '/dashboard/projects/data'
 */
data.url = (options?: RouteQueryOptions) => {
    return data.definition.url + queryParams(options);
};

/**
 * @see \App\Http\Controllers\ProjectController::data
 * @see app/Http/Controllers/ProjectController.php:28
 * @route '/dashboard/projects/data'
 */
data.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: data.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\ProjectController::data
 * @see app/Http/Controllers/ProjectController.php:28
 * @route '/dashboard/projects/data'
 */
data.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: data.url(options),
    method: 'head',
});

/**
 * @see \App\Http\Controllers\ProjectController::data
 * @see app/Http/Controllers/ProjectController.php:28
 * @route '/dashboard/projects/data'
 */
const dataForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: data.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\ProjectController::data
 * @see app/Http/Controllers/ProjectController.php:28
 * @route '/dashboard/projects/data'
 */
dataForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: data.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\ProjectController::data
 * @see app/Http/Controllers/ProjectController.php:28
 * @route '/dashboard/projects/data'
 */
dataForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: data.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        },
    }),
    method: 'get',
});

data.form = dataForm;

/**
 * @see \App\Http\Controllers\ProjectController::create
 * @see app/Http/Controllers/ProjectController.php:41
 * @route '/dashboard/projects/create'
 */
export const create = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
});

create.definition = {
    methods: ['get', 'head'],
    url: '/dashboard/projects/create',
} satisfies RouteDefinition<['get', 'head']>;

/**
 * @see \App\Http\Controllers\ProjectController::create
 * @see app/Http/Controllers/ProjectController.php:41
 * @route '/dashboard/projects/create'
 */
create.url = (options?: RouteQueryOptions) => {
    return create.definition.url + queryParams(options);
};

/**
 * @see \App\Http\Controllers\ProjectController::create
 * @see app/Http/Controllers/ProjectController.php:41
 * @route '/dashboard/projects/create'
 */
create.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\ProjectController::create
 * @see app/Http/Controllers/ProjectController.php:41
 * @route '/dashboard/projects/create'
 */
create.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(options),
    method: 'head',
});

/**
 * @see \App\Http\Controllers\ProjectController::create
 * @see app/Http/Controllers/ProjectController.php:41
 * @route '/dashboard/projects/create'
 */
const createForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\ProjectController::create
 * @see app/Http/Controllers/ProjectController.php:41
 * @route '/dashboard/projects/create'
 */
createForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\ProjectController::create
 * @see app/Http/Controllers/ProjectController.php:41
 * @route '/dashboard/projects/create'
 */
createForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        },
    }),
    method: 'get',
});

create.form = createForm;

/**
 * @see \App\Http\Controllers\ProjectController::show
 * @see app/Http/Controllers/ProjectController.php:46
 * @route '/dashboard/projects/{project}'
 */
export const show = (
    args: { project: number | { id: number } } | [project: number | { id: number }] | number | { id: number },
    options?: RouteQueryOptions,
): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
});

show.definition = {
    methods: ['get', 'head'],
    url: '/dashboard/projects/{project}',
} satisfies RouteDefinition<['get', 'head']>;

/**
 * @see \App\Http\Controllers\ProjectController::show
 * @see app/Http/Controllers/ProjectController.php:46
 * @route '/dashboard/projects/{project}'
 */
show.url = (
    args: { project: number | { id: number } } | [project: number | { id: number }] | number | { id: number },
    options?: RouteQueryOptions,
) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { project: args };
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { project: args.id };
    }

    if (Array.isArray(args)) {
        args = {
            project: args[0],
        };
    }

    args = applyUrlDefaults(args);

    const parsedArgs = {
        project: typeof args.project === 'object' ? args.project.id : args.project,
    };

    return show.definition.url.replace('{project}', parsedArgs.project.toString()).replace(/\/+$/, '') + queryParams(options);
};

/**
 * @see \App\Http\Controllers\ProjectController::show
 * @see app/Http/Controllers/ProjectController.php:46
 * @route '/dashboard/projects/{project}'
 */
show.get = (
    args: { project: number | { id: number } } | [project: number | { id: number }] | number | { id: number },
    options?: RouteQueryOptions,
): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\ProjectController::show
 * @see app/Http/Controllers/ProjectController.php:46
 * @route '/dashboard/projects/{project}'
 */
show.head = (
    args: { project: number | { id: number } } | [project: number | { id: number }] | number | { id: number },
    options?: RouteQueryOptions,
): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
});

/**
 * @see \App\Http\Controllers\ProjectController::show
 * @see app/Http/Controllers/ProjectController.php:46
 * @route '/dashboard/projects/{project}'
 */
const showForm = (
    args: { project: number | { id: number } } | [project: number | { id: number }] | number | { id: number },
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\ProjectController::show
 * @see app/Http/Controllers/ProjectController.php:46
 * @route '/dashboard/projects/{project}'
 */
showForm.get = (
    args: { project: number | { id: number } } | [project: number | { id: number }] | number | { id: number },
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\ProjectController::show
 * @see app/Http/Controllers/ProjectController.php:46
 * @route '/dashboard/projects/{project}'
 */
showForm.head = (
    args: { project: number | { id: number } } | [project: number | { id: number }] | number | { id: number },
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: show.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        },
    }),
    method: 'get',
});

show.form = showForm;

/**
 * @see \App\Http\Controllers\ProjectController::edit
 * @see app/Http/Controllers/ProjectController.php:55
 * @route '/dashboard/projects/{project}/edit'
 */
export const edit = (
    args: { project: number | { id: number } } | [project: number | { id: number }] | number | { id: number },
    options?: RouteQueryOptions,
): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
});

edit.definition = {
    methods: ['get', 'head'],
    url: '/dashboard/projects/{project}/edit',
} satisfies RouteDefinition<['get', 'head']>;

/**
 * @see \App\Http\Controllers\ProjectController::edit
 * @see app/Http/Controllers/ProjectController.php:55
 * @route '/dashboard/projects/{project}/edit'
 */
edit.url = (
    args: { project: number | { id: number } } | [project: number | { id: number }] | number | { id: number },
    options?: RouteQueryOptions,
) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { project: args };
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { project: args.id };
    }

    if (Array.isArray(args)) {
        args = {
            project: args[0],
        };
    }

    args = applyUrlDefaults(args);

    const parsedArgs = {
        project: typeof args.project === 'object' ? args.project.id : args.project,
    };

    return edit.definition.url.replace('{project}', parsedArgs.project.toString()).replace(/\/+$/, '') + queryParams(options);
};

/**
 * @see \App\Http\Controllers\ProjectController::edit
 * @see app/Http/Controllers/ProjectController.php:55
 * @route '/dashboard/projects/{project}/edit'
 */
edit.get = (
    args: { project: number | { id: number } } | [project: number | { id: number }] | number | { id: number },
    options?: RouteQueryOptions,
): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\ProjectController::edit
 * @see app/Http/Controllers/ProjectController.php:55
 * @route '/dashboard/projects/{project}/edit'
 */
edit.head = (
    args: { project: number | { id: number } } | [project: number | { id: number }] | number | { id: number },
    options?: RouteQueryOptions,
): RouteDefinition<'head'> => ({
    url: edit.url(args, options),
    method: 'head',
});

/**
 * @see \App\Http\Controllers\ProjectController::edit
 * @see app/Http/Controllers/ProjectController.php:55
 * @route '/dashboard/projects/{project}/edit'
 */
const editForm = (
    args: { project: number | { id: number } } | [project: number | { id: number }] | number | { id: number },
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: edit.url(args, options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\ProjectController::edit
 * @see app/Http/Controllers/ProjectController.php:55
 * @route '/dashboard/projects/{project}/edit'
 */
editForm.get = (
    args: { project: number | { id: number } } | [project: number | { id: number }] | number | { id: number },
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: edit.url(args, options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\ProjectController::edit
 * @see app/Http/Controllers/ProjectController.php:55
 * @route '/dashboard/projects/{project}/edit'
 */
editForm.head = (
    args: { project: number | { id: number } } | [project: number | { id: number }] | number | { id: number },
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: edit.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        },
    }),
    method: 'get',
});

edit.form = editForm;

/**
 * @see \App\Http\Controllers\ProjectController::store
 * @see app/Http/Controllers/ProjectController.php:62
 * @route '/dashboard/projects'
 */
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
});

store.definition = {
    methods: ['post'],
    url: '/dashboard/projects',
} satisfies RouteDefinition<['post']>;

/**
 * @see \App\Http\Controllers\ProjectController::store
 * @see app/Http/Controllers/ProjectController.php:62
 * @route '/dashboard/projects'
 */
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options);
};

/**
 * @see \App\Http\Controllers\ProjectController::store
 * @see app/Http/Controllers/ProjectController.php:62
 * @route '/dashboard/projects'
 */
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
});

/**
 * @see \App\Http\Controllers\ProjectController::store
 * @see app/Http/Controllers/ProjectController.php:62
 * @route '/dashboard/projects'
 */
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
});

/**
 * @see \App\Http\Controllers\ProjectController::store
 * @see app/Http/Controllers/ProjectController.php:62
 * @route '/dashboard/projects'
 */
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
});

store.form = storeForm;

/**
 * @see \App\Http\Controllers\ProjectController::update
 * @see app/Http/Controllers/ProjectController.php:72
 * @route '/dashboard/projects/{project}'
 */
export const update = (
    args: { project: number | { id: number } } | [project: number | { id: number }] | number | { id: number },
    options?: RouteQueryOptions,
): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
});

update.definition = {
    methods: ['put'],
    url: '/dashboard/projects/{project}',
} satisfies RouteDefinition<['put']>;

/**
 * @see \App\Http\Controllers\ProjectController::update
 * @see app/Http/Controllers/ProjectController.php:72
 * @route '/dashboard/projects/{project}'
 */
update.url = (
    args: { project: number | { id: number } } | [project: number | { id: number }] | number | { id: number },
    options?: RouteQueryOptions,
) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { project: args };
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { project: args.id };
    }

    if (Array.isArray(args)) {
        args = {
            project: args[0],
        };
    }

    args = applyUrlDefaults(args);

    const parsedArgs = {
        project: typeof args.project === 'object' ? args.project.id : args.project,
    };

    return update.definition.url.replace('{project}', parsedArgs.project.toString()).replace(/\/+$/, '') + queryParams(options);
};

/**
 * @see \App\Http\Controllers\ProjectController::update
 * @see app/Http/Controllers/ProjectController.php:72
 * @route '/dashboard/projects/{project}'
 */
update.put = (
    args: { project: number | { id: number } } | [project: number | { id: number }] | number | { id: number },
    options?: RouteQueryOptions,
): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
});

/**
 * @see \App\Http\Controllers\ProjectController::update
 * @see app/Http/Controllers/ProjectController.php:72
 * @route '/dashboard/projects/{project}'
 */
const updateForm = (
    args: { project: number | { id: number } } | [project: number | { id: number }] | number | { id: number },
    options?: RouteQueryOptions,
): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        },
    }),
    method: 'post',
});

/**
 * @see \App\Http\Controllers\ProjectController::update
 * @see app/Http/Controllers/ProjectController.php:72
 * @route '/dashboard/projects/{project}'
 */
updateForm.put = (
    args: { project: number | { id: number } } | [project: number | { id: number }] | number | { id: number },
    options?: RouteQueryOptions,
): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        },
    }),
    method: 'post',
});

update.form = updateForm;

/**
 * @see \App\Http\Controllers\ProjectController::destroy
 * @see app/Http/Controllers/ProjectController.php:82
 * @route '/dashboard/projects/{project}'
 */
export const destroy = (
    args: { project: string | number } | [project: string | number] | string | number,
    options?: RouteQueryOptions,
): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
});

destroy.definition = {
    methods: ['delete'],
    url: '/dashboard/projects/{project}',
} satisfies RouteDefinition<['delete']>;

/**
 * @see \App\Http\Controllers\ProjectController::destroy
 * @see app/Http/Controllers/ProjectController.php:82
 * @route '/dashboard/projects/{project}'
 */
destroy.url = (args: { project: string | number } | [project: string | number] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { project: args };
    }

    if (Array.isArray(args)) {
        args = {
            project: args[0],
        };
    }

    args = applyUrlDefaults(args);

    const parsedArgs = {
        project: args.project,
    };

    return destroy.definition.url.replace('{project}', parsedArgs.project.toString()).replace(/\/+$/, '') + queryParams(options);
};

/**
 * @see \App\Http\Controllers\ProjectController::destroy
 * @see app/Http/Controllers/ProjectController.php:82
 * @route '/dashboard/projects/{project}'
 */
destroy.delete = (
    args: { project: string | number } | [project: string | number] | string | number,
    options?: RouteQueryOptions,
): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
});

/**
 * @see \App\Http\Controllers\ProjectController::destroy
 * @see app/Http/Controllers/ProjectController.php:82
 * @route '/dashboard/projects/{project}'
 */
const destroyForm = (
    args: { project: string | number } | [project: string | number] | string | number,
    options?: RouteQueryOptions,
): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        },
    }),
    method: 'post',
});

/**
 * @see \App\Http\Controllers\ProjectController::destroy
 * @see app/Http/Controllers/ProjectController.php:82
 * @route '/dashboard/projects/{project}'
 */
destroyForm.delete = (
    args: { project: string | number } | [project: string | number] | string | number,
    options?: RouteQueryOptions,
): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        },
    }),
    method: 'post',
});

destroy.form = destroyForm;

const ProjectController = { index, data, create, show, edit, store, update, destroy };

export default ProjectController;
