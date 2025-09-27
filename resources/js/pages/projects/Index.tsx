"use client"

import type React from "react"
import { useState, useEffect } from "react"
import { router, useForm } from "@inertiajs/react"
import { 
  Box, 
  Sidebar, 
  ModelSelection, 
  StackSelection, 
  ProjectCreationModal, 
  DeploymentModal, 
  SandboxModal 
} from "../../components"

interface Project {
  id: number
  name: string
  description?: string
  status: string
  created_at: string
  updated_at: string
  containers: Array<{
    id: number
    status: string
    created_at: string
  }>
  prompts: Array<{
    id: number
    created_at: string
  }>
}

interface Props {
  projects: {
    data: Project[]
    links: any[]
    meta: any
  }
}

export default function VibecodeSandboxPage({ projects }: Props) {
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
      stack: 'nextjs', // Default to nextjs, will be updated when stack is selected
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

  // Update form when stack is selected
  useEffect(() => {
    if (selectedStack) {
      projectForm.setData('settings.stack', selectedStack)
    }
  }, [selectedStack])

  // Update form when model is selected
  useEffect(() => {
    if (selectedModel) {
      projectForm.setData('settings.ai_model', selectedModel)
    }
  }, [selectedModel])

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
    
    console.log('Project creation attempt:', {
      projectName: projectName.trim(),
      projectPrompt: projectPrompt.trim(),
      selectedModel,
      selectedStack,
      nameError
    })
    
    if (!projectName.trim() || !projectPrompt.trim()) {
      setValidationError("Please fill in project name and AI prompt")
      return
    }

    if (nameError) {
      setValidationError("Please fix the project name error before creating the project")
      return
    }

    if (!selectedModel || !selectedStack) {
      const missingItems = []
      if (!selectedModel) missingItems.push('AI model')
      if (!selectedStack) missingItems.push('technology stack')
      setValidationError(`Please select a ${missingItems.join(' and ')} from the main interface before creating a project`)
      console.log('Missing selections:', { selectedModel, selectedStack })
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
        <Sidebar
          sidebarCollapsed={sidebarCollapsed}
          sidebarHidden={sidebarHidden}
          searchQuery={searchQuery}
          showCreditsDropdown={showCreditsDropdown}
          projects={projects.data}
          onToggleCollapse={() => setSidebarCollapsed(!sidebarCollapsed)}
          onToggleHidden={() => setSidebarHidden(!sidebarHidden)}
          onSearchChange={setSearchQuery}
          onToggleCreditsDropdown={() => setShowCreditsDropdown(!showCreditsDropdown)}
          onNewSandbox={handleNewSandbox}
          onNewProject={handleNewProject}
        />

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
              <ModelSelection
                selectedModel={selectedModel}
                onModelSelect={setSelectedModel}
              />
              <StackSelection
                selectedModel={selectedModel}
                selectedStack={selectedStack}
                activeTab={activeTab}
                onTabChange={setActiveTab}
                onStackSelect={setSelectedStack}
                onDeploy={handleDeploy}
              />
            </div>

          </div>
        </div>
      </div>

      <SandboxModal isOpen={showNewSandboxModal} />
      
      <ProjectCreationModal
        isOpen={showNewProjectModal}
        isCreatingProject={isCreatingProject}
        isFromDeploy={isFromDeploy}
        selectedModel={selectedModel}
        selectedStack={selectedStack}
        projectName={projectName}
        projectDescription={projectDescription}
        projectPrompt={projectPrompt}
        validationError={validationError}
        nameError={nameError}
        isCheckingName={isCheckingName}
        creationProgress={creationProgress}
        creationStatus={creationStatus}
        createdProject={createdProject}
        creationState={creationState}
        onClose={safeCloseModal}
        onProjectNameChange={setProjectName}
        onProjectDescriptionChange={setProjectDescription}
        onProjectPromptChange={setProjectPrompt}
        onCreateProject={handleCreateProject}
        onCancelCreation={() => {
          if (window.confirm('Are you sure you want to cancel the project creation? This will stop the process and you\'ll need to start over.')) {
            setIsCreatingProject(false)
            setCreationProgress(0)
            setCreationStatus("")
            setCreatedProject(null)
            setCreationState(null)
            setValidationError("")
          }
        }}
      />

      <DeploymentModal
        isOpen={showDeploymentModal}
        isDeploying={isDeploying}
        deploymentProgress={deploymentProgress}
        selectedModel={selectedModel}
        selectedStack={selectedStack}
        onClose={() => setShowDeploymentModal(false)}
        onDeploy={handleDeploy}
      />

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