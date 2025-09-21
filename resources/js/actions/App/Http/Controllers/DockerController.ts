import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\DockerController::info
* @see app/Http/Controllers/DockerController.php:23
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
* @see \App\Http\Controllers\DockerController::info
* @see app/Http/Controllers/DockerController.php:23
* @route '/api/docker/info'
*/
info.url = (options?: RouteQueryOptions) => {
    return info.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DockerController::info
* @see app/Http/Controllers/DockerController.php:23
* @route '/api/docker/info'
*/
info.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: info.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DockerController::info
* @see app/Http/Controllers/DockerController.php:23
* @route '/api/docker/info'
*/
info.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: info.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DockerController::info
* @see app/Http/Controllers/DockerController.php:23
* @route '/api/docker/info'
*/
const infoForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: info.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DockerController::info
* @see app/Http/Controllers/DockerController.php:23
* @route '/api/docker/info'
*/
infoForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: info.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DockerController::info
* @see app/Http/Controllers/DockerController.php:23
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
* @see \App\Http\Controllers\DockerController::getRunningContainers
* @see app/Http/Controllers/DockerController.php:307
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
* @see \App\Http\Controllers\DockerController::getRunningContainers
* @see app/Http/Controllers/DockerController.php:307
* @route '/api/docker/containers'
*/
getRunningContainers.url = (options?: RouteQueryOptions) => {
    return getRunningContainers.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DockerController::getRunningContainers
* @see app/Http/Controllers/DockerController.php:307
* @route '/api/docker/containers'
*/
getRunningContainers.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getRunningContainers.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DockerController::getRunningContainers
* @see app/Http/Controllers/DockerController.php:307
* @route '/api/docker/containers'
*/
getRunningContainers.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getRunningContainers.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DockerController::getRunningContainers
* @see app/Http/Controllers/DockerController.php:307
* @route '/api/docker/containers'
*/
const getRunningContainersForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getRunningContainers.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DockerController::getRunningContainers
* @see app/Http/Controllers/DockerController.php:307
* @route '/api/docker/containers'
*/
getRunningContainersForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getRunningContainers.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DockerController::getRunningContainers
* @see app/Http/Controllers/DockerController.php:307
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
* @see \App\Http\Controllers\DockerController::cleanup
* @see app/Http/Controllers/DockerController.php:333
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
* @see \App\Http\Controllers\DockerController::cleanup
* @see app/Http/Controllers/DockerController.php:333
* @route '/api/docker/cleanup'
*/
cleanup.url = (options?: RouteQueryOptions) => {
    return cleanup.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DockerController::cleanup
* @see app/Http/Controllers/DockerController.php:333
* @route '/api/docker/cleanup'
*/
cleanup.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: cleanup.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\DockerController::cleanup
* @see app/Http/Controllers/DockerController.php:333
* @route '/api/docker/cleanup'
*/
const cleanupForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: cleanup.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\DockerController::cleanup
* @see app/Http/Controllers/DockerController.php:333
* @route '/api/docker/cleanup'
*/
cleanupForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: cleanup.url(options),
    method: 'post',
})

cleanup.form = cleanupForm

/**
* @see \App\Http\Controllers\DockerController::deploy
* @see app/Http/Controllers/DockerController.php:360
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
* @see \App\Http\Controllers\DockerController::deploy
* @see app/Http/Controllers/DockerController.php:360
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
* @see \App\Http\Controllers\DockerController::deploy
* @see app/Http/Controllers/DockerController.php:360
* @route '/api/projects/{project}/deploy'
*/
deploy.post = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: deploy.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\DockerController::deploy
* @see app/Http/Controllers/DockerController.php:360
* @route '/api/projects/{project}/deploy'
*/
const deployForm = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: deploy.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\DockerController::deploy
* @see app/Http/Controllers/DockerController.php:360
* @route '/api/projects/{project}/deploy'
*/
deployForm.post = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: deploy.url(args, options),
    method: 'post',
})

deploy.form = deployForm

/**
* @see \App\Http\Controllers\DockerController::startContainer
* @see app/Http/Controllers/DockerController.php:48
* @route '/api/projects/{project}/docker/start'
*/
export const startContainer = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: startContainer.url(args, options),
    method: 'post',
})

startContainer.definition = {
    methods: ["post"],
    url: '/api/projects/{project}/docker/start',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\DockerController::startContainer
* @see app/Http/Controllers/DockerController.php:48
* @route '/api/projects/{project}/docker/start'
*/
startContainer.url = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return startContainer.definition.url
            .replace('{project}', parsedArgs.project.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DockerController::startContainer
* @see app/Http/Controllers/DockerController.php:48
* @route '/api/projects/{project}/docker/start'
*/
startContainer.post = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: startContainer.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\DockerController::startContainer
* @see app/Http/Controllers/DockerController.php:48
* @route '/api/projects/{project}/docker/start'
*/
const startContainerForm = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: startContainer.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\DockerController::startContainer
* @see app/Http/Controllers/DockerController.php:48
* @route '/api/projects/{project}/docker/start'
*/
startContainerForm.post = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: startContainer.url(args, options),
    method: 'post',
})

startContainer.form = startContainerForm

/**
* @see \App\Http\Controllers\DockerController::getPreviewUrl
* @see app/Http/Controllers/DockerController.php:401
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
* @see \App\Http\Controllers\DockerController::getPreviewUrl
* @see app/Http/Controllers/DockerController.php:401
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
* @see \App\Http\Controllers\DockerController::getPreviewUrl
* @see app/Http/Controllers/DockerController.php:401
* @route '/api/projects/{project}/docker/preview'
*/
getPreviewUrl.get = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getPreviewUrl.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DockerController::getPreviewUrl
* @see app/Http/Controllers/DockerController.php:401
* @route '/api/projects/{project}/docker/preview'
*/
getPreviewUrl.head = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getPreviewUrl.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DockerController::getPreviewUrl
* @see app/Http/Controllers/DockerController.php:401
* @route '/api/projects/{project}/docker/preview'
*/
const getPreviewUrlForm = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getPreviewUrl.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DockerController::getPreviewUrl
* @see app/Http/Controllers/DockerController.php:401
* @route '/api/projects/{project}/docker/preview'
*/
getPreviewUrlForm.get = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getPreviewUrl.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DockerController::getPreviewUrl
* @see app/Http/Controllers/DockerController.php:401
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

/**
* @see \App\Http\Controllers\DockerController::getContainerStatus
* @see app/Http/Controllers/DockerController.php:223
* @route '/api/projects/{project}/docker/status'
*/
const getContainerStatus5a0edbce5d8e01faacfef6a1ba4f62b3 = (args: { project: string | number } | [project: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getContainerStatus5a0edbce5d8e01faacfef6a1ba4f62b3.url(args, options),
    method: 'get',
})

getContainerStatus5a0edbce5d8e01faacfef6a1ba4f62b3.definition = {
    methods: ["get","head"],
    url: '/api/projects/{project}/docker/status',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DockerController::getContainerStatus
* @see app/Http/Controllers/DockerController.php:223
* @route '/api/projects/{project}/docker/status'
*/
getContainerStatus5a0edbce5d8e01faacfef6a1ba4f62b3.url = (args: { project: string | number } | [project: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { project: args }
    }

    if (Array.isArray(args)) {
        args = {
            project: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        project: args.project,
    }

    return getContainerStatus5a0edbce5d8e01faacfef6a1ba4f62b3.definition.url
            .replace('{project}', parsedArgs.project.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DockerController::getContainerStatus
* @see app/Http/Controllers/DockerController.php:223
* @route '/api/projects/{project}/docker/status'
*/
getContainerStatus5a0edbce5d8e01faacfef6a1ba4f62b3.get = (args: { project: string | number } | [project: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getContainerStatus5a0edbce5d8e01faacfef6a1ba4f62b3.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DockerController::getContainerStatus
* @see app/Http/Controllers/DockerController.php:223
* @route '/api/projects/{project}/docker/status'
*/
getContainerStatus5a0edbce5d8e01faacfef6a1ba4f62b3.head = (args: { project: string | number } | [project: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getContainerStatus5a0edbce5d8e01faacfef6a1ba4f62b3.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DockerController::getContainerStatus
* @see app/Http/Controllers/DockerController.php:223
* @route '/api/projects/{project}/docker/status'
*/
const getContainerStatus5a0edbce5d8e01faacfef6a1ba4f62b3Form = (args: { project: string | number } | [project: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getContainerStatus5a0edbce5d8e01faacfef6a1ba4f62b3.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DockerController::getContainerStatus
* @see app/Http/Controllers/DockerController.php:223
* @route '/api/projects/{project}/docker/status'
*/
getContainerStatus5a0edbce5d8e01faacfef6a1ba4f62b3Form.get = (args: { project: string | number } | [project: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getContainerStatus5a0edbce5d8e01faacfef6a1ba4f62b3.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DockerController::getContainerStatus
* @see app/Http/Controllers/DockerController.php:223
* @route '/api/projects/{project}/docker/status'
*/
getContainerStatus5a0edbce5d8e01faacfef6a1ba4f62b3Form.head = (args: { project: string | number } | [project: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getContainerStatus5a0edbce5d8e01faacfef6a1ba4f62b3.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

getContainerStatus5a0edbce5d8e01faacfef6a1ba4f62b3.form = getContainerStatus5a0edbce5d8e01faacfef6a1ba4f62b3Form
/**
* @see \App\Http\Controllers\DockerController::getContainerStatus
* @see app/Http/Controllers/DockerController.php:223
* @route '/api/containers/{container}/status'
*/
const getContainerStatus9fd0140cb6ce032c62a78da2e221d2a9 = (args: { container: number | { id: number } } | [container: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getContainerStatus9fd0140cb6ce032c62a78da2e221d2a9.url(args, options),
    method: 'get',
})

getContainerStatus9fd0140cb6ce032c62a78da2e221d2a9.definition = {
    methods: ["get","head"],
    url: '/api/containers/{container}/status',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DockerController::getContainerStatus
* @see app/Http/Controllers/DockerController.php:223
* @route '/api/containers/{container}/status'
*/
getContainerStatus9fd0140cb6ce032c62a78da2e221d2a9.url = (args: { container: number | { id: number } } | [container: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { container: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { container: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            container: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        container: typeof args.container === 'object'
        ? args.container.id
        : args.container,
    }

    return getContainerStatus9fd0140cb6ce032c62a78da2e221d2a9.definition.url
            .replace('{container}', parsedArgs.container.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DockerController::getContainerStatus
* @see app/Http/Controllers/DockerController.php:223
* @route '/api/containers/{container}/status'
*/
getContainerStatus9fd0140cb6ce032c62a78da2e221d2a9.get = (args: { container: number | { id: number } } | [container: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getContainerStatus9fd0140cb6ce032c62a78da2e221d2a9.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DockerController::getContainerStatus
* @see app/Http/Controllers/DockerController.php:223
* @route '/api/containers/{container}/status'
*/
getContainerStatus9fd0140cb6ce032c62a78da2e221d2a9.head = (args: { container: number | { id: number } } | [container: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getContainerStatus9fd0140cb6ce032c62a78da2e221d2a9.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DockerController::getContainerStatus
* @see app/Http/Controllers/DockerController.php:223
* @route '/api/containers/{container}/status'
*/
const getContainerStatus9fd0140cb6ce032c62a78da2e221d2a9Form = (args: { container: number | { id: number } } | [container: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getContainerStatus9fd0140cb6ce032c62a78da2e221d2a9.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DockerController::getContainerStatus
* @see app/Http/Controllers/DockerController.php:223
* @route '/api/containers/{container}/status'
*/
getContainerStatus9fd0140cb6ce032c62a78da2e221d2a9Form.get = (args: { container: number | { id: number } } | [container: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getContainerStatus9fd0140cb6ce032c62a78da2e221d2a9.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DockerController::getContainerStatus
* @see app/Http/Controllers/DockerController.php:223
* @route '/api/containers/{container}/status'
*/
getContainerStatus9fd0140cb6ce032c62a78da2e221d2a9Form.head = (args: { container: number | { id: number } } | [container: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getContainerStatus9fd0140cb6ce032c62a78da2e221d2a9.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

getContainerStatus9fd0140cb6ce032c62a78da2e221d2a9.form = getContainerStatus9fd0140cb6ce032c62a78da2e221d2a9Form

export const getContainerStatus = {
    '/api/projects/{project}/docker/status': getContainerStatus5a0edbce5d8e01faacfef6a1ba4f62b3,
    '/api/containers/{container}/status': getContainerStatus9fd0140cb6ce032c62a78da2e221d2a9,
}

/**
* @see \App\Http\Controllers\DockerController::getContainerLogs
* @see app/Http/Controllers/DockerController.php:269
* @route '/api/projects/{project}/docker/logs'
*/
const getContainerLogs20045934d7e2cc4c020414006ab228e4 = (args: { project: string | number } | [project: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getContainerLogs20045934d7e2cc4c020414006ab228e4.url(args, options),
    method: 'get',
})

getContainerLogs20045934d7e2cc4c020414006ab228e4.definition = {
    methods: ["get","head"],
    url: '/api/projects/{project}/docker/logs',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DockerController::getContainerLogs
* @see app/Http/Controllers/DockerController.php:269
* @route '/api/projects/{project}/docker/logs'
*/
getContainerLogs20045934d7e2cc4c020414006ab228e4.url = (args: { project: string | number } | [project: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { project: args }
    }

    if (Array.isArray(args)) {
        args = {
            project: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        project: args.project,
    }

    return getContainerLogs20045934d7e2cc4c020414006ab228e4.definition.url
            .replace('{project}', parsedArgs.project.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DockerController::getContainerLogs
* @see app/Http/Controllers/DockerController.php:269
* @route '/api/projects/{project}/docker/logs'
*/
getContainerLogs20045934d7e2cc4c020414006ab228e4.get = (args: { project: string | number } | [project: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getContainerLogs20045934d7e2cc4c020414006ab228e4.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DockerController::getContainerLogs
* @see app/Http/Controllers/DockerController.php:269
* @route '/api/projects/{project}/docker/logs'
*/
getContainerLogs20045934d7e2cc4c020414006ab228e4.head = (args: { project: string | number } | [project: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getContainerLogs20045934d7e2cc4c020414006ab228e4.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DockerController::getContainerLogs
* @see app/Http/Controllers/DockerController.php:269
* @route '/api/projects/{project}/docker/logs'
*/
const getContainerLogs20045934d7e2cc4c020414006ab228e4Form = (args: { project: string | number } | [project: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getContainerLogs20045934d7e2cc4c020414006ab228e4.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DockerController::getContainerLogs
* @see app/Http/Controllers/DockerController.php:269
* @route '/api/projects/{project}/docker/logs'
*/
getContainerLogs20045934d7e2cc4c020414006ab228e4Form.get = (args: { project: string | number } | [project: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getContainerLogs20045934d7e2cc4c020414006ab228e4.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DockerController::getContainerLogs
* @see app/Http/Controllers/DockerController.php:269
* @route '/api/projects/{project}/docker/logs'
*/
getContainerLogs20045934d7e2cc4c020414006ab228e4Form.head = (args: { project: string | number } | [project: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getContainerLogs20045934d7e2cc4c020414006ab228e4.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

getContainerLogs20045934d7e2cc4c020414006ab228e4.form = getContainerLogs20045934d7e2cc4c020414006ab228e4Form
/**
* @see \App\Http\Controllers\DockerController::getContainerLogs
* @see app/Http/Controllers/DockerController.php:269
* @route '/api/containers/{container}/logs'
*/
const getContainerLogsbcde984199b1fb44b92ed4b6669352c0 = (args: { container: number | { id: number } } | [container: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getContainerLogsbcde984199b1fb44b92ed4b6669352c0.url(args, options),
    method: 'get',
})

getContainerLogsbcde984199b1fb44b92ed4b6669352c0.definition = {
    methods: ["get","head"],
    url: '/api/containers/{container}/logs',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DockerController::getContainerLogs
* @see app/Http/Controllers/DockerController.php:269
* @route '/api/containers/{container}/logs'
*/
getContainerLogsbcde984199b1fb44b92ed4b6669352c0.url = (args: { container: number | { id: number } } | [container: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { container: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { container: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            container: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        container: typeof args.container === 'object'
        ? args.container.id
        : args.container,
    }

    return getContainerLogsbcde984199b1fb44b92ed4b6669352c0.definition.url
            .replace('{container}', parsedArgs.container.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DockerController::getContainerLogs
* @see app/Http/Controllers/DockerController.php:269
* @route '/api/containers/{container}/logs'
*/
getContainerLogsbcde984199b1fb44b92ed4b6669352c0.get = (args: { container: number | { id: number } } | [container: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getContainerLogsbcde984199b1fb44b92ed4b6669352c0.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DockerController::getContainerLogs
* @see app/Http/Controllers/DockerController.php:269
* @route '/api/containers/{container}/logs'
*/
getContainerLogsbcde984199b1fb44b92ed4b6669352c0.head = (args: { container: number | { id: number } } | [container: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getContainerLogsbcde984199b1fb44b92ed4b6669352c0.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DockerController::getContainerLogs
* @see app/Http/Controllers/DockerController.php:269
* @route '/api/containers/{container}/logs'
*/
const getContainerLogsbcde984199b1fb44b92ed4b6669352c0Form = (args: { container: number | { id: number } } | [container: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getContainerLogsbcde984199b1fb44b92ed4b6669352c0.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DockerController::getContainerLogs
* @see app/Http/Controllers/DockerController.php:269
* @route '/api/containers/{container}/logs'
*/
getContainerLogsbcde984199b1fb44b92ed4b6669352c0Form.get = (args: { container: number | { id: number } } | [container: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getContainerLogsbcde984199b1fb44b92ed4b6669352c0.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DockerController::getContainerLogs
* @see app/Http/Controllers/DockerController.php:269
* @route '/api/containers/{container}/logs'
*/
getContainerLogsbcde984199b1fb44b92ed4b6669352c0Form.head = (args: { container: number | { id: number } } | [container: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getContainerLogsbcde984199b1fb44b92ed4b6669352c0.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

getContainerLogsbcde984199b1fb44b92ed4b6669352c0.form = getContainerLogsbcde984199b1fb44b92ed4b6669352c0Form

export const getContainerLogs = {
    '/api/projects/{project}/docker/logs': getContainerLogs20045934d7e2cc4c020414006ab228e4,
    '/api/containers/{container}/logs': getContainerLogsbcde984199b1fb44b92ed4b6669352c0,
}

/**
* @see \App\Http\Controllers\DockerController::stopContainer
* @see app/Http/Controllers/DockerController.php:125
* @route '/api/projects/{project}/docker/stop'
*/
const stopContainer89770e277fc1135c91a7460615068c4a = (args: { project: string | number } | [project: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: stopContainer89770e277fc1135c91a7460615068c4a.url(args, options),
    method: 'post',
})

stopContainer89770e277fc1135c91a7460615068c4a.definition = {
    methods: ["post"],
    url: '/api/projects/{project}/docker/stop',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\DockerController::stopContainer
* @see app/Http/Controllers/DockerController.php:125
* @route '/api/projects/{project}/docker/stop'
*/
stopContainer89770e277fc1135c91a7460615068c4a.url = (args: { project: string | number } | [project: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { project: args }
    }

    if (Array.isArray(args)) {
        args = {
            project: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        project: args.project,
    }

    return stopContainer89770e277fc1135c91a7460615068c4a.definition.url
            .replace('{project}', parsedArgs.project.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DockerController::stopContainer
* @see app/Http/Controllers/DockerController.php:125
* @route '/api/projects/{project}/docker/stop'
*/
stopContainer89770e277fc1135c91a7460615068c4a.post = (args: { project: string | number } | [project: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: stopContainer89770e277fc1135c91a7460615068c4a.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\DockerController::stopContainer
* @see app/Http/Controllers/DockerController.php:125
* @route '/api/projects/{project}/docker/stop'
*/
const stopContainer89770e277fc1135c91a7460615068c4aForm = (args: { project: string | number } | [project: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: stopContainer89770e277fc1135c91a7460615068c4a.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\DockerController::stopContainer
* @see app/Http/Controllers/DockerController.php:125
* @route '/api/projects/{project}/docker/stop'
*/
stopContainer89770e277fc1135c91a7460615068c4aForm.post = (args: { project: string | number } | [project: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: stopContainer89770e277fc1135c91a7460615068c4a.url(args, options),
    method: 'post',
})

stopContainer89770e277fc1135c91a7460615068c4a.form = stopContainer89770e277fc1135c91a7460615068c4aForm
/**
* @see \App\Http\Controllers\DockerController::stopContainer
* @see app/Http/Controllers/DockerController.php:125
* @route '/api/containers/{container}/stop'
*/
const stopContainercfc896a248508a34480d3e936af10576 = (args: { container: number | { id: number } } | [container: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: stopContainercfc896a248508a34480d3e936af10576.url(args, options),
    method: 'post',
})

stopContainercfc896a248508a34480d3e936af10576.definition = {
    methods: ["post"],
    url: '/api/containers/{container}/stop',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\DockerController::stopContainer
* @see app/Http/Controllers/DockerController.php:125
* @route '/api/containers/{container}/stop'
*/
stopContainercfc896a248508a34480d3e936af10576.url = (args: { container: number | { id: number } } | [container: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { container: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { container: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            container: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        container: typeof args.container === 'object'
        ? args.container.id
        : args.container,
    }

    return stopContainercfc896a248508a34480d3e936af10576.definition.url
            .replace('{container}', parsedArgs.container.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DockerController::stopContainer
* @see app/Http/Controllers/DockerController.php:125
* @route '/api/containers/{container}/stop'
*/
stopContainercfc896a248508a34480d3e936af10576.post = (args: { container: number | { id: number } } | [container: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: stopContainercfc896a248508a34480d3e936af10576.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\DockerController::stopContainer
* @see app/Http/Controllers/DockerController.php:125
* @route '/api/containers/{container}/stop'
*/
const stopContainercfc896a248508a34480d3e936af10576Form = (args: { container: number | { id: number } } | [container: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: stopContainercfc896a248508a34480d3e936af10576.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\DockerController::stopContainer
* @see app/Http/Controllers/DockerController.php:125
* @route '/api/containers/{container}/stop'
*/
stopContainercfc896a248508a34480d3e936af10576Form.post = (args: { container: number | { id: number } } | [container: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: stopContainercfc896a248508a34480d3e936af10576.url(args, options),
    method: 'post',
})

stopContainercfc896a248508a34480d3e936af10576.form = stopContainercfc896a248508a34480d3e936af10576Form

export const stopContainer = {
    '/api/projects/{project}/docker/stop': stopContainer89770e277fc1135c91a7460615068c4a,
    '/api/containers/{container}/stop': stopContainercfc896a248508a34480d3e936af10576,
}

/**
* @see \App\Http\Controllers\DockerController::restartContainer
* @see app/Http/Controllers/DockerController.php:173
* @route '/api/projects/{project}/docker/restart'
*/
const restartContainerdde28a289b710195d96eb5b73f9eb336 = (args: { project: string | number } | [project: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: restartContainerdde28a289b710195d96eb5b73f9eb336.url(args, options),
    method: 'post',
})

restartContainerdde28a289b710195d96eb5b73f9eb336.definition = {
    methods: ["post"],
    url: '/api/projects/{project}/docker/restart',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\DockerController::restartContainer
* @see app/Http/Controllers/DockerController.php:173
* @route '/api/projects/{project}/docker/restart'
*/
restartContainerdde28a289b710195d96eb5b73f9eb336.url = (args: { project: string | number } | [project: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { project: args }
    }

    if (Array.isArray(args)) {
        args = {
            project: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        project: args.project,
    }

    return restartContainerdde28a289b710195d96eb5b73f9eb336.definition.url
            .replace('{project}', parsedArgs.project.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DockerController::restartContainer
* @see app/Http/Controllers/DockerController.php:173
* @route '/api/projects/{project}/docker/restart'
*/
restartContainerdde28a289b710195d96eb5b73f9eb336.post = (args: { project: string | number } | [project: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: restartContainerdde28a289b710195d96eb5b73f9eb336.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\DockerController::restartContainer
* @see app/Http/Controllers/DockerController.php:173
* @route '/api/projects/{project}/docker/restart'
*/
const restartContainerdde28a289b710195d96eb5b73f9eb336Form = (args: { project: string | number } | [project: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: restartContainerdde28a289b710195d96eb5b73f9eb336.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\DockerController::restartContainer
* @see app/Http/Controllers/DockerController.php:173
* @route '/api/projects/{project}/docker/restart'
*/
restartContainerdde28a289b710195d96eb5b73f9eb336Form.post = (args: { project: string | number } | [project: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: restartContainerdde28a289b710195d96eb5b73f9eb336.url(args, options),
    method: 'post',
})

restartContainerdde28a289b710195d96eb5b73f9eb336.form = restartContainerdde28a289b710195d96eb5b73f9eb336Form
/**
* @see \App\Http\Controllers\DockerController::restartContainer
* @see app/Http/Controllers/DockerController.php:173
* @route '/api/containers/{container}/restart'
*/
const restartContainer068872b2578f5da61be8f342e37a1450 = (args: { container: number | { id: number } } | [container: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: restartContainer068872b2578f5da61be8f342e37a1450.url(args, options),
    method: 'post',
})

restartContainer068872b2578f5da61be8f342e37a1450.definition = {
    methods: ["post"],
    url: '/api/containers/{container}/restart',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\DockerController::restartContainer
* @see app/Http/Controllers/DockerController.php:173
* @route '/api/containers/{container}/restart'
*/
restartContainer068872b2578f5da61be8f342e37a1450.url = (args: { container: number | { id: number } } | [container: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { container: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { container: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            container: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        container: typeof args.container === 'object'
        ? args.container.id
        : args.container,
    }

    return restartContainer068872b2578f5da61be8f342e37a1450.definition.url
            .replace('{container}', parsedArgs.container.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DockerController::restartContainer
* @see app/Http/Controllers/DockerController.php:173
* @route '/api/containers/{container}/restart'
*/
restartContainer068872b2578f5da61be8f342e37a1450.post = (args: { container: number | { id: number } } | [container: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: restartContainer068872b2578f5da61be8f342e37a1450.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\DockerController::restartContainer
* @see app/Http/Controllers/DockerController.php:173
* @route '/api/containers/{container}/restart'
*/
const restartContainer068872b2578f5da61be8f342e37a1450Form = (args: { container: number | { id: number } } | [container: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: restartContainer068872b2578f5da61be8f342e37a1450.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\DockerController::restartContainer
* @see app/Http/Controllers/DockerController.php:173
* @route '/api/containers/{container}/restart'
*/
restartContainer068872b2578f5da61be8f342e37a1450Form.post = (args: { container: number | { id: number } } | [container: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: restartContainer068872b2578f5da61be8f342e37a1450.url(args, options),
    method: 'post',
})

restartContainer068872b2578f5da61be8f342e37a1450.form = restartContainer068872b2578f5da61be8f342e37a1450Form

export const restartContainer = {
    '/api/projects/{project}/docker/restart': restartContainerdde28a289b710195d96eb5b73f9eb336,
    '/api/containers/{container}/restart': restartContainer068872b2578f5da61be8f342e37a1450,
}

const DockerController = { info, getRunningContainers, cleanup, deploy, startContainer, getPreviewUrl, getContainerStatus, getContainerLogs, stopContainer, restartContainer }

export default DockerController