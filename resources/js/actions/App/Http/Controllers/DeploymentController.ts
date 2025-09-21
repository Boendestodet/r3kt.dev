import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\DeploymentController::deploy
* @see app/Http/Controllers/DeploymentController.php:25
* @route '/projects/{project}/deploy'
*/
export const deploy = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: deploy.url(args, options),
    method: 'post',
})

deploy.definition = {
    methods: ["post"],
    url: '/projects/{project}/deploy',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\DeploymentController::deploy
* @see app/Http/Controllers/DeploymentController.php:25
* @route '/projects/{project}/deploy'
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
* @see \App\Http\Controllers\DeploymentController::deploy
* @see app/Http/Controllers/DeploymentController.php:25
* @route '/projects/{project}/deploy'
*/
deploy.post = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: deploy.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\DeploymentController::deploy
* @see app/Http/Controllers/DeploymentController.php:25
* @route '/projects/{project}/deploy'
*/
const deployForm = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: deploy.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\DeploymentController::deploy
* @see app/Http/Controllers/DeploymentController.php:25
* @route '/projects/{project}/deploy'
*/
deployForm.post = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: deploy.url(args, options),
    method: 'post',
})

deploy.form = deployForm

/**
* @see \App\Http\Controllers\DeploymentController::status
* @see app/Http/Controllers/DeploymentController.php:64
* @route '/projects/{project}/deployment/status'
*/
export const status = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: status.url(args, options),
    method: 'get',
})

status.definition = {
    methods: ["get","head"],
    url: '/projects/{project}/deployment/status',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DeploymentController::status
* @see app/Http/Controllers/DeploymentController.php:64
* @route '/projects/{project}/deployment/status'
*/
status.url = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return status.definition.url
            .replace('{project}', parsedArgs.project.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DeploymentController::status
* @see app/Http/Controllers/DeploymentController.php:64
* @route '/projects/{project}/deployment/status'
*/
status.get = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: status.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DeploymentController::status
* @see app/Http/Controllers/DeploymentController.php:64
* @route '/projects/{project}/deployment/status'
*/
status.head = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: status.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DeploymentController::status
* @see app/Http/Controllers/DeploymentController.php:64
* @route '/projects/{project}/deployment/status'
*/
const statusForm = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: status.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DeploymentController::status
* @see app/Http/Controllers/DeploymentController.php:64
* @route '/projects/{project}/deployment/status'
*/
statusForm.get = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: status.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DeploymentController::status
* @see app/Http/Controllers/DeploymentController.php:64
* @route '/projects/{project}/deployment/status'
*/
statusForm.head = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
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
* @see \App\Http\Controllers\DeploymentController::logs
* @see app/Http/Controllers/DeploymentController.php:194
* @route '/projects/{project}/deployment/logs'
*/
export const logs = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: logs.url(args, options),
    method: 'get',
})

logs.definition = {
    methods: ["get","head"],
    url: '/projects/{project}/deployment/logs',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DeploymentController::logs
* @see app/Http/Controllers/DeploymentController.php:194
* @route '/projects/{project}/deployment/logs'
*/
logs.url = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return logs.definition.url
            .replace('{project}', parsedArgs.project.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DeploymentController::logs
* @see app/Http/Controllers/DeploymentController.php:194
* @route '/projects/{project}/deployment/logs'
*/
logs.get = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: logs.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DeploymentController::logs
* @see app/Http/Controllers/DeploymentController.php:194
* @route '/projects/{project}/deployment/logs'
*/
logs.head = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: logs.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DeploymentController::logs
* @see app/Http/Controllers/DeploymentController.php:194
* @route '/projects/{project}/deployment/logs'
*/
const logsForm = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: logs.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DeploymentController::logs
* @see app/Http/Controllers/DeploymentController.php:194
* @route '/projects/{project}/deployment/logs'
*/
logsForm.get = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: logs.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DeploymentController::logs
* @see app/Http/Controllers/DeploymentController.php:194
* @route '/projects/{project}/deployment/logs'
*/
logsForm.head = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
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
* @see \App\Http\Controllers\DeploymentController::stop
* @see app/Http/Controllers/DeploymentController.php:143
* @route '/projects/{project}/deployment/stop'
*/
export const stop = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: stop.url(args, options),
    method: 'post',
})

stop.definition = {
    methods: ["post"],
    url: '/projects/{project}/deployment/stop',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\DeploymentController::stop
* @see app/Http/Controllers/DeploymentController.php:143
* @route '/projects/{project}/deployment/stop'
*/
stop.url = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return stop.definition.url
            .replace('{project}', parsedArgs.project.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DeploymentController::stop
* @see app/Http/Controllers/DeploymentController.php:143
* @route '/projects/{project}/deployment/stop'
*/
stop.post = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: stop.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\DeploymentController::stop
* @see app/Http/Controllers/DeploymentController.php:143
* @route '/projects/{project}/deployment/stop'
*/
const stopForm = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: stop.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\DeploymentController::stop
* @see app/Http/Controllers/DeploymentController.php:143
* @route '/projects/{project}/deployment/stop'
*/
stopForm.post = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: stop.url(args, options),
    method: 'post',
})

stop.form = stopForm

/**
* @see \App\Http\Controllers\DeploymentController::restart
* @see app/Http/Controllers/DeploymentController.php:92
* @route '/projects/{project}/deployment/restart'
*/
export const restart = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: restart.url(args, options),
    method: 'post',
})

restart.definition = {
    methods: ["post"],
    url: '/projects/{project}/deployment/restart',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\DeploymentController::restart
* @see app/Http/Controllers/DeploymentController.php:92
* @route '/projects/{project}/deployment/restart'
*/
restart.url = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return restart.definition.url
            .replace('{project}', parsedArgs.project.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DeploymentController::restart
* @see app/Http/Controllers/DeploymentController.php:92
* @route '/projects/{project}/deployment/restart'
*/
restart.post = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: restart.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\DeploymentController::restart
* @see app/Http/Controllers/DeploymentController.php:92
* @route '/projects/{project}/deployment/restart'
*/
const restartForm = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: restart.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\DeploymentController::restart
* @see app/Http/Controllers/DeploymentController.php:92
* @route '/projects/{project}/deployment/restart'
*/
restartForm.post = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: restart.url(args, options),
    method: 'post',
})

restart.form = restartForm

/**
* @see \App\Http\Controllers\DeploymentController::containers
* @see app/Http/Controllers/DeploymentController.php:226
* @route '/api/containers'
*/
export const containers = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: containers.url(options),
    method: 'get',
})

containers.definition = {
    methods: ["get","head"],
    url: '/api/containers',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DeploymentController::containers
* @see app/Http/Controllers/DeploymentController.php:226
* @route '/api/containers'
*/
containers.url = (options?: RouteQueryOptions) => {
    return containers.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DeploymentController::containers
* @see app/Http/Controllers/DeploymentController.php:226
* @route '/api/containers'
*/
containers.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: containers.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DeploymentController::containers
* @see app/Http/Controllers/DeploymentController.php:226
* @route '/api/containers'
*/
containers.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: containers.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DeploymentController::containers
* @see app/Http/Controllers/DeploymentController.php:226
* @route '/api/containers'
*/
const containersForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: containers.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DeploymentController::containers
* @see app/Http/Controllers/DeploymentController.php:226
* @route '/api/containers'
*/
containersForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: containers.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DeploymentController::containers
* @see app/Http/Controllers/DeploymentController.php:226
* @route '/api/containers'
*/
containersForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: containers.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

containers.form = containersForm

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

const DeploymentController = { deploy, status, logs, stop, restart, containers, cleanup }

export default DeploymentController