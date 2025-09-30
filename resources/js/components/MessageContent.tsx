import React from 'react'
import { parseCodeBlocks } from '../utils/codeBlockParser'
import CodeBlock from './CodeBlock'
import MarkdownMessage from './MarkdownMessage'

interface MessageContentProps {
  content: string
  className?: string
}

export default function MessageContent({ content, className = '' }: MessageContentProps) {
  const blocks = parseCodeBlocks(content)

  return (
    <div className={`space-y-3 ${className}`}>
      {blocks.map((block, index) => {
        if (block.type === 'code') {
          return (
            <CodeBlock
              key={index}
              content={block.content}
              language={block.language || 'text'}
            />
          )
        }

        return (
          <MarkdownMessage
            key={index}
            content={block.content}
          />
        )
      })}
    </div>
  )
}
