import type React from "react"
import { Box, ChevronLeft, ChevronRight, ChevronDown, X, Menu, Plus, Search } from "../icons"
import { Button } from "../ui"
import { Skeleton } from "../ui/skeleton"

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

interface SidebarProps {
  sidebarCollapsed: boolean
  sidebarHidden: boolean
  searchQuery: string
  showCreditsDropdown: boolean
  projects: Project[]
  balanceInfo: {
    balance: number
    formatted_balance: string
    total_spent: number
    formatted_total_spent: string
    can_generate: boolean
  }
  isLoading?: boolean
  onToggleCollapse: () => void
  onToggleHidden: () => void
  onSearchChange: (query: string) => void
  onToggleCreditsDropdown: () => void
  onNewSandbox: () => void
  onNewProject: () => void
}

export const Sidebar = ({
  sidebarCollapsed,
  sidebarHidden,
  searchQuery,
  showCreditsDropdown,
  projects,
  balanceInfo,
  isLoading = false,
  onToggleCollapse,
  onToggleHidden,
  onSearchChange,
  onToggleCreditsDropdown,
  onNewSandbox,
  onNewProject,
}: SidebarProps) => {
  // Helper function to get project status
  const getProjectStatus = (project: Project) => {
    const activeContainer = project.containers.find(container => container.status === 'running')
    if (activeContainer) return 'running'
    if (project.containers.length > 0) return 'stopped'
    return 'draft'
  }

  // Helper function to get project status color
  const getStatusColor = (status: string) => {
    switch (status) {
      case 'running': return 'bg-green-400'
      case 'stopped': return 'bg-slate-500'
      case 'draft': return 'bg-yellow-400'
      default: return 'bg-slate-500'
    }
  }

  // Helper function to format relative time
  const formatRelativeTime = (dateString: string) => {
    const date = new Date(dateString)
    const now = new Date()
    const diffInHours = Math.floor((now.getTime() - date.getTime()) / (1000 * 60 * 60))
    
    if (diffInHours < 1) return 'Just now'
    if (diffInHours < 24) return `${diffInHours}h ago`
    const diffInDays = Math.floor(diffInHours / 24)
    if (diffInDays < 7) return `${diffInDays}d ago`
    return date.toLocaleDateString()
  }

  // Helper function to get project initial
  const getProjectInitial = (name: string) => {
    return name.charAt(0).toUpperCase()
  }

  // Helper function to get project avatar colors
  const getProjectAvatarColors = (name: string) => {
    const colors = [
      'from-[#6D28D9] to-[#8B5CF6]', // Purple
      'from-[#DB2777] to-[#F472B6]', // Pink
      'from-[#059669] to-[#10B981]', // Green
      'from-[#DC2626] to-[#EF4444]', // Red
      'from-[#D97706] to-[#F59E0B]', // Orange
      'from-[#2563EB] to-[#3B82F6]', // Blue
      'from-[#7C3AED] to-[#8B5CF6]', // Violet
      'from-[#0891B2] to-[#06B6D4]', // Cyan
    ]
    const hash = name.split('').reduce((a, b) => {
      a = ((a << 5) - a) + b.charCodeAt(0)
      return a & a
    }, 0)
    return colors[Math.abs(hash) % colors.length]
  }

  // Filter projects based on search query
  const filteredProjects = projects.filter(project =>
    project.name.toLowerCase().includes(searchQuery.toLowerCase()) ||
    (project.description && project.description.toLowerCase().includes(searchQuery.toLowerCase()))
  )
  if (sidebarHidden) {
    return (
      <button
        onClick={onToggleHidden}
        className="fixed top-6 left-6 z-50 w-12 h-12 bg-slate-800/90 hover:bg-slate-700 border border-slate-700/60 rounded-lg flex items-center justify-center transition-all duration-200 backdrop-blur-sm"
      >
        <Menu className="w-6 h-6 text-slate-300" />
      </button>
    )
  }

  return (
    <div
      className={`${
        sidebarCollapsed ? "w-20" : "w-80"
      } bg-gradient-to-b from-[#0B0F1A] via-[#0D1220] to-[#0B0F1A] border-gradient-to-b from-[#1B2432] to-[#151B28] flex flex-col transition-all duration-300 ease-in-out overflow-hidden shadow-2xl relative border-r-0`}
    >
      <div className="absolute top-0 right-0 w-px h-full bg-gradient-to-b from-transparent via-slate-600/20 to-transparent py-0"></div>
      
      {/* Header */}
      <div className="p-6 border-b border-slate-800/50 bg-gradient-to-r from-slate-900/50 to-slate-800/30 backdrop-blur-sm">
        <div className={`flex items-center gap-3 ${sidebarCollapsed ? "justify-center" : ""}`}>
          <div className="w-10 h-10 bg-gradient-to-br from-orange-500 via-orange-600 to-orange-700 rounded-xl flex items-center justify-center shadow-lg shadow-orange-500/30 ring-2 ring-orange-500/20">
            <Box className="w-6 h-6 text-white" />
          </div>
          {!sidebarCollapsed && (
            <div className="flex flex-col">
              <span className="font-bold text-white text-xl tracking-tight">R3KT</span>
              <span className="text-xs text-slate-400 font-medium">Development Platform</span>
            </div>
          )}
        </div>
        {!sidebarCollapsed && (
          <div className="flex items-center gap-2 mt-4">
            <Button
              size="sm"
              className="w-9 h-9 p-0 bg-slate-800/50 hover:bg-slate-700/70 border border-slate-700/50 rounded-lg backdrop-blur-sm"
              onClick={onToggleCollapse}
            >
              <ChevronLeft className="w-4 h-4" />
            </Button>
            <Button
              size="sm"
              className="w-9 h-9 p-0 bg-slate-800/50 hover:bg-slate-700/70 border border-slate-700/50 rounded-lg backdrop-blur-sm"
              onClick={onToggleHidden}
            >
              <X className="w-4 h-4" />
            </Button>
          </div>
        )}
        {sidebarCollapsed && (
          <Button
            size="sm"
            className="w-9 h-9 p-0 bg-slate-800/50 hover:bg-slate-700/70 border border-slate-700/50 rounded-lg backdrop-blur-sm absolute top-6 right-4"
            onClick={onToggleCollapse}
          >
            <ChevronRight className="w-4 h-4" />
          </Button>
        )}
      </div>

      {!sidebarCollapsed && (
        <>
          {/* Sandboxes Section */}
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
                  onClick={onNewSandbox}
                >
                  <Plus className="w-4 h-4 text-blue-400" />
                </Button>
                <Button
                  size="sm"
                  className="w-8 h-8 p-0 bg-slate-800/50 hover:bg-slate-700/70 border border-slate-700/50 rounded-lg backdrop-blur-sm"
                  onClick={() => onSearchChange(searchQuery ? "" : "search")}
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
                    onChange={(e) => onSearchChange(e.target.value)}
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

          {/* Projects Section */}
          <div className="px-6 mt-10 flex-1">
            <div className="flex items-center justify-between mb-6">
              <div className="flex items-center gap-3">
                <div className="w-1 h-6 bg-gradient-to-b from-purple-400 to-purple-600 rounded-full"></div>
                <h3 className="text-sm tracking-wider text-slate-300 uppercase font-bold">Projects</h3>
              </div>
              <div className="flex items-center gap-2">
                <Button
                  size="sm"
                  className="w-8 h-8 p-0 bg-slate-800/50 hover:bg-slate-700/70 border border-slate-700/50 rounded-lg backdrop-blur-sm"
                >
                  <Search className="w-4 h-4 text-slate-400" />
                </Button>
              </div>
            </div>
            <div className="space-y-3">
              {isLoading ? (
                // Loading skeletons
                Array.from({ length: 3 }).map((_, index) => (
                  <div key={index} className="group flex items-center gap-4 p-4 rounded-xl bg-gradient-to-r from-slate-800/40 to-slate-900/40 border border-slate-700/40 backdrop-blur-sm">
                    <div className="relative">
                      <Skeleton className="w-10 h-10 rounded-xl bg-slate-700" />
                      <Skeleton className="absolute -top-1 -right-1 w-3 h-3 rounded-full bg-slate-600" />
                    </div>
                    <div className="flex-1 min-w-0 space-y-1">
                      <Skeleton className="h-4 w-24 bg-slate-700" />
                      <Skeleton className="h-3 w-16 bg-slate-700" />
                    </div>
                  </div>
                ))
              ) : filteredProjects.length > 0 ? (
                filteredProjects.map((project) => {
                  const status = getProjectStatus(project)
                  const statusColor = getStatusColor(status)
                  const statusText = status === 'running' ? 'Active' : status === 'stopped' ? 'Idle' : 'Draft'
                  
                  return (
                    <div 
                      key={project.id}
                      className="group flex items-center gap-4 p-4 rounded-xl bg-gradient-to-r from-slate-800/40 to-slate-900/40 border border-slate-700/40 hover:from-slate-800/60 hover:to-slate-900/60 hover:border-slate-600/60 transition-all duration-200 cursor-pointer backdrop-blur-sm"
                      onClick={() => window.location.href = `/projects/${project.id}/sandbox`}
                    >
                      <div className="relative">
                        <div className={`w-10 h-10 bg-gradient-to-br ${getProjectAvatarColors(project.name)} rounded-xl flex items-center justify-center shadow-lg`}>
                          <span className="text-sm font-bold text-white">{getProjectInitial(project.name)}</span>
                        </div>
                        <div className={`absolute -top-1 -right-1 w-3 h-3 ${statusColor} rounded-full border-2 border-slate-900`}></div>
                      </div>
                      <div className="flex-1 min-w-0">
                        <span className="text-sm font-semibold text-white group-hover:text-slate-100 transition-colors">
                          {project.name}
                        </span>
                        <p className="text-xs text-slate-400 mt-0.5">
                          {statusText} • {formatRelativeTime(project.updated_at)}
                        </p>
                      </div>
                    </div>
                  )
                })
              ) : (
                <div className="bg-gradient-to-br from-slate-800/30 to-slate-900/30 rounded-xl border border-slate-700/40 p-8 text-center backdrop-blur-sm">
                  <div className="w-12 h-12 bg-gradient-to-br from-slate-600/50 to-slate-700/50 rounded-xl flex items-center justify-center mx-auto mb-4">
                    <Box className="w-6 h-6 text-slate-400" />
                  </div>
                  <p className="text-sm text-slate-400 font-medium">
                    {searchQuery ? 'No projects found' : 'No projects yet'}
                  </p>
                  <p className="text-xs text-slate-500 mt-1">
                    {searchQuery ? 'Try a different search term' : 'Create your first project to get started'}
                  </p>
                </div>
              )}
            </div>
          </div>

          {/* Footer */}
          <div className="p-6 space-y-4 border-t border-slate-800/50 bg-gradient-to-r from-slate-900/50 to-slate-800/30 backdrop-blur-sm">
            <div className="relative">
              <button
                onClick={onToggleCreditsDropdown}
                className="w-full flex items-center justify-between bg-gradient-to-r from-slate-800/60 to-slate-900/60 rounded-xl p-4 hover:from-slate-800/80 hover:to-slate-900/80 transition-all duration-200 border border-slate-700/40 backdrop-blur-sm group"
              >
                <div className="flex items-center gap-3">
                  <div className="w-8 h-8 bg-gradient-to-br from-emerald-500 to-green-600 rounded-lg flex items-center justify-center">
                    <span className="text-xs font-bold text-white">$</span>
                  </div>
                  <div className="text-left">
                    <span className="text-sm font-semibold text-white block">{balanceInfo.formatted_balance} credits</span>
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
                      <span className="text-emerald-400 font-semibold">{balanceInfo.formatted_balance}</span>
                    </div>
                    <div className="flex justify-between items-center">
                      <span className="text-slate-400">Total spent:</span>
                      <span className="text-slate-300 font-semibold">{balanceInfo.formatted_total_spent}</span>
                    </div>
                    <div className="w-full bg-slate-700 rounded-full h-2">
                      <div
                        className={`h-2 rounded-full ${balanceInfo.can_generate ? 'bg-gradient-to-r from-emerald-400 to-green-500' : 'bg-gradient-to-r from-red-400 to-red-500'}`}
                        style={{ width: `${Math.min((balanceInfo.balance / 10) * 100, 100)}%` }}
                      ></div>
                    </div>
                    {!balanceInfo.can_generate && (
                      <div className="p-3 bg-yellow-900/20 border border-yellow-700/40 rounded-lg">
                        <div className="flex items-center">
                          <div className="flex-shrink-0">
                            <svg className="h-4 w-4 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                              <path fillRule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clipRule="evenodd" />
                            </svg>
                          </div>
                          <div className="ml-2">
                            <p className="text-xs text-yellow-300">
                              Insufficient balance for AI generation
                            </p>
                          </div>
                        </div>
                      </div>
                    )}
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
            onClick={onNewSandbox}
          >
            <Plus className="w-5 h-5 text-blue-400" />
          </Button>
          {filteredProjects.slice(0, 3).map((project) => {
            const status = getProjectStatus(project)
            const statusColor = getStatusColor(status)
            
            return (
              <div 
                key={project.id}
                className={`w-12 h-12 bg-gradient-to-br ${getProjectAvatarColors(project.name)} rounded-xl flex items-center justify-center shadow-lg relative cursor-pointer hover:scale-105 transition-transform duration-200`}
                onClick={() => window.location.href = `/projects/${project.id}/sandbox`}
              >
                <span className="text-sm font-bold text-white">{getProjectInitial(project.name)}</span>
                <div className={`absolute -top-1 -right-1 w-3 h-3 ${statusColor} rounded-full border-2 border-slate-900`}></div>
              </div>
            )
          })}
        </div>
      )}
    </div>
  )
}
