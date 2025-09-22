import type React from "react"
import { Check } from "../icons"
import { Badge, Card } from "../ui"

interface Model {
  name: string
  description: string
  comingSoon: boolean
}

interface ModelSelectionProps {
  selectedModel: string | null
  onModelSelect: (model: string) => void
}

const models: Model[] = [
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

export const ModelSelection = ({ selectedModel, onModelSelect }: ModelSelectionProps) => {
  return (
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
              onClick={() => !model.comingSoon && onModelSelect(model.name)}
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
  )
}
