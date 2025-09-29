import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../wayfinder'
/**
* @see \App\Http\Controllers\BalanceController::index
* @see app/Http/Controllers/BalanceController.php:21
* @route '/api/balance'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/api/balance',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\BalanceController::index
* @see app/Http/Controllers/BalanceController.php:21
* @route '/api/balance'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\BalanceController::index
* @see app/Http/Controllers/BalanceController.php:21
* @route '/api/balance'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\BalanceController::index
* @see app/Http/Controllers/BalanceController.php:21
* @route '/api/balance'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\BalanceController::index
* @see app/Http/Controllers/BalanceController.php:21
* @route '/api/balance'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\BalanceController::index
* @see app/Http/Controllers/BalanceController.php:21
* @route '/api/balance'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\BalanceController::index
* @see app/Http/Controllers/BalanceController.php:21
* @route '/api/balance'
*/
indexForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

index.form = indexForm

/**
* @see \App\Http\Controllers\BalanceController::costEstimates
* @see app/Http/Controllers/BalanceController.php:32
* @route '/api/balance/cost-estimates'
*/
export const costEstimates = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: costEstimates.url(options),
    method: 'get',
})

costEstimates.definition = {
    methods: ["get","head"],
    url: '/api/balance/cost-estimates',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\BalanceController::costEstimates
* @see app/Http/Controllers/BalanceController.php:32
* @route '/api/balance/cost-estimates'
*/
costEstimates.url = (options?: RouteQueryOptions) => {
    return costEstimates.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\BalanceController::costEstimates
* @see app/Http/Controllers/BalanceController.php:32
* @route '/api/balance/cost-estimates'
*/
costEstimates.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: costEstimates.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\BalanceController::costEstimates
* @see app/Http/Controllers/BalanceController.php:32
* @route '/api/balance/cost-estimates'
*/
costEstimates.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: costEstimates.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\BalanceController::costEstimates
* @see app/Http/Controllers/BalanceController.php:32
* @route '/api/balance/cost-estimates'
*/
const costEstimatesForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: costEstimates.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\BalanceController::costEstimates
* @see app/Http/Controllers/BalanceController.php:32
* @route '/api/balance/cost-estimates'
*/
costEstimatesForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: costEstimates.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\BalanceController::costEstimates
* @see app/Http/Controllers/BalanceController.php:32
* @route '/api/balance/cost-estimates'
*/
costEstimatesForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: costEstimates.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

costEstimates.form = costEstimatesForm

/**
* @see \App\Http\Controllers\BalanceController::canAfford
* @see app/Http/Controllers/BalanceController.php:71
* @route '/api/balance/can-afford'
*/
export const canAfford = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: canAfford.url(options),
    method: 'get',
})

canAfford.definition = {
    methods: ["get","head"],
    url: '/api/balance/can-afford',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\BalanceController::canAfford
* @see app/Http/Controllers/BalanceController.php:71
* @route '/api/balance/can-afford'
*/
canAfford.url = (options?: RouteQueryOptions) => {
    return canAfford.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\BalanceController::canAfford
* @see app/Http/Controllers/BalanceController.php:71
* @route '/api/balance/can-afford'
*/
canAfford.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: canAfford.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\BalanceController::canAfford
* @see app/Http/Controllers/BalanceController.php:71
* @route '/api/balance/can-afford'
*/
canAfford.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: canAfford.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\BalanceController::canAfford
* @see app/Http/Controllers/BalanceController.php:71
* @route '/api/balance/can-afford'
*/
const canAffordForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: canAfford.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\BalanceController::canAfford
* @see app/Http/Controllers/BalanceController.php:71
* @route '/api/balance/can-afford'
*/
canAffordForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: canAfford.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\BalanceController::canAfford
* @see app/Http/Controllers/BalanceController.php:71
* @route '/api/balance/can-afford'
*/
canAffordForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: canAfford.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

canAfford.form = canAffordForm

/**
* @see \App\Http\Controllers\BalanceController::addCredits
* @see app/Http/Controllers/BalanceController.php:47
* @route '/api/balance/add-credits'
*/
export const addCredits = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: addCredits.url(options),
    method: 'post',
})

addCredits.definition = {
    methods: ["post"],
    url: '/api/balance/add-credits',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\BalanceController::addCredits
* @see app/Http/Controllers/BalanceController.php:47
* @route '/api/balance/add-credits'
*/
addCredits.url = (options?: RouteQueryOptions) => {
    return addCredits.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\BalanceController::addCredits
* @see app/Http/Controllers/BalanceController.php:47
* @route '/api/balance/add-credits'
*/
addCredits.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: addCredits.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\BalanceController::addCredits
* @see app/Http/Controllers/BalanceController.php:47
* @route '/api/balance/add-credits'
*/
const addCreditsForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: addCredits.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\BalanceController::addCredits
* @see app/Http/Controllers/BalanceController.php:47
* @route '/api/balance/add-credits'
*/
addCreditsForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: addCredits.url(options),
    method: 'post',
})

addCredits.form = addCreditsForm

const balance = {
    index,
    costEstimates,
    canAfford,
    addCredits,
}

export default balance