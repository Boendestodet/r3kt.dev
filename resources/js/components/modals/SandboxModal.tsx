import type React from "react"

interface SandboxModalProps {
  isOpen: boolean
}

export const SandboxModal = ({ isOpen }: SandboxModalProps) => {
  if (!isOpen) return null

  return (
    <div className="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50">
      <div className="bg-slate-800 rounded-2xl p-8 border border-slate-700/60 shadow-2xl">
        <div className="flex items-center gap-4 mb-6">
          <div className="w-10 h-10 border-2 border-orange-400 border-t-transparent rounded-full animate-spin" />
          <span className="text-white font-medium text-lg">
            Creating sandbox...
          </span>
        </div>
      </div>
    </div>
  )
}
