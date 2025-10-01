import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../wayfinder'
/**
* @see \App\Http\Controllers\SubdomainController::test
* @see app/Http/Controllers/SubdomainController.php:174
* @route '/api/cloudflare/test'
*/
export const test = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: test.url(options),
    method: 'get',
})

test.definition = {
    methods: ["get","head"],
    url: '/api/cloudflare/test',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\SubdomainController::test
* @see app/Http/Controllers/SubdomainController.php:174
* @route '/api/cloudflare/test'
*/
test.url = (options?: RouteQueryOptions) => {
    return test.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\SubdomainController::test
* @see app/Http/Controllers/SubdomainController.php:174
* @route '/api/cloudflare/test'
*/
test.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: test.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SubdomainController::test
* @see app/Http/Controllers/SubdomainController.php:174
* @route '/api/cloudflare/test'
*/
test.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: test.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\SubdomainController::test
* @see app/Http/Controllers/SubdomainController.php:174
* @route '/api/cloudflare/test'
*/
const testForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: test.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SubdomainController::test
* @see app/Http/Controllers/SubdomainController.php:174
* @route '/api/cloudflare/test'
*/
testForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: test.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SubdomainController::test
* @see app/Http/Controllers/SubdomainController.php:174
* @route '/api/cloudflare/test'
*/
testForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: test.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

test.form = testForm

const cloudflare = {
    test: Object.assign(test, test),
}

export default cloudflare