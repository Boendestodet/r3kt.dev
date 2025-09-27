import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\ProjectDeploymentController::deploy
* @see app/Http/Controllers/ProjectDeploymentController.php:21
* @route '/api/projects/{project}/deploy'
*/
export const deploy = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: deploy.url(args, options),
    method: 'post',
})

deploy.definition = {
    methods: ["post"],
    url: '/api/projects/{project}/deploy',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\ProjectDeploymentController::deploy
* @see app/Http/Controllers/ProjectDeploymentController.php:21
* @route '/api/projects/{project}/deploy'
*/
deploy.url = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return deploy.definition.url
            .replace('{project}', parsedArgs.project.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ProjectDeploymentController::deploy
* @see app/Http/Controllers/ProjectDeploymentController.php:21
* @route '/api/projects/{project}/deploy'
*/
deploy.post = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: deploy.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\ProjectDeploymentController::deploy
* @see app/Http/Controllers/ProjectDeploymentController.php:21
* @route '/api/projects/{project}/deploy'
*/
const deployForm = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: deploy.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\ProjectDeploymentController::deploy
* @see app/Http/Controllers/ProjectDeploymentController.php:21
* @route '/api/projects/{project}/deploy'
*/
deployForm.post = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: deploy.url(args, options),
    method: 'post',
})

deploy.form = deployForm

/**
* @see \App\Http\Controllers\ProjectDeploymentController::getPreviewUrl
* @see app/Http/Controllers/ProjectDeploymentController.php:93
* @route '/api/projects/{project}/docker/preview'
*/
export const getPreviewUrl = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getPreviewUrl.url(args, options),
    method: 'get',
})

getPreviewUrl.definition = {
    methods: ["get","head"],
    url: '/api/projects/{project}/docker/preview',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\ProjectDeploymentController::getPreviewUrl
* @see app/Http/Controllers/ProjectDeploymentController.php:93
* @route '/api/projects/{project}/docker/preview'
*/
getPreviewUrl.url = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return getPreviewUrl.definition.url
            .replace('{project}', parsedArgs.project.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ProjectDeploymentController::getPreviewUrl
* @see app/Http/Controllers/ProjectDeploymentController.php:93
* @route '/api/projects/{project}/docker/preview'
*/
getPreviewUrl.get = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getPreviewUrl.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ProjectDeploymentController::getPreviewUrl
* @see app/Http/Controllers/ProjectDeploymentController.php:93
* @route '/api/projects/{project}/docker/preview'
*/
getPreviewUrl.head = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getPreviewUrl.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\ProjectDeploymentController::getPreviewUrl
* @see app/Http/Controllers/ProjectDeploymentController.php:93
* @route '/api/projects/{project}/docker/preview'
*/
const getPreviewUrlForm = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getPreviewUrl.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ProjectDeploymentController::getPreviewUrl
* @see app/Http/Controllers/ProjectDeploymentController.php:93
* @route '/api/projects/{project}/docker/preview'
*/
getPreviewUrlForm.get = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getPreviewUrl.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ProjectDeploymentController::getPreviewUrl
* @see app/Http/Controllers/ProjectDeploymentController.php:93
* @route '/api/projects/{project}/docker/preview'
*/
getPreviewUrlForm.head = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getPreviewUrl.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

getPreviewUrl.form = getPreviewUrlForm

const ProjectDeploymentController = { deploy, getPreviewUrl }

export default ProjectDeploymentController