import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\ChatController::status
* @see app/Http/Controllers/ChatController.php:203
* @route '/api/projects/{project}/chat/status'
*/
export const status = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: status.url(args, options),
    method: 'get',
})

status.definition = {
    methods: ["get","head"],
    url: '/api/projects/{project}/chat/status',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\ChatController::status
* @see app/Http/Controllers/ChatController.php:203
* @route '/api/projects/{project}/chat/status'
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
* @see \App\Http\Controllers\ChatController::status
* @see app/Http/Controllers/ChatController.php:203
* @route '/api/projects/{project}/chat/status'
*/
status.get = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: status.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ChatController::status
* @see app/Http/Controllers/ChatController.php:203
* @route '/api/projects/{project}/chat/status'
*/
status.head = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: status.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\ChatController::status
* @see app/Http/Controllers/ChatController.php:203
* @route '/api/projects/{project}/chat/status'
*/
const statusForm = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: status.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ChatController::status
* @see app/Http/Controllers/ChatController.php:203
* @route '/api/projects/{project}/chat/status'
*/
statusForm.get = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: status.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ChatController::status
* @see app/Http/Controllers/ChatController.php:203
* @route '/api/projects/{project}/chat/status'
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
* @see \App\Http\Controllers\ChatController::conversation
* @see app/Http/Controllers/ChatController.php:24
* @route '/api/projects/{project}/chat/conversation'
*/
export const conversation = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: conversation.url(args, options),
    method: 'get',
})

conversation.definition = {
    methods: ["get","head"],
    url: '/api/projects/{project}/chat/conversation',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\ChatController::conversation
* @see app/Http/Controllers/ChatController.php:24
* @route '/api/projects/{project}/chat/conversation'
*/
conversation.url = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return conversation.definition.url
            .replace('{project}', parsedArgs.project.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ChatController::conversation
* @see app/Http/Controllers/ChatController.php:24
* @route '/api/projects/{project}/chat/conversation'
*/
conversation.get = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: conversation.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ChatController::conversation
* @see app/Http/Controllers/ChatController.php:24
* @route '/api/projects/{project}/chat/conversation'
*/
conversation.head = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: conversation.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\ChatController::conversation
* @see app/Http/Controllers/ChatController.php:24
* @route '/api/projects/{project}/chat/conversation'
*/
const conversationForm = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: conversation.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ChatController::conversation
* @see app/Http/Controllers/ChatController.php:24
* @route '/api/projects/{project}/chat/conversation'
*/
conversationForm.get = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: conversation.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ChatController::conversation
* @see app/Http/Controllers/ChatController.php:24
* @route '/api/projects/{project}/chat/conversation'
*/
conversationForm.head = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: conversation.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

conversation.form = conversationForm

/**
* @see \App\Http\Controllers\ChatController::conversations
* @see app/Http/Controllers/ChatController.php:60
* @route '/api/projects/{project}/chat/conversations'
*/
export const conversations = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: conversations.url(args, options),
    method: 'get',
})

conversations.definition = {
    methods: ["get","head"],
    url: '/api/projects/{project}/chat/conversations',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\ChatController::conversations
* @see app/Http/Controllers/ChatController.php:60
* @route '/api/projects/{project}/chat/conversations'
*/
conversations.url = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return conversations.definition.url
            .replace('{project}', parsedArgs.project.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ChatController::conversations
* @see app/Http/Controllers/ChatController.php:60
* @route '/api/projects/{project}/chat/conversations'
*/
conversations.get = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: conversations.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ChatController::conversations
* @see app/Http/Controllers/ChatController.php:60
* @route '/api/projects/{project}/chat/conversations'
*/
conversations.head = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: conversations.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\ChatController::conversations
* @see app/Http/Controllers/ChatController.php:60
* @route '/api/projects/{project}/chat/conversations'
*/
const conversationsForm = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: conversations.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ChatController::conversations
* @see app/Http/Controllers/ChatController.php:60
* @route '/api/projects/{project}/chat/conversations'
*/
conversationsForm.get = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: conversations.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ChatController::conversations
* @see app/Http/Controllers/ChatController.php:60
* @route '/api/projects/{project}/chat/conversations'
*/
conversationsForm.head = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: conversations.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

conversations.form = conversationsForm

/**
* @see \App\Http\Controllers\ChatController::message
* @see app/Http/Controllers/ChatController.php:174
* @route '/api/projects/{project}/chat/message'
*/
export const message = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: message.url(args, options),
    method: 'post',
})

message.definition = {
    methods: ["post"],
    url: '/api/projects/{project}/chat/message',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\ChatController::message
* @see app/Http/Controllers/ChatController.php:174
* @route '/api/projects/{project}/chat/message'
*/
message.url = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return message.definition.url
            .replace('{project}', parsedArgs.project.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ChatController::message
* @see app/Http/Controllers/ChatController.php:174
* @route '/api/projects/{project}/chat/message'
*/
message.post = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: message.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\ChatController::message
* @see app/Http/Controllers/ChatController.php:174
* @route '/api/projects/{project}/chat/message'
*/
const messageForm = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: message.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\ChatController::message
* @see app/Http/Controllers/ChatController.php:174
* @route '/api/projects/{project}/chat/message'
*/
messageForm.post = (args: { project: number | { id: number } } | [project: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: message.url(args, options),
    method: 'post',
})

message.form = messageForm

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

const chat = {
    status: Object.assign(status, status),
    conversation: Object.assign(conversation, conversation),
    conversations: Object.assign(conversations, conversations),
    message: Object.assign(message, message),
    createSession: Object.assign(createSession, createSession),
}

export default chat