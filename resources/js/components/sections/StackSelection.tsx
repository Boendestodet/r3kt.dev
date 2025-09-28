import type React from "react"
import { Check, ChevronRight, Flame, Box } from "../icons"
import { Badge } from "../ui"

interface Stack {
  name: string
  description: string
  hot: boolean
  comingSoon: boolean
}

interface StackSelectionProps {
  selectedModel: string | null
  selectedStack: string | null
  activeTab: string
  onTabChange: (tab: string) => void
  onStackSelect: (stack: string) => void
  onDeploy: () => void
}

const tabs = ["Modern Web", "Backend", "Game Dev", "Traditional", "Static", "Manual"]

const modernWebStacks: Stack[] = [
  { name: "Next.js", description: "Next.js with App Router", hot: true, comingSoon: false },
  { name: "Vite + React", description: "Vite + React + TypeScript", hot: true, comingSoon: false },
  { name: "SvelteKit", description: "SvelteKit with TypeScript", hot: false, comingSoon: false },
  { name: "Vite + Vue", description: "Vite + Vue + TypeScript", hot: true, comingSoon: false },
  { name: "Astro", description: "Astro with TypeScript", hot: false, comingSoon: false },
  { name: "Nuxt 3", description: "Nuxt 3 with TypeScript", hot: false, comingSoon: false },
]

const backendStacks: Stack[] = [
  { name: "Node.js + Express", description: "Express.js with TypeScript", hot: true, comingSoon: false },
  { name: "Python + FastAPI", description: "FastAPI with async support", hot: true, comingSoon: false },
  { name: "Go + Gin", description: "Gin framework with Go", hot: false, comingSoon: false },
  { name: "Rust + Axum", description: "Axum web framework", hot: false, comingSoon: true },
]

const gameDevStacks: Stack[] = [
  { name: "Unity + C#", description: "Unity game engine", hot: true, comingSoon: false },
  { name: "Unreal + C++", description: "Unreal Engine 5", hot: false, comingSoon: false },
  { name: "Godot + GDScript", description: "Godot 4.x engine", hot: false, comingSoon: true },
]

const traditionalStacks: Stack[] = [
  { name: "PHP + Laravel", description: "Laravel framework", hot: false, comingSoon: false },
  { name: "Java + Spring", description: "Spring Boot", hot: false, comingSoon: false },
  { name: "C# + .NET", description: ".NET Core", hot: false, comingSoon: true },
]

const staticStacks: Stack[] = [
  { name: "Jekyll", description: "Ruby-based static site", hot: false, comingSoon: false },
  { name: "Hugo", description: "Go-based static site", hot: false, comingSoon: false },
  { name: "11ty", description: "JavaScript static site", hot: false, comingSoon: true },
]

const getStacksForTab = (tab: string): Stack[] => {
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

export const StackSelection = ({
  selectedModel,
  selectedStack,
  activeTab,
  onTabChange,
  onStackSelect,
  onDeploy,
}: StackSelectionProps) => {
  const stacks = getStacksForTab(activeTab)

  return (
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
            onClick={() => selectedModel && onTabChange(tab)}
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
          stacks.length > 0 ? (
            <div className="grid gap-5">
              {stacks.map((stack) => (
                <div
                  key={stack.name}
                  className={`group relative overflow-hidden rounded-2xl border transition-all duration-300 cursor-pointer ${
                    selectedStack === stack.name
                      ? "ring-2 ring-orange-400/50 border-orange-400/60 bg-gradient-to-br from-slate-800/90 to-slate-900/90 shadow-xl shadow-orange-500/20"
                      : stack.comingSoon
                        ? "border-slate-700/40 bg-gradient-to-br from-slate-800/30 to-slate-900/30 opacity-60"
                        : "border-slate-700/40 bg-gradient-to-br from-slate-800/50 to-slate-900/50 hover:ring-1 hover:ring-slate-600/60 hover:bg-gradient-to-br hover:from-slate-800/70 hover:to-slate-900/70 hover:shadow-lg hover:shadow-slate-900/50"
                  }`}
                  onClick={() => !stack.comingSoon && onStackSelect(stack.name)}
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
                                if (selectedStack === stack.name) onDeploy()
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
  )
}
