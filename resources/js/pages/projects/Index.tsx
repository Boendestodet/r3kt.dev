import { Head, Link, router } from '@inertiajs/react'
import { useState } from 'react'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'
import StatusIndicator from '@/components/StatusIndicator'
import { Plus, Copy, Trash2, Play, Eye } from 'lucide-react'
import { formatDistanceToNow } from 'date-fns'
import * as routes from '@/routes/projects'

interface Project {
  id: number
  name: string
  description?: string
  status: 'draft' | 'building' | 'ready' | 'error'
  is_public: boolean
  preview_url?: string
  last_built_at?: string
  created_at: string
  containers: Container[]
  prompts: Prompt[]
}

interface Container {
  id: number
  status: 'starting' | 'running' | 'stopped' | 'error'
  url?: string
}

interface Prompt {
  id: number
  status: 'pending' | 'processing' | 'completed' | 'failed'
}

interface Props {
  projects: {
    data: Project[]
    links: any[]
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

export default function ProjectsIndex({ projects }: Props) {
  const [deleting, setDeleting] = useState<number | null>(null)

  const handleDelete = (project: Project) => {
    if (confirm(`Are you sure you want to delete "${project.name}"? This action cannot be undone.`)) {
      router.delete(routes.destroy.url({ project: project.id }))
    }
  }

  const handleDuplicate = (project: Project) => {
    router.post(routes.duplicate.url({ project: project.id }))
  }

  const getContainerStatus = (containers: Container[]) => {
    const running = containers.find(c => c.status === 'running')
    if (running) return { status: 'running', url: running.url }
    
    const starting = containers.find(c => c.status === 'starting')
    if (starting) return { status: 'starting' }
    
    return { status: 'stopped' }
  }

  return (
    <>
      <Head title="Projects" />
      
      <div className="min-h-screen bg-gray-50">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
          {/* Header */}
          <div className="flex justify-between items-center mb-8">
            <div>
              <h1 className="text-3xl font-bold text-gray-900">My Projects</h1>
              <p className="text-gray-600 mt-2">Create and manage your AI-generated websites</p>
            </div>
            <Link href={routes.create.url()}>
              <Button className="flex items-center gap-2">
                <Plus className="w-4 h-4" />
                New Project
              </Button>
            </Link>
          </div>

          {/* Projects Grid */}
          {projects.data.length === 0 ? (
            <div className="text-center py-12">
              <div className="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                <Plus className="w-8 h-8 text-gray-400" />
              </div>
              <h3 className="text-lg font-medium text-gray-900 mb-2">No projects yet</h3>
              <p className="text-gray-600 mb-6">Get started by creating your first AI-generated website</p>
              <Link href={routes.create.url()}>
                <Button>Create Your First Project</Button>
              </Link>
            </div>
          ) : (
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              {projects.data.map((project) => {
                const containerStatus = getContainerStatus(project.containers)
                const lastPrompt = project.prompts[0]
                
                return (
                  <Card key={project.id} className="hover:shadow-lg transition-shadow">
                    <CardHeader>
                      <div className="flex justify-between items-start">
                        <div className="flex-1">
                          <CardTitle className="text-lg">{project.name}</CardTitle>
                          {project.description && (
                            <CardDescription className="mt-1">
                              {project.description}
                            </CardDescription>
                          )}
                        </div>
                        <div className="flex gap-1">
                                          <StatusIndicator status={project.status} size="sm" />
                          {project.is_public && (
                            <Badge variant="outline">Public</Badge>
                          )}
                        </div>
                      </div>
                    </CardHeader>
                    
                    <CardContent>
                      <div className="space-y-4">
                        {/* Container Status */}
                        <div className="flex items-center gap-2">
                          <div className={`w-2 h-2 rounded-full ${
                            containerStatus.status === 'running' ? 'bg-green-500' :
                            containerStatus.status === 'starting' ? 'bg-yellow-500' :
                            'bg-gray-400'
                          }`} />
                          <span className="text-sm text-gray-600">
                            {containerStatus.status === 'running' ? 'Live' :
                             containerStatus.status === 'starting' ? 'Starting...' :
                             'Stopped'}
                          </span>
                        </div>

                        {/* Last Activity */}
                        <div className="text-sm text-gray-500">
                          {project.last_built_at ? (
                            <>Last built {formatDistanceToNow(new Date(project.last_built_at), { addSuffix: true })}</>
                          ) : (
                            <>Created {formatDistanceToNow(new Date(project.created_at), { addSuffix: true })}</>
                          )}
                        </div>

                        {/* Actions */}
                        <div className="flex gap-2">
                          <Link href={routes.show.url({ project: project.id })} className="flex-1">
                            <Button variant="outline" className="w-full">
                              <Eye className="w-4 h-4 mr-2" />
                              View
                            </Button>
                          </Link>
                          
                          {containerStatus.status === 'running' && containerStatus.url && (
                            <Button
                              variant="outline"
                              size="sm"
                              onClick={() => window.open(containerStatus.url, '_blank')}
                            >
                              <Play className="w-4 h-4" />
                            </Button>
                          )}
                          
                          <Button
                            variant="outline"
                            size="sm"
                            onClick={() => handleDuplicate(project)}
                          >
                            <Copy className="w-4 h-4" />
                          </Button>
                          
                          <Button
                            variant="outline"
                            size="sm"
                            onClick={() => handleDelete(project)}
                            disabled={deleting === project.id}
                          >
                            <Trash2 className="w-4 h-4" />
                          </Button>
                        </div>
                      </div>
                    </CardContent>
                  </Card>
                )
              })}
            </div>
          )}
        </div>
      </div>
    </>
  )
}
