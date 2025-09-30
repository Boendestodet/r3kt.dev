import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\ChatController::getStatus
* @see app/Http/Controllers/ChatController.php:203
* @route '/api/projects/{project}/chat/status'
*/
export const getStatus = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getStatus.url(args, options),
    method: 'get',
})

getStatus.definition = {
    methods: ["get","head"],
    url: '/api/projects/{project}/chat/status',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\ChatController::getStatus
* @see app/Http/Controllers/ChatController.php:203
* @route '/api/projects/{project}/chat/status'
*/
getStatus.url = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return getStatus.definition.url
            .replace('{project}', parsedArgs.project.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ChatController::getStatus
* @see app/Http/Controllers/ChatController.php:203
* @route '/api/projects/{project}/chat/status'
*/
getStatus.get = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getStatus.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ChatController::getStatus
* @see app/Http/Controllers/ChatController.php:203
* @route '/api/projects/{project}/chat/status'
*/
getStatus.head = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getStatus.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\ChatController::getStatus
* @see app/Http/Controllers/ChatController.php:203
* @route '/api/projects/{project}/chat/status'
*/
const getStatusForm = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getStatus.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ChatController::getStatus
* @see app/Http/Controllers/ChatController.php:203
* @route '/api/projects/{project}/chat/status'
*/
getStatusForm.get = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getStatus.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ChatController::getStatus
* @see app/Http/Controllers/ChatController.php:203
* @route '/api/projects/{project}/chat/status'
*/
getStatusForm.head = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getStatus.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

getStatus.form = getStatusForm

/**
* @see \App\Http\Controllers\ChatController::getConversation
* @see app/Http/Controllers/ChatController.php:24
* @route '/api/projects/{project}/chat/conversation'
*/
export const getConversation = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getConversation.url(args, options),
    method: 'get',
})

getConversation.definition = {
    methods: ["get","head"],
    url: '/api/projects/{project}/chat/conversation',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\ChatController::getConversation
* @see app/Http/Controllers/ChatController.php:24
* @route '/api/projects/{project}/chat/conversation'
*/
getConversation.url = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return getConversation.definition.url
            .replace('{project}', parsedArgs.project.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ChatController::getConversation
* @see app/Http/Controllers/ChatController.php:24
* @route '/api/projects/{project}/chat/conversation'
*/
getConversation.get = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getConversation.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ChatController::getConversation
* @see app/Http/Controllers/ChatController.php:24
* @route '/api/projects/{project}/chat/conversation'
*/
getConversation.head = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getConversation.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\ChatController::getConversation
* @see app/Http/Controllers/ChatController.php:24
* @route '/api/projects/{project}/chat/conversation'
*/
const getConversationForm = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getConversation.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ChatController::getConversation
* @see app/Http/Controllers/ChatController.php:24
* @route '/api/projects/{project}/chat/conversation'
*/
getConversationForm.get = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getConversation.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ChatController::getConversation
* @see app/Http/Controllers/ChatController.php:24
* @route '/api/projects/{project}/chat/conversation'
*/
getConversationForm.head = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getConversation.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

getConversation.form = getConversationForm

/**
* @see \App\Http\Controllers\ChatController::getAllConversations
* @see app/Http/Controllers/ChatController.php:60
* @route '/api/projects/{project}/chat/conversations'
*/
export const getAllConversations = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getAllConversations.url(args, options),
    method: 'get',
})

getAllConversations.definition = {
    methods: ["get","head"],
    url: '/api/projects/{project}/chat/conversations',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\ChatController::getAllConversations
* @see app/Http/Controllers/ChatController.php:60
* @route '/api/projects/{project}/chat/conversations'
*/
getAllConversations.url = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return getAllConversations.definition.url
            .replace('{project}', parsedArgs.project.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ChatController::getAllConversations
* @see app/Http/Controllers/ChatController.php:60
* @route '/api/projects/{project}/chat/conversations'
*/
getAllConversations.get = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getAllConversations.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ChatController::getAllConversations
* @see app/Http/Controllers/ChatController.php:60
* @route '/api/projects/{project}/chat/conversations'
*/
getAllConversations.head = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getAllConversations.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\ChatController::getAllConversations
* @see app/Http/Controllers/ChatController.php:60
* @route '/api/projects/{project}/chat/conversations'
*/
const getAllConversationsForm = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getAllConversations.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ChatController::getAllConversations
* @see app/Http/Controllers/ChatController.php:60
* @route '/api/projects/{project}/chat/conversations'
*/
getAllConversationsForm.get = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getAllConversations.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ChatController::getAllConversations
* @see app/Http/Controllers/ChatController.php:60
* @route '/api/projects/{project}/chat/conversations'
*/
getAllConversationsForm.head = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getAllConversations.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

getAllConversations.form = getAllConversationsForm

/**
* @see \App\Http\Controllers\ChatController::sendMessage
* @see app/Http/Controllers/ChatController.php:174
* @route '/api/projects/{project}/chat/message'
*/
export const sendMessage = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: sendMessage.url(args, options),
    method: 'post',
})

sendMessage.definition = {
    methods: ["post"],
    url: '/api/projects/{project}/chat/message',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\ChatController::sendMessage
* @see app/Http/Controllers/ChatController.php:174
* @route '/api/projects/{project}/chat/message'
*/
sendMessage.url = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return sendMessage.definition.url
            .replace('{project}', parsedArgs.project.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ChatController::sendMessage
* @see app/Http/Controllers/ChatController.php:174
* @route '/api/projects/{project}/chat/message'
*/
sendMessage.post = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: sendMessage.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\ChatController::sendMessage
* @see app/Http/Controllers/ChatController.php:174
* @route '/api/projects/{project}/chat/message'
*/
const sendMessageForm = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: sendMessage.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\ChatController::sendMessage
* @see app/Http/Controllers/ChatController.php:174
* @route '/api/projects/{project}/chat/message'
*/
sendMessageForm.post = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: sendMessage.url(args, options),
    method: 'post',
})

sendMessage.form = sendMessageForm

/**
* @see \App\Http\Controllers\ChatController::createSession
* @see app/Http/Controllers/ChatController.php:192
* @route '/api/projects/{project}/chat/create-session'
*/
export const createSession = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: createSession.url(args, options),
    method: 'post',
})

createSession.definition = {
    methods: ["post"],
    url: '/api/projects/{project}/chat/create-session',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\ChatController::createSession
* @see app/Http/Controllers/ChatController.php:192
* @route '/api/projects/{project}/chat/create-session'
*/
createSession.url = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return createSession.definition.url
            .replace('{project}', parsedArgs.project.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ChatController::createSession
* @see app/Http/Controllers/ChatController.php:192
* @route '/api/projects/{project}/chat/create-session'
*/
createSession.post = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: createSession.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\ChatController::createSession
* @see app/Http/Controllers/ChatController.php:192
* @route '/api/projects/{project}/chat/create-session'
*/
const createSessionForm = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: createSession.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\ChatController::createSession
* @see app/Http/Controllers/ChatController.php:192
* @route '/api/projects/{project}/chat/create-session'
*/
createSessionForm.post = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: createSession.url(args, options),
    method: 'post',
})

createSession.form = createSessionForm

const ChatController = { getStatus, getConversation, getAllConversations, sendMessage, createSession }

export default ChatController