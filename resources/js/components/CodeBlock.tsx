import React, { useState } from 'react'
import { Copy, Check, ChevronDown, ChevronUp } from 'lucide-react'
import { getLanguageDisplayName } from '../utils/codeBlockParser'

interface CodeBlockProps {
  content: string
  language: string
  className?: string
}

export default function CodeBlock({ content, language, className = '' }: CodeBlockProps) {
  const [copied, setCopied] = useState(false)
  const [expanded, setExpanded] = useState(false)
  
  const displayName = getLanguageDisplayName(language)
  const isLongCode = content.length > 500
  const displayContent = isLongCode && !expanded ? content.slice(0, 500) + '\n...' : content

  const copyToClipboard = async () => {
    try {
      await navigator.clipboard.writeText(content)
      setCopied(true)
      setTimeout(() => setCopied(false), 2000)
    } catch (err) {
      console.error('Failed to copy code:', err)
    }
  }

  return (
    <div className={`bg-slate-900 border border-slate-700 rounded-lg overflow-hidden ${className}`}>
      {/* Header */}
      <div className="flex items-center justify-between px-4 py-2 bg-slate-800 border-b border-slate-700">
        <div className="flex items-center gap-2">
          <span className="text-xs font-medium text-slate-300">{displayName}</span>
          {isLongCode && (
            <button
              onClick={() => setExpanded(!expanded)}
              className="flex items-center gap-1 text-xs text-slate-400 hover:text-slate-300 transition-colors"
            >
              {expanded ? (
                <>
                  <ChevronUp className="w-3 h-3" />
                  Show Less
                </>
              ) : (
                <>
                  <ChevronDown className="w-3 h-3" />
                  Show More
                </>
              )}
            </button>
          )}
        </div>
        
        <button
          onClick={copyToClipboard}
          className="flex items-center gap-1 px-2 py-1 text-xs text-slate-400 hover:text-slate-300 transition-colors rounded"
        >
          {copied ? (
            <>
              <Check className="w-3 h-3" />
              Copied!
            </>
          ) : (
            <>
              <Copy className="w-3 h-3" />
              Copy
            </>
          )}
        </button>
      </div>

      {/* Code Content */}
      <div className="relative">
        <pre className="p-4 overflow-x-auto">
          <code className={`text-sm leading-relaxed ${getCodeStyles(language)}`}>
            {displayContent}
          </code>
        </pre>
      </div>
    </div>
  )
}

/**
 * Get basic syntax highlighting styles for different languages
 */
function getCodeStyles(language: string): string {
  const baseStyles = 'font-mono'
  
  // Basic color coding for common languages
  switch (language.toLowerCase()) {
    case 'javascript':
    case 'js':
    case 'typescript':
    case 'ts':
    case 'jsx':
    case 'tsx':
      return `${baseStyles} text-blue-300`
    case 'html':
      return `${baseStyles} text-orange-300`
    case 'css':
    case 'scss':
    case 'sass':
    case 'less':
      return `${baseStyles} text-green-300`
    case 'json':
      return `${baseStyles} text-yellow-300`
    case 'python':
    case 'py':
      return `${baseStyles} text-purple-300`
    case 'php':
      return `${baseStyles} text-pink-300`
    case 'bash':
    case 'shell':
    case 'sh':
      return `${baseStyles} text-cyan-300`
    case 'sql':
      return `${baseStyles} text-indigo-300`
    case 'yaml':
    case 'yml':
      return `${baseStyles} text-emerald-300`
    case 'xml':
      return `${baseStyles} text-red-300`
    case 'markdown':
    case 'md':
      return `${baseStyles} text-gray-300`
    default:
      return `${baseStyles} text-slate-300`
  }
}
