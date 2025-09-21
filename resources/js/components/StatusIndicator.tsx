import { Badge } from '@/components/ui/badge'
import { CheckCircle, Clock, AlertCircle, XCircle, Loader2, Play, Square, RefreshCw } from 'lucide-react'

interface StatusIndicatorProps {
  status: string
  size?: 'sm' | 'md' | 'lg'
  showIcon?: boolean
  className?: string
}

const statusConfig = {
  // Project statuses
  draft: {
    color: 'bg-gray-100 text-gray-800',
    icon: Clock,
    label: 'Draft'
  },
  building: {
    color: 'bg-blue-100 text-blue-800',
    icon: Loader2,
    label: 'Building',
    animate: true
  },
  ready: {
    color: 'bg-green-100 text-green-800',
    icon: CheckCircle,
    label: 'Ready'
  },
  error: {
    color: 'bg-red-100 text-red-800',
    icon: XCircle,
    label: 'Error'
  },
  
  // Container statuses
  starting: {
    color: 'bg-yellow-100 text-yellow-800',
    icon: Play,
    label: 'Starting',
    animate: true
  },
  running: {
    color: 'bg-green-100 text-green-800',
    icon: CheckCircle,
    label: 'Running'
  },
  stopped: {
    color: 'bg-gray-100 text-gray-800',
    icon: Square,
    label: 'Stopped'
  },
  
  // Prompt statuses
  pending: {
    color: 'bg-yellow-100 text-yellow-800',
    icon: Clock,
    label: 'Pending'
  },
  processing: {
    color: 'bg-blue-100 text-blue-800',
    icon: Loader2,
    label: 'Processing',
    animate: true
  },
  completed: {
    color: 'bg-green-100 text-green-800',
    icon: CheckCircle,
    label: 'Completed'
  },
  failed: {
    color: 'bg-red-100 text-red-800',
    icon: XCircle,
    label: 'Failed'
  }
}

const sizeClasses = {
  sm: 'text-xs px-2 py-1',
  md: 'text-sm px-3 py-1',
  lg: 'text-base px-4 py-2'
}

const iconSizes = {
  sm: 'w-3 h-3',
  md: 'w-4 h-4',
  lg: 'w-5 h-5'
}

export default function StatusIndicator({ 
  status, 
  size = 'md', 
  showIcon = true, 
  className = '' 
}: StatusIndicatorProps) {
  const config = statusConfig[status as keyof typeof statusConfig] || {
    color: 'bg-gray-100 text-gray-800',
    icon: AlertCircle,
    label: status,
    animate: false
  }

  const Icon = config.icon
  const isAnimated = config.animate

  return (
    <Badge 
      className={`${config.color} ${sizeClasses[size]} ${className} ${
        isAnimated ? 'animate-pulse' : ''
      }`}
    >
      {showIcon && (
        <Icon className={`${iconSizes[size]} mr-1 ${
          isAnimated ? 'animate-spin' : ''
        }`} />
      )}
      {config.label}
    </Badge>
  )
}
