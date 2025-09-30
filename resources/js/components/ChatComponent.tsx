import React, { useState, useEffect, useRef } from 'react'
import { router } from '@inertiajs/react'
import { Send, Bot, User, Loader2 } from 'lucide-react'

interface ChatMessage {
  id: string
  role: 'user' | 'assistant'
  content: string
  timestamp: string
}

interface ChatProps {
  projectId: number
  chatId?: string
}

export default function ChatComponent({ projectId, chatId }: ChatProps) {
  const [messages, setMessages] = useState<ChatMessage[]>([])
  const [inputMessage, setInputMessage] = useState('')
  const [isLoading, setIsLoading] = useState(false)
  const [hasChat, setHasChat] = useState(!!chatId)
  const messagesEndRef = useRef<HTMLDivElement>(null)

  const scrollToBottom = () => {
    messagesEndRef.current?.scrollIntoView({ behavior: 'smooth' })
  }

  useEffect(() => {
    scrollToBottom()
  }, [messages])

  useEffect(() => {
    if (chatId) {
      loadConversation()
    }
  }, [chatId])

  const loadConversation = async () => {
    if (!chatId) return

    try {
      const response = await fetch(`/api/projects/${projectId}/chat/conversation`)
      const data = await response.json()
      
      if (data.success) {
        // Parse the conversation and convert to messages
        // This is a simplified version - you might need to parse the actual Cursor CLI output
        const conversationMessages = parseConversation(data.conversation)
        setMessages(conversationMessages)
      }
    } catch (error) {
      console.error('Failed to load conversation:', error)
    }
  }

  const parseConversation = (conversation: string): ChatMessage[] => {
    // This is a simplified parser - you might need to adjust based on actual Cursor CLI output format
    const lines = conversation.split('\n')
    const parsedMessages: ChatMessage[] = []
    let currentMessage = ''
    let currentRole: 'user' | 'assistant' = 'assistant'

    for (const line of lines) {
      if (line.includes('"role":"user"')) {
        if (currentMessage) {
          parsedMessages.push({
            id: Date.now().toString(),
            role: currentRole,
            content: currentMessage.trim(),
            timestamp: new Date().toISOString(),
          })
        }
        currentMessage = ''
        currentRole = 'user'
      } else if (line.includes('"role":"assistant"')) {
        if (currentMessage) {
          parsedMessages.push({
            id: Date.now().toString(),
            role: currentRole,
            content: currentMessage.trim(),
            timestamp: new Date().toISOString(),
          })
        }
        currentMessage = ''
        currentRole = 'assistant'
      } else if (line.trim()) {
        currentMessage += line + '\n'
      }
    }

    if (currentMessage) {
      parsedMessages.push({
        id: Date.now().toString(),
        role: currentRole,
        content: currentMessage.trim(),
        timestamp: new Date().toISOString(),
      })
    }

    return parsedMessages
  }

  const createChatSession = async () => {
    try {
      const response = await fetch(`/api/projects/${projectId}/chat/create-session`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
        },
      })
      
      const data = await response.json()
      
      if (data.success) {
        setHasChat(true)
        // Add welcome message
        setMessages([{
          id: 'welcome',
          role: 'assistant',
          content: 'Hello! I\'m your AI assistant. How can I help you build your project today?',
          timestamp: new Date().toISOString(),
        }])
      }
    } catch (error) {
      console.error('Failed to create chat session:', error)
    }
  }

  const sendMessage = async () => {
    if (!inputMessage.trim() || isLoading) return

    const userMessage: ChatMessage = {
      id: Date.now().toString(),
      role: 'user',
      content: inputMessage,
      timestamp: new Date().toISOString(),
    }

    setMessages(prev => [...prev, userMessage])
    setInputMessage('')
    setIsLoading(true)

    try {
      const response = await fetch(`/api/projects/${projectId}/chat/message`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
        },
        body: JSON.stringify({ message: inputMessage }),
      })
      
      const data = await response.json()
      
      if (data.success) {
        // Parse the response and add to messages
        const assistantMessage: ChatMessage = {
          id: (Date.now() + 1).toString(),
          role: 'assistant',
          content: data.response || 'I received your message but couldn\'t generate a response.',
          timestamp: new Date().toISOString(),
        }
        
        setMessages(prev => [...prev, assistantMessage])
      } else {
        // Add error message
        const errorMessage: ChatMessage = {
          id: (Date.now() + 1).toString(),
          role: 'assistant',
          content: `Error: ${data.error || 'Failed to send message'}`,
          timestamp: new Date().toISOString(),
        }
        
        setMessages(prev => [...prev, errorMessage])
      }
    } catch (error) {
      console.error('Failed to send message:', error)
      
      const errorMessage: ChatMessage = {
        id: (Date.now() + 1).toString(),
        role: 'assistant',
        content: 'Sorry, I encountered an error. Please try again.',
        timestamp: new Date().toISOString(),
      }
      
      setMessages(prev => [...prev, errorMessage])
    } finally {
      setIsLoading(false)
    }
  }

  const handleKeyPress = (e: React.KeyboardEvent) => {
    if (e.key === 'Enter' && !e.shiftKey) {
      e.preventDefault()
      sendMessage()
    }
  }

  if (!hasChat) {
    return (
      <div className="h-full flex items-center justify-center bg-slate-900/50 rounded-lg border border-slate-700/50">
        <div className="text-center">
          <Bot className="w-12 h-12 text-slate-400 mx-auto mb-4" />
          <h3 className="text-lg font-semibold text-white mb-2">Start AI Chat</h3>
          <p className="text-slate-400 mb-4">Begin a conversation with AI to build your project</p>
          <button
            onClick={createChatSession}
            className="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition-colors"
          >
            Start Chat
          </button>
        </div>
      </div>
    )
  }

  return (
    <div className="h-full flex flex-col bg-slate-900/50 rounded-lg border border-slate-700/50">
      {/* Chat Header */}
      <div className="p-4 border-b border-slate-700/50">
        <div className="flex items-center gap-2">
          <Bot className="w-5 h-5 text-blue-400" />
          <h3 className="text-lg font-semibold text-white">AI Assistant</h3>
        </div>
      </div>

      {/* Messages */}
      <div className="flex-1 overflow-y-auto p-4 space-y-4">
        {messages.map((message) => (
          <div
            key={message.id}
            className={`flex gap-3 ${message.role === 'user' ? 'justify-end' : 'justify-start'}`}
          >
            {message.role === 'assistant' && (
              <div className="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center flex-shrink-0">
                <Bot className="w-4 h-4 text-white" />
              </div>
            )}
            
            <div
              className={`max-w-[80%] rounded-lg px-4 py-2 ${
                message.role === 'user'
                  ? 'bg-blue-600 text-white'
                  : 'bg-slate-800 text-slate-100'
              }`}
            >
              <p className="whitespace-pre-wrap">{message.content}</p>
              <p className="text-xs opacity-70 mt-1">
                {new Date(message.timestamp).toLocaleTimeString()}
              </p>
            </div>

            {message.role === 'user' && (
              <div className="w-8 h-8 bg-slate-600 rounded-full flex items-center justify-center flex-shrink-0">
                <User className="w-4 h-4 text-white" />
              </div>
            )}
          </div>
        ))}
        
        {isLoading && (
          <div className="flex gap-3 justify-start">
            <div className="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center flex-shrink-0">
              <Bot className="w-4 h-4 text-white" />
            </div>
            <div className="bg-slate-800 text-slate-100 rounded-lg px-4 py-2">
              <div className="flex items-center gap-2">
                <Loader2 className="w-4 h-4 animate-spin" />
                <span>AI is thinking...</span>
              </div>
            </div>
          </div>
        )}
        
        <div ref={messagesEndRef} />
      </div>

      {/* Input */}
      <div className="p-4 border-t border-slate-700/50">
        <div className="flex gap-2">
          <textarea
            value={inputMessage}
            onChange={(e) => setInputMessage(e.target.value)}
            onKeyPress={handleKeyPress}
            placeholder="Type your message..."
            className="flex-1 bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-white placeholder-slate-400 resize-none focus:outline-none focus:ring-2 focus:ring-blue-500"
            rows={2}
            disabled={isLoading}
          />
          <button
            onClick={sendMessage}
            disabled={!inputMessage.trim() || isLoading}
            className="bg-blue-600 hover:bg-blue-700 disabled:bg-slate-600 disabled:cursor-not-allowed text-white p-2 rounded-lg transition-colors"
          >
            <Send className="w-4 h-4" />
          </button>
        </div>
      </div>
    </div>
  )
}
