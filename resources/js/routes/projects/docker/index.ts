import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\ProjectDeploymentController::preview
* @see app/Http/Controllers/ProjectDeploymentController.php:93
* @route '/api/projects/{project}/docker/preview'
*/
export const preview = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: preview.url(args, options),
    method: 'get',
})

preview.definition = {
    methods: ["get","head"],
    url: '/api/projects/{project}/docker/preview',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\ProjectDeploymentController::preview
* @see app/Http/Controllers/ProjectDeploymentController.php:93
* @route '/api/projects/{project}/docker/preview'
*/
preview.url = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return preview.definition.url
            .replace('{project}', parsedArgs.project.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ProjectDeploymentController::preview
* @see app/Http/Controllers/ProjectDeploymentController.php:93
* @route '/api/projects/{project}/docker/preview'
*/
preview.get = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: preview.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ProjectDeploymentController::preview
* @see app/Http/Controllers/ProjectDeploymentController.php:93
* @route '/api/projects/{project}/docker/preview'
*/
preview.head = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: preview.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\ProjectDeploymentController::preview
* @see app/Http/Controllers/ProjectDeploymentController.php:93
* @route '/api/projects/{project}/docker/preview'
*/
const previewForm = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: preview.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ProjectDeploymentController::preview
* @see app/Http/Controllers/ProjectDeploymentController.php:93
* @route '/api/projects/{project}/docker/preview'
*/
previewForm.get = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: preview.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ProjectDeploymentController::preview
* @see app/Http/Controllers/ProjectDeploymentController.php:93
* @route '/api/projects/{project}/docker/preview'
*/
previewForm.head = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: preview.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

preview.form = previewForm

/**
* @see \App\Http\Controllers\ContainerController::start
* @see app/Http/Controllers/ContainerController.php:23
* @route '/api/projects/{project}/docker/start'
*/
export const start = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: start.url(args, options),
    method: 'post',
})

start.definition = {
    methods: ["post"],
    url: '/api/projects/{project}/docker/start',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\ContainerController::start
* @see app/Http/Controllers/ContainerController.php:23
* @route '/api/projects/{project}/docker/start'
*/
start.url = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return start.definition.url
            .replace('{project}', parsedArgs.project.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ContainerController::start
* @see app/Http/Controllers/ContainerController.php:23
* @route '/api/projects/{project}/docker/start'
*/
start.post = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: start.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\ContainerController::start
* @see app/Http/Controllers/ContainerController.php:23
* @route '/api/projects/{project}/docker/start'
*/
const startForm = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: start.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\ContainerController::start
* @see app/Http/Controllers/ContainerController.php:23
* @route '/api/projects/{project}/docker/start'
*/
startForm.post = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: start.url(args, options),
    method: 'post',
})

start.form = startForm

/**
* @see \App\Http\Controllers\ContainerController::status
* @see app/Http/Controllers/ContainerController.php:228
* @route '/api/projects/{project}/docker/status'
*/
export const status = (args: { project: string | number } | [project: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: status.url(args, options),
    method: 'get',
})

status.definition = {
    methods: ["get","head"],
    url: '/api/projects/{project}/docker/status',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\ContainerController::status
* @see app/Http/Controllers/ContainerController.php:228
* @route '/api/projects/{project}/docker/status'
*/
status.url = (args: { project: string | number } | [project: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return status.definition.url
            .replace('{project}', parsedArgs.project.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ContainerController::status
* @see app/Http/Controllers/ContainerController.php:228
* @route '/api/projects/{project}/docker/status'
*/
status.get = (args: { project: string | number } | [project: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: status.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ContainerController::status
* @see app/Http/Controllers/ContainerController.php:228
* @route '/api/projects/{project}/docker/status'
*/
status.head = (args: { project: string | number } | [project: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: status.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\ContainerController::status
* @see app/Http/Controllers/ContainerController.php:228
* @route '/api/projects/{project}/docker/status'
*/
const statusForm = (args: { project: string | number } | [project: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: status.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ContainerController::status
* @see app/Http/Controllers/ContainerController.php:228
* @route '/api/projects/{project}/docker/status'
*/
statusForm.get = (args: { project: string | number } | [project: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: status.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ContainerController::status
* @see app/Http/Controllers/ContainerController.php:228
* @route '/api/projects/{project}/docker/status'
*/
statusForm.head = (args: { project: string | number } | [project: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: status.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

status.form = statusForm

/**
* @see \App\Http\Controllers\ContainerController::logs
* @see app/Http/Controllers/ContainerController.php:271
* @route '/api/projects/{project}/docker/logs'
*/
export const logs = (args: { project: string | number } | [project: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: logs.url(args, options),
    method: 'get',
})

logs.definition = {
    methods: ["get","head"],
    url: '/api/projects/{project}/docker/logs',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\ContainerController::logs
* @see app/Http/Controllers/ContainerController.php:271
* @route '/api/projects/{project}/docker/logs'
*/
logs.url = (args: { project: string | number } | [project: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return logs.definition.url
            .replace('{project}', parsedArgs.project.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ContainerController::logs
* @see app/Http/Controllers/ContainerController.php:271
* @route '/api/projects/{project}/docker/logs'
*/
logs.get = (args: { project: string | number } | [project: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: logs.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ContainerController::logs
* @see app/Http/Controllers/ContainerController.php:271
* @route '/api/projects/{project}/docker/logs'
*/
logs.head = (args: { project: string | number } | [project: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: logs.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\ContainerController::logs
* @see app/Http/Controllers/ContainerController.php:271
* @route '/api/projects/{project}/docker/logs'
*/
const logsForm = (args: { project: string | number } | [project: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: logs.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ContainerController::logs
* @see app/Http/Controllers/ContainerController.php:271
* @route '/api/projects/{project}/docker/logs'
*/
logsForm.get = (args: { project: string | number } | [project: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: logs.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ContainerController::logs
* @see app/Http/Controllers/ContainerController.php:271
* @route '/api/projects/{project}/docker/logs'
*/
logsForm.head = (args: { project: string | number } | [project: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: logs.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

logs.form = logsForm

/**
* @see \App\Http\Controllers\ContainerController::stop
* @see app/Http/Controllers/ContainerController.php:132
* @route '/api/projects/{project}/docker/stop'
*/
export const stop = (args: { project: string | number } | [project: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: stop.url(args, options),
    method: 'post',
})

stop.definition = {
    methods: ["post"],
    url: '/api/projects/{project}/docker/stop',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\ContainerController::stop
* @see app/Http/Controllers/ContainerController.php:132
* @route '/api/projects/{project}/docker/stop'
*/
stop.url = (args: { project: string | number } | [project: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return stop.definition.url
            .replace('{project}', parsedArgs.project.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ContainerController::stop
* @see app/Http/Controllers/ContainerController.php:132
* @route '/api/projects/{project}/docker/stop'
*/
stop.post = (args: { project: string | number } | [project: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: stop.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\ContainerController::stop
* @see app/Http/Controllers/ContainerController.php:132
* @route '/api/projects/{project}/docker/stop'
*/
const stopForm = (args: { project: string | number } | [project: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: stop.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\ContainerController::stop
* @see app/Http/Controllers/ContainerController.php:132
* @route '/api/projects/{project}/docker/stop'
*/
stopForm.post = (args: { project: string | number } | [project: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: stop.url(args, options),
    method: 'post',
})

stop.form = stopForm

/**
* @see \App\Http\Controllers\ContainerController::restart
* @see app/Http/Controllers/ContainerController.php:180
* @route '/api/projects/{project}/docker/restart'
*/
export const restart = (args: { project: string | number } | [project: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: restart.url(args, options),
    method: 'post',
})

restart.definition = {
    methods: ["post"],
    url: '/api/projects/{project}/docker/restart',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\ContainerController::restart
* @see app/Http/Controllers/ContainerController.php:180
* @route '/api/projects/{project}/docker/restart'
*/
restart.url = (args: { project: string | number } | [project: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return restart.definition.url
            .replace('{project}', parsedArgs.project.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ContainerController::restart
* @see app/Http/Controllers/ContainerController.php:180
* @route '/api/projects/{project}/docker/restart'
*/
restart.post = (args: { project: string | number } | [project: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: restart.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\ContainerController::restart
* @see app/Http/Controllers/ContainerController.php:180
* @route '/api/projects/{project}/docker/restart'
*/
const restartForm = (args: { project: string | number } | [project: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: restart.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\ContainerController::restart
* @see app/Http/Controllers/ContainerController.php:180
* @route '/api/projects/{project}/docker/restart'
*/
restartForm.post = (args: { project: string | number } | [project: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: restart.url(args, options),
    method: 'post',
})

restart.form = restartForm

const docker = {
    preview: Object.assign(preview, preview),
    start: Object.assign(start, start),
    status: Object.assign(status, status),
    logs: Object.assign(logs, logs),
    stop: Object.assign(stop, stop),
    restart: Object.assign(restart, restart),
}

export default docker