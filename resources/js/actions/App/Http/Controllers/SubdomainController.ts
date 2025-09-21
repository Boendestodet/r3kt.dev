import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\SubdomainController::checkAvailability
* @see app/Http/Controllers/SubdomainController.php:24
* @route '/api/subdomain/check'
*/
export const checkAvailability = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: checkAvailability.url(options),
    method: 'get',
})

checkAvailability.definition = {
    methods: ["get","head"],
    url: '/api/subdomain/check',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\SubdomainController::checkAvailability
* @see app/Http/Controllers/SubdomainController.php:24
* @route '/api/subdomain/check'
*/
checkAvailability.url = (options?: RouteQueryOptions) => {
    return checkAvailability.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\SubdomainController::checkAvailability
* @see app/Http/Controllers/SubdomainController.php:24
* @route '/api/subdomain/check'
*/
checkAvailability.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: checkAvailability.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SubdomainController::checkAvailability
* @see app/Http/Controllers/SubdomainController.php:24
* @route '/api/subdomain/check'
*/
checkAvailability.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: checkAvailability.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\SubdomainController::checkAvailability
* @see app/Http/Controllers/SubdomainController.php:24
* @route '/api/subdomain/check'
*/
const checkAvailabilityForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: checkAvailability.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SubdomainController::checkAvailability
* @see app/Http/Controllers/SubdomainController.php:24
* @route '/api/subdomain/check'
*/
checkAvailabilityForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: checkAvailability.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SubdomainController::checkAvailability
* @see app/Http/Controllers/SubdomainController.php:24
* @route '/api/subdomain/check'
*/
checkAvailabilityForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: checkAvailability.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

checkAvailability.form = checkAvailabilityForm

/**
* @see \App\Http\Controllers\SubdomainController::updateSubdomain
* @see app/Http/Controllers/SubdomainController.php:51
* @route '/projects/{project}/subdomain'
*/
export const updateSubdomain = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: updateSubdomain.url(args, options),
    method: 'post',
})

updateSubdomain.definition = {
    methods: ["post"],
    url: '/projects/{project}/subdomain',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\SubdomainController::updateSubdomain
* @see app/Http/Controllers/SubdomainController.php:51
* @route '/projects/{project}/subdomain'
*/
updateSubdomain.url = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return updateSubdomain.definition.url
            .replace('{project}', parsedArgs.project.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\SubdomainController::updateSubdomain
* @see app/Http/Controllers/SubdomainController.php:51
* @route '/projects/{project}/subdomain'
*/
updateSubdomain.post = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: updateSubdomain.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\SubdomainController::updateSubdomain
* @see app/Http/Controllers/SubdomainController.php:51
* @route '/projects/{project}/subdomain'
*/
const updateSubdomainForm = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: updateSubdomain.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\SubdomainController::updateSubdomain
* @see app/Http/Controllers/SubdomainController.php:51
* @route '/projects/{project}/subdomain'
*/
updateSubdomainForm.post = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: updateSubdomain.url(args, options),
    method: 'post',
})

updateSubdomain.form = updateSubdomainForm

/**
* @see \App\Http\Controllers\SubdomainController::configureCustomDomain
* @see app/Http/Controllers/SubdomainController.php:113
* @route '/projects/{project}/custom-domain'
*/
export const configureCustomDomain = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: configureCustomDomain.url(args, options),
    method: 'post',
})

configureCustomDomain.definition = {
    methods: ["post"],
    url: '/projects/{project}/custom-domain',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\SubdomainController::configureCustomDomain
* @see app/Http/Controllers/SubdomainController.php:113
* @route '/projects/{project}/custom-domain'
*/
configureCustomDomain.url = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return configureCustomDomain.definition.url
            .replace('{project}', parsedArgs.project.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\SubdomainController::configureCustomDomain
* @see app/Http/Controllers/SubdomainController.php:113
* @route '/projects/{project}/custom-domain'
*/
configureCustomDomain.post = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: configureCustomDomain.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\SubdomainController::configureCustomDomain
* @see app/Http/Controllers/SubdomainController.php:113
* @route '/projects/{project}/custom-domain'
*/
const configureCustomDomainForm = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: configureCustomDomain.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\SubdomainController::configureCustomDomain
* @see app/Http/Controllers/SubdomainController.php:113
* @route '/projects/{project}/custom-domain'
*/
configureCustomDomainForm.post = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: configureCustomDomain.url(args, options),
    method: 'post',
})

configureCustomDomain.form = configureCustomDomainForm

/**
* @see \App\Http\Controllers\SubdomainController::removeCustomDomain
* @see app/Http/Controllers/SubdomainController.php:147
* @route '/projects/{project}/custom-domain'
*/
export const removeCustomDomain = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: removeCustomDomain.url(args, options),
    method: 'delete',
})

removeCustomDomain.definition = {
    methods: ["delete"],
    url: '/projects/{project}/custom-domain',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\SubdomainController::removeCustomDomain
* @see app/Http/Controllers/SubdomainController.php:147
* @route '/projects/{project}/custom-domain'
*/
removeCustomDomain.url = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return removeCustomDomain.definition.url
            .replace('{project}', parsedArgs.project.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\SubdomainController::removeCustomDomain
* @see app/Http/Controllers/SubdomainController.php:147
* @route '/projects/{project}/custom-domain'
*/
removeCustomDomain.delete = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: removeCustomDomain.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\SubdomainController::removeCustomDomain
* @see app/Http/Controllers/SubdomainController.php:147
* @route '/projects/{project}/custom-domain'
*/
const removeCustomDomainForm = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: removeCustomDomain.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\SubdomainController::removeCustomDomain
* @see app/Http/Controllers/SubdomainController.php:147
* @route '/projects/{project}/custom-domain'
*/
removeCustomDomainForm.delete = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: removeCustomDomain.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

removeCustomDomain.form = removeCustomDomainForm

/**
* @see \App\Http\Controllers\SubdomainController::testCloudflareConnection
* @see app/Http/Controllers/SubdomainController.php:174
* @route '/api/cloudflare/test'
*/
export const testCloudflareConnection = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: testCloudflareConnection.url(options),
    method: 'get',
})

testCloudflareConnection.definition = {
    methods: ["get","head"],
    url: '/api/cloudflare/test',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\SubdomainController::testCloudflareConnection
* @see app/Http/Controllers/SubdomainController.php:174
* @route '/api/cloudflare/test'
*/
testCloudflareConnection.url = (options?: RouteQueryOptions) => {
    return testCloudflareConnection.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\SubdomainController::testCloudflareConnection
* @see app/Http/Controllers/SubdomainController.php:174
* @route '/api/cloudflare/test'
*/
testCloudflareConnection.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: testCloudflareConnection.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SubdomainController::testCloudflareConnection
* @see app/Http/Controllers/SubdomainController.php:174
* @route '/api/cloudflare/test'
*/
testCloudflareConnection.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: testCloudflareConnection.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\SubdomainController::testCloudflareConnection
* @see app/Http/Controllers/SubdomainController.php:174
* @route '/api/cloudflare/test'
*/
const testCloudflareConnectionForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: testCloudflareConnection.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SubdomainController::testCloudflareConnection
* @see app/Http/Controllers/SubdomainController.php:174
* @route '/api/cloudflare/test'
*/
testCloudflareConnectionForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: testCloudflareConnection.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SubdomainController::testCloudflareConnection
* @see app/Http/Controllers/SubdomainController.php:174
* @route '/api/cloudflare/test'
*/
testCloudflareConnectionForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: testCloudflareConnection.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

testCloudflareConnection.form = testCloudflareConnectionForm

const SubdomainController = { checkAvailability, updateSubdomain, configureCustomDomain, removeCustomDomain, testCloudflareConnection }

export default SubdomainController