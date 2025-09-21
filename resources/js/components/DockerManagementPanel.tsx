import { useState, useEffect } from 'react'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { Badge } from '@/components/ui/badge'
import { 
  Activity, 
  RefreshCw, 
  Square, 
  Terminal, 
  Trash2, 
  HardDrive, 
  Cpu, 
  Clock,
  AlertCircle,
  CheckCircle,
  XCircle
} from 'lucide-react'

interface ContainerStats {
  status: string
  cpu_usage: string
  memory_usage: string
  uptime: string
}

interface ContainerHealth {
  status: string
  message: string
  healthy: boolean
}

interface DockerManagementPanelProps {
  projectId: number
  containerId?: string
  isOpen: boolean
  onClose: () => void
}

export default function DockerManagementPanel({ 
  projectId, 
  containerId, 
  isOpen, 
  onClose 
}: DockerManagementPanelProps) {
  const [stats, setStats] = useState<ContainerStats | null>(null)
  const [health, setHealth] = useState<ContainerHealth | null>(null)
  const [logs, setLogs] = useState('')
  const [isLoading, setIsLoading] = useState(false)
  const [showLogs, setShowLogs] = useState(false)
  const [allContainers, setAllContainers] = useState<any[]>([])

  const fetchStats = async () => {
    if (!containerId) return
    
    try {
      const response = await fetch(`/api/projects/${projectId}/docker/status`)
      const data = await response.json()
      
      if (data.stats) {
        setStats(data.stats)
      }
      if (data.health) {
        setHealth(data.health)
      }
    } catch (error) {
      console.error('Failed to fetch stats:', error)
    }
  }

  const fetchLogs = async () => {
    try {
      const response = await fetch(`/api/projects/${projectId}/docker/logs`)
      const data = await response.json()
      
      if (data.success) {
        setLogs(data.logs)
        setShowLogs(true)
      }
    } catch (error) {
      console.error('Failed to fetch logs:', error)
    }
  }

  const fetchAllContainers = async () => {
    try {
      const response = await fetch('/api/containers')
      const data = await response.json()
      
      if (data.success) {
        setAllContainers(data.containers)
      }
    } catch (error) {
      console.error('Failed to fetch containers:', error)
    }
  }

  const restartContainer = async () => {
    setIsLoading(true)
    try {
      const response = await fetch(`/api/projects/${projectId}/docker/restart`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
        },
      })
      
      const data = await response.json()
      if (data.success) {
        // Refresh stats after restart
        setTimeout(() => fetchStats(), 2000)
      }
    } catch (error) {
      console.error('Failed to restart container:', error)
    } finally {
      setIsLoading(false)
    }
  }

  const stopContainer = async () => {
    setIsLoading(true)
    try {
      const response = await fetch(`/api/projects/${projectId}/docker/stop`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
        },
      })
      
      const data = await response.json()
      if (data.success) {
        onClose()
      }
    } catch (error) {
      console.error('Failed to stop container:', error)
    } finally {
      setIsLoading(false)
    }
  }

  const cleanupResources = async () => {
    try {
      const response = await fetch('/api/docker/cleanup', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
        },
      })
      
      const data = await response.json()
      if (data.success) {
        // Refresh containers list
        fetchAllContainers()
      }
    } catch (error) {
      console.error('Failed to cleanup resources:', error)
    }
  }

  useEffect(() => {
    if (isOpen) {
      fetchStats()
      fetchAllContainers()
      
      // Refresh stats every 30 seconds
      const interval = setInterval(fetchStats, 30000)
      return () => clearInterval(interval)
    }
  }, [isOpen, containerId])

  if (!isOpen) return null

  const getHealthIcon = (healthy: boolean) => {
    if (healthy) {
      return <CheckCircle className="w-4 h-4 text-green-500" />
    }
    return <XCircle className="w-4 h-4 text-red-500" />
  }

  const getStatusColor = (status: string) => {
    switch (status.toLowerCase()) {
      case 'running':
        return 'bg-green-100 text-green-800'
      case 'starting':
        return 'bg-yellow-100 text-yellow-800'
      case 'stopped':
        return 'bg-gray-100 text-gray-800'
      case 'error':
        return 'bg-red-100 text-red-800'
      default:
        return 'bg-gray-100 text-gray-800'
    }
  }

  return (
    <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div className="bg-white rounded-lg p-6 max-w-6xl max-h-[90vh] w-full mx-4 overflow-y-auto">
        <div className="flex justify-between items-center mb-6">
          <h2 className="text-2xl font-bold">Docker Management</h2>
          <Button variant="outline" onClick={onClose}>
            Close
          </Button>
        </div>

        <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
          {/* Container Stats */}
          <Card>
            <CardHeader>
              <CardTitle className="flex items-center gap-2">
                <Activity className="w-5 h-5" />
                Container Statistics
              </CardTitle>
            </CardHeader>
            <CardContent>
              {stats ? (
                <div className="space-y-4">
                  <div className="flex items-center justify-between">
                    <span className="text-sm font-medium">Status:</span>
                    <Badge className={getStatusColor(stats.status)}>
                      {stats.status}
                    </Badge>
                  </div>
                  
                  <div className="flex items-center justify-between">
                    <span className="text-sm font-medium">CPU Usage:</span>
                    <span className="text-sm">{stats.cpu_usage}</span>
                  </div>
                  
                  <div className="flex items-center justify-between">
                    <span className="text-sm font-medium">Memory Usage:</span>
                    <span className="text-sm">{stats.memory_usage}</span>
                  </div>
                  
                  <div className="flex items-center justify-between">
                    <span className="text-sm font-medium">Uptime:</span>
                    <span className="text-sm">{stats.uptime}</span>
                  </div>
                </div>
              ) : (
                <div className="text-center py-4">
                  <p className="text-gray-600">No container running</p>
                </div>
              )}
            </CardContent>
          </Card>

          {/* Container Health */}
          <Card>
            <CardHeader>
              <CardTitle className="flex items-center gap-2">
                <AlertCircle className="w-5 h-5" />
                Health Status
              </CardTitle>
            </CardHeader>
            <CardContent>
              {health ? (
                <div className="space-y-4">
                  <div className="flex items-center gap-2">
                    {getHealthIcon(health.healthy)}
                    <span className="text-sm font-medium">
                      {health.healthy ? 'Healthy' : 'Unhealthy'}
                    </span>
                  </div>
                  
                  <div className="text-sm text-gray-600">
                    <p>{health.message}</p>
                  </div>
                </div>
              ) : (
                <div className="text-center py-4">
                  <p className="text-gray-600">No health data available</p>
                </div>
              )}
            </CardContent>
          </Card>

          {/* Container Actions */}
          <Card>
            <CardHeader>
              <CardTitle>Container Actions</CardTitle>
            </CardHeader>
            <CardContent>
              <div className="space-y-3">
                <Button 
                  onClick={restartContainer}
                  disabled={isLoading}
                  className="w-full"
                >
                  <RefreshCw className={`w-4 h-4 mr-2 ${isLoading ? 'animate-spin' : ''}`} />
                  Restart Container
                </Button>
                
                <Button 
                  onClick={stopContainer}
                  disabled={isLoading}
                  variant="outline"
                  className="w-full"
                >
                  <Square className="w-4 h-4 mr-2" />
                  Stop Container
                </Button>
                
                <Button 
                  onClick={fetchLogs}
                  variant="outline"
                  className="w-full"
                >
                  <Terminal className="w-4 h-4 mr-2" />
                  View Logs
                </Button>
              </div>
            </CardContent>
          </Card>

          {/* All Containers */}
          <Card>
            <CardHeader>
              <CardTitle>All Running Containers</CardTitle>
              <CardDescription>
                {allContainers.length} container(s) running
              </CardDescription>
            </CardHeader>
            <CardContent>
              <div className="space-y-3">
                {allContainers.map((container, index) => (
                  <div key={index} className="flex items-center justify-between p-3 border rounded-lg">
                    <div>
                      <p className="font-medium text-sm">{container.name}</p>
                      <p className="text-xs text-gray-500">ID: {container.id}</p>
                    </div>
                    <Badge className={getStatusColor(container.status)}>
                      {container.status}
                    </Badge>
                  </div>
                ))}
                
                {allContainers.length === 0 && (
                  <p className="text-center text-gray-600 py-4">No containers running</p>
                )}
              </div>
              
              <div className="mt-4 pt-4 border-t">
                <Button 
                  onClick={cleanupResources}
                  variant="outline"
                  size="sm"
                  className="w-full"
                >
                  <Trash2 className="w-4 h-4 mr-2" />
                  Cleanup Old Resources
                </Button>
              </div>
            </CardContent>
          </Card>
        </div>

        {/* Logs Modal */}
        {showLogs && (
          <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-60">
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
      </div>
    </div>
  )
}
