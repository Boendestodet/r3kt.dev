import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\ContainerController::store
* @see app/Http/Controllers/ContainerController.php:0
* @route '/projects/{project}/containers'
*/
export const store = (args: { project: string | number } | [project: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/projects/{project}/containers',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\ContainerController::store
* @see app/Http/Controllers/ContainerController.php:0
* @route '/projects/{project}/containers'
*/
store.url = (args: { project: string | number } | [project: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return store.definition.url
            .replace('{project}', parsedArgs.project.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ContainerController::store
* @see app/Http/Controllers/ContainerController.php:0
* @route '/projects/{project}/containers'
*/
store.post = (args: { project: string | number } | [project: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\ContainerController::store
* @see app/Http/Controllers/ContainerController.php:0
* @route '/projects/{project}/containers'
*/
const storeForm = (args: { project: string | number } | [project: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\ContainerController::store
* @see app/Http/Controllers/ContainerController.php:0
* @route '/projects/{project}/containers'
*/
storeForm.post = (args: { project: string | number } | [project: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(args, options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\ContainerController::show
* @see app/Http/Controllers/ContainerController.php:0
* @route '/containers/{container}'
*/
export const show = (args: { container: string | number } | [container: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/containers/{container}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\ContainerController::show
* @see app/Http/Controllers/ContainerController.php:0
* @route '/containers/{container}'
*/
show.url = (args: { container: string | number } | [container: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { container: args }
    }

    if (Array.isArray(args)) {
        args = {
            container: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        container: args.container,
    }

    return show.definition.url
            .replace('{container}', parsedArgs.container.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ContainerController::show
* @see app/Http/Controllers/ContainerController.php:0
* @route '/containers/{container}'
*/
show.get = (args: { container: string | number } | [container: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ContainerController::show
* @see app/Http/Controllers/ContainerController.php:0
* @route '/containers/{container}'
*/
show.head = (args: { container: string | number } | [container: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\ContainerController::show
* @see app/Http/Controllers/ContainerController.php:0
* @route '/containers/{container}'
*/
const showForm = (args: { container: string | number } | [container: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ContainerController::show
* @see app/Http/Controllers/ContainerController.php:0
* @route '/containers/{container}'
*/
showForm.get = (args: { container: string | number } | [container: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ContainerController::show
* @see app/Http/Controllers/ContainerController.php:0
* @route '/containers/{container}'
*/
showForm.head = (args: { container: string | number } | [container: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
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
* @see app/Http/Controllers/ContainerController.php:0
* @route '/containers/{container}'
*/
export const destroy = (args: { container: string | number } | [container: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/containers/{container}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\ContainerController::destroy
* @see app/Http/Controllers/ContainerController.php:0
* @route '/containers/{container}'
*/
destroy.url = (args: { container: string | number } | [container: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { container: args }
    }

    if (Array.isArray(args)) {
        args = {
            container: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        container: args.container,
    }

    return destroy.definition.url
            .replace('{container}', parsedArgs.container.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ContainerController::destroy
* @see app/Http/Controllers/ContainerController.php:0
* @route '/containers/{container}'
*/
destroy.delete = (args: { container: string | number } | [container: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\ContainerController::destroy
* @see app/Http/Controllers/ContainerController.php:0
* @route '/containers/{container}'
*/
const destroyForm = (args: { container: string | number } | [container: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
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
* @see app/Http/Controllers/ContainerController.php:0
* @route '/containers/{container}'
*/
destroyForm.delete = (args: { container: string | number } | [container: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
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
const status5a0edbce5d8e01faacfef6a1ba4f62b3 = (args: { project: string | number } | [project: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: status5a0edbce5d8e01faacfef6a1ba4f62b3.url(args, options),
    method: 'get',
})

status5a0edbce5d8e01faacfef6a1ba4f62b3.definition = {
    methods: ["get","head"],
    url: '/api/projects/{project}/docker/status',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\ContainerController::status
* @see app/Http/Controllers/ContainerController.php:228
* @route '/api/projects/{project}/docker/status'
*/
status5a0edbce5d8e01faacfef6a1ba4f62b3.url = (args: { project: string | number } | [project: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return status5a0edbce5d8e01faacfef6a1ba4f62b3.definition.url
            .replace('{project}', parsedArgs.project.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ContainerController::status
* @see app/Http/Controllers/ContainerController.php:228
* @route '/api/projects/{project}/docker/status'
*/
status5a0edbce5d8e01faacfef6a1ba4f62b3.get = (args: { project: string | number } | [project: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: status5a0edbce5d8e01faacfef6a1ba4f62b3.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ContainerController::status
* @see app/Http/Controllers/ContainerController.php:228
* @route '/api/projects/{project}/docker/status'
*/
status5a0edbce5d8e01faacfef6a1ba4f62b3.head = (args: { project: string | number } | [project: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: status5a0edbce5d8e01faacfef6a1ba4f62b3.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\ContainerController::status
* @see app/Http/Controllers/ContainerController.php:228
* @route '/api/projects/{project}/docker/status'
*/
const status5a0edbce5d8e01faacfef6a1ba4f62b3Form = (args: { project: string | number } | [project: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: status5a0edbce5d8e01faacfef6a1ba4f62b3.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ContainerController::status
* @see app/Http/Controllers/ContainerController.php:228
* @route '/api/projects/{project}/docker/status'
*/
status5a0edbce5d8e01faacfef6a1ba4f62b3Form.get = (args: { project: string | number } | [project: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: status5a0edbce5d8e01faacfef6a1ba4f62b3.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ContainerController::status
* @see app/Http/Controllers/ContainerController.php:228
* @route '/api/projects/{project}/docker/status'
*/
status5a0edbce5d8e01faacfef6a1ba4f62b3Form.head = (args: { project: string | number } | [project: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: status5a0edbce5d8e01faacfef6a1ba4f62b3.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

status5a0edbce5d8e01faacfef6a1ba4f62b3.form = status5a0edbce5d8e01faacfef6a1ba4f62b3Form
/**
* @see \App\Http\Controllers\ContainerController::status
* @see app/Http/Controllers/ContainerController.php:228
* @route '/api/containers/{container}/status'
*/
const status9fd0140cb6ce032c62a78da2e221d2a9 = (args: { container: number | { id: number } } | [container: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: status9fd0140cb6ce032c62a78da2e221d2a9.url(args, options),
    method: 'get',
})

status9fd0140cb6ce032c62a78da2e221d2a9.definition = {
    methods: ["get","head"],
    url: '/api/containers/{container}/status',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\ContainerController::status
* @see app/Http/Controllers/ContainerController.php:228
* @route '/api/containers/{container}/status'
*/
status9fd0140cb6ce032c62a78da2e221d2a9.url = (args: { container: number | { id: number } } | [container: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return status9fd0140cb6ce032c62a78da2e221d2a9.definition.url
            .replace('{container}', parsedArgs.container.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ContainerController::status
* @see app/Http/Controllers/ContainerController.php:228
* @route '/api/containers/{container}/status'
*/
status9fd0140cb6ce032c62a78da2e221d2a9.get = (args: { container: number | { id: number } } | [container: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: status9fd0140cb6ce032c62a78da2e221d2a9.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ContainerController::status
* @see app/Http/Controllers/ContainerController.php:228
* @route '/api/containers/{container}/status'
*/
status9fd0140cb6ce032c62a78da2e221d2a9.head = (args: { container: number | { id: number } } | [container: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: status9fd0140cb6ce032c62a78da2e221d2a9.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\ContainerController::status
* @see app/Http/Controllers/ContainerController.php:228
* @route '/api/containers/{container}/status'
*/
const status9fd0140cb6ce032c62a78da2e221d2a9Form = (args: { container: number | { id: number } } | [container: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: status9fd0140cb6ce032c62a78da2e221d2a9.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ContainerController::status
* @see app/Http/Controllers/ContainerController.php:228
* @route '/api/containers/{container}/status'
*/
status9fd0140cb6ce032c62a78da2e221d2a9Form.get = (args: { container: number | { id: number } } | [container: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: status9fd0140cb6ce032c62a78da2e221d2a9.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ContainerController::status
* @see app/Http/Controllers/ContainerController.php:228
* @route '/api/containers/{container}/status'
*/
status9fd0140cb6ce032c62a78da2e221d2a9Form.head = (args: { container: number | { id: number } } | [container: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: status9fd0140cb6ce032c62a78da2e221d2a9.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

status9fd0140cb6ce032c62a78da2e221d2a9.form = status9fd0140cb6ce032c62a78da2e221d2a9Form

export const status = {
    '/api/projects/{project}/docker/status': status5a0edbce5d8e01faacfef6a1ba4f62b3,
    '/api/containers/{container}/status': status9fd0140cb6ce032c62a78da2e221d2a9,
}

/**
* @see \App\Http\Controllers\ContainerController::logs
* @see app/Http/Controllers/ContainerController.php:271
* @route '/api/projects/{project}/docker/logs'
*/
const logs20045934d7e2cc4c020414006ab228e4 = (args: { project: string | number } | [project: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: logs20045934d7e2cc4c020414006ab228e4.url(args, options),
    method: 'get',
})

logs20045934d7e2cc4c020414006ab228e4.definition = {
    methods: ["get","head"],
    url: '/api/projects/{project}/docker/logs',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\ContainerController::logs
* @see app/Http/Controllers/ContainerController.php:271
* @route '/api/projects/{project}/docker/logs'
*/
logs20045934d7e2cc4c020414006ab228e4.url = (args: { project: string | number } | [project: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return logs20045934d7e2cc4c020414006ab228e4.definition.url
            .replace('{project}', parsedArgs.project.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ContainerController::logs
* @see app/Http/Controllers/ContainerController.php:271
* @route '/api/projects/{project}/docker/logs'
*/
logs20045934d7e2cc4c020414006ab228e4.get = (args: { project: string | number } | [project: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: logs20045934d7e2cc4c020414006ab228e4.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ContainerController::logs
* @see app/Http/Controllers/ContainerController.php:271
* @route '/api/projects/{project}/docker/logs'
*/
logs20045934d7e2cc4c020414006ab228e4.head = (args: { project: string | number } | [project: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: logs20045934d7e2cc4c020414006ab228e4.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\ContainerController::logs
* @see app/Http/Controllers/ContainerController.php:271
* @route '/api/projects/{project}/docker/logs'
*/
const logs20045934d7e2cc4c020414006ab228e4Form = (args: { project: string | number } | [project: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: logs20045934d7e2cc4c020414006ab228e4.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ContainerController::logs
* @see app/Http/Controllers/ContainerController.php:271
* @route '/api/projects/{project}/docker/logs'
*/
logs20045934d7e2cc4c020414006ab228e4Form.get = (args: { project: string | number } | [project: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: logs20045934d7e2cc4c020414006ab228e4.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ContainerController::logs
* @see app/Http/Controllers/ContainerController.php:271
* @route '/api/projects/{project}/docker/logs'
*/
logs20045934d7e2cc4c020414006ab228e4Form.head = (args: { project: string | number } | [project: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: logs20045934d7e2cc4c020414006ab228e4.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

logs20045934d7e2cc4c020414006ab228e4.form = logs20045934d7e2cc4c020414006ab228e4Form
/**
* @see \App\Http\Controllers\ContainerController::logs
* @see app/Http/Controllers/ContainerController.php:271
* @route '/api/containers/{container}/logs'
*/
const logsbcde984199b1fb44b92ed4b6669352c0 = (args: { container: number | { id: number } } | [container: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: logsbcde984199b1fb44b92ed4b6669352c0.url(args, options),
    method: 'get',
})

logsbcde984199b1fb44b92ed4b6669352c0.definition = {
    methods: ["get","head"],
    url: '/api/containers/{container}/logs',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\ContainerController::logs
* @see app/Http/Controllers/ContainerController.php:271
* @route '/api/containers/{container}/logs'
*/
logsbcde984199b1fb44b92ed4b6669352c0.url = (args: { container: number | { id: number } } | [container: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return logsbcde984199b1fb44b92ed4b6669352c0.definition.url
            .replace('{container}', parsedArgs.container.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ContainerController::logs
* @see app/Http/Controllers/ContainerController.php:271
* @route '/api/containers/{container}/logs'
*/
logsbcde984199b1fb44b92ed4b6669352c0.get = (args: { container: number | { id: number } } | [container: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: logsbcde984199b1fb44b92ed4b6669352c0.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ContainerController::logs
* @see app/Http/Controllers/ContainerController.php:271
* @route '/api/containers/{container}/logs'
*/
logsbcde984199b1fb44b92ed4b6669352c0.head = (args: { container: number | { id: number } } | [container: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: logsbcde984199b1fb44b92ed4b6669352c0.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\ContainerController::logs
* @see app/Http/Controllers/ContainerController.php:271
* @route '/api/containers/{container}/logs'
*/
const logsbcde984199b1fb44b92ed4b6669352c0Form = (args: { container: number | { id: number } } | [container: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: logsbcde984199b1fb44b92ed4b6669352c0.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ContainerController::logs
* @see app/Http/Controllers/ContainerController.php:271
* @route '/api/containers/{container}/logs'
*/
logsbcde984199b1fb44b92ed4b6669352c0Form.get = (args: { container: number | { id: number } } | [container: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: logsbcde984199b1fb44b92ed4b6669352c0.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ContainerController::logs
* @see app/Http/Controllers/ContainerController.php:271
* @route '/api/containers/{container}/logs'
*/
logsbcde984199b1fb44b92ed4b6669352c0Form.head = (args: { container: number | { id: number } } | [container: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: logsbcde984199b1fb44b92ed4b6669352c0.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

logsbcde984199b1fb44b92ed4b6669352c0.form = logsbcde984199b1fb44b92ed4b6669352c0Form

export const logs = {
    '/api/projects/{project}/docker/logs': logs20045934d7e2cc4c020414006ab228e4,
    '/api/containers/{container}/logs': logsbcde984199b1fb44b92ed4b6669352c0,
}

/**
* @see \App\Http\Controllers\ContainerController::stop
* @see app/Http/Controllers/ContainerController.php:132
* @route '/api/projects/{project}/docker/stop'
*/
const stop89770e277fc1135c91a7460615068c4a = (args: { project: string | number } | [project: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: stop89770e277fc1135c91a7460615068c4a.url(args, options),
    method: 'post',
})

stop89770e277fc1135c91a7460615068c4a.definition = {
    methods: ["post"],
    url: '/api/projects/{project}/docker/stop',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\ContainerController::stop
* @see app/Http/Controllers/ContainerController.php:132
* @route '/api/projects/{project}/docker/stop'
*/
stop89770e277fc1135c91a7460615068c4a.url = (args: { project: string | number } | [project: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return stop89770e277fc1135c91a7460615068c4a.definition.url
            .replace('{project}', parsedArgs.project.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ContainerController::stop
* @see app/Http/Controllers/ContainerController.php:132
* @route '/api/projects/{project}/docker/stop'
*/
stop89770e277fc1135c91a7460615068c4a.post = (args: { project: string | number } | [project: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: stop89770e277fc1135c91a7460615068c4a.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\ContainerController::stop
* @see app/Http/Controllers/ContainerController.php:132
* @route '/api/projects/{project}/docker/stop'
*/
const stop89770e277fc1135c91a7460615068c4aForm = (args: { project: string | number } | [project: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: stop89770e277fc1135c91a7460615068c4a.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\ContainerController::stop
* @see app/Http/Controllers/ContainerController.php:132
* @route '/api/projects/{project}/docker/stop'
*/
stop89770e277fc1135c91a7460615068c4aForm.post = (args: { project: string | number } | [project: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: stop89770e277fc1135c91a7460615068c4a.url(args, options),
    method: 'post',
})

stop89770e277fc1135c91a7460615068c4a.form = stop89770e277fc1135c91a7460615068c4aForm
/**
* @see \App\Http\Controllers\ContainerController::stop
* @see app/Http/Controllers/ContainerController.php:132
* @route '/api/containers/{container}/stop'
*/
const stopcfc896a248508a34480d3e936af10576 = (args: { container: number | { id: number } } | [container: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: stopcfc896a248508a34480d3e936af10576.url(args, options),
    method: 'post',
})

stopcfc896a248508a34480d3e936af10576.definition = {
    methods: ["post"],
    url: '/api/containers/{container}/stop',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\ContainerController::stop
* @see app/Http/Controllers/ContainerController.php:132
* @route '/api/containers/{container}/stop'
*/
stopcfc896a248508a34480d3e936af10576.url = (args: { container: number | { id: number } } | [container: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return stopcfc896a248508a34480d3e936af10576.definition.url
            .replace('{container}', parsedArgs.container.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ContainerController::stop
* @see app/Http/Controllers/ContainerController.php:132
* @route '/api/containers/{container}/stop'
*/
stopcfc896a248508a34480d3e936af10576.post = (args: { container: number | { id: number } } | [container: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: stopcfc896a248508a34480d3e936af10576.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\ContainerController::stop
* @see app/Http/Controllers/ContainerController.php:132
* @route '/api/containers/{container}/stop'
*/
const stopcfc896a248508a34480d3e936af10576Form = (args: { container: number | { id: number } } | [container: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: stopcfc896a248508a34480d3e936af10576.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\ContainerController::stop
* @see app/Http/Controllers/ContainerController.php:132
* @route '/api/containers/{container}/stop'
*/
stopcfc896a248508a34480d3e936af10576Form.post = (args: { container: number | { id: number } } | [container: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: stopcfc896a248508a34480d3e936af10576.url(args, options),
    method: 'post',
})

stopcfc896a248508a34480d3e936af10576.form = stopcfc896a248508a34480d3e936af10576Form

export const stop = {
    '/api/projects/{project}/docker/stop': stop89770e277fc1135c91a7460615068c4a,
    '/api/containers/{container}/stop': stopcfc896a248508a34480d3e936af10576,
}

/**
* @see \App\Http\Controllers\ContainerController::restart
* @see app/Http/Controllers/ContainerController.php:180
* @route '/api/projects/{project}/docker/restart'
*/
const restartdde28a289b710195d96eb5b73f9eb336 = (args: { project: string | number } | [project: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: restartdde28a289b710195d96eb5b73f9eb336.url(args, options),
    method: 'post',
})

restartdde28a289b710195d96eb5b73f9eb336.definition = {
    methods: ["post"],
    url: '/api/projects/{project}/docker/restart',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\ContainerController::restart
* @see app/Http/Controllers/ContainerController.php:180
* @route '/api/projects/{project}/docker/restart'
*/
restartdde28a289b710195d96eb5b73f9eb336.url = (args: { project: string | number } | [project: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return restartdde28a289b710195d96eb5b73f9eb336.definition.url
            .replace('{project}', parsedArgs.project.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ContainerController::restart
* @see app/Http/Controllers/ContainerController.php:180
* @route '/api/projects/{project}/docker/restart'
*/
restartdde28a289b710195d96eb5b73f9eb336.post = (args: { project: string | number } | [project: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: restartdde28a289b710195d96eb5b73f9eb336.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\ContainerController::restart
* @see app/Http/Controllers/ContainerController.php:180
* @route '/api/projects/{project}/docker/restart'
*/
const restartdde28a289b710195d96eb5b73f9eb336Form = (args: { project: string | number } | [project: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: restartdde28a289b710195d96eb5b73f9eb336.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\ContainerController::restart
* @see app/Http/Controllers/ContainerController.php:180
* @route '/api/projects/{project}/docker/restart'
*/
restartdde28a289b710195d96eb5b73f9eb336Form.post = (args: { project: string | number } | [project: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: restartdde28a289b710195d96eb5b73f9eb336.url(args, options),
    method: 'post',
})

restartdde28a289b710195d96eb5b73f9eb336.form = restartdde28a289b710195d96eb5b73f9eb336Form
/**
* @see \App\Http\Controllers\ContainerController::restart
* @see app/Http/Controllers/ContainerController.php:180
* @route '/api/containers/{container}/restart'
*/
const restart068872b2578f5da61be8f342e37a1450 = (args: { container: number | { id: number } } | [container: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: restart068872b2578f5da61be8f342e37a1450.url(args, options),
    method: 'post',
})

restart068872b2578f5da61be8f342e37a1450.definition = {
    methods: ["post"],
    url: '/api/containers/{container}/restart',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\ContainerController::restart
* @see app/Http/Controllers/ContainerController.php:180
* @route '/api/containers/{container}/restart'
*/
restart068872b2578f5da61be8f342e37a1450.url = (args: { container: number | { id: number } } | [container: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return restart068872b2578f5da61be8f342e37a1450.definition.url
            .replace('{container}', parsedArgs.container.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ContainerController::restart
* @see app/Http/Controllers/ContainerController.php:180
* @route '/api/containers/{container}/restart'
*/
restart068872b2578f5da61be8f342e37a1450.post = (args: { container: number | { id: number } } | [container: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: restart068872b2578f5da61be8f342e37a1450.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\ContainerController::restart
* @see app/Http/Controllers/ContainerController.php:180
* @route '/api/containers/{container}/restart'
*/
const restart068872b2578f5da61be8f342e37a1450Form = (args: { container: number | { id: number } } | [container: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: restart068872b2578f5da61be8f342e37a1450.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\ContainerController::restart
* @see app/Http/Controllers/ContainerController.php:180
* @route '/api/containers/{container}/restart'
*/
restart068872b2578f5da61be8f342e37a1450Form.post = (args: { container: number | { id: number } } | [container: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: restart068872b2578f5da61be8f342e37a1450.url(args, options),
    method: 'post',
})

restart068872b2578f5da61be8f342e37a1450.form = restart068872b2578f5da61be8f342e37a1450Form

export const restart = {
    '/api/projects/{project}/docker/restart': restartdde28a289b710195d96eb5b73f9eb336,
    '/api/containers/{container}/restart': restart068872b2578f5da61be8f342e37a1450,
}

const ContainerController = { store, show, destroy, start, status, logs, stop, restart }

export default ContainerController