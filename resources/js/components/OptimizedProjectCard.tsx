import React, { memo } from 'react'
import { formatDistanceToNow } from 'date-fns'
import { Play, Square, MoreHorizontal, ExternalLink } from 'lucide-react'
import { Button } from '@/components/ui/button'
import { Badge } from '@/components/ui/badge'

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

interface OptimizedProjectCardProps {
  project: Project
  onDeploy: (project: Project) => void
  onStop: (project: Project) => void
  onView: (project: Project) => void
}

// Memoized component to prevent unnecessary re-renders
const OptimizedProjectCard = memo<OptimizedProjectCardProps>(({ 
  project, 
  onDeploy, 
  onStop, 
  onView 
}) => {
  const getStatusColor = (status: string) => {
    switch (status) {
      case 'running': return 'bg-green-500'
      case 'stopped': return 'bg-yellow-500'
      case 'error': return 'bg-red-500'
      default: return 'bg-slate-500'
    }
  }

  const getStatusText = (status: string) => {
    switch (status) {
      case 'running': return 'Active'
      case 'stopped': return 'Idle'
      case 'error': return 'Error'
      default: return 'Draft'
    }
  }

  const activeContainer = project.containers.find(container => container.status === 'running')
  const status = activeContainer ? 'running' : (project.containers.length > 0 ? 'stopped' : 'draft')

  return (
    <div className="bg-white dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-700 p-6 hover:shadow-lg transition-shadow duration-200">
      {/* Project Header */}
      <div className="flex items-start justify-between mb-4">
        <div className="flex-1 min-w-0">
          <h3 className="text-lg font-semibold text-slate-900 dark:text-white truncate">
            {project.name}
          </h3>
          {project.description && (
            <p className="text-sm text-slate-600 dark:text-slate-400 mt-1 line-clamp-2">
              {project.description}
            </p>
          )}
        </div>
        
        <div className="flex items-center gap-2 ml-4">
          <div className={`w-3 h-3 ${getStatusColor(status)} rounded-full`}></div>
          <Badge variant="secondary" className="text-xs">
            {getStatusText(status)}
          </Badge>
        </div>
      </div>

      {/* Project Stats */}
      <div className="flex items-center gap-4 text-sm text-slate-500 dark:text-slate-400 mb-4">
        <span>{project.prompts.length} prompts</span>
        <span>•</span>
        <span>Updated {formatDistanceToNow(new Date(project.updated_at), { addSuffix: true })}</span>
      </div>

      {/* Action Buttons */}
      <div className="flex gap-2">
        <Button
          onClick={() => onView(project)}
          variant="outline"
          size="sm"
          className="flex-1"
        >
          <ExternalLink className="w-4 h-4 mr-2" />
          View
        </Button>
        
        {status === 'running' ? (
          <Button
            onClick={() => onStop(project)}
            variant="outline"
            size="sm"
            className="text-red-600 hover:text-red-700"
          >
            <Square className="w-4 h-4" />
          </Button>
        ) : (
          <Button
            onClick={() => onDeploy(project)}
            size="sm"
            className="bg-blue-600 hover:bg-blue-700"
          >
            <Play className="w-4 h-4 mr-2" />
            Deploy
          </Button>
        )}
        
        <Button variant="outline" size="sm">
          <MoreHorizontal className="w-4 h-4" />
        </Button>
      </div>
    </div>
  )
})

OptimizedProjectCard.displayName = 'OptimizedProjectCard'

export default OptimizedProjectCard
