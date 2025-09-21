import { Head, Link, router } from '@inertiajs/react'
import { useState, useEffect } from 'react'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'
import { Textarea } from '@/components/ui/textarea'
import { ArrowLeft, Play, Square, Copy, Trash2, ExternalLink, Loader2, Users, Rocket, RefreshCw, Terminal, Activity, Globe, Lock, MessageCircle, Settings, Link as LinkIcon } from 'lucide-react'
import { formatDistanceToNow } from 'date-fns'
import CollaborationSidebar from '@/components/CollaborationSidebar'
import CommentsPanel from '@/components/CommentsPanel'
import DockerManagementPanel from '@/components/DockerManagementPanel'
import SubdomainManagementPanel from '@/components/SubdomainManagementPanel'
import StatusIndicator from '@/components/StatusIndicator'
import ProgressBar from '@/components/ProgressBar'
import LoadingSpinner from '@/components/LoadingSpinner'
import { ToastContainer, useToast } from '@/components/Toast'
import * as routes from '@/routes/projects'
import * as promptRoutes from '@/routes/prompts'
import * as containerRoutes from '@/routes/containers'

interface Project {
  id: number
  name: string
  description?: string
  status: 'draft' | 'building' | 'ready' | 'error'
  is_public: boolean
  preview_url?: string
  generated_code?: string
  last_built_at?: string
  created_at: string
  subdomain?: string
  custom_domain?: string
  dns_configured?: boolean
  containers: Container[]
  prompts: Prompt[]
}

interface Container {
  id: number
  status: 'starting' | 'running' | 'stopped' | 'error'
  url?: string
  port?: string
}

interface Prompt {
  id: number
  prompt: string
  response?: string
  status: 'pending' | 'processing' | 'completed' | 'failed'
  created_at: string
  processed_at?: string
}

interface Collaborator {
  user_id: number
  user_name: string
  action: string
  data: any
  last_seen: string
}

interface CollaborationHistory {
  user: {
    name: string
    email: string
  }
  action: string
  message: string
  timestamp: string
}

interface Props {
  project: Project
  activeCollaborators: Collaborator[]
  collaborationHistory: CollaborationHistory[]
  flash?: {
    success?: string
    error?: string
  }
}

const statusColors = {
  draft: 'bg-gray-100 text-gray-800',
  building: 'bg-blue-100 text-blue-800',
  ready: 'bg-green-100 text-green-800',
  error: 'bg-red-100 text-red-800',
}

const statusLabels = {
  draft: 'Draft',
  building: 'Building',
  ready: 'Ready',
  error: 'Error',
}

const promptStatusColors = {
  pending: 'bg-yellow-100 text-yellow-800',
  processing: 'bg-blue-100 text-blue-800',
  completed: 'bg-green-100 text-green-800',
  failed: 'bg-red-100 text-red-800',
}

const promptStatusLabels = {
  pending: 'Pending',
  processing: 'Processing',
  completed: 'Completed',
  failed: 'Failed',
}

export default function ProjectShow({ project, activeCollaborators, collaborationHistory, flash }: Props) {
  const [prompt, setPrompt] = useState('')
  const [isSubmitting, setIsSubmitting] = useState(false)
  const [activeContainer, setActiveContainer] = useState<Container | null>(
    project.containers.find(c => c.status === 'running') || null
  )
  const [isCollaborationOpen, setIsCollaborationOpen] = useState(false)
  const [deploymentStatus, setDeploymentStatus] = useState<any>(null)
  const [isDeploying, setIsDeploying] = useState(false)
  const [showLogs, setShowLogs] = useState(false)
  const [logs, setLogs] = useState('')
  const [isTogglingPublic, setIsTogglingPublic] = useState(false)
  const [isCommentsOpen, setIsCommentsOpen] = useState(false)
  const [isDockerManagementOpen, setIsDockerManagementOpen] = useState(false)
  const [isSubdomainManagementOpen, setIsSubdomainManagementOpen] = useState(false)
  const { toasts, success, error, removeToast } = useToast()

  // Handle flash messages from backend
  useEffect(() => {
    if (flash?.success) {
      success('Success!', flash.success)
    }
    if (flash?.error) {
      error('Error', flash.error)
    }
  }, [flash])

  const handleSubmitPrompt = async (e: React.FormEvent) => {
    e.preventDefault()
    if (!prompt.trim() || isSubmitting) return

    setIsSubmitting(true)
    
    try {
      const response = await fetch(promptRoutes.store.url({ project: project.id }), {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
        },
        body: JSON.stringify({ prompt: prompt.trim() }),
      })

      if (response.ok) {
        setPrompt('')
        // Refresh the page to show the new prompt
        router.reload()
      }
    } catch (error) {
      console.error('Error submitting prompt:', error)
    } finally {
      setIsSubmitting(false)
    }
  }

  const handleStartContainer = () => {
    if (activeContainer) return

    // Use the same deployment endpoint as the main deploy button
    handleDeploy()
  }

  const handleStopContainer = () => {
    if (!activeContainer) return

    // Use the same deployment stop endpoint as the main stop button
    handleStopDeployment()
  }

  const handleDelete = () => {
    if (confirm(`Are you sure you want to delete "${project.name}"? This action cannot be undone.`)) {
      router.delete(routes.destroy.url({ project: project.id }))
    }
  }

  const handleDuplicate = () => {
    router.post(routes.duplicate.url({ project: project.id }))
  }

  // Deployment functions
  const handleDeploy = () => {
    setIsDeploying(true)
    // Use Inertia router to avoid CSRF issues
    router.post(`/projects/${project.id}/deploy`, {}, {
      onFinish: () => {
        setIsDeploying(false)
      }
    })
  }

  const handleStopDeployment = () => {
    // Use Inertia router to avoid CSRF issues
    router.post(`/projects/${project.id}/deployment/stop`)
  }

  const handleRestartDeployment = () => {
    setIsDeploying(true)
    // Use Inertia router to avoid CSRF issues
    router.post(`/projects/${project.id}/deployment/restart`, {}, {
      onFinish: () => {
        setIsDeploying(false)
      }
    })
  }

  const handleViewLogs = async () => {
    try {
      const response = await fetch(`/projects/${project.id}/deployment/logs`)
      const data = await response.json()
      if (data.success) {
        setLogs(data.logs)
        setShowLogs(true)
      } else {
        error('Failed to fetch logs', data.message || 'Failed to fetch logs')
      }
    } catch (err) {
      error('Failed to fetch logs', 'An unexpected error occurred while fetching logs')
      console.error('Fetch logs error:', err)
    }
  }

  const handleTogglePublic = async () => {
    setIsTogglingPublic(true)
    try {
      const response = await fetch(`/projects/${project.id}/toggle-public`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
        },
      })
      
      const data = await response.json()
      if (data.success) {
        // Refresh the page to show updated status
        window.location.reload()
      } else {
        alert(data.message || 'Failed to update project visibility')
      }
    } catch (error) {
      alert('Failed to update project visibility: ' + error)
    } finally {
      setIsTogglingPublic(false)
    }
  }

  return (
    <>
      <Head title={project.name} />
      
      <div className="min-h-screen bg-gray-50">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
          {/* Header */}
          <div className="flex items-center gap-4 mb-8">
            <Link href={routes.index.url()}>
              <Button variant="outline" size="sm">
                <ArrowLeft className="w-4 h-4 mr-2" />
                Back to Projects
              </Button>
            </Link>
            
            <div className="flex-1">
              <div className="flex items-center gap-3">
                <h1 className="text-3xl font-bold text-gray-900">{project.name}</h1>
                <StatusIndicator status={project.status} size="md" />
                {project.is_public && (
                  <Badge variant="outline">Public</Badge>
                )}
              </div>
              {project.description && (
                <p className="text-gray-600 mt-2">{project.description}</p>
              )}
            </div>

            <div className="flex gap-2">
              {/* Deployment Controls */}
              {!activeContainer ? (
                <Button 
                  onClick={handleDeploy}
                  disabled={isDeploying}
                  className="bg-green-600 hover:bg-green-700"
                >
                  {isDeploying ? (
                    <Loader2 className="w-4 h-4 mr-2 animate-spin" />
                  ) : (
                    <Rocket className="w-4 h-4 mr-2" />
                  )}
                  {isDeploying ? 'Deploying...' : 'Deploy'}
                </Button>
              ) : (
                <div className="flex gap-2">
                  <Button 
                    variant="outline"
                    onClick={handleRestartDeployment}
                    disabled={isDeploying}
                  >
                    {isDeploying ? (
                      <Loader2 className="w-4 h-4 mr-2 animate-spin" />
                    ) : (
                      <RefreshCw className="w-4 h-4 mr-2" />
                    )}
                    Restart
                  </Button>
                  <Button 
                    variant="outline"
                    onClick={handleStopDeployment}
                  >
                    <Square className="w-4 h-4 mr-2" />
                    Stop
                  </Button>
                  <Button 
                    variant="outline"
                    onClick={handleViewLogs}
                  >
                    <Terminal className="w-4 h-4 mr-2" />
                    Logs
                  </Button>
                </div>
              )}
              
              <Button 
                variant="outline" 
                onClick={handleTogglePublic}
                disabled={isTogglingPublic}
                className={project.is_public ? 'bg-green-50 border-green-200 text-green-700' : ''}
              >
                {isTogglingPublic ? (
                  <Loader2 className="w-4 h-4 mr-2 animate-spin" />
                ) : project.is_public ? (
                  <Globe className="w-4 h-4 mr-2" />
                ) : (
                  <Lock className="w-4 h-4 mr-2" />
                )}
                {project.is_public ? 'Public' : 'Private'}
              </Button>
              
              <Button 
                variant="outline" 
                onClick={() => setIsCommentsOpen(true)}
              >
                <MessageCircle className="w-4 h-4 mr-2" />
                Comments
              </Button>
              
              <Button 
                variant="outline" 
                onClick={() => setIsDockerManagementOpen(true)}
              >
                <Settings className="w-4 h-4 mr-2" />
                Docker
              </Button>
              
              <Button 
                variant="outline" 
                onClick={() => setIsSubdomainManagementOpen(true)}
              >
                <LinkIcon className="w-4 h-4 mr-2" />
                Domains
              </Button>
              
              <Button 
                variant="outline" 
                onClick={() => setIsCollaborationOpen(true)}
                className="relative"
              >
                <Users className="w-4 h-4 mr-2" />
                Collaboration
                {activeCollaborators.length > 0 && (
                  <span className="absolute -top-2 -right-2 bg-blue-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">
                    {activeCollaborators.length}
                  </span>
                )}
              </Button>
              <Button variant="outline" onClick={handleDuplicate}>
                <Copy className="w-4 h-4 mr-2" />
                Duplicate
              </Button>
              <Button variant="outline" onClick={handleDelete}>
                <Trash2 className="w-4 h-4 mr-2" />
                Delete
              </Button>
            </div>
          </div>

          <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {/* Main Content */}
            <div className="lg:col-span-2 space-y-6">
              {/* AI Prompt Interface */}
              <Card>
                <CardHeader>
                  <CardTitle>AI Website Generator</CardTitle>
                  <CardDescription>
                    Describe what you want your website to look like and our AI will generate it for you
                  </CardDescription>
                </CardHeader>
                <CardContent>
                  <form onSubmit={handleSubmitPrompt} className="space-y-4">
                    <Textarea
                      value={prompt}
                      onChange={(e) => setPrompt(e.target.value)}
                      placeholder="Describe your website... (e.g., 'Create a modern landing page for a tech startup with a hero section, features, and contact form')"
                      className="min-h-[120px]"
                      disabled={isSubmitting}
                    />
                    <div className="flex justify-between items-center">
                      <span className="text-sm text-gray-500">
                        {prompt.length}/2000 characters
                      </span>
                      <Button type="submit" disabled={!prompt.trim() || isSubmitting}>
                        {isSubmitting ? (
                          <>
                            <Loader2 className="w-4 h-4 mr-2 animate-spin" />
                            Generating...
                          </>
                        ) : (
                          'Generate Website'
                        )}
                      </Button>
                    </div>
                  </form>
                </CardContent>
              </Card>

              {/* Generated Code Preview */}
              {project.generated_code && (
                <Card>
                  <CardHeader>
                    <CardTitle>Generated Code</CardTitle>
                    <CardDescription>
                      This is the HTML/CSS/JS code generated by AI
                    </CardDescription>
                  </CardHeader>
                  <CardContent>
                    <pre className="bg-gray-900 text-green-400 p-4 rounded-lg overflow-x-auto text-sm">
                      {project.generated_code}
                    </pre>
                  </CardContent>
                </Card>
              )}

              {/* Live Preview */}
              {activeContainer?.url && (
                <Card>
                  <CardHeader>
                    <CardTitle>Live Preview</CardTitle>
                    <CardDescription>
                      Your website is running live in a container
                    </CardDescription>
                  </CardHeader>
                  <CardContent>
                    <div className="aspect-video bg-gray-100 rounded-lg border-2 border-dashed border-gray-300 flex items-center justify-center">
                      <div className="text-center">
                        <ExternalLink className="w-8 h-8 text-gray-400 mx-auto mb-2" />
                        <p className="text-gray-600 mb-4">Preview not available in this demo</p>
                        <Button
                          variant="outline"
                          onClick={() => window.open(activeContainer.url, '_blank')}
                        >
                          Open in New Tab
                        </Button>
                      </div>
                    </div>
                  </CardContent>
                </Card>
              )}
            </div>

            {/* Sidebar */}
            <div className="space-y-6">
              {/* Container Status */}
              <Card>
                <CardHeader>
                  <CardTitle>Container Status</CardTitle>
                </CardHeader>
                <CardContent>
                  {activeContainer ? (
                    <div className="space-y-4">
                      <div className="flex items-center gap-2">
                        <div className={`w-2 h-2 rounded-full ${
                          activeContainer.status === 'running' ? 'bg-green-500' :
                          activeContainer.status === 'starting' ? 'bg-yellow-500' :
                          'bg-gray-400'
                        }`} />
                        <span className="text-sm font-medium">
                          {activeContainer.status === 'running' ? 'Running' :
                           activeContainer.status === 'starting' ? 'Starting...' :
                           'Stopped'}
                        </span>
                      </div>
                      
                      {activeContainer.url && (
                        <div className="text-sm text-gray-600">
                          <p>URL: {activeContainer.url}</p>
                          {activeContainer.port && <p>Port: {activeContainer.port}</p>}
                        </div>
                      )}

                      <div className="flex gap-2">
                        {activeContainer.status === 'running' ? (
                          <Button variant="outline" size="sm" onClick={handleStopContainer}>
                            <Square className="w-4 h-4 mr-2" />
                            Stop
                          </Button>
                        ) : (
                          <Button size="sm" onClick={handleStartContainer}>
                            <Play className="w-4 h-4 mr-2" />
                            Start
                          </Button>
                        )}
                      </div>
                    </div>
                  ) : (
                    <div className="text-center py-4">
                      <p className="text-gray-600 mb-4">No container running</p>
                      <Button size="sm" onClick={handleStartContainer}>
                        <Play className="w-4 h-4 mr-2" />
                        Start Container
                      </Button>
                    </div>
                  )}
                </CardContent>
              </Card>

              {/* Recent Prompts */}
              <Card>
                <CardHeader>
                  <CardTitle>Recent Prompts</CardTitle>
                </CardHeader>
                <CardContent>
                  {project.prompts.length === 0 ? (
                    <p className="text-gray-600 text-sm">No prompts yet</p>
                  ) : (
                    <div className="space-y-3">
                      {project.prompts.map((prompt) => (
                        <div key={prompt.id} className="border rounded-lg p-3">
                          <div className="flex items-center gap-2 mb-2">
                            <Badge className={promptStatusColors[prompt.status]}>
                              {promptStatusLabels[prompt.status]}
                            </Badge>
                            <span className="text-xs text-gray-500">
                              {formatDistanceToNow(new Date(prompt.created_at), { addSuffix: true })}
                            </span>
                          </div>
                          <p className="text-sm text-gray-700 line-clamp-2">
                            {prompt.prompt}
                          </p>
                        </div>
                      ))}
                    </div>
                  )}
                </CardContent>
              </Card>

              {/* Project Info */}
              <Card>
                <CardHeader>
                  <CardTitle>Project Info</CardTitle>
                </CardHeader>
                <CardContent className="space-y-2 text-sm">
                  <div className="flex justify-between">
                    <span className="text-gray-600">Created:</span>
                    <span>{formatDistanceToNow(new Date(project.created_at), { addSuffix: true })}</span>
                  </div>
                  {project.last_built_at && (
                    <div className="flex justify-between">
                      <span className="text-gray-600">Last built:</span>
                      <span>{formatDistanceToNow(new Date(project.last_built_at), { addSuffix: true })}</span>
                    </div>
                  )}
                  <div className="flex justify-between">
                    <span className="text-gray-600">Prompts:</span>
                    <span>{project.prompts.length}</span>
                  </div>
                </CardContent>
              </Card>
            </div>
          </div>
        </div>
      </div>

      {/* Collaboration Sidebar */}
              <CollaborationSidebar
          activeCollaborators={activeCollaborators}
          collaborationHistory={collaborationHistory}
          isOpen={isCollaborationOpen}
          onClose={() => setIsCollaborationOpen(false)}
        />
        
        <CommentsPanel
          projectId={project.id}
          isOpen={isCommentsOpen}
          onClose={() => setIsCommentsOpen(false)}
        />
        
        <DockerManagementPanel
          projectId={project.id}
          containerId={activeContainer?.id?.toString()}
          isOpen={isDockerManagementOpen}
          onClose={() => setIsDockerManagementOpen(false)}
        />
        
        <SubdomainManagementPanel
          projectId={project.id}
          currentSubdomain={project.subdomain}
          customDomain={project.custom_domain}
          dnsConfigured={project.dns_configured}
          isOpen={isSubdomainManagementOpen}
          onClose={() => setIsSubdomainManagementOpen(false)}
        />

      {/* Logs Modal */}
      {showLogs && (
        <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
          <div className="bg-white rounded-lg p-6 max-w-4xl max-h-[80vh] w-full mx-4">
            <div className="flex justify-between items-center mb-4">
              <h3 className="text-lg font-semibold">Container Logs</h3>
              <Button variant="outline" onClick={() => setShowLogs(false)}>
                Close
              </Button>
            </div>
            <div className="bg-gray-900 text-green-400 p-4 rounded-lg font-mono text-sm overflow-auto max-h-96">
              <pre className="whitespace-pre-wrap">{logs || 'No logs available'}</pre>
            </div>
          </div>
        </div>
      )}

      {/* Toast Notifications */}
      <ToastContainer toasts={toasts} onRemove={removeToast} />
    </>
  )
}
