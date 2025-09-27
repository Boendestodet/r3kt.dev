import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\DockerSystemController::info
* @see app/Http/Controllers/DockerSystemController.php:20
* @route '/api/docker/info'
*/
export const info = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: info.url(options),
    method: 'get',
})

info.definition = {
    methods: ["get","head"],
    url: '/api/docker/info',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DockerSystemController::info
* @see app/Http/Controllers/DockerSystemController.php:20
* @route '/api/docker/info'
*/
info.url = (options?: RouteQueryOptions) => {
    return info.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DockerSystemController::info
* @see app/Http/Controllers/DockerSystemController.php:20
* @route '/api/docker/info'
*/
info.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: info.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DockerSystemController::info
* @see app/Http/Controllers/DockerSystemController.php:20
* @route '/api/docker/info'
*/
info.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: info.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DockerSystemController::info
* @see app/Http/Controllers/DockerSystemController.php:20
* @route '/api/docker/info'
*/
const infoForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: info.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DockerSystemController::info
* @see app/Http/Controllers/DockerSystemController.php:20
* @route '/api/docker/info'
*/
infoForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: info.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DockerSystemController::info
* @see app/Http/Controllers/DockerSystemController.php:20
* @route '/api/docker/info'
*/
infoForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: info.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

info.form = infoForm

/**
* @see \App\Http\Controllers\DockerSystemController::getRunningContainers
* @see app/Http/Controllers/DockerSystemController.php:45
* @route '/api/docker/containers'
*/
export const getRunningContainers = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getRunningContainers.url(options),
    method: 'get',
})

getRunningContainers.definition = {
    methods: ["get","head"],
    url: '/api/docker/containers',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DockerSystemController::getRunningContainers
* @see app/Http/Controllers/DockerSystemController.php:45
* @route '/api/docker/containers'
*/
getRunningContainers.url = (options?: RouteQueryOptions) => {
    return getRunningContainers.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DockerSystemController::getRunningContainers
* @see app/Http/Controllers/DockerSystemController.php:45
* @route '/api/docker/containers'
*/
getRunningContainers.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getRunningContainers.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DockerSystemController::getRunningContainers
* @see app/Http/Controllers/DockerSystemController.php:45
* @route '/api/docker/containers'
*/
getRunningContainers.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getRunningContainers.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DockerSystemController::getRunningContainers
* @see app/Http/Controllers/DockerSystemController.php:45
* @route '/api/docker/containers'
*/
const getRunningContainersForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getRunningContainers.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DockerSystemController::getRunningContainers
* @see app/Http/Controllers/DockerSystemController.php:45
* @route '/api/docker/containers'
*/
getRunningContainersForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getRunningContainers.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DockerSystemController::getRunningContainers
* @see app/Http/Controllers/DockerSystemController.php:45
* @route '/api/docker/containers'
*/
getRunningContainersForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getRunningContainers.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

getRunningContainers.form = getRunningContainersForm

/**
* @see \App\Http\Controllers\DockerSystemController::cleanup
* @see app/Http/Controllers/DockerSystemController.php:70
* @route '/api/docker/cleanup'
*/
export const cleanup = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: cleanup.url(options),
    method: 'post',
})

cleanup.definition = {
    methods: ["post"],
    url: '/api/docker/cleanup',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\DockerSystemController::cleanup
* @see app/Http/Controllers/DockerSystemController.php:70
* @route '/api/docker/cleanup'
*/
cleanup.url = (options?: RouteQueryOptions) => {
    return cleanup.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DockerSystemController::cleanup
* @see app/Http/Controllers/DockerSystemController.php:70
* @route '/api/docker/cleanup'
*/
cleanup.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: cleanup.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\DockerSystemController::cleanup
* @see app/Http/Controllers/DockerSystemController.php:70
* @route '/api/docker/cleanup'
*/
const cleanupForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: cleanup.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\DockerSystemController::cleanup
* @see app/Http/Controllers/DockerSystemController.php:70
* @route '/api/docker/cleanup'
*/
cleanupForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: cleanup.url(options),
    method: 'post',
})

cleanup.form = cleanupForm

const DockerSystemController = { info, getRunningContainers, cleanup }

export default DockerSystemController