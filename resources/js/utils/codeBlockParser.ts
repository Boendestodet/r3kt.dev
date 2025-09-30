/**
 * Parse text content and extract code blocks with language detection
 */
export interface CodeBlock {
  type: 'text' | 'code'
  content: string
  language?: string
}

export function parseCodeBlocks(text: string): CodeBlock[] {
  const blocks: CodeBlock[] = []
  const codeBlockRegex = /```(\w+)?\n([\s\S]*?)```/g
  
  let lastIndex = 0
  let match

  while ((match = codeBlockRegex.exec(text)) !== null) {
    // Add text before code block
    if (match.index > lastIndex) {
      const textContent = text.slice(lastIndex, match.index).trim()
      if (textContent) {
        blocks.push({
          type: 'text',
          content: textContent
        })
      }
    }

    // Add code block
    const language = match[1] || 'text'
    const codeContent = match[2].trim()
    
    blocks.push({
      type: 'code',
      content: codeContent,
      language: language
    })

    lastIndex = match.index + match[0].length
  }

  // Add remaining text after last code block
  if (lastIndex < text.length) {
    const textContent = text.slice(lastIndex).trim()
    if (textContent) {
      blocks.push({
        type: 'text',
        content: textContent
      })
    }
  }

  // If no code blocks found, return the entire text as a single text block
  if (blocks.length === 0) {
    blocks.push({
      type: 'text',
      content: text
    })
  }

  return blocks
}

/**
 * Get syntax highlighting class for language
 */
export function getLanguageClass(language: string): string {
  const languageMap: Record<string, string> = {
    'javascript': 'language-javascript',
    'js': 'language-javascript',
    'typescript': 'language-typescript',
    'ts': 'language-typescript',
    'jsx': 'language-jsx',
    'tsx': 'language-tsx',
    'html': 'language-html',
    'css': 'language-css',
    'scss': 'language-scss',
    'sass': 'language-sass',
    'less': 'language-less',
    'json': 'language-json',
    'python': 'language-python',
    'py': 'language-python',
    'php': 'language-php',
    'java': 'language-java',
    'c': 'language-c',
    'cpp': 'language-cpp',
    'csharp': 'language-csharp',
    'cs': 'language-csharp',
    'go': 'language-go',
    'rust': 'language-rust',
    'sql': 'language-sql',
    'bash': 'language-bash',
    'shell': 'language-bash',
    'sh': 'language-bash',
    'yaml': 'language-yaml',
    'yml': 'language-yaml',
    'xml': 'language-xml',
    'markdown': 'language-markdown',
    'md': 'language-markdown',
    'dockerfile': 'language-dockerfile',
    'docker': 'language-dockerfile',
    'nginx': 'language-nginx',
    'apache': 'language-apache',
    'vim': 'language-vim',
    'viml': 'language-vim',
    'diff': 'language-diff',
    'git': 'language-git',
    'text': 'language-text',
    'plain': 'language-text',
  }

  return languageMap[language.toLowerCase()] || 'language-text'
}

/**
 * Get display name for language
 */
export function getLanguageDisplayName(language: string): string {
  const displayNames: Record<string, string> = {
    'javascript': 'JavaScript',
    'js': 'JavaScript',
    'typescript': 'TypeScript',
    'ts': 'TypeScript',
    'jsx': 'JSX',
    'tsx': 'TSX',
    'html': 'HTML',
    'css': 'CSS',
    'scss': 'SCSS',
    'sass': 'Sass',
    'less': 'Less',
    'json': 'JSON',
    'python': 'Python',
    'py': 'Python',
    'php': 'PHP',
    'java': 'Java',
    'c': 'C',
    'cpp': 'C++',
    'csharp': 'C#',
    'cs': 'C#',
    'go': 'Go',
    'rust': 'Rust',
    'sql': 'SQL',
    'bash': 'Bash',
    'shell': 'Shell',
    'sh': 'Shell',
    'yaml': 'YAML',
    'yml': 'YAML',
    'xml': 'XML',
    'markdown': 'Markdown',
    'md': 'Markdown',
    'dockerfile': 'Dockerfile',
    'docker': 'Docker',
    'nginx': 'Nginx',
    'apache': 'Apache',
    'vim': 'Vim',
    'viml': 'Vim',
    'diff': 'Diff',
    'git': 'Git',
    'text': 'Text',
    'plain': 'Text',
  }

  return displayNames[language.toLowerCase()] || language.toUpperCase()
}
