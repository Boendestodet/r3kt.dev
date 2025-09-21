"use client"

import type React from "react"
import { useState, useEffect } from "react"
import { Head, router } from "@inertiajs/react"
import { Textarea } from '@/components/ui/textarea'

const Terminal = ({ className }: { className?: string }) => (
  <svg className={className} fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
  </svg>
)

const Folder = ({ className }: { className?: string }) => (
  <svg className={className} fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
  </svg>
)

const File = ({ className }: { className?: string }) => (
  <svg className={className} fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
  </svg>
)

const Play = ({ className }: { className?: string }) => (
  <svg className={className} fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
  </svg>
)

const RefreshCw = ({ className }: { className?: string }) => (
  <svg className={className} fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
  </svg>
)

const ExternalLink = ({ className }: { className?: string }) => (
  <svg className={className} fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
  </svg>
)

const ChevronRight = ({ className }: { className?: string }) => (
  <svg className={className} fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5l7 7-7 7" />
  </svg>
)

const ChevronDown = ({ className }: { className?: string }) => (
  <svg className={className} fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 9l-7 7-7-7" />
  </svg>
)

const X = ({ className }: { className?: string }) => (
  <svg className={className} fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" />
  </svg>
)

const ArrowLeft = ({ className }: { className?: string }) => (
  <svg className={className} fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M10 19l-7-7m0 0l7-7m-7 7h18" />
  </svg>
)

const MessageCircle = ({ className }: { className?: string }) => (
  <svg className={className} fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
  </svg>
)

const Send = ({ className }: { className?: string }) => (
  <svg className={className} fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
  </svg>
)

const Bot = ({ className }: { className?: string }) => (
  <svg className={className} fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
  </svg>
)

const User = ({ className }: { className?: string }) => (
  <svg className={className} fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
  </svg>
)

const Sparkles = ({ className }: { className?: string }) => (
  <svg className={className} fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
  </svg>
)

const Square = ({ className }: { className?: string }) => (
  <svg className={className} fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 6h12v12H6z" />
  </svg>
)

interface FileNode {
  name: string
  type: 'file' | 'folder'
  children?: FileNode[]
  content?: string
}

interface ChatMessage {
  id: string
  type: 'user' | 'ai'
  content: string
  timestamp: Date
  isTyping?: boolean
}

interface Props {
  project: {
    id: number
    name: string
    stack: string
    model: string
    status: 'building' | 'ready' | 'error'
    preview_url?: string
  }
}

export default function SandboxPage({ project }: Props) {
  // Mock file structure
  const fileStructure: FileNode[] = [
    {
      name: 'src',
      type: 'folder',
      children: [
        {
          name: 'components',
          type: 'folder',
          children: [
            { name: 'Header.tsx', type: 'file', content: 'export default function Header() {\n  return <header>Header Component</header>\n}' },
            { name: 'Footer.tsx', type: 'file', content: 'export default function Footer() {\n  return <footer>Footer Component</footer>\n}' }
          ]
        },
        {
          name: 'pages',
          type: 'folder',
          children: [
            { name: 'Home.tsx', type: 'file', content: 'export default function Home() {\n  return <div>Home Page</div>\n}' },
            { name: 'About.tsx', type: 'file', content: 'export default function About() {\n  return <div>About Page</div>\n}' }
          ]
        },
        { name: 'App.tsx', type: 'file', content: 'import React from \'react\'\n\nexport default function App() {\n  return (\n    <div className="App">\n      <h1>My App</h1>\n    </div>\n  )\n}' },
        { name: 'index.tsx', type: 'file', content: 'import React from \'react\'\nimport ReactDOM from \'react-dom/client\'\nimport App from \'./App\'\n\nReactDOM.createRoot(document.getElementById(\'root\')!).render(\n  <React.StrictMode>\n    <App />\n  </React.StrictMode>\n)' }
      ]
    },
    {
      name: 'public',
      type: 'folder',
      children: [
        { name: 'index.html', type: 'file', content: '<!DOCTYPE html>\n<html>\n<head>\n  <title>My App</title>\n</head>\n<body>\n  <div id="root"></div>\n</body>\n</html>' },
        { name: 'favicon.ico', type: 'file' }
      ]
    },
    { name: 'package.json', type: 'file', content: '{\n  "name": "my-app",\n  "version": "1.0.0",\n  "dependencies": {\n    "react": "^18.0.0",\n    "react-dom": "^18.0.0"\n  }\n}' },
    { name: 'README.md', type: 'file', content: '# My App\n\nThis is a React application built with Vite.' }
  ]

  const [activeTab, setActiveTab] = useState<'console' | 'files' | 'preview' | 'chat'>('chat')
  const [consoleOutput, setConsoleOutput] = useState<string[]>([])
  const [isRunning, setIsRunning] = useState(false)
  const [selectedFile, setSelectedFile] = useState<FileNode | null>(null)
  const [expandedFolders, setExpandedFolders] = useState<Set<string>>(new Set(['src', 'public']))
  const [commandInput, setCommandInput] = useState('')
  const [commandHistory, setCommandHistory] = useState<string[]>([])
  const [historyIndex, setHistoryIndex] = useState(-1)
  const [chatMessages, setChatMessages] = useState<ChatMessage[]>([])
  const [chatInput, setChatInput] = useState('')
  const [isAiTyping, setIsAiTyping] = useState(false)
  const [fileSystem, setFileSystem] = useState<FileNode[]>(fileStructure)
  const [isEnhancing, setIsEnhancing] = useState(false)

  // Mock console output
  useEffect(() => {
    const mockOutput = [
      '$ npm install',
      'added 1234 packages in 2.3s',
      '',
      '$ npm run dev',
      'vite v4.0.0 dev server running at:',
      '  ➜  Local:   http://localhost:3000/',
      '  ➜  Network: use --host to expose',
      '',
      '✓ ready in 234ms.',
      '✓ 15 modules transformed.',
      '✓ page reload src/App.tsx.',
      '',
      '> Building for production...',
      '✓ built in 1.2s.',
      '✓ 15 modules transformed.',
      '✓ 2 assets generated.',
    ]
    
    let index = 0
    const interval = setInterval(() => {
      if (index < mockOutput.length) {
        setConsoleOutput(prev => [...prev, mockOutput[index]])
        index++
      } else {
        clearInterval(interval)
        setIsRunning(true)
        // Add initial prompt after setup
        setConsoleOutput(prev => [...prev, '', '$ '])
      }
    }, 500)

    return () => clearInterval(interval)
  }, [])

  // Command execution logic
  const executeCommand = (command: string) => {
    if (!command.trim()) return

    // Add command to output
    setConsoleOutput(prev => [...prev, `$ ${command}`])
    
    // Add to history
    setCommandHistory(prev => [...prev, command])
    setHistoryIndex(-1)

    // Simulate command execution
    setTimeout(() => {
      const response = getCommandResponse(command)
      setConsoleOutput(prev => [...prev, ...response, '', '$ '])
    }, 300)
  }

  // Mock command responses
  const getCommandResponse = (command: string): string[] => {
    const cmd = command.trim().toLowerCase()
    
    switch (cmd) {
      case 'ls':
      case 'dir':
        return [
          'src/',
          'public/',
          'package.json',
          'README.md',
          'node_modules/',
          'dist/'
        ]
      case 'pwd':
        return ['/app']
      case 'whoami':
        return ['root']
      case 'npm --version':
        return ['9.6.7']
      case 'node --version':
        return ['v18.17.0']
      case 'npm run build':
        return [
          '> my-app@1.0.0 build',
          '> vite build',
          '',
          'vite v4.0.0 building for production...',
          '✓ 15 modules transformed.',
          '✓ 2 assets generated.',
          'dist/index.html                   0.45 kB',
          'dist/assets/index-abc123.js        1.23 kB',
          'dist/assets/index-def456.css       0.12 kB'
        ]
      case 'npm run dev':
        return [
          '> my-app@1.0.0 dev',
          '> vite',
          '',
          'vite v4.0.0 dev server running at:',
          '  ➜  Local:   http://localhost:3000/',
          '  ➜  Network: use --host to expose',
          '',
          '✓ ready in 234ms.'
        ]
      case 'cat package.json':
        return [
          '{',
          '  "name": "my-app",',
          '  "version": "1.0.0",',
          '  "type": "module",',
          '  "scripts": {',
          '    "dev": "vite",',
          '    "build": "vite build",',
          '    "preview": "vite preview"',
          '  },',
          '  "dependencies": {',
          '    "react": "^18.2.0",',
          '    "react-dom": "^18.2.0"',
          '  }',
          '}'
        ]
      case 'help':
        return [
          'Available commands:',
          '  ls, dir          - List directory contents',
          '  pwd              - Print working directory',
          '  whoami           - Print current user',
          '  cat <file>       - Display file contents',
          '  npm run <script> - Run npm script',
          '  npm --version    - Show npm version',
          '  node --version   - Show node version',
          '  help             - Show this help',
          '  clear            - Clear console'
        ]
      case 'clear':
        setConsoleOutput([])
        return []
      default:
        if (cmd.startsWith('cat ')) {
          const filename = cmd.substring(4)
          return [`cat: ${filename}: No such file or directory`]
        }
        return [`Command not found: ${command}`]
    }
  }

  // Handle command input
  const handleCommandSubmit = (e: React.FormEvent) => {
    e.preventDefault()
    if (!commandInput.trim()) return
    
    executeCommand(commandInput)
    setCommandInput('')
  }

  // Handle keyboard navigation for console
  const handleConsoleKeyDown = (e: React.KeyboardEvent) => {
    if (e.key === 'ArrowUp') {
      e.preventDefault()
      if (commandHistory.length > 0) {
        const newIndex = historyIndex === -1 ? commandHistory.length - 1 : Math.max(0, historyIndex - 1)
        setHistoryIndex(newIndex)
        setCommandInput(commandHistory[newIndex])
      }
    } else if (e.key === 'ArrowDown') {
      e.preventDefault()
      if (historyIndex !== -1) {
        const newIndex = historyIndex + 1
        if (newIndex >= commandHistory.length) {
          setHistoryIndex(-1)
          setCommandInput('')
        } else {
          setHistoryIndex(newIndex)
          setCommandInput(commandHistory[newIndex])
        }
      }
    }
  }

  // Chat functionality
  const sendMessage = async (message: string) => {
    if (!message.trim() || isAiTyping) return

    const userMessage: ChatMessage = {
      id: Date.now().toString(),
      type: 'user',
      content: message,
      timestamp: new Date()
    }

    setChatMessages(prev => [...prev, userMessage])
    setChatInput('')
    setIsAiTyping(true)

    // Simulate AI thinking and response
    setTimeout(() => {
      const aiResponse = generateAiResponse(message)
      const aiMessage: ChatMessage = {
        id: (Date.now() + 1).toString(),
        type: 'ai',
        content: aiResponse,
        timestamp: new Date()
      }
      
      setChatMessages(prev => [...prev, aiMessage])
      setIsAiTyping(false)
    }, 1500)
  }

  const generateAiResponse = (message: string): string => {
    const lowerMessage = message.toLowerCase()
    
    if (lowerMessage.includes('button') || lowerMessage.includes('click')) {
      return `I'll add a button component for you! Let me create a reusable Button component and update your App.tsx to include it.

\`\`\`tsx
// src/components/Button.tsx
interface ButtonProps {
  children: React.ReactNode
  onClick?: () => void
  variant?: 'primary' | 'secondary'
  className?: string
}

export default function Button({ children, onClick, variant = 'primary', className = '' }: ButtonProps) {
  const baseClasses = 'px-4 py-2 rounded-lg font-medium transition-colors'
  const variantClasses = variant === 'primary' 
    ? 'bg-blue-500 text-white hover:bg-blue-600' 
    : 'bg-gray-200 text-gray-800 hover:bg-gray-300'
  
  return (
    <button 
      className={\`\${baseClasses} \${variantClasses} \${className}\`}
      onClick={onClick}
    >
      {children}
    </button>
  )
}
\`\`\`

I've created a flexible Button component with different variants. Would you like me to add it to your main App component?`
    }
    
    if (lowerMessage.includes('form') || lowerMessage.includes('input')) {
      return `I'll create a contact form for you! Let me build a complete form with validation.

\`\`\`tsx
// src/components/ContactForm.tsx
import { useState } from 'react'

export default function ContactForm() {
  const [formData, setFormData] = useState({
    name: '',
    email: '',
    message: ''
  })

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault()
    console.log('Form submitted:', formData)
    // Add your form submission logic here
  }

  return (
    <form onSubmit={handleSubmit} className="max-w-md mx-auto space-y-4">
      <div>
        <label className="block text-sm font-medium mb-1">Name</label>
        <input
          type="text"
          value={formData.name}
          onChange={(e) => setFormData(prev => ({ ...prev, name: e.target.value }))}
          className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
          required
        />
      </div>
      <div>
        <label className="block text-sm font-medium mb-1">Email</label>
        <input
          type="email"
          value={formData.email}
          onChange={(e) => setFormData(prev => ({ ...prev, email: e.target.value }))}
          className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
          required
        />
      </div>
      <div>
        <label className="block text-sm font-medium mb-1">Message</label>
        <textarea
          value={formData.message}
          onChange={(e) => setFormData(prev => ({ ...prev, message: e.target.value }))}
          className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
          rows={4}
          required
        />
      </div>
      <button
        type="submit"
        className="w-full bg-blue-500 text-white py-2 px-4 rounded-lg hover:bg-blue-600 transition-colors"
      >
        Send Message
      </button>
    </form>
  )
}
\`\`\`

This form includes proper validation and state management. Would you like me to integrate it into your app?`
    }
    
    if (lowerMessage.includes('navbar') || lowerMessage.includes('navigation') || lowerMessage.includes('menu')) {
      return `I'll create a responsive navigation bar for you! Let me build a modern navbar with mobile support.

\`\`\`tsx
// src/components/Navbar.tsx
import { useState } from 'react'

export default function Navbar() {
  const [isOpen, setIsOpen] = useState(false)

  return (
    <nav className="bg-white shadow-lg">
      <div className="max-w-7xl mx-auto px-4">
        <div className="flex justify-between h-16">
          <div className="flex items-center">
            <div className="flex-shrink-0">
              <h1 className="text-xl font-bold text-gray-800">My App</h1>
            </div>
          </div>
          
          {/* Desktop Menu */}
          <div className="hidden md:flex items-center space-x-8">
            <a href="#" className="text-gray-600 hover:text-gray-900 px-3 py-2">Home</a>
            <a href="#" className="text-gray-600 hover:text-gray-900 px-3 py-2">About</a>
            <a href="#" className="text-gray-600 hover:text-gray-900 px-3 py-2">Services</a>
            <a href="#" className="text-gray-600 hover:text-gray-900 px-3 py-2">Contact</a>
          </div>
          
          {/* Mobile menu button */}
          <div className="md:hidden flex items-center">
            <button
              onClick={() => setIsOpen(!isOpen)}
              className="text-gray-600 hover:text-gray-900 focus:outline-none"
            >
              <svg className="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                {isOpen ? (
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" />
                ) : (
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4 6h16M4 12h16M4 18h16" />
                )}
              </svg>
            </button>
          </div>
        </div>
        
        {/* Mobile Menu */}
        {isOpen && (
          <div className="md:hidden">
            <div className="px-2 pt-2 pb-3 space-y-1 sm:px-3">
              <a href="#" className="text-gray-600 hover:text-gray-900 block px-3 py-2">Home</a>
              <a href="#" className="text-gray-600 hover:text-gray-900 block px-3 py-2">About</a>
              <a href="#" className="text-gray-600 hover:text-gray-900 block px-3 py-2">Services</a>
              <a href="#" className="text-gray-600 hover:text-gray-900 block px-3 py-2">Contact</a>
            </div>
          </div>
        )}
      </div>
    </nav>
  )
}
\`\`\`

This navbar is fully responsive and includes mobile menu functionality. Should I add it to your app?`
    }
    
    if (lowerMessage.includes('card') || lowerMessage.includes('component')) {
      return `I'll create a reusable Card component for you! This will be perfect for displaying content in a structured way.

\`\`\`tsx
// src/components/Card.tsx
interface CardProps {
  title?: string
  children: React.ReactNode
  className?: string
  onClick?: () => void
}

export default function Card({ title, children, className = '', onClick }: CardProps) {
  return (
    <div 
      className={\`bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow \${className}\`}
      onClick={onClick}
    >
      {title && (
        <h3 className="text-lg font-semibold text-gray-900 mb-4">{title}</h3>
      )}
      {children}
    </div>
  )
}

// Usage example:
// <Card title="My Card">
//   <p>This is the card content</p>
// </Card>
\`\`\`

The Card component is flexible and can be used throughout your app. Would you like me to show you how to use it in your main component?`
    }
    
    if (lowerMessage.includes('style') || lowerMessage.includes('css') || lowerMessage.includes('design')) {
      return `I'll help you improve the styling! Let me add some modern CSS utilities and create a consistent design system.

\`\`\`css
/* src/styles/globals.css */
@tailwind base;
@tailwind components;
@tailwind utilities;

@layer components {
  .btn-primary {
    @apply bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition-colors;
  }
  
  .btn-secondary {
    @apply bg-gray-200 text-gray-800 px-4 py-2 rounded-lg hover:bg-gray-300 transition-colors;
  }
  
  .card {
    @apply bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow;
  }
  
  .input {
    @apply w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500;
  }
}

/* Custom animations */
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}

.animate-fade-in {
  animation: fadeIn 0.3s ease-out;
}
\`\`\`

I've created a design system with reusable utility classes. Would you like me to apply these styles to your components?`
    }
    
    if (lowerMessage.includes('api') || lowerMessage.includes('fetch') || lowerMessage.includes('data')) {
      return `I'll help you set up API integration! Let me create a data fetching utility and show you how to use it.

\`\`\`tsx
// src/utils/api.ts
const API_BASE_URL = process.env.REACT_APP_API_URL || 'http://localhost:3001'

export class ApiClient {
  private baseURL: string

  constructor(baseURL: string = API_BASE_URL) {
    this.baseURL = baseURL
  }

  async get<T>(endpoint: string): Promise<T> {
    const response = await fetch(\`\${this.baseURL}\${endpoint}\`)
    if (!response.ok) {
      throw new Error(\`HTTP error! status: \${response.status}\`)
    }
    return response.json()
  }

  async post<T>(endpoint: string, data: any): Promise<T> {
    const response = await fetch(\`\${this.baseURL}\${endpoint}\`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(data),
    })
    if (!response.ok) {
      throw new Error(\`HTTP error! status: \${response.status}\`)
    }
    return response.json()
  }
}

export const apiClient = new ApiClient()

// Usage example:
// const users = await apiClient.get<User[]>('/users')
// const newUser = await apiClient.post<User>('/users', { name: 'John' })
\`\`\`

This API client handles errors and provides type safety. Would you like me to create a specific data fetching hook for your use case?`
    }
    
    return `I understand you want me to help build something! I can assist you with:

🎨 **UI Components**: Buttons, forms, cards, navigation, modals
📱 **Layout & Design**: Responsive layouts, styling, animations
🔧 **Functionality**: State management, API integration, data fetching
📊 **Features**: Charts, tables, user authentication, file uploads

Just tell me what specific feature or component you'd like me to create, and I'll build it for you! For example:
- "Create a login form"
- "Add a dark mode toggle"
- "Build a data table"
- "Make a modal component"

What would you like me to build?`
  }

  const handleChatSubmit = (e: React.FormEvent) => {
    e.preventDefault()
    sendMessage(chatInput)
  }

  // Enhance prompt function
  const enhancePrompt = async () => {
    if (!chatInput.trim() || isEnhancing) return

    setIsEnhancing(true)
    
    // Simulate AI enhancement
    setTimeout(() => {
      const enhanced = enhanceUserPrompt(chatInput)
      setChatInput(enhanced)
      setIsEnhancing(false)
    }, 1500)
  }

  // Mock prompt enhancement
  const enhanceUserPrompt = (prompt: string): string => {
    const lowerPrompt = prompt.toLowerCase()
    
    // Add context and specificity based on the request
    if (lowerPrompt.includes('button') || lowerPrompt.includes('click')) {
      return `Create a modern, accessible button component with the following features:
- Multiple variants (primary, secondary, outline)
- Hover and focus states
- Loading state support
- Proper TypeScript interfaces
- Tailwind CSS styling
- Accessibility attributes (ARIA labels, keyboard navigation)
- Size variants (sm, md, lg)
- Icon support

Please make it reusable and follow React best practices.`
    }
    
    if (lowerPrompt.includes('form') || lowerPrompt.includes('input')) {
      return `Build a comprehensive form component with:
- Form validation with error messages
- Multiple input types (text, email, password, textarea, select)
- Real-time validation feedback
- Submit handling with loading states
- Responsive design
- Accessibility features
- TypeScript interfaces
- Custom styling with Tailwind CSS

Include proper form state management and validation rules.`
    }
    
    if (lowerPrompt.includes('navbar') || lowerPrompt.includes('navigation') || lowerPrompt.includes('menu')) {
      return `Create a responsive navigation bar with:
- Mobile hamburger menu
- Desktop horizontal navigation
- Logo/brand area
- Active page highlighting
- Smooth animations and transitions
- Accessibility features (keyboard navigation, ARIA labels)
- Dark/light mode support
- Sticky positioning option
- TypeScript interfaces
- Tailwind CSS styling

Make it fully responsive and accessible.`
    }
    
    if (lowerPrompt.includes('card') || lowerPrompt.includes('component')) {
      return `Design a flexible card component system with:
- Multiple card variants (default, elevated, outlined, filled)
- Header, body, and footer sections
- Image support with proper aspect ratios
- Action buttons and links
- Hover effects and animations
- Responsive design
- TypeScript interfaces
- Tailwind CSS styling
- Accessibility features

Make it highly customizable and reusable.`
    }
    
    if (lowerPrompt.includes('api') || lowerPrompt.includes('fetch') || lowerPrompt.includes('data')) {
      return `Set up a robust data fetching system with:
- Custom React hooks for API calls
- Loading, error, and success states
- Request caching and deduplication
- Retry logic for failed requests
- TypeScript interfaces for API responses
- Error boundary integration
- Optimistic updates
- Request cancellation
- Environment-based API URLs

Include examples for GET, POST, PUT, DELETE operations.`
    }
    
    if (lowerPrompt.includes('style') || lowerPrompt.includes('css') || lowerPrompt.includes('design')) {
      return `Create a comprehensive design system with:
- Color palette with semantic color tokens
- Typography scale and font families
- Spacing system (margins, padding, gaps)
- Component variants and states
- Animation and transition utilities
- Responsive breakpoints
- Dark mode support
- CSS custom properties
- Tailwind CSS configuration
- Design tokens documentation

Ensure consistency and maintainability across the application.`
    }
    
    // Default enhancement for general requests
    return `Please create a professional, production-ready solution with the following requirements:

**Technical Requirements:**
- Use TypeScript for type safety
- Implement proper error handling
- Follow React best practices and hooks
- Use Tailwind CSS for styling
- Ensure accessibility (ARIA labels, keyboard navigation)
- Make it responsive and mobile-friendly
- Include proper loading and error states

**Code Quality:**
- Clean, readable, and well-commented code
- Proper component structure and separation of concerns
- Reusable and modular design
- Performance optimizations where applicable

**Specific Request:** ${prompt}

Please provide a complete, working implementation with examples and usage instructions.`
  }

  // Get file type for syntax highlighting
  const getFileType = (filename: string): string => {
    const extension = filename.split('.').pop()?.toLowerCase()
    switch (extension) {
      case 'tsx':
      case 'ts':
        return 'typescript'
      case 'jsx':
      case 'js':
        return 'javascript'
      case 'css':
        return 'css'
      case 'html':
        return 'html'
      case 'json':
        return 'json'
      case 'md':
        return 'markdown'
      default:
        return 'text'
    }
  }

  // Update file content in file system
  const updateFileContent = (filename: string, content: string) => {
    const updateNode = (nodes: FileNode[]): FileNode[] => {
      return nodes.map(node => {
        if (node.type === 'file' && node.name === filename) {
          return { ...node, content }
        } else if (node.children) {
          return { ...node, children: updateNode(node.children) }
        }
        return node
      })
    }
    setFileSystem(updateNode(fileSystem))
  }

  // Save file function
  const saveFile = () => {
    if (selectedFile) {
      updateFileContent(selectedFile.name, selectedFile.content || '')
      // In a real app, this would save to the backend
      console.log('File saved:', selectedFile.name)
    }
  }

  // Format file function
  const formatFile = () => {
    if (selectedFile && selectedFile.content) {
      // Simple formatting - in a real app, this would use a proper formatter
      const formatted = selectedFile.content
        .split('\n')
        .map(line => line.trim())
        .join('\n')
      
      setSelectedFile(prev => prev ? { ...prev, content: formatted } : null)
    }
  }

  // Handle keyboard shortcuts
  const handleKeyDown = (e: React.KeyboardEvent) => {
    if (e.ctrlKey || e.metaKey) {
      switch (e.key) {
        case 's':
          e.preventDefault()
          saveFile()
          break
        case 'f':
          e.preventDefault()
          formatFile()
          break
      }
    }
  }

  const toggleFolder = (folderName: string) => {
    setExpandedFolders(prev => {
      const newSet = new Set(prev)
      if (newSet.has(folderName)) {
        newSet.delete(folderName)
      } else {
        newSet.add(folderName)
      }
      return newSet
    })
  }

  const renderFileTree = (nodes: FileNode[], level = 0) => {
    return nodes.map((node, index) => (
      <div key={`${node.name}-${index}`} className="select-none">
        <div
          className={`flex items-center gap-2 py-1 px-2 hover:bg-slate-700/50 rounded cursor-pointer ${
            selectedFile?.name === node.name ? 'bg-slate-700/70' : ''
          }`}
          style={{ paddingLeft: `${level * 16 + 8}px` }}
          onClick={() => {
            if (node.type === 'file') {
              setSelectedFile(node)
            } else {
              toggleFolder(node.name)
            }
          }}
        >
          {node.type === 'folder' ? (
            <>
              {expandedFolders.has(node.name) ? (
                <ChevronDown className="w-4 h-4 text-slate-400" />
              ) : (
                <ChevronRight className="w-4 h-4 text-slate-400" />
              )}
              <Folder className="w-4 h-4 text-blue-400" />
            </>
          ) : (
            <>
              <div className="w-4 h-4" />
              <File className="w-4 h-4 text-slate-400" />
            </>
          )}
          <span className="text-sm text-slate-300">{node.name}</span>
        </div>
        {node.type === 'folder' && expandedFolders.has(node.name) && node.children && (
          <div>
            {renderFileTree(node.children, level + 1)}
          </div>
        )}
      </div>
    ))
  }

  return (
    <>
      <Head title={`${project.name} - Sandbox`} />
      
      <div className="min-h-screen bg-gradient-to-br from-[#0B0F1A] to-[#0A0E18] text-white">
        {/* Header */}
        <div className="border-b border-slate-800/50 bg-slate-900/30 backdrop-blur-sm">
          <div className="px-6 py-4">
            <div className="flex items-center justify-between">
              <div className="flex items-center gap-4">
                <button
                  onClick={() => router.visit('/projects')}
                  className="w-8 h-8 bg-slate-700/50 hover:bg-slate-600/50 rounded-lg flex items-center justify-center transition-colors duration-200"
                >
                  <ArrowLeft className="w-4 h-4 text-slate-300" />
                </button>
                
                <div>
                  <h1 className="text-xl font-bold text-white">{project.name}</h1>
                  <p className="text-sm text-slate-400">
                    {project.stack} • {project.model}
                  </p>
                </div>
                
                <div className="flex items-center gap-2">
                  <div className={`w-2 h-2 rounded-full ${
                    project.status === 'ready' ? 'bg-green-400' :
                    project.status === 'building' ? 'bg-yellow-400' :
                    'bg-red-400'
                  }`} />
                  <span className="text-sm text-slate-300 capitalize">{project.status}</span>
                </div>
              </div>

              <div className="flex items-center gap-3">
                {project.preview_url && (
                  <a
                    href={project.preview_url}
                    target="_blank"
                    rel="noopener noreferrer"
                    className="flex items-center gap-2 px-4 py-2 bg-orange-500/20 hover:bg-orange-500/30 border border-orange-500/30 rounded-lg transition-colors duration-200"
                  >
                    <ExternalLink className="w-4 h-4 text-orange-400" />
                    <span className="text-sm text-orange-400">Open Preview</span>
                  </a>
                )}
                
                <button className="flex items-center gap-2 px-4 py-2 bg-green-500/20 hover:bg-green-500/30 border border-green-500/30 rounded-lg transition-colors duration-200">
                  <Play className="w-4 h-4 text-green-400" />
                  <span className="text-sm text-green-400">Start</span>
                </button>
                
                <button className="flex items-center gap-2 px-4 py-2 bg-red-500/20 hover:bg-red-500/30 border border-red-500/30 rounded-lg transition-colors duration-200">
                  <Square className="w-4 h-4 text-red-400" />
                  <span className="text-sm text-red-400">Stop</span>
                </button>
                
                <button className="flex items-center gap-2 px-4 py-2 bg-slate-700/50 hover:bg-slate-600/50 border border-slate-600/30 rounded-lg transition-colors duration-200">
                  <RefreshCw className="w-4 h-4 text-slate-300" />
                  <span className="text-sm text-slate-300">Restart</span>
                </button>
              </div>
            </div>
          </div>
        </div>

        {/* Tab Navigation */}
        <div className="border-b border-slate-800/50 bg-slate-900/20">
          <div className="px-6">
            <div className="flex gap-1">
              {[
                { id: 'chat', label: 'Chat', icon: MessageCircle },
                { id: 'console', label: 'Console', icon: Terminal },
                { id: 'files', label: 'Files', icon: Folder },
                { id: 'preview', label: 'Preview', icon: Play }
              ].map(({ id, label, icon: Icon }) => (
                <button
                  key={id}
                  onClick={() => setActiveTab(id as any)}
                  className={`flex items-center gap-2 px-4 py-3 text-sm font-medium transition-colors duration-200 border-b-2 ${
                    activeTab === id
                      ? 'text-orange-400 border-orange-400'
                      : 'text-slate-400 border-transparent hover:text-slate-300'
                  }`}
                >
                  <Icon className="w-4 h-4" />
                  {label}
                </button>
              ))}
            </div>
          </div>
        </div>

        {/* Main Content */}
        <div className="flex-1 flex">
          {/* Chat Tab */}
          {activeTab === 'chat' && (
            <div className="flex-1 flex flex-col">
              <div className="flex-1 bg-slate-900/50 p-6">
                <div className="bg-slate-800/50 rounded-lg border border-slate-700/50 h-full overflow-hidden flex flex-col">
                  {/* Chat Header */}
                  <div className="flex items-center gap-3 px-4 py-3 border-b border-slate-700/50 bg-slate-800/30">
                    <div className="w-8 h-8 bg-gradient-to-br from-orange-400 to-orange-600 rounded-full flex items-center justify-center">
                      <Bot className="w-5 h-5 text-white" />
                    </div>
                    <div>
                      <h3 className="text-sm font-semibold text-white">AI Assistant</h3>
                      <p className="text-xs text-slate-400">Tell me what you want to build</p>
                    </div>
                    <div className="ml-auto">
                      <div className={`w-2 h-2 rounded-full ${isAiTyping ? 'bg-orange-400 animate-pulse' : 'bg-green-400'}`} />
                    </div>
                  </div>

                  {/* Messages */}
                  <div className="flex-1 overflow-y-auto p-4 space-y-4">
                    {chatMessages.length === 0 && (
                      <div className="text-center py-8">
                        <Bot className="w-12 h-12 text-slate-600 mx-auto mb-4" />
                        <p className="text-slate-400 mb-2">Start a conversation with AI</p>
                        <p className="text-sm text-slate-500">Ask me to build components, add features, or help with styling</p>
                      </div>
                    )}
                    
                    {chatMessages.map((message) => (
                      <div
                        key={message.id}
                        className={`flex gap-3 ${message.type === 'user' ? 'justify-end' : 'justify-start'}`}
                      >
                        {message.type === 'ai' && (
                          <div className="w-8 h-8 bg-gradient-to-br from-orange-400 to-orange-600 rounded-full flex items-center justify-center flex-shrink-0">
                            <Bot className="w-5 h-5 text-white" />
                          </div>
                        )}
                        
                        <div
                          className={`max-w-[80%] rounded-lg px-4 py-3 ${
                            message.type === 'user'
                              ? 'bg-orange-500 text-white'
                              : 'bg-slate-700 text-slate-200'
                          }`}
                        >
                          <div className="whitespace-pre-wrap text-sm leading-relaxed">
                            {message.content}
                          </div>
                          <div className={`text-xs mt-2 ${
                            message.type === 'user' ? 'text-orange-200' : 'text-slate-400'
                          }`}>
                            {message.timestamp.toLocaleTimeString()}
                          </div>
                        </div>
                        
                        {message.type === 'user' && (
                          <div className="w-8 h-8 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center flex-shrink-0">
                            <User className="w-5 h-5 text-white" />
                          </div>
                        )}
                      </div>
                    ))}
                    
                    {isAiTyping && (
                      <div className="flex gap-3 justify-start">
                        <div className="w-8 h-8 bg-gradient-to-br from-orange-400 to-orange-600 rounded-full flex items-center justify-center flex-shrink-0">
                          <Bot className="w-5 h-5 text-white" />
                        </div>
                        <div className="bg-slate-700 text-slate-200 rounded-lg px-4 py-3">
                          <div className="flex items-center gap-1">
                            <div className="w-2 h-2 bg-slate-400 rounded-full animate-bounce" />
                            <div className="w-2 h-2 bg-slate-400 rounded-full animate-bounce" style={{ animationDelay: '0.1s' }} />
                            <div className="w-2 h-2 bg-slate-400 rounded-full animate-bounce" style={{ animationDelay: '0.2s' }} />
                          </div>
                        </div>
                      </div>
                    )}
                  </div>

                  {/* Chat Toolbar */}
                  <div className="border-t border-slate-700/50 bg-slate-800/30 px-4 py-2">
                    <div className="flex items-center justify-between">
                      <div className="flex items-center gap-2">
                        <button
                          onClick={enhancePrompt}
                          disabled={!chatInput.trim() || isAiTyping || isEnhancing}
                          className={`px-3 py-2 rounded-lg transition-colors duration-200 flex items-center gap-2 text-sm ${
                            !chatInput.trim() || isAiTyping || isEnhancing
                              ? 'bg-slate-700/50 hover:bg-slate-600/50 text-slate-400 cursor-not-allowed'
                              : 'bg-orange-500/20 hover:bg-orange-500/30 text-orange-400'
                          }`}
                        >
                          {isEnhancing ? (
                            <div className={`w-4 h-4 border-2 rounded-full animate-spin ${
                              !chatInput.trim() || isAiTyping || isEnhancing
                                ? 'border-slate-400/30 border-t-slate-400'
                                : 'border-orange-400/30 border-t-orange-400'
                            }`} />
                          ) : (
                            <Sparkles className="w-4 h-4" />
                          )}
                          {isEnhancing ? 'Enhancing...' : 'Enhance Prompt'}
                        </button>
                        <div className="text-xs text-slate-500">
                          Make your request more detailed and professional
                        </div>
                      </div>
                      <div className="text-xs text-slate-500">
                        {chatInput.length > 0 && `${chatInput.length} characters`}
                      </div>
                    </div>
                  </div>

                  {/* Chat Input */}
                  <div className="border-t border-slate-700/50 bg-slate-800/30 p-4">
                    <form onSubmit={handleChatSubmit} className="flex gap-3">
                      <Textarea
                        value={chatInput}
                        onChange={(e) => setChatInput(e.target.value)}
                        placeholder="Tell me what you want to build..."
                        className="flex-1 bg-slate-700/50 text-white placeholder-slate-400 focus:ring-0 focus:shadow-lg focus:shadow-orange-500/25 resize-none min-h-[44px] max-h-32 overflow-hidden"
                        style={{
                          border: '1px solid rgba(249, 115, 22, 0.3)',
                          outline: 'none',
                          boxShadow: '0 0 8px rgba(249, 115, 22, 0.15)',
                          borderTopColor: 'rgba(249, 115, 22, 0.3)',
                          borderRightColor: 'rgba(249, 115, 22, 0.3)',
                          borderBottomColor: 'rgba(249, 115, 22, 0.3)',
                          borderLeftColor: 'rgba(249, 115, 22, 0.3)'
                        }}
                        onFocus={(e) => {
                          e.target.style.border = '1px solid rgba(249, 115, 22, 0.6)';
                          e.target.style.borderTopColor = 'rgba(249, 115, 22, 0.6)';
                          e.target.style.borderRightColor = 'rgba(249, 115, 22, 0.6)';
                          e.target.style.borderBottomColor = 'rgba(249, 115, 22, 0.6)';
                          e.target.style.borderLeftColor = 'rgba(249, 115, 22, 0.6)';
                          e.target.style.boxShadow = '0 0 12px rgba(249, 115, 22, 0.3)';
                        }}
                        onBlur={(e) => {
                          e.target.style.border = '1px solid rgba(249, 115, 22, 0.3)';
                          e.target.style.borderTopColor = 'rgba(249, 115, 22, 0.3)';
                          e.target.style.borderRightColor = 'rgba(249, 115, 22, 0.3)';
                          e.target.style.borderBottomColor = 'rgba(249, 115, 22, 0.3)';
                          e.target.style.borderLeftColor = 'rgba(249, 115, 22, 0.3)';
                          e.target.style.boxShadow = '0 0 8px rgba(249, 115, 22, 0.15)';
                        }}
                        disabled={isAiTyping || isEnhancing}
                        rows={3}
                        onInput={(e) => {
                          const target = e.target as HTMLTextAreaElement;
                          target.style.height = 'auto';
                          target.style.height = Math.min(target.scrollHeight, 128) + 'px';
                        }}
                        onKeyDown={(e) => {
                          if (e.key === 'Enter' && !e.shiftKey) {
                            e.preventDefault();
                            handleChatSubmit(e);
                          }
                        }}
                      />
                      <button
                        type="submit"
                        disabled={!chatInput.trim() || isAiTyping || isEnhancing}
                        className="px-6 py-3 bg-orange-500 hover:bg-orange-600 disabled:bg-slate-600 disabled:cursor-not-allowed text-white rounded-lg transition-colors duration-200 flex items-center gap-2 self-end"
                      >
                        <Send className="w-4 h-4" />
                        Send
                      </button>
                    </form>
                  </div>
                </div>
              </div>
            </div>
          )}

          {/* Console Tab */}
          {activeTab === 'console' && (
            <div className="flex-1 flex flex-col">
              <div className="flex-1 bg-slate-900/50 p-6">
                <div className="bg-black/50 rounded-lg border border-slate-700/50 h-full overflow-hidden flex flex-col">
                  <div className="flex items-center gap-2 px-4 py-2 border-b border-slate-700/50 bg-slate-800/50">
                    <div className="flex gap-2">
                      <div className="w-3 h-3 bg-red-500 rounded-full" />
                      <div className="w-3 h-3 bg-yellow-500 rounded-full" />
                      <div className="w-3 h-3 bg-green-500 rounded-full" />
                    </div>
                    <span className="text-sm text-slate-400 ml-2">Terminal</span>
                  </div>
                  
                  {/* Console Output */}
                  <div className="flex-1 p-4 overflow-y-auto">
                    <pre className="text-green-400 font-mono text-sm leading-relaxed">
                      {consoleOutput.map((line, index) => (
                        <div key={index}>{line}</div>
                      ))}
                      {isRunning && (
                        <div className="flex items-center gap-2 mt-2">
                          <div className="w-2 h-2 bg-green-400 rounded-full animate-pulse" />
                          <span>Ready</span>
                        </div>
                      )}
                    </pre>
                  </div>
                  
                  {/* Command Input */}
                  <div className="border-t border-slate-700/50 bg-slate-800/30">
                    <form onSubmit={handleCommandSubmit} className="flex items-center">
                      <span className="text-green-400 font-mono text-sm px-4 py-3">$</span>
                      <input
                        type="text"
                        value={commandInput}
                        onChange={(e) => setCommandInput(e.target.value)}
                        onKeyDown={handleConsoleKeyDown}
                        placeholder="Enter command..."
                        className="flex-1 bg-transparent text-green-400 font-mono text-sm py-3 pr-4 focus:outline-none placeholder-slate-500"
                        autoFocus
                      />
                    </form>
                  </div>
                </div>
              </div>
            </div>
          )}

          {/* Files Tab */}
          {activeTab === 'files' && (
            <div className="flex-1 flex">
              {/* File Tree */}
              <div className="w-80 border-r border-slate-800/50 bg-slate-900/30">
                <div className="p-4">
                  <h3 className="text-sm font-semibold text-slate-300 mb-4">Project Files</h3>
                  <div className="space-y-1">
                    {renderFileTree(fileSystem)}
                  </div>
                </div>
              </div>

              {/* File Content */}
              <div className="flex-1 bg-slate-900/50">
                {selectedFile ? (
                  <div className="h-full flex flex-col">
                    {/* File Header */}
                    <div className="px-4 py-3 border-b border-slate-700/50 bg-slate-800/50 flex items-center justify-between">
                      <div className="flex items-center gap-3">
                        <File className="w-4 h-4 text-slate-400" />
                        <span className="text-sm text-slate-300 font-medium">{selectedFile.name}</span>
                        <span className="text-xs text-slate-500">
                          {selectedFile.content ? `${selectedFile.content.split('\n').length} lines` : 'Binary file'}
                        </span>
                      </div>
                      <div className="flex items-center gap-2">
                        <button 
                          onClick={saveFile}
                          className="px-3 py-1 text-xs bg-green-500/20 hover:bg-green-500/30 text-green-400 rounded transition-colors"
                        >
                          Save
                        </button>
                        <button 
                          onClick={formatFile}
                          className="px-3 py-1 text-xs bg-blue-500/20 hover:bg-blue-500/30 text-blue-400 rounded transition-colors"
                        >
                          Format
                        </button>
                      </div>
                    </div>
                    
                    {/* Code Editor */}
                    <div className="flex-1 relative">
                      {selectedFile.content ? (
                        <div className="h-full flex">
                          {/* Line Numbers */}
                          <div className="bg-slate-800/50 border-r border-slate-700/50 px-3 py-4 text-slate-500 font-mono text-sm select-none">
                            {selectedFile.content.split('\n').map((_, index) => (
                              <div key={index} className="leading-6">
                                {index + 1}
                              </div>
                            ))}
                          </div>
                          
                          {/* Code Content */}
                          <div className="flex-1 overflow-auto">
                            <textarea
                              value={selectedFile.content}
                              onChange={(e) => {
                                // Update file content (in a real app, this would update the file system)
                                setSelectedFile(prev => prev ? { ...prev, content: e.target.value } : null)
                              }}
                              onKeyDown={handleKeyDown}
                              className="w-full h-full bg-transparent text-slate-300 font-mono text-sm leading-6 p-4 resize-none focus:outline-none"
                              spellCheck={false}
                              style={{ 
                                fontFamily: 'Monaco, Menlo, "Ubuntu Mono", monospace',
                                lineHeight: '1.5',
                                tabSize: 2
                              }}
                            />
                          </div>
                        </div>
                      ) : (
                        <div className="h-full flex items-center justify-center">
                          <div className="text-center">
                            <File className="w-12 h-12 text-slate-600 mx-auto mb-4" />
                            <p className="text-slate-400">Binary file - content not available</p>
                          </div>
                        </div>
                      )}
                    </div>
                    
                    {/* Status Bar */}
                    <div className="px-4 py-2 border-t border-slate-700/50 bg-slate-800/30 flex items-center justify-between text-xs text-slate-400">
                      <div className="flex items-center gap-4">
                        <span>UTF-8</span>
                        <span className="capitalize">{getFileType(selectedFile.name)}</span>
                        <span>LF</span>
                      </div>
                      <div className="flex items-center gap-4">
                        <span>Ln 1, Col 1</span>
                        <span>Spaces: 2</span>
                        <span className="text-slate-500">Ctrl+S to save</span>
                      </div>
                    </div>
                  </div>
                ) : (
                  <div className="h-full flex items-center justify-center">
                    <div className="text-center">
                      <File className="w-12 h-12 text-slate-600 mx-auto mb-4" />
                      <p className="text-slate-400">Select a file to view its contents</p>
                    </div>
                  </div>
                )}
              </div>
            </div>
          )}

          {/* Preview Tab */}
          {activeTab === 'preview' && (
            <div className="flex-1 bg-slate-900/50 p-6">
              <div className="h-full bg-white rounded-lg border border-slate-700/50 overflow-hidden">
                {project.preview_url ? (
                  <iframe
                    src={project.preview_url}
                    className="w-full h-full border-0"
                    title="Project Preview"
                  />
                ) : (
                  <div className="h-full flex items-center justify-center">
                    <div className="text-center">
                      <Play className="w-12 h-12 text-slate-600 mx-auto mb-4" />
                      <p className="text-slate-400 mb-2">Preview not available</p>
                      <p className="text-sm text-slate-500">The project is still building or there was an error</p>
                    </div>
                  </div>
                )}
              </div>
            </div>
          )}
        </div>
      </div>
    </>
  )
}
