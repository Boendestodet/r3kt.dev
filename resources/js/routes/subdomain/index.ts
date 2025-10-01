import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../wayfinder'
/**
* @see \App\Http\Controllers\SubdomainController::check
* @see app/Http/Controllers/SubdomainController.php:24
* @route '/api/subdomain/check'
*/
export const check = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: check.url(options),
    method: 'get',
})

check.definition = {
    methods: ["get","head"],
    url: '/api/subdomain/check',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\SubdomainController::check
* @see app/Http/Controllers/SubdomainController.php:24
* @route '/api/subdomain/check'
*/
check.url = (options?: RouteQueryOptions) => {
    return check.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\SubdomainController::check
* @see app/Http/Controllers/SubdomainController.php:24
* @route '/api/subdomain/check'
*/
check.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: check.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SubdomainController::check
* @see app/Http/Controllers/SubdomainController.php:24
* @route '/api/subdomain/check'
*/
check.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: check.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\SubdomainController::check
* @see app/Http/Controllers/SubdomainController.php:24
* @route '/api/subdomain/check'
*/
const checkForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: check.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SubdomainController::check
* @see app/Http/Controllers/SubdomainController.php:24
* @route '/api/subdomain/check'
*/
checkForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: check.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SubdomainController::check
* @see app/Http/Controllers/SubdomainController.php:24
* @route '/api/subdomain/check'
*/
checkForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: check.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

check.form = checkForm

const subdomain = {
    check: Object.assign(check, check),
}

export default subdomain