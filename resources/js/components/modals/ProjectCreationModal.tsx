import type React from "react"
import { X, Plus, Box } from "../icons"

interface ProjectCreationModalProps {
  isOpen: boolean
  isCreatingProject: boolean
  isFromDeploy: boolean
  selectedModel: string | null
  selectedStack: string | null
  projectName: string
  projectDescription: string
  projectPrompt: string
  validationError: string
  nameError: string
  isCheckingName: boolean
  creationProgress: number
  creationStatus: string
  createdProject: { id: number; name: string } | null
  creationState: {
    isActive: boolean
    projectId?: number
    promptId?: number
    step: 'project' | 'ai' | 'docker' | 'start' | 'complete'
  } | null
  balanceInfo: {
    balance: number
    formatted_balance: string
    total_spent: number
    formatted_total_spent: string
    can_generate: boolean
  }
  onClose: () => void
  onProjectNameChange: (name: string) => void
  onProjectDescriptionChange: (description: string) => void
  onProjectPromptChange: (prompt: string) => void
  onCreateProject: () => void
  onCancelCreation: () => void
}

export const ProjectCreationModal = ({
  isOpen,
  isCreatingProject,
  isFromDeploy,
  selectedModel,
  selectedStack,
  projectName,
  projectDescription,
  projectPrompt,
  validationError,
  nameError,
  isCheckingName,
  creationProgress,
  creationStatus,
  createdProject,
  creationState,
  balanceInfo,
  onClose,
  onProjectNameChange,
  onProjectDescriptionChange,
  onProjectPromptChange,
  onCreateProject,
  onCancelCreation,
}: ProjectCreationModalProps) => {
  if (!isOpen) return null

  const safeCloseModal = () => {
    if (isCreatingProject && creationState?.isActive) {
      return false
    }
    onClose()
    return true
  }

  return (
    <div 
      className="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50"
      onClick={(e) => {
        if (e.target === e.currentTarget) {
          safeCloseModal()
        }
      }}
    >
      <div className="relative overflow-hidden bg-gradient-to-br from-slate-800/60 via-slate-900/60 to-slate-800/60 rounded-3xl p-10 border border-slate-700/50 backdrop-blur-sm shadow-2xl max-w-2xl w-full mx-4">
        <div className="absolute inset-0 bg-gradient-to-r from-orange-500/5 via-transparent to-orange-500/5"></div>
        <div className="relative">
          <button
            onClick={safeCloseModal}
            className="absolute top-4 right-4 w-8 h-8 bg-slate-700/50 hover:bg-slate-600/50 rounded-lg flex items-center justify-center transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
            disabled={isCreatingProject && creationState?.isActive}
          >
            <X className="w-4 h-4 text-slate-300" />
          </button>
          
          <div className={`w-20 h-20 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-xl ${
            isFromDeploy 
              ? 'bg-gradient-to-br from-orange-400 to-orange-600 shadow-orange-500/30' 
              : 'bg-gradient-to-br from-purple-400 to-pink-600 shadow-purple-500/30'
          }`}>
            {isFromDeploy ? (
              <Box className="w-10 h-10 text-white" />
            ) : (
              <Plus className="w-10 h-10 text-white" />
            )}
          </div>
          
          {/* Warning message when modal is locked during creation */}
          {isCreatingProject && creationState?.isActive && (
            <div className="bg-yellow-500/10 border border-yellow-500/30 rounded-lg p-3 mb-4 text-yellow-400 text-sm text-center">
              <div className="flex items-center justify-center gap-2">
                <div className="w-4 h-4 border-2 border-yellow-400 border-t-transparent rounded-full animate-spin"></div>
                <span>Project creation in progress - Modal locked to prevent data loss</span>
              </div>
            </div>
          )}
          
          <h3 className="text-2xl font-bold bg-gradient-to-r from-white to-slate-300 bg-clip-text text-transparent mb-4 text-center">
            {isCreatingProject ? 'Creating Project...' : (isFromDeploy ? 'Deploy with AI' : 'Create New Project')}
          </h3>
          
          {!isCreatingProject && isFromDeploy && (
            <p className="text-slate-400 text-center mb-6">
              Ready to deploy <span className="text-orange-400 font-semibold">{selectedStack}</span> with <span className="text-orange-400 font-semibold">{selectedModel}</span>
            </p>
          )}
          
          {!isCreatingProject && !isFromDeploy && (
            <div className="bg-slate-700/30 border border-slate-600/50 rounded-lg p-4 mb-6">
              <div className="text-sm text-slate-300 mb-2">Selected Configuration:</div>
              <div className="flex items-center gap-4 text-sm">
                <div className="flex items-center gap-2">
                  <span className="text-slate-400">Model:</span>
                  <span className={`font-medium ${selectedModel ? 'text-orange-400' : 'text-red-400'}`}>
                    {selectedModel || 'Not selected'}
                  </span>
                </div>
                <div className="flex items-center gap-2">
                  <span className="text-slate-400">Stack:</span>
                  <span className={`font-medium ${selectedStack ? 'text-orange-400' : 'text-red-400'}`}>
                    {selectedStack || 'Not selected'}
                  </span>
                </div>
              </div>
              {(!selectedModel || !selectedStack) && (
                <div className="text-xs text-yellow-400 mt-2">
                  Please select both a model and stack from the main interface before creating a project.
                </div>
              )}
            </div>
          )}
          
          {!isCreatingProject ? (
            <div className="space-y-6">
              {/* Balance Warning */}
              {!balanceInfo.can_generate && (
                <div className="p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
                  <div className="flex items-center">
                    <div className="flex-shrink-0">
                      <svg className="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fillRule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clipRule="evenodd" />
                      </svg>
                    </div>
                    <div className="ml-3">
                      <h3 className="text-sm font-medium text-yellow-800 dark:text-yellow-200">
                        Insufficient Balance
                      </h3>
                      <p className="text-sm text-yellow-700 dark:text-yellow-300 mt-1">
                        Your current balance is {balanceInfo.formatted_balance}. You need credits to generate projects with AI.
                      </p>
                    </div>
                  </div>
                </div>
              )}

              <div>
                <label className="block text-sm font-medium text-slate-300 mb-2">
                  Project Name
                </label>
                <div className="relative">
                  <input
                    type="text"
                    value={projectName}
                    onChange={(e) => onProjectNameChange(e.target.value)}
                    placeholder="Enter project name..."
                    className={`w-full bg-slate-700/50 text-white placeholder-slate-400 border rounded-lg px-4 py-3 focus:outline-none focus:ring-2 transition-all duration-200 pr-10 ${
                      nameError 
                        ? 'border-red-500/50 focus:ring-red-400/40 focus:border-red-400/40' 
                        : 'border-slate-600/50 focus:ring-purple-400/40 focus:border-purple-400/40'
                    }`}
                  />
                  {isCheckingName && (
                    <div className="absolute right-3 top-1/2 transform -translate-y-1/2">
                      <div className="w-4 h-4 border-2 border-slate-400 border-t-transparent rounded-full animate-spin"></div>
                    </div>
                  )}
                </div>
                {nameError && (
                  <p className="text-red-400 text-sm mt-1">{nameError}</p>
                )}
              </div>
              
              <div>
                <label className="block text-sm font-medium text-slate-300 mb-2">
                  Description (Optional)
                </label>
                <input
                  type="text"
                  value={projectDescription}
                  onChange={(e) => onProjectDescriptionChange(e.target.value)}
                  placeholder="Brief description of your project..."
                  className="w-full bg-slate-700/50 text-white placeholder-slate-400 border border-slate-600/50 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-purple-400/40 focus:border-purple-400/40 transition-all duration-200"
                />
              </div>
              
              <div>
                <label className="block text-sm font-medium text-slate-300 mb-2">
                  AI Prompt
                </label>
                <textarea
                  value={projectPrompt}
                  onChange={(e) => onProjectPromptChange(e.target.value)}
                  placeholder="Describe what you want to build... (e.g., 'Create a modern e-commerce website with product catalog, shopping cart, and user authentication')"
                  rows={4}
                  className="w-full bg-slate-700/50 text-white placeholder-slate-400 border border-slate-600/50 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-purple-400/40 focus:border-purple-400/40 transition-all duration-200 resize-none"
                />
              </div>
              
              <div className="bg-slate-800/50 rounded-lg p-4 border border-slate-700/50">
                <h4 className="text-sm font-semibold text-slate-300 mb-3">Selected Configuration</h4>
                <div className="flex items-center gap-6">
                  <div className="flex items-center gap-2">
                    <div className="w-3 h-3 bg-orange-400 rounded-full"></div>
                    <span className="text-slate-300 font-medium">AI Model:</span>
                    <span className="text-orange-400 font-semibold">{selectedModel || 'Claude Code'}</span>
                  </div>
                  <div className="flex items-center gap-2">
                    <div className="w-3 h-3 bg-blue-400 rounded-full"></div>
                    <span className="text-slate-300 font-medium">Stack:</span>
                    <span className="text-blue-400 font-semibold">{selectedStack || 'Next.js'}</span>
                  </div>
                </div>
                <p className="text-xs text-slate-500 mt-2">
                  These selections were made from the main interface and will be used for your project.
                </p>
              </div>
              
              {validationError && (
                <div className="bg-red-500/10 border border-red-500/30 rounded-lg p-3 text-red-400 text-sm">
                  {validationError}
                </div>
              )}
              
              <button
                onClick={onCreateProject}
                disabled={!projectName.trim() || !projectPrompt.trim() || isCreatingProject || !!nameError || isCheckingName || !selectedModel || !selectedStack || !balanceInfo.can_generate}
                className="relative group bg-gradient-to-r from-purple-500 via-pink-500 to-purple-500 text-white px-8 py-4 rounded-2xl font-bold text-lg hover:shadow-2xl hover:shadow-purple-500/40 transition-all duration-300 transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none border border-purple-400/20 overflow-hidden w-full"
              >
                <div className="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                <span className="relative flex items-center justify-center gap-3">
                  {isCreatingProject ? (
                    <>
                      <div className="w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin" />
                      Creating...
                    </>
                  ) : (
                    <>
                      <Plus className="w-5 h-5" />
                      {isFromDeploy ? 'Deploy Project with AI' : 'Create Project with AI'}
                    </>
                  )}
                </span>
              </button>
            </div>
          ) : (
            <div className="space-y-6">
              <div className="text-center">
                <p className="text-lg text-slate-300 mb-4 font-medium">
                  {creationStatus}
                </p>
                
                <div className="w-full bg-slate-700/50 rounded-full h-4 overflow-hidden mb-4">
                  <div
                    className="bg-gradient-to-r from-purple-500 via-pink-500 to-purple-500 h-4 rounded-full transition-all duration-300 shadow-lg shadow-purple-500/50"
                    style={{ width: `${creationProgress}%` }}
                  />
                </div>
                <p className="text-slate-400 font-medium">{creationProgress}% complete</p>
              </div>
              
              {/* Cancel button */}
              <div className="flex justify-center mt-6">
                <button
                  onClick={onCancelCreation}
                  className="px-6 py-2 bg-red-600/20 hover:bg-red-600/30 text-red-400 border border-red-500/30 rounded-lg transition-colors duration-200"
                >
                  Cancel Creation
                </button>
              </div>
              
              {createdProject && (
                <div className="bg-slate-800/50 rounded-lg p-4 border border-slate-700/50">
                  <div className="flex items-center gap-3">
                    <div className="w-8 h-8 bg-gradient-to-br from-purple-400 to-pink-600 rounded-lg flex items-center justify-center">
                      <span className="text-sm font-bold text-white">
                        {createdProject.name?.charAt(0) || 'P'}
                      </span>
                    </div>
                    <div>
                      <p className="text-white font-semibold">{createdProject.name}</p>
                      <p className="text-slate-400 text-sm">Project created successfully</p>
                    </div>
                  </div>
                </div>
              )}
            </div>
          )}
        </div>
      </div>
    </div>
  )
}
