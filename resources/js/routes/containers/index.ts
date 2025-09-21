import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../wayfinder'
/**
* @see \App\Http\Controllers\ContainerController::store
* @see app/Http/Controllers/ContainerController.php:18
* @route '/projects/{project}/containers'
*/
export const store = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/projects/{project}/containers',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\ContainerController::store
* @see app/Http/Controllers/ContainerController.php:18
* @route '/projects/{project}/containers'
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
* @see \App\Http\Controllers\ContainerController::store
* @see app/Http/Controllers/ContainerController.php:18
* @route '/projects/{project}/containers'
*/
store.post = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\ContainerController::store
* @see app/Http/Controllers/ContainerController.php:18
* @route '/projects/{project}/containers'
*/
const storeForm = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\ContainerController::store
* @see app/Http/Controllers/ContainerController.php:18
* @route '/projects/{project}/containers'
*/
storeForm.post = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(args, options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\ContainerController::show
* @see app/Http/Controllers/ContainerController.php:41
* @route '/containers/{container}'
*/
export const show = (args: { container: number | { id: number } } | [container: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/containers/{container}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\ContainerController::show
* @see app/Http/Controllers/ContainerController.php:41
* @route '/containers/{container}'
*/
show.url = (args: { container: number | { id: number } } | [container: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return show.definition.url
            .replace('{container}', parsedArgs.container.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ContainerController::show
* @see app/Http/Controllers/ContainerController.php:41
* @route '/containers/{container}'
*/
show.get = (args: { container: number | { id: number } } | [container: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ContainerController::show
* @see app/Http/Controllers/ContainerController.php:41
* @route '/containers/{container}'
*/
show.head = (args: { container: number | { id: number } } | [container: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\ContainerController::show
* @see app/Http/Controllers/ContainerController.php:41
* @route '/containers/{container}'
*/
const showForm = (args: { container: number | { id: number } } | [container: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ContainerController::show
* @see app/Http/Controllers/ContainerController.php:41
* @route '/containers/{container}'
*/
showForm.get = (args: { container: number | { id: number } } | [container: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ContainerController::show
* @see app/Http/Controllers/ContainerController.php:41
* @route '/containers/{container}'
*/
showForm.head = (args: { container: number | { id: number } } | [container: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

show.form = showForm

/**
* @see \App\Http\Controllers\ContainerController::destroy
* @see app/Http/Controllers/ContainerController.php:84
* @route '/containers/{container}'
*/
export const destroy = (args: { container: number | { id: number } } | [container: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/containers/{container}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\ContainerController::destroy
* @see app/Http/Controllers/ContainerController.php:84
* @route '/containers/{container}'
*/
destroy.url = (args: { container: number | { id: number } } | [container: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return destroy.definition.url
            .replace('{container}', parsedArgs.container.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ContainerController::destroy
* @see app/Http/Controllers/ContainerController.php:84
* @route '/containers/{container}'
*/
destroy.delete = (args: { container: number | { id: number } } | [container: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\ContainerController::destroy
* @see app/Http/Controllers/ContainerController.php:84
* @route '/containers/{container}'
*/
const destroyForm = (args: { container: number | { id: number } } | [container: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\ContainerController::destroy
* @see app/Http/Controllers/ContainerController.php:84
* @route '/containers/{container}'
*/
destroyForm.delete = (args: { container: number | { id: number } } | [container: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroy.form = destroyForm

/**
* @see \App\Http\Controllers\DockerController::stop
* @see app/Http/Controllers/DockerController.php:125
* @route '/api/containers/{container}/stop'
*/
export const stop = (args: { container: number | { id: number } } | [container: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: stop.url(args, options),
    method: 'post',
})

stop.definition = {
    methods: ["post"],
    url: '/api/containers/{container}/stop',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\DockerController::stop
* @see app/Http/Controllers/DockerController.php:125
* @route '/api/containers/{container}/stop'
*/
stop.url = (args: { container: number | { id: number } } | [container: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return stop.definition.url
            .replace('{container}', parsedArgs.container.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DockerController::stop
* @see app/Http/Controllers/DockerController.php:125
* @route '/api/containers/{container}/stop'
*/
stop.post = (args: { container: number | { id: number } } | [container: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: stop.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\DockerController::stop
* @see app/Http/Controllers/DockerController.php:125
* @route '/api/containers/{container}/stop'
*/
const stopForm = (args: { container: number | { id: number } } | [container: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: stop.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\DockerController::stop
* @see app/Http/Controllers/DockerController.php:125
* @route '/api/containers/{container}/stop'
*/
stopForm.post = (args: { container: number | { id: number } } | [container: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: stop.url(args, options),
    method: 'post',
})

stop.form = stopForm

/**
* @see \App\Http\Controllers\DockerController::restart
* @see app/Http/Controllers/DockerController.php:173
* @route '/api/containers/{container}/restart'
*/
export const restart = (args: { container: number | { id: number } } | [container: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: restart.url(args, options),
    method: 'post',
})

restart.definition = {
    methods: ["post"],
    url: '/api/containers/{container}/restart',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\DockerController::restart
* @see app/Http/Controllers/DockerController.php:173
* @route '/api/containers/{container}/restart'
*/
restart.url = (args: { container: number | { id: number } } | [container: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return restart.definition.url
            .replace('{container}', parsedArgs.container.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DockerController::restart
* @see app/Http/Controllers/DockerController.php:173
* @route '/api/containers/{container}/restart'
*/
restart.post = (args: { container: number | { id: number } } | [container: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: restart.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\DockerController::restart
* @see app/Http/Controllers/DockerController.php:173
* @route '/api/containers/{container}/restart'
*/
const restartForm = (args: { container: number | { id: number } } | [container: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: restart.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\DockerController::restart
* @see app/Http/Controllers/DockerController.php:173
* @route '/api/containers/{container}/restart'
*/
restartForm.post = (args: { container: number | { id: number } } | [container: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: restart.url(args, options),
    method: 'post',
})

restart.form = restartForm

/**
* @see \App\Http\Controllers\DockerController::status
* @see app/Http/Controllers/DockerController.php:223
* @route '/api/containers/{container}/status'
*/
export const status = (args: { container: number | { id: number } } | [container: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: status.url(args, options),
    method: 'get',
})

status.definition = {
    methods: ["get","head"],
    url: '/api/containers/{container}/status',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DockerController::status
* @see app/Http/Controllers/DockerController.php:223
* @route '/api/containers/{container}/status'
*/
status.url = (args: { container: number | { id: number } } | [container: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return status.definition.url
            .replace('{container}', parsedArgs.container.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DockerController::status
* @see app/Http/Controllers/DockerController.php:223
* @route '/api/containers/{container}/status'
*/
status.get = (args: { container: number | { id: number } } | [container: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: status.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DockerController::status
* @see app/Http/Controllers/DockerController.php:223
* @route '/api/containers/{container}/status'
*/
status.head = (args: { container: number | { id: number } } | [container: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: status.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DockerController::status
* @see app/Http/Controllers/DockerController.php:223
* @route '/api/containers/{container}/status'
*/
const statusForm = (args: { container: number | { id: number } } | [container: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: status.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DockerController::status
* @see app/Http/Controllers/DockerController.php:223
* @route '/api/containers/{container}/status'
*/
statusForm.get = (args: { container: number | { id: number } } | [container: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: status.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DockerController::status
* @see app/Http/Controllers/DockerController.php:223
* @route '/api/containers/{container}/status'
*/
statusForm.head = (args: { container: number | { id: number } } | [container: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
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
* @see \App\Http\Controllers\DockerController::logs
* @see app/Http/Controllers/DockerController.php:269
* @route '/api/containers/{container}/logs'
*/
export const logs = (args: { container: number | { id: number } } | [container: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: logs.url(args, options),
    method: 'get',
})

logs.definition = {
    methods: ["get","head"],
    url: '/api/containers/{container}/logs',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DockerController::logs
* @see app/Http/Controllers/DockerController.php:269
* @route '/api/containers/{container}/logs'
*/
logs.url = (args: { container: number | { id: number } } | [container: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return logs.definition.url
            .replace('{container}', parsedArgs.container.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DockerController::logs
* @see app/Http/Controllers/DockerController.php:269
* @route '/api/containers/{container}/logs'
*/
logs.get = (args: { container: number | { id: number } } | [container: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: logs.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DockerController::logs
* @see app/Http/Controllers/DockerController.php:269
* @route '/api/containers/{container}/logs'
*/
logs.head = (args: { container: number | { id: number } } | [container: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: logs.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DockerController::logs
* @see app/Http/Controllers/DockerController.php:269
* @route '/api/containers/{container}/logs'
*/
const logsForm = (args: { container: number | { id: number } } | [container: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: logs.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DockerController::logs
* @see app/Http/Controllers/DockerController.php:269
* @route '/api/containers/{container}/logs'
*/
logsForm.get = (args: { container: number | { id: number } } | [container: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: logs.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DockerController::logs
* @see app/Http/Controllers/DockerController.php:269
* @route '/api/containers/{container}/logs'
*/
logsForm.head = (args: { container: number | { id: number } } | [container: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: logs.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

logs.form = logsForm

const containers = {
    store,
    show,
    destroy,
    stop,
    restart,
    status,
    logs,
}

export default containers