import React, { useState, useEffect } from 'react';
import { router } from '@inertiajs/react';

interface DockerInfo {
  available: boolean;
  version: string;
  containers: number;
  images: number;
  status: string;
  error?: string;
}

interface Container {
  id: string;
  name: string;
  status: string;
  ports: string;
}

interface DockerManagerProps {
  projectId?: number;
  onContainerStart?: (url: string) => void;
  onContainerStop?: () => void;
}

export default function DockerManager({ projectId, onContainerStart, onContainerStop }: DockerManagerProps) {
  const [dockerInfo, setDockerInfo] = useState<DockerInfo | null>(null);
  const [containers, setContainers] = useState<Container[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [actionLoading, setActionLoading] = useState<string | null>(null);

  useEffect(() => {
    loadDockerInfo();
    loadContainers();
  }, []);

  const loadDockerInfo = async () => {
    try {
      const response = await fetch('/api/docker/info');
      const data = await response.json();
      
      if (data.success) {
        setDockerInfo(data.data);
      } else {
        setError(data.message || 'Failed to load Docker info');
      }
    } catch (err) {
      setError('Failed to connect to Docker service');
    } finally {
      setLoading(false);
    }
  };

  const loadContainers = async () => {
    try {
      const response = await fetch('/api/docker/containers');
      const data = await response.json();
      
      if (data.success) {
        setContainers(data.data);
      }
    } catch (err) {
      console.error('Failed to load containers:', err);
    }
  };

  const startContainer = async () => {
    if (!projectId) return;
    
    setActionLoading('start');
    try {
      const response = await fetch(`/api/projects/${projectId}/docker/start`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
        },
      });
      
      const data = await response.json();
      
      if (data.success) {
        onContainerStart?.(data.data.external_url || data.data.url);
        loadContainers();
      } else {
        setError(data.message || 'Failed to start container');
      }
    } catch (err) {
      setError('Failed to start container');
    } finally {
      setActionLoading(null);
    }
  };

  const stopContainer = async (containerId: string) => {
    setActionLoading(`stop-${containerId}`);
    try {
      const response = await fetch(`/api/containers/${containerId}/stop`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
        },
      });
      
      const data = await response.json();
      
      if (data.success) {
        onContainerStop?.();
        loadContainers();
      } else {
        setError(data.message || 'Failed to stop container');
      }
    } catch (err) {
      setError('Failed to stop container');
    } finally {
      setActionLoading(null);
    }
  };

  const restartContainer = async (containerId: string) => {
    setActionLoading(`restart-${containerId}`);
    try {
      const response = await fetch(`/api/containers/${containerId}/restart`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
        },
      });
      
      const data = await response.json();
      
      if (data.success) {
        loadContainers();
      } else {
        setError(data.message || 'Failed to restart container');
      }
    } catch (err) {
      setError('Failed to restart container');
    } finally {
      setActionLoading(null);
    }
  };

  const cleanupDocker = async () => {
    setActionLoading('cleanup');
    try {
      const response = await fetch('/api/docker/cleanup', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
        },
      });
      
      const data = await response.json();
      
      if (data.success) {
        loadDockerInfo();
        loadContainers();
      } else {
        setError(data.message || 'Failed to cleanup Docker');
      }
    } catch (err) {
      setError('Failed to cleanup Docker');
    } finally {
      setActionLoading(null);
    }
  };

  if (loading) {
    return (
      <div className="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <div className="animate-pulse">
          <div className="h-4 bg-gray-200 dark:bg-gray-700 rounded w-1/4 mb-4"></div>
          <div className="space-y-2">
            <div className="h-3 bg-gray-200 dark:bg-gray-700 rounded"></div>
            <div className="h-3 bg-gray-200 dark:bg-gray-700 rounded w-5/6"></div>
          </div>
        </div>
      </div>
    );
  }

  return (
    <div className="bg-white dark:bg-gray-800 rounded-lg shadow">
      <div className="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
        <h3 className="text-lg font-medium text-gray-900 dark:text-white">
          Docker Management
        </h3>
      </div>
      
      <div className="p-6 space-y-6">
        {error && (
          <div className="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-md p-4">
            <div className="flex">
              <div className="ml-3">
                <h3 className="text-sm font-medium text-red-800 dark:text-red-200">
                  Error
                </h3>
                <div className="mt-2 text-sm text-red-700 dark:text-red-300">
                  {error}
                </div>
              </div>
            </div>
          </div>
        )}

        {/* Docker Status */}
        {dockerInfo && (
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div className="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
              <div className="text-sm font-medium text-gray-500 dark:text-gray-400">Status</div>
              <div className={`text-lg font-semibold ${
                dockerInfo.available ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'
              }`}>
                {dockerInfo.available ? 'Available' : 'Unavailable'}
              </div>
            </div>
            
            <div className="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
              <div className="text-sm font-medium text-gray-500 dark:text-gray-400">Version</div>
              <div className="text-lg font-semibold text-gray-900 dark:text-white">
                {dockerInfo.version.split(' ')[2] || 'Unknown'}
              </div>
            </div>
            
            <div className="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
              <div className="text-sm font-medium text-gray-500 dark:text-gray-400">Containers</div>
              <div className="text-lg font-semibold text-gray-900 dark:text-white">
                {dockerInfo.containers}
              </div>
            </div>
            
            <div className="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
              <div className="text-sm font-medium text-gray-500 dark:text-gray-400">Images</div>
              <div className="text-lg font-semibold text-gray-900 dark:text-white">
                {dockerInfo.images}
              </div>
            </div>
          </div>
        )}

        {/* Actions */}
        <div className="flex flex-wrap gap-3">
          {projectId && (
            <button
              onClick={startContainer}
              disabled={!dockerInfo?.available || actionLoading === 'start'}
              className="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              {actionLoading === 'start' ? (
                <>
                  <svg className="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4"></circle>
                    <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  Starting...
                </>
              ) : (
                'Start Container'
              )}
            </button>
          )}
          
          <button
            onClick={cleanupDocker}
            disabled={actionLoading === 'cleanup'}
            className="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            {actionLoading === 'cleanup' ? (
              <>
                <svg className="animate-spin -ml-1 mr-3 h-5 w-5 text-gray-700 dark:text-gray-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4"></circle>
                  <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Cleaning...
              </>
            ) : (
              'Cleanup Docker'
            )}
          </button>
        </div>

        {/* Running Containers */}
        {containers.length > 0 && (
          <div>
            <h4 className="text-lg font-medium text-gray-900 dark:text-white mb-4">
              Running Containers
            </h4>
            <div className="space-y-3">
              {containers.map((container) => (
                <div key={container.id} className="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                  <div className="flex items-center justify-between">
                    <div>
                      <div className="text-sm font-medium text-gray-900 dark:text-white">
                        {container.name}
                      </div>
                      <div className="text-sm text-gray-500 dark:text-gray-400">
                        ID: {container.id} | Status: {container.status} | Ports: {container.ports}
                      </div>
                    </div>
                    <div className="flex space-x-2">
                      <button
                        onClick={() => restartContainer(container.id)}
                        disabled={actionLoading === `restart-${container.id}`}
                        className="inline-flex items-center px-3 py-1 border border-gray-300 dark:border-gray-600 text-xs font-medium rounded text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-600 hover:bg-gray-50 dark:hover:bg-gray-500 disabled:opacity-50 disabled:cursor-not-allowed"
                      >
                        {actionLoading === `restart-${container.id}` ? 'Restarting...' : 'Restart'}
                      </button>
                      <button
                        onClick={() => stopContainer(container.id)}
                        disabled={actionLoading === `stop-${container.id}`}
                        className="inline-flex items-center px-3 py-1 border border-red-300 dark:border-red-600 text-xs font-medium rounded text-red-700 dark:text-red-300 bg-white dark:bg-red-900/20 hover:bg-red-50 dark:hover:bg-red-900/30 disabled:opacity-50 disabled:cursor-not-allowed"
                      >
                        {actionLoading === `stop-${container.id}` ? 'Stopping...' : 'Stop'}
                      </button>
                    </div>
                  </div>
                </div>
              ))}
            </div>
          </div>
        )}
      </div>
    </div>
  );
}
