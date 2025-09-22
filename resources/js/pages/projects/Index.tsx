"use client"

import type React from "react"
import { useState, useEffect } from "react"
import { router, useForm } from "@inertiajs/react"

const Plus = ({ className }: { className?: string }) => (
  <svg className={className} fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 4v16m8-8H4" />
  </svg>
)

const Check = ({ className }: { className?: string }) => (
  <svg className={className} fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M5 13l4 4L19 7" />
  </svg>
)

const ChevronDown = ({ className }: { className?: string }) => (
  <svg className={className} fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 9l-7 7-7-7" />
  </svg>
)

const Box = ({ className }: { className?: string }) => (
  <svg className={className} fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path
      strokeLinecap="round"
      strokeLinejoin="round"
      strokeWidth={2}
      d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"
    />
  </svg>
)

const Flame = ({ className }: { className?: string }) => (
  <svg className={className} fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 2s3 3 3 9-3 9-3 9-3-3-3-9 3-9 3-9z" />
    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 16s1-2 1-4-1-4-1-4" />
  </svg>
)

const Search = ({ className }: { className?: string }) => (
  <svg className={className} fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <circle cx="11" cy="11" r="8" />
    <path d="m21 21-4.35-4.35" />
  </svg>
)

const Menu = ({ className }: { className?: string }) => (
  <svg className={className} fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4 6h16M4 12h16M4 18h16" />
  </svg>
)

const X = ({ className }: { className?: string }) => (
  <svg className={className} fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" />
  </svg>
)

const ChevronLeft = ({ className }: { className?: string }) => (
  <svg className={className} fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 19l-7-7 7-7" />
  </svg>
)

const ChevronRight = ({ className }: { className?: string }) => (
  <svg className={className} fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5l7 7-7 7" />
  </svg>
)

const Badge = ({
  children,
  variant = "default",
  className = "",
  role,
}: {
  children: React.ReactNode
  variant?: "ready" | "selected" | "hot" | "coming-soon" | "default"
  className?: string
  role?: string
}) => {
  const variants = {
    ready: "bg-emerald-500/20 text-emerald-400 border border-emerald-500/30",
    selected: "bg-emerald-500/20 text-emerald-400 border border-emerald-500/30",
    hot: "bg-orange-500 text-white",
    "coming-soon": "bg-slate-600/50 text-slate-400 border border-slate-600/30",
    default: "bg-slate-700 text-slate-300",
  }

  return (
    <span
      className={`inline-flex items-center px-2 py-1 text-xs font-medium rounded-xl ${variants[variant]} ${className}`}
      role={role}
    >
      {children}
    </span>
  )
}

const Button = ({
  children,
  variant = "ghost",
  size = "default",
  className = "",
  onClick,
  disabled = false,
}: {
  children: React.ReactNode
  variant?: "ghost" | "default"
  size?: "sm" | "default"
  className?: string
  onClick?: () => void
  disabled?: boolean
}) => {
  const baseClasses =
    "inline-flex items-center justify-center font-medium transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-orange-400/40 disabled:opacity-50 disabled:pointer-events-none"
  const variants = {
    ghost: "hover:bg-white/5 hover:ring-1 hover:ring-slate-700/60",
    default: "bg-slate-700 hover:bg-slate-600",
  }
  const sizes = {
    sm: "h-6 px-2 text-xs rounded-md",
    default: "h-9 px-4 py-2 rounded-lg",
  }

  return (
    <button
      className={`${baseClasses} ${variants[variant]} ${sizes[size]} ${className}`}
      onClick={onClick}
      disabled={disabled}
    >
      {children}
    </button>
  )
}

const Card = ({
  children,
  className = "",
  onClick,
  selected = false,
  disabled = false,
}: {
  children: React.ReactNode
  className?: string
  onClick?: () => void
  selected?: boolean
  disabled?: boolean
}) => {
  const baseClasses = "rounded-2xl border transition-all duration-200"
  const stateClasses = selected
    ? "ring-2 ring-orange-400/40 border-orange-400/60 bg-slate-800/80"
    : disabled
      ? "border-slate-700/60 bg-slate-800/30 opacity-60"
      : "border-slate-700/60 bg-slate-800/50 hover:ring-1 hover:ring-slate-700/60 hover:bg-slate-800/70"

  return (
    <div
      className={`${baseClasses} ${stateClasses} ${onClick && !disabled ? "cursor-pointer" : ""} ${className}`}
      onClick={disabled ? undefined : onClick}
    >
      {children}
    </div>
  )
}

export default function VibecodeSandboxPage() {
  const [selectedModel, setSelectedModel] = useState<string | null>(null) // Changed to null initially
  const [selectedStack, setSelectedStack] = useState<string | null>(null)
  const [activeTab, setActiveTab] = useState("Modern Web")
  const [hoveredModel, setHoveredModel] = useState<string | null>(null)
  const [hoveredStack, setHoveredStack] = useState<string | null>(null)
  const [isDeploying, setIsDeploying] = useState(false)
  const [searchQuery, setSearchQuery] = useState("")
  const [showCreditsDropdown, setShowCreditsDropdown] = useState(false)
  const [showNewSandboxModal, setShowNewSandboxModal] = useState(false)
  const [showNewProjectModal, setShowNewProjectModal] = useState(false)
  const [deploymentProgress, setDeploymentProgress] = useState(0)
  const [sidebarCollapsed, setSidebarCollapsed] = useState(false)
  const [sidebarHidden, setSidebarHidden] = useState(false)
  const [showDeploymentModal, setShowDeploymentModal] = useState(false)
  
  // New project creation states
  const [projectName, setProjectName] = useState("")
  const [projectDescription, setProjectDescription] = useState("")
  const [projectPrompt, setProjectPrompt] = useState("")
  const [isCreatingProject, setIsCreatingProject] = useState(false)
  const [creationProgress, setCreationProgress] = useState(0)
  const [creationStatus, setCreationStatus] = useState("")
  const [createdProject, setCreatedProject] = useState<{id: number, name: string} | null>(null)
  const [isFromDeploy, setIsFromDeploy] = useState(false)
  const [validationError, setValidationError] = useState("")
  const [nameError, setNameError] = useState("")
  const [isCheckingName, setIsCheckingName] = useState(false)
  const [creationState, setCreationState] = useState<{
    isActive: boolean
    projectId?: number
    promptId?: number
    step: 'project' | 'ai' | 'docker' | 'start' | 'complete'
  } | null>(null)

  // Inertia forms for all operations
  const projectForm = useForm({
    name: '',
    description: '',
    settings: {
      ai_model: 'gpt-4',
      stack: 'nextjs',
      auto_deploy: true
    }
  })

  const aiForm = useForm({
    prompt: '',
    auto_start_container: true
  })

  const deployForm = useForm({})
  const startForm = useForm({})

  // Helper function to get CSRF token
  const getCsrfToken = (): string => {
    // Try meta tag first
    let csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
    
    // Fallback: try to get from cookies
    if (!csrfToken) {
      const cookies = document.cookie.split(';')
      for (let cookie of cookies) {
        const [name, value] = cookie.trim().split('=')
        if (name === 'XSRF-TOKEN') {
          csrfToken = decodeURIComponent(value)
          break
        }
      }
    }
    
    // If still no token, try to get it from the page
    if (!csrfToken) {
      // Try to get from window object if available
      csrfToken = (window as any).Laravel?.csrfToken || (window as any).csrfToken
    }
    
    if (!csrfToken) {
      console.error('CSRF token not found in meta tag, cookies, or window object')
      throw new Error('CSRF token not found. Please refresh the page and try again.')
    }
    
    return csrfToken
  }

  // Persist creation state to localStorage
  const saveCreationState = (state: typeof creationState) => {
    if (state) {
      localStorage.setItem('projectCreationState', JSON.stringify(state))
    } else {
      localStorage.removeItem('projectCreationState')
    }
  }

  // Load creation state from localStorage on mount (only once)
  useEffect(() => {
    const savedState = localStorage.getItem('projectCreationState')
    if (savedState) {
      try {
        const state = JSON.parse(savedState)
        if (state.isActive) {
          console.log('Resuming project creation from saved state:', state)
          setCreationState(state)
          setIsCreatingProject(true)
          setCreationProgress(20) // Start from a reasonable point
          setCreationStatus("Resuming project creation...")
          
          // Open the modal if it's not already open
          setShowNewProjectModal(true)
          
          // Resume the creation flow based on the step
          if (state.step === 'ai' && state.projectId && state.promptId) {
            waitForAIGeneration(state.projectId, state.promptId)
          } else if (state.step === 'docker' && state.projectId) {
            deployToDocker(state.projectId)
          } else if (state.step === 'start' && state.projectId) {
            startContainer(state.projectId)
          }
        }
      } catch (error) {
        console.error('Failed to parse saved creation state:', error)
        localStorage.removeItem('projectCreationState')
      }
    }
  }, []) // Only run once on mount

  // Save creation state whenever it changes
  useEffect(() => {
    saveCreationState(creationState)
  }, [creationState])

  // Note: Removed beforeunload protection as it was interfering with browser refresh

  // Debug modal state changes
  useEffect(() => {
    console.log('Modal state changed:', {
      showNewProjectModal,
      isCreatingProject,
      creationState: creationState?.isActive ? creationState : null
    })
  }, [showNewProjectModal, isCreatingProject, creationState])

  // Prevent modal from closing if we're in creation state
  useEffect(() => {
    if (isCreatingProject && creationState?.isActive && !showNewProjectModal) {
      console.log('Preventing modal from closing during creation - reopening modal')
      setShowNewProjectModal(true)
    }
  }, [isCreatingProject, creationState, showNewProjectModal])

  // Function to safely close modal (only when not creating)
  const safeCloseModal = () => {
    if (isCreatingProject && creationState?.isActive) {
      console.log('Cannot close modal during active project creation')
      return false
    }
    
    console.log('Closing modal safely')
    setShowNewProjectModal(false)
    // Reset form when closing
    setProjectName("")
    setProjectDescription("")
    setProjectPrompt("")
    setValidationError("")
    setNameError("")
    setIsCreatingProject(false)
    setCreationProgress(0)
    setCreationStatus("")
    setCreatedProject(null)
    setCreationState(null)
    return true
  }

  // Debounced project name checking
  useEffect(() => {
    const timeoutId = setTimeout(() => {
      if (projectName.trim()) {
        checkProjectName(projectName)
      } else {
        setNameError("")
      }
    }, 500) // 500ms delay

    return () => clearTimeout(timeoutId)
  }, [projectName])

  const models = [
    {
      name: "Claude Code",
      description:
        "Powered by Anthropic. Advanced AI coding assistant that works directly in your terminal with deep codebase understanding and multi-file editing capabilities.",
      comingSoon: false,
    },
    {
      name: "Codex (GPT-5)",
      description:
        "Powered by OpenAI. State-of-the-art coding model with exceptional performance on software engineering tasks, achieving 74.9% on SWE-bench Verified.",
      comingSoon: false,
    },
    {
      name: "Gemini CLI",
      description:
        "Powered by Google. Open-source AI agent with massive 1 million token context window and built-in tools for Google Search grounding.",
      comingSoon: false,
    },
    {
      name: "OpenCode",
      description:
        "Powered by Open Source Community. Collection of state-of-the-art open-source coding models including StarCoder2 and CodeLlama.",
      comingSoon: true,
    },
    {
      name: "Cursor™ Agent",
      description:
        "Powered by Anysphere. AI-first code editor with autonomous background agents and advanced codebase understanding.",
      comingSoon: true,
    },
  ]

  const tabs = ["Modern Web", "Backend", "Game Dev", "Traditional", "Static", "Manual"]

  const modernWebStacks = [
    { name: "Next.js", description: "Next.js with App Router", hot: true },
    { name: "Vite + React", description: "Vite + React + TypeScript", hot: true },
    { name: "SvelteKit", description: "SvelteKit with TypeScript", hot: false },
    { name: "Vite + Vue", description: "Vite + Vue + TypeScript", hot: true },
    { name: "Astro", description: "Astro with TypeScript", comingSoon: true },
    { name: "Nuxt 3", description: "Nuxt 3 with TypeScript", comingSoon: true },
  ]

  const backendStacks = [
    { name: "Node.js + Express", description: "Express.js with TypeScript", hot: true },
    { name: "Python + FastAPI", description: "FastAPI with async support", hot: true },
    { name: "Go + Gin", description: "Gin framework with Go", hot: false },
    { name: "Rust + Axum", description: "Axum web framework", comingSoon: true },
  ]

  const gameDevStacks = [
    { name: "Unity + C#", description: "Unity game engine", hot: true },
    { name: "Unreal + C++", description: "Unreal Engine 5", hot: false },
    { name: "Godot + GDScript", description: "Godot 4.x engine", comingSoon: true },
  ]

  const traditionalStacks = [
    { name: "PHP + Laravel", description: "Laravel framework", hot: false },
    { name: "Java + Spring", description: "Spring Boot", hot: false },
    { name: "C# + .NET", description: ".NET Core", comingSoon: true },
  ]

  const staticStacks = [
    { name: "Jekyll", description: "Ruby-based static site", hot: false },
    { name: "Hugo", description: "Go-based static site", hot: false },
    { name: "11ty", description: "JavaScript static site", comingSoon: true },
  ]

  const getStacksForTab = (tab: string) => {
    switch (tab) {
      case "Modern Web":
        return modernWebStacks
      case "Backend":
        return backendStacks
      case "Game Dev":
        return gameDevStacks
      case "Traditional":
        return traditionalStacks
      case "Static":
        return staticStacks
      case "Manual":
        return []
      default:
        return []
    }
  }

  const handleDeploy = () => {
    if (selectedModel && selectedStack) {
      // Open the new project creation modal instead of deployment modal
      setShowNewProjectModal(true)
      setProjectName("")
      setProjectDescription("")
      setProjectPrompt("")
      setIsCreatingProject(false)
      setCreationProgress(0)
      setCreationStatus("")
      setCreatedProject(null)
      setIsFromDeploy(true)
      setValidationError("")
    }
  }

  const handleNewSandbox = () => {
    setShowNewSandboxModal(true)
    setTimeout(() => {
      setShowNewSandboxModal(false)
      alert("New sandbox created!")
    }, 1500)
  }

  const handleNewProject = () => {
    setShowNewProjectModal(true)
    setProjectName("")
    setProjectDescription("")
    setProjectPrompt("")
    setIsCreatingProject(false)
    setCreationProgress(0)
    setCreationStatus("")
    setCreatedProject(null)
    setIsFromDeploy(false)
    setValidationError("")
    setNameError("")
  }

  const checkProjectName = async (name: string) => {
    if (!name.trim()) {
      setNameError("")
      return
    }

    setIsCheckingName(true)
    try {
        // Use fetch for real-time name checking (no page reload)
        const response = await fetch(`/api/projects/check-name?name=${encodeURIComponent(name)}`, {
          method: 'GET',
          headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
          }
        })

        if (response.ok) {
          const data = await response.json()
          if (data.exists) {
            setNameError("A project with this name already exists. Please choose a different name.")
          } else {
            setNameError("")
          }
        }
    } catch (error) {
      console.error('Error checking project name:', error)
    } finally {
      setIsCheckingName(false)
    }
  }

  const handleCreateProject = () => {
    setValidationError("")
    
    if (!projectName.trim() || !projectPrompt.trim()) {
      setValidationError("Please fill in project name and AI prompt")
      return
    }

    if (nameError) {
      setValidationError("Please fix the project name error before creating the project")
      return
    }

    if (!selectedModel || !selectedStack) {
      setValidationError("Please select both an AI model and a stack from the main interface")
      return
    }

    setIsCreatingProject(true)
    setCreationProgress(0)
    setCreationStatus("Initializing project...")

    // Set initial creation state
    setCreationState({
      isActive: true,
      step: 'project'
    })

    // Step 1: Create the project using Inertia form (no page reload)
    setCreationProgress(5)
    setCreationStatus("Creating project and setting up the docker container...")
    
    console.log('Creating project with:', {
      name: projectName,
      description: projectDescription || `AI-generated project: ${projectPrompt}`,
      settings: {
        ai_model: selectedModel,
        stack: selectedStack,
        auto_deploy: true
      }
    })
    
    // Use router.post with preserveState to prevent page refresh and ensure data is sent
    router.post('/projects', {
      name: projectName,
      description: projectDescription || `AI-generated project: ${projectPrompt}`,
      settings: {
        ai_model: selectedModel,
        stack: selectedStack,
        auto_deploy: true
      }
    }, {
      preserveState: true,
      preserveScroll: true,
      only: ['createdProject'],
      onStart: () => {
        console.log('Starting project creation...')
        console.log('Data being sent:', {
          name: projectName,
          description: projectDescription || `AI-generated project: ${projectPrompt}`,
          settings: {
            ai_model: selectedModel,
            stack: selectedStack,
            auto_deploy: true
          }
        })
      },
      onProgress: (progress: any) => {
        console.log('Project creation progress:', progress)
      },
      onSuccess: (page: any) => {
        console.log('Project created successfully:', page)
        // Get project from props
        const project = page.props?.createdProject
        if (project) {
          setCreatedProject({ id: project.id, name: project.name })
          setCreationProgress(10)
          setCreationStatus("Checking container...")
          
          // Update creation state
          setCreationState({
            isActive: true,
            projectId: project.id,
            step: 'project'
          })
          
          // Step 1.5: Verify project setup (database + files)
          verifyProjectSetup(project.id)
        }
      },
      onError: (errors: any) => {
        console.error('Project creation failed:', errors)
        setCreationStatus(`Error: ${Object.values(errors).join(', ')}`)
        setIsCreatingProject(false)
        setValidationError(Object.values(errors).join(', '))
        // Clear creation state on failure
        setCreationState(null)
      }
    })
  }

  const verifyProjectSetup = (projectId: number) => {
    console.log('Verifying project setup for project:', projectId)
    
    // Add a timeout fallback in case verification gets stuck
    const verificationTimeout = setTimeout(() => {
      console.warn('Verification timeout - proceeding anyway')
      setCreationProgress(15)
      setCreationStatus("Verification timeout - proceeding to code generation...")
      
      setTimeout(() => {
        setCreationProgress(20)
        setCreationStatus("Generating the code...")
        generateAICode(projectId)
      }, 1000)
    }, 5000) // 5 second timeout
    
    // Use fetch for verification to avoid Inertia complications
    fetch(`/api/projects/${projectId}/verify-setup`, {
      method: 'GET',
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      }
    })
    .then(response => {
      console.log('Verification response status:', response.status)
      console.log('Verification response headers:', response.headers)
      return response.json()
    })
    .then(data => {
      console.log('Project verification completed:', data)
      clearTimeout(verificationTimeout) // Clear the timeout since we got a response
      const verification = data.verification
      
      if (verification) {
        console.log('Verification results:', verification)
        
        if (verification.overall_status === 'success') {
          setCreationProgress(15)
          setCreationStatus("Project verified successfully! Generating code...")
          
          // Add a small delay to show the verification success
          setTimeout(() => {
            setCreationProgress(20)
            setCreationStatus("Generating the code...")
            
            // Step 2: Generate AI code
            generateAICode(projectId)
          }, 1000)
        } else if (verification.overall_status === 'partial') {
          setCreationProgress(15)
          setCreationStatus("Project partially verified. Some files missing, but continuing...")
          
          // Add a small delay to show the partial verification
          setTimeout(() => {
            setCreationProgress(20)
            setCreationStatus("Generating the code...")
            
            // Step 2: Generate AI code
            generateAICode(projectId)
          }, 1000)
        } else {
          setCreationStatus(`Error: Project verification failed. Database: ${verification.database_exists ? 'OK' : 'FAIL'}, Folder: ${verification.folder_exists ? 'OK' : 'FAIL'}, Files: ${verification.all_files_present ? 'OK' : 'FAIL'}`)
          setIsCreatingProject(false)
          setValidationError('Project verification failed. Please try again.')
          setCreationState(null)
        }
      } else {
        console.error('No verification data received')
        setCreationStatus("Error: No verification data received")
        setIsCreatingProject(false)
        setValidationError('Project verification failed. Please try again.')
        setCreationState(null)
      }
    })
    .catch(error => {
      console.error('Project verification failed:', error)
      clearTimeout(verificationTimeout) // Clear the timeout on error too
      setCreationStatus(`Error: Project verification failed - ${error.message}`)
      setIsCreatingProject(false)
      setValidationError('Project verification failed. Please try again.')
      setCreationState(null)
    })
  }

  const generateAICode = (projectId: number) => {
    console.log('Generating AI code for project:', projectId)
    
    // Use router.post with preserveState to generate AI code (no page reload)
    router.post(`/projects/${projectId}/prompts`, {
      prompt: projectPrompt,
      auto_start_container: true
    }, {
      preserveState: true,
      preserveScroll: true,
      only: ['flash'],
      onStart: () => {
        console.log('Starting AI code generation...')
      },
      onProgress: (progress: any) => {
        console.log('AI generation progress:', progress)
      },
      onSuccess: (page: any) => {
        console.log('AI code generated:', page)
        console.log('Flash data:', page.props?.flash)
        
        // Get prompt data from flash message
        const prompt = page.props?.flash?.prompt
        console.log('Prompt data:', prompt)
        
        if (prompt) {
          setCreationProgress(20)
          setCreationStatus("Generating the code...")
          
          // Update creation state
          setCreationState({
            isActive: true,
            projectId: projectId,
            promptId: prompt.id,
            step: 'ai'
          })
          
          // Step 3: Wait for AI generation to complete
          waitForAIGeneration(projectId, prompt.id)
        } else {
          console.warn('No prompt data found in flash message, but continuing with AI generation...')
          setCreationProgress(20)
          setCreationStatus("Generating the code...")
          
          // Update creation state without prompt ID
          setCreationState({
            isActive: true,
            projectId: projectId,
            step: 'ai'
          })
          
          // Wait for AI generation to complete by checking the latest prompt for this project
          waitForAIGenerationByProject(projectId)
        }
      },
      onError: (errors: any) => {
        console.error('AI generation failed:', errors)
        setCreationStatus(`Error: ${Object.values(errors).join(', ')}`)
        setIsCreatingProject(false)
        setValidationError(Object.values(errors).join(', '))
        // Clear creation state on failure
        setCreationState(null)
      }
    })
  }

  const waitForAIGeneration = async (projectId: number, promptId: number) => {
    const maxAttempts = 60 // 5 minutes max (5 seconds * 60 attempts)
    let attempts = 0

    const checkStatus = async () => {
      try {
        attempts++
        console.log(`Checking AI generation status (attempt ${attempts}/${maxAttempts})`)
        
        // Use Inertia with lazy loading to check AI generation status
        router.get(`/prompts/${promptId}/status`, {}, {
          onStart: () => {
            console.log('Checking AI generation status...')
          },
          onSuccess: (page) => {
            const data = (page as any).props
            console.log('Prompt status:', data)
            
            if (data.status === 'completed') {
              setCreationProgress(50)
              setCreationStatus("Starting the project...")
              
              // Update creation state
              setCreationState({
                isActive: true,
                projectId: projectId,
                promptId: promptId,
                step: 'docker'
              })
              
              // Step 4: Deploy to Docker
              deployToDocker(projectId)
              return
            } else if (data.status === 'failed') {
              setCreationStatus(`Error: AI generation failed - ${data.response || 'Unknown error'}`)
              setIsCreatingProject(false)
              setValidationError('AI generation failed')
              // Clear creation state on failure
              setCreationState(null)
              return
            } else if (data.status === 'pending' || data.status === 'processing') {
              // Update progress based on time elapsed
              const progress = Math.min(20 + (attempts * 1.5), 50) // Gradually increase from 20% to 50%
              setCreationProgress(progress)
              setCreationStatus(`Generating the code... (${attempts * 5}s elapsed)`)
              
              if (attempts < maxAttempts) {
                setTimeout(checkStatus, 5000) // Check again in 5 seconds
              } else {
                setCreationStatus("Error: AI generation timed out")
                setIsCreatingProject(false)
                setValidationError('AI generation timed out')
              }
            }
          },
          onError: (errors) => {
            console.error('Failed to check prompt status:', errors)
            if (attempts < maxAttempts) {
              setTimeout(checkStatus, 5000) // Retry in 5 seconds
            } else {
              setCreationStatus("Error: Failed to check AI generation status")
              setIsCreatingProject(false)
              setValidationError('Failed to check AI generation status')
            }
          }
        })
      } catch (error) {
        console.error('Error checking AI generation status:', error)
        if (attempts < maxAttempts) {
          setTimeout(checkStatus, 5000) // Retry in 5 seconds
        } else {
          setCreationStatus("Error: Failed to check AI generation status")
          setIsCreatingProject(false)
          setValidationError('Failed to check AI generation status')
        }
      }
    }

    // Start checking status
    checkStatus()
  }

  const waitForAIGenerationByProject = async (projectId: number) => {
    const maxAttempts = 60 // 5 minutes max (5 seconds * 60 attempts)
    let attempts = 0

    const checkProjectStatus = async () => {
      try {
        attempts++
        console.log(`Checking project AI generation status (attempt ${attempts}/${maxAttempts})`)
        
        // Check if the project has generated_code
        const response = await fetch(`/api/projects/${projectId}/verify-setup`, {
          method: 'GET',
          headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
          }
        })
        
        if (response.ok) {
          const data = await response.json()
          console.log('Project verification data:', data)
          
          // Check if project has generated_code by looking at the project directly
          const projectResponse = await fetch(`/api/projects/${projectId}`, {
            method: 'GET',
            headers: {
              'Accept': 'application/json',
              'X-Requested-With': 'XMLHttpRequest'
            }
          })
          
          if (projectResponse.ok) {
            const projectData = await projectResponse.json()
            console.log('Project data:', projectData)
            
            if (projectData.project && projectData.project.generated_code) {
              setCreationProgress(50)
              setCreationStatus("Code generation complete! Deploying to Docker...")
              
              // Update creation state
              setCreationState({
                isActive: true,
                projectId: projectId,
                step: 'docker'
              })
              
              // Step 4: Deploy to Docker
              deployToDocker(projectId)
              return
            } else {
              // Update progress based on time elapsed
              const progress = Math.min(20 + (attempts * 1.5), 50) // Gradually increase from 20% to 50%
              setCreationProgress(progress)
              setCreationStatus(`Generating the code... (${attempts * 5}s elapsed)`)
              
              if (attempts < maxAttempts) {
                setTimeout(checkProjectStatus, 5000) // Check again in 5 seconds
              } else {
                setCreationStatus("Error: AI generation timed out")
                setIsCreatingProject(false)
                setValidationError('AI generation timed out')
              }
            }
          } else {
            console.error('Failed to fetch project data')
            if (attempts < maxAttempts) {
              setTimeout(checkProjectStatus, 5000) // Retry in 5 seconds
            } else {
              setCreationStatus("Error: Failed to check project status")
              setIsCreatingProject(false)
              setValidationError('Failed to check project status')
            }
          }
        } else {
          console.error('Failed to verify project setup')
          if (attempts < maxAttempts) {
            setTimeout(checkProjectStatus, 5000) // Retry in 5 seconds
          } else {
            setCreationStatus("Error: Failed to verify project setup")
            setIsCreatingProject(false)
            setValidationError('Failed to verify project setup')
          }
        }
      } catch (error) {
        console.error('Error checking project AI generation status:', error)
        if (attempts < maxAttempts) {
          setTimeout(checkProjectStatus, 5000) // Retry in 5 seconds
        } else {
          setCreationStatus("Error: Failed to check project AI generation status")
          setIsCreatingProject(false)
          setValidationError('Failed to check project AI generation status')
        }
      }
    }

    // Start checking status
    checkProjectStatus()
  }

  const deployToDocker = (projectId: number) => {
    console.log('Deploying project to Docker:', projectId)
    
    // Use router.post with preserveState to deploy to Docker (no page reload)
    router.post(`/api/projects/${projectId}/deploy`, {}, {
      preserveState: true,
      preserveScroll: true,
      only: ['flash'],
      onStart: () => {
        console.log('Starting Docker deployment...')
      },
      onProgress: (progress) => {
        console.log('Docker deployment progress:', progress)
      },
      onSuccess: (page) => {
        console.log('Project deployed successfully:', page)
        setCreationProgress(70)
        setCreationStatus("Checking status...")
        
        // Update creation state
        setCreationState({
          isActive: true,
          projectId: projectId,
          step: 'start'
        })
        
        // Step 5: Start container
        startContainer(projectId)
      },
      onError: (errors) => {
        console.error('Docker deployment failed:', errors)
        setCreationStatus(`Error: ${Object.values(errors).join(', ')}`)
        setIsCreatingProject(false)
        setValidationError(Object.values(errors).join(', '))
        // Clear creation state on failure
        setCreationState(null)
      }
    })
  }

  const startContainer = (projectId: number) => {
    console.log('Starting container for project:', projectId)
    
    // Use router.post with preserveState to start container (no page reload)
    router.post(`/api/projects/${projectId}/docker/start`, {}, {
      preserveState: true,
      preserveScroll: true,
      only: ['flash'],
      onStart: () => {
        console.log('Starting container...')
      },
      onProgress: (progress) => {
        console.log('Container start progress:', progress)
      },
      onSuccess: (page) => {
        console.log('Container started successfully:', page)
        setCreationProgress(95)
        setCreationStatus("Redirect to sandbox...")

        // Update creation state to complete
        setCreationState({
          isActive: true,
          projectId: projectId,
          step: 'complete'
        })

        // Redirect to sandbox after a short delay
        setTimeout(() => {
          setCreationProgress(100)
          setCreationStatus("Project created and deployed successfully!")
          // Clear creation state on success
          setCreationState(null)
          window.location.href = `/projects/${projectId}/sandbox`
        }, 1500)
      },
      onError: (errors) => {
        console.error('Container start failed:', errors)
        setCreationStatus(`Error: ${Object.values(errors).join(', ')}`)
        setIsCreatingProject(false)
        setValidationError(Object.values(errors).join(', '))
        // Clear creation state on failure
        setCreationState(null)
      }
    })
  }

  return (
    <div className="min-h-screen bg-gradient-to-br from-[#0B0F1A] to-[#0A0E18] text-white flex flex-col">
      <div className="flex flex-1 relative">
        {sidebarHidden && (
          <button
            onClick={() => setSidebarHidden(false)}
            className="fixed top-6 left-6 z-50 w-12 h-12 bg-slate-800/90 hover:bg-slate-700 border border-slate-700/60 rounded-lg flex items-center justify-center transition-all duration-200 backdrop-blur-sm"
          >
            <Menu className="w-6 h-6 text-slate-300" />
          </button>
        )}

        <div
          className={`${
            sidebarHidden ? "w-0 opacity-0 pointer-events-none" : sidebarCollapsed ? "w-20" : "w-80"
          } bg-gradient-to-b from-[#0B0F1A] via-[#0D1220] to-[#0B0F1A] border-gradient-to-b from-[#1B2432] to-[#151B28] flex flex-col transition-all duration-300 ease-in-out overflow-hidden shadow-2xl relative border-r-0`}
        >
          <div className="absolute top-0 right-0 w-px h-full bg-gradient-to-b from-transparent via-slate-600/20 to-transparent py-0"></div>
          <div className="p-6 border-b border-slate-800/50 bg-gradient-to-r from-slate-900/50 to-slate-800/30 backdrop-blur-sm">
            <div className={`flex items-center gap-3 ${sidebarCollapsed ? "justify-center" : ""}`}>
              <div className="w-10 h-10 bg-gradient-to-br from-orange-500 via-orange-600 to-orange-700 rounded-xl flex items-center justify-center shadow-lg shadow-orange-500/30 ring-2 ring-orange-500/20">
                <Box className="w-6 h-6 text-white" />
              </div>
              {!sidebarCollapsed && (
                <div className="flex flex-col">
                  <span className="font-bold text-white text-xl tracking-tight">Vibecode</span>
                  <span className="text-xs text-slate-400 font-medium">Development Platform</span>
                </div>
              )}
            </div>
            {!sidebarCollapsed && (
              <div className="flex items-center gap-2 mt-4">
                <Button
                  size="sm"
                  className="w-9 h-9 p-0 bg-slate-800/50 hover:bg-slate-700/70 border border-slate-700/50 rounded-lg backdrop-blur-sm"
                  onClick={() => setSidebarCollapsed(true)}
                >
                  <ChevronLeft className="w-4 h-4" />
                </Button>
                <Button
                  size="sm"
                  className="w-9 h-9 p-0 bg-slate-800/50 hover:bg-slate-700/70 border border-slate-700/50 rounded-lg backdrop-blur-sm"
                  onClick={() => setSidebarHidden(true)}
                >
                  <X className="w-4 h-4" />
                </Button>
              </div>
            )}
            {sidebarCollapsed && (
              <Button
                size="sm"
                className="w-9 h-9 p-0 bg-slate-800/50 hover:bg-slate-700/70 border border-slate-700/50 rounded-lg backdrop-blur-sm absolute top-6 right-4"
                onClick={() => setSidebarCollapsed(false)}
              >
                <ChevronRight className="w-4 h-4" />
              </Button>
            )}
          </div>

          {!sidebarCollapsed && (
            <>
              <div className="px-6 mt-8">
                <div className="flex items-center justify-between mb-6">
                  <div className="flex items-center gap-3">
                    <div className="w-2 h-2 bg-gradient-to-r from-blue-400 to-cyan-400 rounded-full animate-pulse"></div>
                    <h3 className="text-sm tracking-wider text-slate-300 uppercase font-bold">Sandboxes</h3>
                  </div>
                  <div className="flex items-center gap-2">
                    <Button
                      size="sm"
                      className="w-8 h-8 p-0 bg-gradient-to-r from-blue-500/20 to-cyan-500/20 hover:from-blue-500/30 hover:to-cyan-500/30 border border-blue-500/30 rounded-lg backdrop-blur-sm"
                      onClick={handleNewSandbox}
                    >
                      <Plus className="w-4 h-4 text-blue-400" />
                    </Button>
                    <Button
                      size="sm"
                      className="w-8 h-8 p-0 bg-slate-800/50 hover:bg-slate-700/70 border border-slate-700/50 rounded-lg backdrop-blur-sm"
                      onClick={() => setSearchQuery(searchQuery ? "" : "search")}
                    >
                      <Search className="w-4 h-4 text-slate-400" />
                    </Button>
                  </div>
                </div>
                {searchQuery && (
                  <div className="mb-6">
                    <div className="relative">
                      <input
                        type="text"
                        placeholder="Search sandboxes..."
                        className="w-full bg-gradient-to-r from-slate-800/80 to-slate-900/80 border border-slate-700/60 rounded-xl px-4 py-3 text-sm text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-400/40 focus:border-blue-400/40 backdrop-blur-sm transition-all duration-200"
                        value={searchQuery === "search" ? "" : searchQuery}
                        onChange={(e) => setSearchQuery(e.target.value)}
                        autoFocus
                      />
                      <Search className="absolute right-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-slate-400" />
                    </div>
                  </div>
                )}
                <div className="bg-gradient-to-br from-slate-800/30 to-slate-900/30 rounded-xl border border-slate-700/40 p-8 text-center backdrop-blur-sm">
                  <div className="w-12 h-12 bg-gradient-to-br from-slate-600/50 to-slate-700/50 rounded-xl flex items-center justify-center mx-auto mb-4">
                    <Box className="w-6 h-6 text-slate-400" />
                  </div>
                  <p className="text-sm text-slate-400 font-medium">No Sandboxes yet</p>
                  <p className="text-xs text-slate-500 mt-1">Create your first sandbox to get started</p>
                </div>
              </div>

              <div className="px-6 mt-10 flex-1">
                <div className="flex items-center justify-between mb-6">
                  <div className="flex items-center gap-3">
                    <div className="w-1 h-6 bg-gradient-to-b from-purple-400 to-purple-600 rounded-full"></div>
                    <h3 className="text-sm tracking-wider text-slate-300 uppercase font-bold">Projects</h3>
                  </div>
                  <div className="flex items-center gap-2">
                    <Button
                      size="sm"
                      className="w-8 h-8 p-0 bg-gradient-to-r from-purple-500/20 to-pink-500/20 hover:from-purple-500/30 hover:to-pink-500/30 border border-purple-500/30 rounded-lg backdrop-blur-sm"
                      onClick={handleNewProject}
                    >
                      <Plus className="w-4 h-4 text-purple-400" />
                    </Button>
                    <Button
                      size="sm"
                      className="w-8 h-8 p-0 bg-slate-800/50 hover:bg-slate-700/70 border border-slate-700/50 rounded-lg backdrop-blur-sm"
                    >
                      <Search className="w-4 h-4 text-slate-400" />
                    </Button>
                  </div>
                </div>
                <div className="space-y-3">
                  <div 
                    className="group flex items-center gap-4 p-4 rounded-xl bg-gradient-to-r from-slate-800/40 to-slate-900/40 border border-slate-700/40 hover:from-slate-800/60 hover:to-slate-900/60 hover:border-slate-600/60 transition-all duration-200 cursor-pointer backdrop-blur-sm"
                    onClick={() => window.location.href = '/projects/1/sandbox'}
                  >
                    <div className="relative">
                      <div className="w-10 h-10 bg-gradient-to-br from-[#6D28D9] to-[#8B5CF6] rounded-xl flex items-center justify-center shadow-lg shadow-purple-500/20">
                        <span className="text-sm font-bold text-white">L</span>
                      </div>
                      <div className="absolute -top-1 -right-1 w-3 h-3 bg-green-400 rounded-full border-2 border-slate-900"></div>
                    </div>
                    <div className="flex-1 min-w-0">
                      <span className="text-sm font-semibold text-white group-hover:text-slate-100 transition-colors">
                        Lovable Clone
                      </span>
                      <p className="text-xs text-slate-400 mt-0.5">Active • 2 hours ago</p>
                    </div>
                  </div>
                  <div 
                    className="group flex items-center gap-4 p-4 rounded-xl bg-gradient-to-r from-slate-800/40 to-slate-900/40 border border-slate-700/40 hover:from-slate-800/60 hover:to-slate-900/60 hover:border-slate-600/60 transition-all duration-200 cursor-pointer backdrop-blur-sm"
                    onClick={() => window.location.href = '/projects/2/sandbox'}
                  >
                    <div className="relative">
                      <div className="w-10 h-10 bg-gradient-to-br from-[#DB2777] to-[#F472B6] rounded-xl flex items-center justify-center shadow-lg shadow-pink-500/20">
                        <span className="text-sm font-bold text-white">C</span>
                      </div>
                      <div className="absolute -top-1 -right-1 w-3 h-3 bg-slate-500 rounded-full border-2 border-slate-900"></div>
                    </div>
                    <div className="flex-1 min-w-0">
                      <span className="text-sm font-semibold text-white group-hover:text-slate-100 transition-colors">
                        ChatGPT Clone
                      </span>
                      <p className="text-xs text-slate-400 mt-0.5">Idle • 1 day ago</p>
                    </div>
                  </div>
                </div>
              </div>

              <div className="p-6 space-y-4 border-t border-slate-800/50 bg-gradient-to-r from-slate-900/50 to-slate-800/30 backdrop-blur-sm">
                <div className="relative">
                  <button
                    onClick={() => setShowCreditsDropdown(!showCreditsDropdown)}
                    className="w-full flex items-center justify-between bg-gradient-to-r from-slate-800/60 to-slate-900/60 rounded-xl p-4 hover:from-slate-800/80 hover:to-slate-900/80 transition-all duration-200 border border-slate-700/40 backdrop-blur-sm group"
                  >
                    <div className="flex items-center gap-3">
                      <div className="w-8 h-8 bg-gradient-to-br from-emerald-500 to-green-600 rounded-lg flex items-center justify-center">
                        <span className="text-xs font-bold text-white">$</span>
                      </div>
                      <div className="text-left">
                        <span className="text-sm font-semibold text-white block">$1.43 credits</span>
                        <span className="text-xs text-slate-400">Available balance</span>
                      </div>
                    </div>
                    <ChevronDown
                      className={`w-4 h-4 text-slate-400 group-hover:text-slate-300 transition-all duration-200 ${showCreditsDropdown ? "rotate-180" : ""}`}
                    />
                  </button>
                  {showCreditsDropdown && (
                    <div className="absolute bottom-full left-0 right-0 mb-2 bg-gradient-to-br from-slate-800 to-slate-900 border border-slate-700/60 rounded-xl p-5 shadow-2xl backdrop-blur-sm">
                      <div className="space-y-4 text-sm">
                        <div className="flex justify-between items-center">
                          <span className="text-slate-400">Available:</span>
                          <span className="text-emerald-400 font-semibold">$1.43</span>
                        </div>
                        <div className="flex justify-between items-center">
                          <span className="text-slate-400">Used this month:</span>
                          <span className="text-slate-300 font-semibold">$8.57</span>
                        </div>
                        <div className="w-full bg-slate-700 rounded-full h-2">
                          <div
                            className="bg-gradient-to-r from-emerald-400 to-green-500 h-2 rounded-full"
                            style={{ width: "14%" }}
                          ></div>
                        </div>
                        <hr className="border-slate-700" />
                        <button className="w-full text-left text-orange-400 hover:text-orange-300 transition-colors font-medium">
                          Add credits →
                        </button>
                      </div>
                    </div>
                  )}
                </div>
                <div className="flex items-center gap-4 p-4 rounded-xl bg-gradient-to-r from-slate-800/40 to-slate-900/40 border border-slate-700/40 backdrop-blur-sm">
                  <div className="relative">
                    <div className="w-10 h-10 bg-gradient-to-br from-slate-600 to-slate-700 rounded-xl flex items-center justify-center shadow-lg">
                      <span className="text-sm font-bold text-white">L</span>
                    </div>
                    <div className="absolute -bottom-1 -right-1 w-4 h-4 bg-green-400 rounded-full border-2 border-slate-900 flex items-center justify-center">
                      <div className="w-2 h-2 bg-green-600 rounded-full"></div>
                    </div>
                  </div>
                  <div className="flex-1 min-w-0">
                    <span className="text-sm font-semibold text-white block">Linus Brandt</span>
                    <span className="text-xs text-slate-400">Online • Pro Plan</span>
                  </div>
                </div>
              </div>
            </>
          )}

          {sidebarCollapsed && (
            <div className="flex flex-col items-center py-8 space-y-8">
              <Button
                size="sm"
                className="w-12 h-12 p-0 bg-gradient-to-r from-blue-500/20 to-cyan-500/20 hover:from-blue-500/30 hover:to-cyan-500/30 border border-blue-500/30 rounded-xl backdrop-blur-sm"
                onClick={handleNewSandbox}
              >
                <Plus className="w-5 h-5 text-blue-400" />
              </Button>
              <div 
                className="w-12 h-12 bg-gradient-to-br from-[#6D28D9] to-[#8B5CF6] rounded-xl flex items-center justify-center shadow-lg shadow-purple-500/20 relative cursor-pointer hover:scale-105 transition-transform duration-200"
                onClick={() => window.location.href = '/projects/1/sandbox'}
              >
                <span className="text-sm font-bold text-white">L</span>
                <div className="absolute -top-1 -right-1 w-3 h-3 bg-green-400 rounded-full border-2 border-slate-900"></div>
              </div>
              <div 
                className="w-12 h-12 bg-gradient-to-br from-[#DB2777] to-[#F472B6] rounded-xl flex items-center justify-center shadow-lg shadow-pink-500/20 relative cursor-pointer hover:scale-105 transition-transform duration-200"
                onClick={() => window.location.href = '/projects/2/sandbox'}
              >
                <span className="text-sm font-bold text-white">C</span>
                <div className="absolute -top-1 -right-1 w-3 h-3 bg-slate-500 rounded-full border-2 border-slate-900"></div>
              </div>
            </div>
          )}
        </div>

        <div className="flex-1 px-6 sm:px-8 lg:px-12 xl:px-16 py-12 min-w-0">
          <div className="max-w-none mx-auto">
            <div className="text-center mb-16 relative">
              <div className="absolute inset-0 bg-gradient-to-r from-orange-500/5 via-orange-400/10 to-orange-500/5 rounded-3xl blur-3xl"></div>
              <div className="relative">
                <div className="flex items-center justify-center gap-6 mb-6">
                  <div className="relative group">
                    <div className="absolute inset-0 bg-gradient-to-r from-orange-400 to-orange-600 rounded-2xl blur-xl opacity-50 group-hover:opacity-75 transition-opacity duration-300"></div>
                    <div className="relative w-24 h-24 bg-gradient-to-br from-[#F59E0B] via-[#EA580C] to-[#DC2626] rounded-2xl flex items-center justify-center transform rotate-12 shadow-2xl shadow-orange-500/40 hover:rotate-6 hover:scale-110 transition-all duration-500 border border-orange-400/20">
                      <Box className="w-12 h-12 text-white drop-shadow-lg" />
                    </div>
                  </div>
                </div>
                <div className="space-y-2">
                  <h1 className="text-4xl font-mono tracking-wide bg-gradient-to-r from-[#F59E0B] via-[#EA580C] to-[#F59E0B] bg-clip-text text-transparent font-bold">
                    VIBECODE SANDBOX
                  </h1>
                  <p className="text-slate-400 text-lg font-medium">Build, Deploy, and Scale Your Ideas</p>
                </div>
              </div>
            </div>

            <div className="grid grid-cols-1 xl:grid-cols-2 gap-12 lg:gap-16">
              <div className="space-y-8">
                <div className="flex items-center gap-6 mb-8">
                  <div className="relative">
                    <div className="absolute inset-0 bg-gradient-to-r from-orange-400 to-orange-600 rounded-full blur-lg opacity-50"></div>
                    <div className="relative w-14 h-14 bg-gradient-to-br from-[#F59E0B] to-[#EA580C] rounded-full flex items-center justify-center text-white font-bold text-xl shadow-xl shadow-orange-500/40 border border-orange-400/20">
                      1
                    </div>
                  </div>
                  <div className="flex items-center gap-4">
                    <h2 className="text-3xl font-bold bg-gradient-to-r from-white to-slate-300 bg-clip-text text-transparent">
                      SELECT MODEL
                    </h2>
                    <Badge variant="ready" role="status" className="text-sm px-3 py-1.5">
                      ✓ READY
                    </Badge>
                  </div>
                </div>

                <div className="space-y-6">
                  <div className="flex items-center gap-3 mb-6">
                    <div className="w-1 h-6 bg-gradient-to-b from-orange-400 to-orange-600 rounded-full"></div>
                    <h3 className="text-sm tracking-wider text-slate-300 uppercase font-bold">AVAILABLE MODELS</h3>
                  </div>
                  <div className="grid gap-5">
                    {models.map((model) => (
                      <div
                        key={model.name}
                        className={`group relative overflow-hidden rounded-2xl border transition-all duration-300 cursor-pointer ${
                          selectedModel === model.name
                            ? "ring-2 ring-orange-400/50 border-orange-400/60 bg-gradient-to-br from-slate-800/90 to-slate-900/90 shadow-xl shadow-orange-500/20"
                            : model.comingSoon
                              ? "border-slate-700/40 bg-gradient-to-br from-slate-800/30 to-slate-900/30 opacity-60"
                              : "border-slate-700/40 bg-gradient-to-br from-slate-800/50 to-slate-900/50 hover:ring-1 hover:ring-slate-600/60 hover:bg-gradient-to-br hover:from-slate-800/70 hover:to-slate-900/70 hover:shadow-lg hover:shadow-slate-900/50"
                        }`}
                        onClick={() => !model.comingSoon && setSelectedModel(model.name)}
                      >
                        <div className="absolute inset-0 bg-gradient-to-r from-transparent via-white/[0.02] to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        <div className="relative p-6">
                          <div className="flex items-start justify-between">
                            <div className="flex-1">
                              <div className="flex items-center gap-4 mb-4">
                                <h4 className="font-bold text-white text-xl group-hover:text-orange-100 transition-colors">
                                  {model.name}
                                </h4>
                                {selectedModel === model.name && !model.comingSoon && (
                                  <div className="flex items-center gap-2 animate-in fade-in duration-300">
                                    <div className="w-6 h-6 bg-gradient-to-r from-emerald-400 to-green-500 rounded-full flex items-center justify-center">
                                      <Check className="w-4 h-4 text-white" />
                                    </div>
                                    <Badge variant="selected" className="text-sm px-3 py-1">
                                      SELECTED
                                    </Badge>
                                  </div>
                                )}
                              </div>
                              <p className="text-slate-300 leading-relaxed mb-6 group-hover:text-slate-200 transition-colors">
                                {model.description}
                              </p>
                            </div>
                            {model.comingSoon && (
                              <Badge variant="coming-soon" role="status" className="ml-4 text-sm px-3 py-1">
                                COMING SOON
                              </Badge>
                            )}
                          </div>
                        </div>
                      </div>
                    ))}
                  </div>
                </div>
              </div>

              <div className="space-y-8">
                <div className="flex items-center gap-6 mb-8">
                  <div className="relative">
                    <div
                      className={`absolute inset-0 rounded-full blur-lg opacity-50 transition-all duration-300 ${
                        selectedModel ? "bg-gradient-to-r from-orange-400 to-orange-600" : "bg-slate-600"
                      }`}
                    ></div>
                    <div
                      className={`relative w-14 h-14 rounded-full flex items-center justify-center text-white font-bold text-xl shadow-xl border transition-all duration-300 ${
                        selectedModel
                          ? "bg-gradient-to-br from-[#F59E0B] to-[#EA580C] shadow-orange-500/40 border-orange-400/20"
                          : "bg-slate-600 shadow-slate-600/40 border border-slate-500/20"
                      }`}
                    >
                      2
                    </div>
                  </div>
                  <h2
                    className={`text-3xl font-bold transition-colors duration-300 ${
                      selectedModel
                        ? "bg-gradient-to-r from-white to-slate-300 bg-clip-text text-transparent"
                        : "text-slate-500"
                    }`}
                  >
                    SELECT STACK
                  </h2>
                </div>

                <div className="flex flex-wrap gap-3 mb-8">
                  {tabs.map((tab) => (
                    <button
                      key={tab}
                      onClick={() => selectedModel && setActiveTab(tab)}
                      disabled={!selectedModel}
                      className={`relative px-5 py-3 text-sm font-semibold rounded-xl transition-all duration-300 transform hover:scale-105 ${
                        activeTab === tab
                          ? "bg-gradient-to-r from-[#F59E0B] to-[#EA580C] text-white shadow-lg shadow-orange-500/40 border border-orange-400/20"
                          : selectedModel
                            ? "bg-gradient-to-r from-slate-700/50 to-slate-800/50 text-slate-300 hover:from-slate-700/70 hover:to-slate-800/70 hover:text-white border border-slate-600/30 hover:border-slate-500/50"
                            : "bg-slate-800/30 text-slate-600 cursor-not-allowed border border-slate-700/20"
                      }`}
                    >
                      {activeTab === tab && (
                        <div className="absolute inset-0 bg-gradient-to-r from-orange-400/20 to-orange-600/20 rounded-xl blur-sm"></div>
                      )}
                      <span className="relative">{tab}</span>
                    </button>
                  ))}
                </div>

                <div className="space-y-5">
                  {selectedModel ? (
                    getStacksForTab(activeTab).length > 0 ? (
                      <div className="grid gap-5">
                        {getStacksForTab(activeTab).map((stack) => (
                          <div
                            key={stack.name}
                            className={`group relative overflow-hidden rounded-2xl border transition-all duration-300 cursor-pointer ${
                              selectedStack === stack.name
                                ? "ring-2 ring-orange-400/50 border-orange-400/60 bg-gradient-to-br from-slate-800/90 to-slate-900/90 shadow-xl shadow-orange-500/20"
                                : stack.comingSoon
                                  ? "border-slate-700/40 bg-gradient-to-br from-slate-800/30 to-slate-900/30 opacity-60"
                                  : "border-slate-700/40 bg-gradient-to-br from-slate-800/50 to-slate-900/50 hover:ring-1 hover:ring-slate-600/60 hover:bg-gradient-to-br hover:from-slate-800/70 hover:to-slate-900/70 hover:shadow-lg hover:shadow-slate-900/50"
                            }`}
                            onClick={() => !stack.comingSoon && setSelectedStack(stack.name)}
                          >
                            <div className="absolute inset-0 bg-gradient-to-r from-transparent via-white/[0.02] to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                            <div className="relative p-6">
                              <div className="flex items-center justify-between">
                                <div className="flex-1">
                                  <div className="flex items-center gap-4 mb-3">
                                    <h4 className="font-bold text-white text-xl group-hover:text-orange-100 transition-colors">
                                      {stack.name}
                                    </h4>
                                    {stack.hot && (
                                      <Badge variant="hot" className="animate-pulse text-sm px-2 py-1">
                                        <Flame className="w-4 h-4 mr-1" />
                                        HOT
                                      </Badge>
                                    )}
                                    {selectedStack === stack.name && !stack.comingSoon && (
                                      <div className="flex items-center gap-2 animate-in fade-in duration-300">
                                        <div className="w-6 h-6 bg-gradient-to-r from-emerald-400 to-green-500 rounded-full flex items-center justify-center">
                                          <Check className="w-4 h-4 text-white" />
                                        </div>
                                        <Badge variant="selected" className="text-sm px-3 py-1">
                                          SELECTED
                                        </Badge>
                                      </div>
                                    )}
                                  </div>
                                  <p className="text-slate-300 mb-5 group-hover:text-slate-200 transition-colors">
                                    {stack.description}
                                  </p>
                                  {!stack.comingSoon && (
                                    <div className="flex justify-end">
                                      <button
                                        className={`group/btn flex items-center gap-2 px-4 py-2 rounded-xl transition-all duration-200 font-medium ${
                                          selectedModel && selectedStack === stack.name
                                            ? "bg-gradient-to-r from-orange-500/80 to-orange-600/80 hover:from-orange-500 hover:to-orange-600 border border-orange-400/80 hover:border-orange-400 text-white hover:text-white shadow-lg shadow-orange-500/40 hover:shadow-xl hover:shadow-orange-500/50"
                                            : "bg-slate-800/30 border border-slate-700/30 text-slate-500 cursor-not-allowed opacity-50"
                                        }`}
                                        onClick={(e) => {
                                          e.stopPropagation()
                                          if (selectedStack === stack.name) handleDeploy()
                                        }}
                                        disabled={selectedStack !== stack.name}
                                      >
                                        <span>Deploy</span>
                                        <ChevronRight
                                          className={`w-4 h-4 transition-transform ${
                                            selectedModel && selectedStack === stack.name
                                              ? "group-hover/btn:translate-x-0.5"
                                              : ""
                                          }`}
                                        />
                                      </button>
                                    </div>
                                  )}
                                </div>
                                {stack.comingSoon && (
                                  <Badge variant="coming-soon" role="status" className="ml-4 text-sm px-3 py-1">
                                    COMING SOON
                                  </Badge>
                                )}
                              </div>
                            </div>
                          </div>
                        ))}
                      </div>
                    ) : (
                      <div className="text-center py-20 bg-gradient-to-br from-slate-800/30 to-slate-900/30 rounded-2xl border border-slate-700/40">
                        <div className="w-16 h-16 bg-gradient-to-br from-slate-600/50 to-slate-700/50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                          <Box className="w-8 h-8 text-slate-400" />
                        </div>
                        <p className="text-slate-400 text-lg font-medium">No stacks available yet</p>
                        <p className="text-slate-500 text-sm mt-1">Check back soon for more options</p>
                      </div>
                    )
                  ) : (
                    <div className="text-center py-20 bg-gradient-to-br from-slate-800/20 to-slate-900/20 rounded-2xl border border-slate-700/30 opacity-50">
                      <div className="w-16 h-16 bg-gradient-to-br from-slate-700/30 to-slate-800/30 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <Box className="w-8 h-8 text-slate-500" />
                      </div>
                      <p className="text-slate-500 text-lg font-medium">Select a model first</p>
                      <p className="text-slate-600 text-sm mt-1">Choose your AI model to see available stacks</p>
                    </div>
                  )}
                </div>
              </div>
            </div>

          </div>
        </div>
      </div>

      {showNewSandboxModal && (
        <div className="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50">
          <div className="bg-slate-800 rounded-2xl p-8 border border-slate-700/60 shadow-2xl">
            <div className="flex items-center gap-4 mb-6">
              <div className="w-10 h-10 border-2 border-orange-400 border-t-transparent rounded-full animate-spin" />
              <span className="text-white font-medium text-lg">
                Creating sandbox...
              </span>
            </div>
          </div>
        </div>
      )}

      {showNewProjectModal && (
        <div 
          className="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50"
          onClick={(e) => {
            // Only close if clicking the backdrop (not the modal content)
            if (e.target === e.currentTarget) {
              safeCloseModal()
            }
          }}
        >
          <div className="relative overflow-hidden bg-gradient-to-br from-slate-800/60 via-slate-900/60 to-slate-800/60 rounded-3xl p-10 border border-slate-700/50 backdrop-blur-sm shadow-2xl max-w-2xl w-full mx-4">
            <div className="absolute inset-0 bg-gradient-to-r from-orange-500/5 via-transparent to-orange-500/5"></div>
            <div className="relative">
              <button
                onClick={safeCloseModal}
                className="absolute top-4 right-4 w-8 h-8 bg-slate-700/50 hover:bg-slate-600/50 rounded-lg flex items-center justify-center transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                disabled={isCreatingProject && creationState?.isActive}
              >
                <X className="w-4 h-4 text-slate-300" />
              </button>
              
              <div className={`w-20 h-20 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-xl ${
                isFromDeploy 
                  ? 'bg-gradient-to-br from-orange-400 to-orange-600 shadow-orange-500/30' 
                  : 'bg-gradient-to-br from-purple-400 to-pink-600 shadow-purple-500/30'
              }`}>
                {isFromDeploy ? (
                  <Box className="w-10 h-10 text-white" />
                ) : (
                  <Plus className="w-10 h-10 text-white" />
                )}
              </div>
              
              {/* Warning message when modal is locked during creation */}
              {isCreatingProject && creationState?.isActive && (
                <div className="bg-yellow-500/10 border border-yellow-500/30 rounded-lg p-3 mb-4 text-yellow-400 text-sm text-center">
                  <div className="flex items-center justify-center gap-2">
                    <div className="w-4 h-4 border-2 border-yellow-400 border-t-transparent rounded-full animate-spin"></div>
                    <span>Project creation in progress - Modal locked to prevent data loss</span>
                  </div>
                </div>
              )}
              
              <h3 className="text-2xl font-bold bg-gradient-to-r from-white to-slate-300 bg-clip-text text-transparent mb-4 text-center">
                {isCreatingProject ? 'Creating Project...' : (isFromDeploy ? 'Deploy with AI' : 'Create New Project')}
              </h3>
              
              {!isCreatingProject && isFromDeploy && (
                <p className="text-slate-400 text-center mb-6">
                  Ready to deploy <span className="text-orange-400 font-semibold">{selectedStack}</span> with <span className="text-orange-400 font-semibold">{selectedModel}</span>
                </p>
              )}
              
              {!isCreatingProject ? (
                <div className="space-y-6">
                  <div>
                    <label className="block text-sm font-medium text-slate-300 mb-2">
                      Project Name
                    </label>
                    <div className="relative">
                      <input
                        type="text"
                        value={projectName}
                        onChange={(e) => setProjectName(e.target.value)}
                        placeholder="Enter project name..."
                        className={`w-full bg-slate-700/50 text-white placeholder-slate-400 border rounded-lg px-4 py-3 focus:outline-none focus:ring-2 transition-all duration-200 pr-10 ${
                          nameError 
                            ? 'border-red-500/50 focus:ring-red-400/40 focus:border-red-400/40' 
                            : 'border-slate-600/50 focus:ring-purple-400/40 focus:border-purple-400/40'
                        }`}
                      />
                      {isCheckingName && (
                        <div className="absolute right-3 top-1/2 transform -translate-y-1/2">
                          <div className="w-4 h-4 border-2 border-slate-400 border-t-transparent rounded-full animate-spin"></div>
                        </div>
                      )}
                    </div>
                    {nameError && (
                      <p className="text-red-400 text-sm mt-1">{nameError}</p>
                    )}
                  </div>
                  
                  <div>
                    <label className="block text-sm font-medium text-slate-300 mb-2">
                      Description (Optional)
                    </label>
                    <input
                      type="text"
                      value={projectDescription}
                      onChange={(e) => setProjectDescription(e.target.value)}
                      placeholder="Brief description of your project..."
                      className="w-full bg-slate-700/50 text-white placeholder-slate-400 border border-slate-600/50 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-purple-400/40 focus:border-purple-400/40 transition-all duration-200"
                    />
                  </div>
                  
                  <div>
                    <label className="block text-sm font-medium text-slate-300 mb-2">
                      AI Prompt
                    </label>
                    <textarea
                      value={projectPrompt}
                      onChange={(e) => setProjectPrompt(e.target.value)}
                      placeholder="Describe what you want to build... (e.g., 'Create a modern e-commerce website with product catalog, shopping cart, and user authentication')"
                      rows={4}
                      className="w-full bg-slate-700/50 text-white placeholder-slate-400 border border-slate-600/50 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-purple-400/40 focus:border-purple-400/40 transition-all duration-200 resize-none"
                    />
                  </div>
                  
                  <div className="bg-slate-800/50 rounded-lg p-4 border border-slate-700/50">
                    <h4 className="text-sm font-semibold text-slate-300 mb-3">Selected Configuration</h4>
                    <div className="flex items-center gap-6">
                      <div className="flex items-center gap-2">
                        <div className="w-3 h-3 bg-orange-400 rounded-full"></div>
                        <span className="text-slate-300 font-medium">AI Model:</span>
                        <span className="text-orange-400 font-semibold">{selectedModel || 'Claude Code'}</span>
                      </div>
                      <div className="flex items-center gap-2">
                        <div className="w-3 h-3 bg-blue-400 rounded-full"></div>
                        <span className="text-slate-300 font-medium">Stack:</span>
                        <span className="text-blue-400 font-semibold">{selectedStack || 'Next.js'}</span>
                      </div>
                    </div>
                    <p className="text-xs text-slate-500 mt-2">
                      These selections were made from the main interface and will be used for your project.
                    </p>
                  </div>
                  
                  {validationError && (
                    <div className="bg-red-500/10 border border-red-500/30 rounded-lg p-3 text-red-400 text-sm">
                      {validationError}
                    </div>
                  )}

                  {projectForm.errors && Object.keys(projectForm.errors).length > 0 && (
                    <div className="bg-red-500/10 border border-red-500/30 rounded-lg p-3 text-red-400 text-sm">
                      <div className="font-semibold mb-2">Form Validation Errors:</div>
                      {Object.entries(projectForm.errors).map(([field, error]) => (
                        <div key={field} className="text-sm">
                          <strong>{field}:</strong> {Array.isArray(error) ? error.join(', ') : error}
                        </div>
                      ))}
                    </div>
                  )}
                  
                  <button
                    onClick={handleCreateProject}
                    disabled={!projectName.trim() || !projectPrompt.trim() || isCreatingProject || !!nameError || isCheckingName}
                    className="relative group bg-gradient-to-r from-purple-500 via-pink-500 to-purple-500 text-white px-8 py-4 rounded-2xl font-bold text-lg hover:shadow-2xl hover:shadow-purple-500/40 transition-all duration-300 transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none border border-purple-400/20 overflow-hidden w-full"
                  >
                    <div className="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <span className="relative flex items-center justify-center gap-3">
                      {isCreatingProject ? (
                        <>
                          <div className="w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin" />
                          Creating...
                        </>
                      ) : (
                        <>
                          <Plus className="w-5 h-5" />
                          {isFromDeploy ? 'Deploy Project with AI' : 'Create Project with AI'}
                        </>
                      )}
                    </span>
                  </button>
                </div>
              ) : (
                <div className="space-y-6">
                  <div className="text-center">
                    <p className="text-lg text-slate-300 mb-4 font-medium">
                      {creationStatus}
                    </p>
                    
                    <div className="w-full bg-slate-700/50 rounded-full h-4 overflow-hidden mb-4">
                      <div
                        className="bg-gradient-to-r from-purple-500 via-pink-500 to-purple-500 h-4 rounded-full transition-all duration-300 shadow-lg shadow-purple-500/50"
                        style={{ width: `${creationProgress}%` }}
                      />
                    </div>
                    <p className="text-slate-400 font-medium">{creationProgress}% complete</p>
                  </div>
                  
                  {/* Cancel button */}
                  <div className="flex justify-center mt-6">
                    <button
                      onClick={() => {
                        if (window.confirm('Are you sure you want to cancel the project creation? This will stop the process and you\'ll need to start over.')) {
                          setIsCreatingProject(false)
                          setCreationProgress(0)
                          setCreationStatus("")
                          setCreatedProject(null)
                          setCreationState(null)
                          setValidationError("")
                        }
                      }}
                      className="px-6 py-2 bg-red-600/20 hover:bg-red-600/30 text-red-400 border border-red-500/30 rounded-lg transition-colors duration-200"
                    >
                      Cancel Creation
                    </button>
                  </div>
                  
                  {createdProject && (
                    <div className="bg-slate-800/50 rounded-lg p-4 border border-slate-700/50">
                      <div className="flex items-center gap-3">
                        <div className="w-8 h-8 bg-gradient-to-br from-purple-400 to-pink-600 rounded-lg flex items-center justify-center">
                          <span className="text-sm font-bold text-white">
                            {createdProject.name?.charAt(0) || 'P'}
                          </span>
                        </div>
                        <div>
                          <p className="text-white font-semibold">{createdProject.name}</p>
                          <p className="text-slate-400 text-sm">Project created successfully</p>
                        </div>
                      </div>
                    </div>
                  )}
                </div>
              )}
            </div>
          </div>
        </div>
      )}

      {showDeploymentModal && (
        <div className="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50">
          <div className="relative overflow-hidden bg-gradient-to-br from-slate-800/60 via-slate-900/60 to-slate-800/60 rounded-3xl p-10 border border-slate-700/50 backdrop-blur-sm shadow-2xl max-w-md w-full mx-4">
            <div className="absolute inset-0 bg-gradient-to-r from-orange-500/5 via-transparent to-orange-500/5"></div>
            <div className="relative">
              <button
                onClick={() => setShowDeploymentModal(false)}
                className="absolute top-4 right-4 w-8 h-8 bg-slate-700/50 hover:bg-slate-600/50 rounded-lg flex items-center justify-center transition-colors duration-200"
                disabled={isDeploying}
              >
                <X className="w-4 h-4 text-slate-300" />
              </button>
              
              <div className="w-20 h-20 bg-gradient-to-br from-emerald-400 to-green-600 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-xl shadow-emerald-500/30">
                <Check className="w-10 h-10 text-white" />
              </div>
              
              <h3 className="text-2xl font-bold bg-gradient-to-r from-white to-slate-300 bg-clip-text text-transparent mb-4 text-center">
                {isDeploying ? 'Deploying...' : deploymentProgress === 100 ? 'Deployment Complete!' : 'Ready to Deploy'}
              </h3>
              
              <p className="text-lg text-slate-300 mb-8 font-medium text-center">
                {deploymentProgress === 100 ? (
                  <span className="text-emerald-400">Redirecting to sandbox...</span>
                ) : (
                  <>
                    <span className="text-orange-400">{selectedStack}</span> with{" "}
                    <span className="text-orange-400">{selectedModel}</span>
                  </>
                )}
              </p>
              
              {isDeploying && (
                <div className="mb-8">
                  <div className="w-full bg-slate-700/50 rounded-full h-4 overflow-hidden">
                    <div
                      className="bg-gradient-to-r from-[#F59E0B] via-[#EA580C] to-[#F59E0B] h-4 rounded-full transition-all duration-300 shadow-lg shadow-orange-500/50"
                      style={{ width: `${deploymentProgress}%` }}
                    />
                  </div>
                  <p className="text-slate-400 mt-4 font-medium text-center">{deploymentProgress}% complete</p>
                </div>
              )}
              
              <button
                onClick={handleDeploy}
                disabled={isDeploying}
                className="relative group bg-gradient-to-r from-[#F59E0B] via-[#EA580C] to-[#F59E0B] text-white px-12 py-5 rounded-2xl font-bold text-lg hover:shadow-2xl hover:shadow-orange-500/40 transition-all duration-300 transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none border border-orange-400/20 overflow-hidden w-full"
              >
                <div className="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                <span className="relative">
                  {isDeploying ? (
                    <div className="flex items-center justify-center gap-3">
                      <div className="w-6 h-6 border-2 border-white/30 border-t-white rounded-full animate-spin" />
                      Deploying... {deploymentProgress}%
                    </div>
                  ) : (
                    "Deploy Sandbox"
                  )}
                </span>
              </button>
            </div>
          </div>
        </div>
      )}

      <div className="border-t border-[#1B2432] bg-[#0A0E18]/80 backdrop-blur-sm">
        <div className="px-12 py-4">
          <div className="max-w-[1200px] mx-auto">
            <p className="text-sm text-[#7C8AA5] text-center">
              🚀 SANDBOX v1.0 | More models coming soon • Customize stack after deployment
            </p>
          </div>
        </div>
      </div>
    </div>
  )
}