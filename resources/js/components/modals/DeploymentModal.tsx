import type React from "react"
import { X, Check } from "../icons"

interface DeploymentModalProps {
  isOpen: boolean
  isDeploying: boolean
  deploymentProgress: number
  selectedModel: string | null
  selectedStack: string | null
  onClose: () => void
  onDeploy: () => void
}

export const DeploymentModal = ({
  isOpen,
  isDeploying,
  deploymentProgress,
  selectedModel,
  selectedStack,
  onClose,
  onDeploy,
}: DeploymentModalProps) => {
  if (!isOpen) return null

  return (
    <div className="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50">
      <div className="relative overflow-hidden bg-gradient-to-br from-slate-800/60 via-slate-900/60 to-slate-800/60 rounded-3xl p-10 border border-slate-700/50 backdrop-blur-sm shadow-2xl max-w-md w-full mx-4">
        <div className="absolute inset-0 bg-gradient-to-r from-orange-500/5 via-transparent to-orange-500/5"></div>
        <div className="relative">
          <button
            onClick={onClose}
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
            onClick={onDeploy}
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
  )
}
