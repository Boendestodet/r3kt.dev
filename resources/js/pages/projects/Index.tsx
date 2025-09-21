"use client"

import type React from "react"
import { useState } from "react"

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
      setShowDeploymentModal(true)
      setIsDeploying(true)
      setDeploymentProgress(0)

      const interval = setInterval(() => {
        setDeploymentProgress((prev) => {
          if (prev >= 100) {
            clearInterval(interval)
            setIsDeploying(false)
            setTimeout(() => {
              // Redirect to sandbox page after successful deployment
              window.location.href = `/projects/1/sandbox`
            }, 1000)
            return 100
          }
          return prev + 10
        })
      }, 200)
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
    setTimeout(() => {
      setShowNewProjectModal(false)
      alert("New project created!")
    }, 1500)
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

      {(showNewSandboxModal || showNewProjectModal) && (
        <div className="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50">
          <div className="bg-slate-800 rounded-2xl p-8 border border-slate-700/60 shadow-2xl">
            <div className="flex items-center gap-4 mb-6">
              <div className="w-10 h-10 border-2 border-orange-400 border-t-transparent rounded-full animate-spin" />
              <span className="text-white font-medium text-lg">
                Creating {showNewSandboxModal ? "sandbox" : "project"}...
              </span>
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