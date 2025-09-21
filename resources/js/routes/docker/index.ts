import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../wayfinder'
/**
* @see \App\Http\Controllers\DeploymentController::cleanup
* @see app/Http/Controllers/DeploymentController.php:247
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
* @see \App\Http\Controllers\DeploymentController::cleanup
* @see app/Http/Controllers/DeploymentController.php:247
* @route '/api/docker/cleanup'
*/
cleanup.url = (options?: RouteQueryOptions) => {
    return cleanup.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DeploymentController::cleanup
* @see app/Http/Controllers/DeploymentController.php:247
* @route '/api/docker/cleanup'
*/
cleanup.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: cleanup.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\DeploymentController::cleanup
* @see app/Http/Controllers/DeploymentController.php:247
* @route '/api/docker/cleanup'
*/
const cleanupForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: cleanup.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\DeploymentController::cleanup
* @see app/Http/Controllers/DeploymentController.php:247
* @route '/api/docker/cleanup'
*/
cleanupForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: cleanup.url(options),
    method: 'post',
})

cleanup.form = cleanupForm

const docker = {
    cleanup,
}

export default docker