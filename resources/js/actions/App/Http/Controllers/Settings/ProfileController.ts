import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Settings\ProfileController::edit
* @see app/Http/Controllers/Settings/ProfileController.php:28
* @route '/settings/account'
*/
export const edit = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/settings/account',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Settings\ProfileController::edit
* @see app/Http/Controllers/Settings/ProfileController.php:28
* @route '/settings/account'
*/
edit.url = (options?: RouteQueryOptions) => {
    return edit.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Settings\ProfileController::edit
* @see app/Http/Controllers/Settings/ProfileController.php:28
* @route '/settings/account'
*/
edit.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Settings\ProfileController::edit
* @see app/Http/Controllers/Settings/ProfileController.php:28
* @route '/settings/account'
*/
edit.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Settings\ProfileController::update
* @see app/Http/Controllers/Settings/ProfileController.php:39
* @route '/settings/profile'
*/
export const update = (options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(options),
    method: 'patch',
})

update.definition = {
    methods: ["patch"],
    url: '/settings/profile',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Http\Controllers\Settings\ProfileController::update
* @see app/Http/Controllers/Settings/ProfileController.php:39
* @route '/settings/profile'
*/
update.url = (options?: RouteQueryOptions) => {
    return update.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Settings\ProfileController::update
* @see app/Http/Controllers/Settings/ProfileController.php:39
* @route '/settings/profile'
*/
update.patch = (options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\Settings\ProfileController::destroy
* @see app/Http/Controllers/Settings/ProfileController.php:49
* @route '/settings/profile'
*/
export const destroy = (options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/settings/profile',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Settings\ProfileController::destroy
* @see app/Http/Controllers/Settings/ProfileController.php:49
* @route '/settings/profile'
*/
destroy.url = (options?: RouteQueryOptions) => {
    return destroy.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Settings\ProfileController::destroy
* @see app/Http/Controllers/Settings/ProfileController.php:49
* @route '/settings/profile'
*/
destroy.delete = (options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\Settings\ProfileController::updateAvatar
* @see app/Http/Controllers/Settings/ProfileController.php:70
* @route '/settings/avatar'
*/
export const updateAvatar = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: updateAvatar.url(options),
    method: 'post',
})

updateAvatar.definition = {
    methods: ["post"],
    url: '/settings/avatar',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Settings\ProfileController::updateAvatar
* @see app/Http/Controllers/Settings/ProfileController.php:70
* @route '/settings/avatar'
*/
updateAvatar.url = (options?: RouteQueryOptions) => {
    return updateAvatar.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Settings\ProfileController::updateAvatar
* @see app/Http/Controllers/Settings/ProfileController.php:70
* @route '/settings/avatar'
*/
updateAvatar.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: updateAvatar.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Settings\ProfileController::destroyAvatar
* @see app/Http/Controllers/Settings/ProfileController.php:87
* @route '/settings/avatar'
*/
export const destroyAvatar = (options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroyAvatar.url(options),
    method: 'delete',
})

destroyAvatar.definition = {
    methods: ["delete"],
    url: '/settings/avatar',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Settings\ProfileController::destroyAvatar
* @see app/Http/Controllers/Settings/ProfileController.php:87
* @route '/settings/avatar'
*/
destroyAvatar.url = (options?: RouteQueryOptions) => {
    return destroyAvatar.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Settings\ProfileController::destroyAvatar
* @see app/Http/Controllers/Settings/ProfileController.php:87
* @route '/settings/avatar'
*/
destroyAvatar.delete = (options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroyAvatar.url(options),
    method: 'delete',
})

const ProfileController = { edit, update, destroy, updateAvatar, destroyAvatar }

export default ProfileController