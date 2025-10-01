import { useEffect } from 'react'
import { router } from '@inertiajs/react'

interface KeyboardShortcut {
  key: string
  ctrlKey?: boolean
  metaKey?: boolean
  shiftKey?: boolean
  altKey?: boolean
  action: () => void
  description: string
}

export function useKeyboardShortcuts(shortcuts: KeyboardShortcut[]) {
  useEffect(() => {
    const handleKeyDown = (event: KeyboardEvent) => {
      const matchingShortcut = shortcuts.find(shortcut => {
        return (
          shortcut.key.toLowerCase() === event.key.toLowerCase() &&
          !!shortcut.ctrlKey === event.ctrlKey &&
          !!shortcut.metaKey === event.metaKey &&
          !!shortcut.shiftKey === event.shiftKey &&
          !!shortcut.altKey === event.altKey
        )
      })

      if (matchingShortcut) {
        event.preventDefault()
        matchingShortcut.action()
      }
    }

    document.addEventListener('keydown', handleKeyDown)
    return () => document.removeEventListener('keydown', handleKeyDown)
  }, [shortcuts])
}

// Common shortcuts for the application
export const useAppKeyboardShortcuts = () => {
  const shortcuts: KeyboardShortcut[] = [
    {
      key: 'n',
      ctrlKey: true,
      action: () => {
        // Trigger new project modal
        const event = new CustomEvent('openNewProjectModal')
        window.dispatchEvent(event)
      },
      description: 'Create new project'
    },
    {
      key: 's',
      ctrlKey: true,
      action: () => {
        // Trigger new sandbox modal
        const event = new CustomEvent('openNewSandboxModal')
        window.dispatchEvent(event)
      },
      description: 'Create new sandbox'
    },
    {
      key: '/',
      action: () => {
        // Focus search input
        const searchInput = document.querySelector('input[placeholder*="Search"]') as HTMLInputElement
        if (searchInput) {
          searchInput.focus()
        }
      },
      description: 'Focus search'
    },
    {
      key: 'Escape',
      action: () => {
        // Close any open modals
        const modals = document.querySelectorAll('[role="dialog"]')
        modals.forEach(modal => {
          const closeButton = modal.querySelector('[data-dismiss="modal"]') as HTMLButtonElement
          if (closeButton) {
            closeButton.click()
          }
        })
      },
      description: 'Close modals'
    },
    {
      key: 'h',
      ctrlKey: true,
      action: () => {
        // Show keyboard shortcuts help
        const event = new CustomEvent('showKeyboardShortcuts')
        window.dispatchEvent(event)
      },
      description: 'Show keyboard shortcuts'
    }
  ]

  useKeyboardShortcuts(shortcuts)
}

// Chat-specific shortcuts
export const useChatKeyboardShortcuts = (onSendMessage: () => void) => {
  const shortcuts: KeyboardShortcut[] = [
    {
      key: 'Enter',
      shiftKey: true,
      action: onSendMessage,
      description: 'Send message'
    },
    {
      key: 'Escape',
      action: () => {
        // Clear chat input
        const chatInput = document.querySelector('textarea[placeholder*="message"]') as HTMLTextAreaElement
        if (chatInput) {
          chatInput.value = ''
          chatInput.blur()
        }
      },
      description: 'Clear chat input'
    }
  ]

  useKeyboardShortcuts(shortcuts)
}

// Sandbox-specific shortcuts
export const useSandboxKeyboardShortcuts = () => {
  const shortcuts: KeyboardShortcut[] = [
    {
      key: '1',
      ctrlKey: true,
      action: () => {
        // Switch to Chat tab
        const chatTab = document.querySelector('[data-tab="chat"]') as HTMLElement
        if (chatTab) {
          chatTab.click()
        }
      },
      description: 'Switch to Chat tab'
    },
    {
      key: '2',
      ctrlKey: true,
      action: () => {
        // Switch to Console tab
        const consoleTab = document.querySelector('[data-tab="console"]') as HTMLElement
        if (consoleTab) {
          consoleTab.click()
        }
      },
      description: 'Switch to Console tab'
    },
    {
      key: '3',
      ctrlKey: true,
      action: () => {
        // Switch to Files tab
        const filesTab = document.querySelector('[data-tab="files"]') as HTMLElement
        if (filesTab) {
          filesTab.click()
        }
      },
      description: 'Switch to Files tab'
    },
    {
      key: 'r',
      ctrlKey: true,
      action: () => {
        // Refresh container
        const refreshButton = document.querySelector('[data-action="refresh-container"]') as HTMLElement
        if (refreshButton) {
          refreshButton.click()
        }
      },
      description: 'Refresh container'
    }
  ]

  useKeyboardShortcuts(shortcuts)
}
