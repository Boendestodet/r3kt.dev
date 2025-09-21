import React, { useState, useEffect } from 'react';
import { User, Users, Clock, MessageSquare } from 'lucide-react';

interface Collaborator {
  user_id: number;
  user_name: string;
  action: string;
  data: any;
  last_seen: string;
}

interface CollaborationHistory {
  user: {
    name: string;
    email: string;
  };
  action: string;
  message: string;
  timestamp: string;
}

interface CollaborationSidebarProps {
  activeCollaborators: Collaborator[];
  collaborationHistory: CollaborationHistory[];
  isOpen: boolean;
  onClose: () => void;
}

export default function CollaborationSidebar({
  activeCollaborators,
  collaborationHistory,
  isOpen,
  onClose,
}: CollaborationSidebarProps) {
  const [activeTab, setActiveTab] = useState<'collaborators' | 'history'>('collaborators');

  const formatTime = (timestamp: string) => {
    return new Date(timestamp).toLocaleTimeString();
  };

  const getActionIcon = (action: string) => {
    switch (action) {
      case 'joined':
        return <Users className="w-4 h-4 text-green-500" />;
      case 'left':
        return <Users className="w-4 h-4 text-red-500" />;
      case 'ai_generation_started':
        return <MessageSquare className="w-4 h-4 text-blue-500" />;
      case 'ai_generation_completed':
        return <MessageSquare className="w-4 h-4 text-green-500" />;
      case 'project_updated':
        return <Clock className="w-4 h-4 text-yellow-500" />;
      case 'code_editing':
        return <MessageSquare className="w-4 h-4 text-purple-500" />;
      default:
        return <MessageSquare className="w-4 h-4 text-gray-500" />;
    }
  };

  const getActionColor = (action: string) => {
    switch (action) {
      case 'joined':
        return 'text-green-600 bg-green-50';
      case 'left':
        return 'text-red-600 bg-red-50';
      case 'ai_generation_started':
        return 'text-blue-600 bg-blue-50';
      case 'ai_generation_completed':
        return 'text-green-600 bg-green-50';
      case 'project_updated':
        return 'text-yellow-600 bg-yellow-50';
      case 'code_editing':
        return 'text-purple-600 bg-purple-50';
      default:
        return 'text-gray-600 bg-gray-50';
    }
  };

  if (!isOpen) return null;

  return (
    <div className="fixed right-0 top-0 h-full w-80 bg-white border-l border-gray-200 shadow-lg z-50">
      <div className="flex flex-col h-full">
        {/* Header */}
        <div className="flex items-center justify-between p-4 border-b border-gray-200">
          <h3 className="text-lg font-semibold text-gray-900">Collaboration</h3>
          <button
            onClick={onClose}
            className="text-gray-400 hover:text-gray-600 transition-colors"
          >
            <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        {/* Tabs */}
        <div className="flex border-b border-gray-200">
          <button
            onClick={() => setActiveTab('collaborators')}
            className={`flex-1 px-4 py-3 text-sm font-medium transition-colors ${
              activeTab === 'collaborators'
                ? 'text-blue-600 border-b-2 border-blue-600'
                : 'text-gray-500 hover:text-gray-700'
            }`}
          >
            <Users className="w-4 h-4 inline mr-2" />
            Active ({activeCollaborators.length})
          </button>
          <button
            onClick={() => setActiveTab('history')}
            className={`flex-1 px-4 py-3 text-sm font-medium transition-colors ${
              activeTab === 'history'
                ? 'text-blue-600 border-b-2 border-blue-600'
                : 'text-gray-500 hover:text-gray-700'
            }`}
          >
            <Clock className="w-4 h-4 inline mr-2" />
            History
          </button>
        </div>

        {/* Content */}
        <div className="flex-1 overflow-y-auto">
          {activeTab === 'collaborators' ? (
            <div className="p-4">
              {activeCollaborators.length === 0 ? (
                <div className="text-center text-gray-500 py-8">
                  <Users className="w-12 h-12 mx-auto mb-3 text-gray-300" />
                  <p>No active collaborators</p>
                </div>
              ) : (
                <div className="space-y-3">
                  {activeCollaborators.map((collaborator, index) => (
                    <div
                      key={index}
                      className="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg"
                    >
                      <div className="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white text-sm font-medium">
                        {collaborator.user_name.charAt(0).toUpperCase()}
                      </div>
                      <div className="flex-1 min-w-0">
                        <p className="text-sm font-medium text-gray-900 truncate">
                          {collaborator.user_name}
                        </p>
                        <p className="text-xs text-gray-500">
                          {collaborator.action.replace('_', ' ')}
                        </p>
                      </div>
                      <div className="text-xs text-gray-400">
                        {formatTime(collaborator.last_seen)}
                      </div>
                    </div>
                  ))}
                </div>
              )}
            </div>
          ) : (
            <div className="p-4">
              {collaborationHistory.length === 0 ? (
                <div className="text-center text-gray-500 py-8">
                  <Clock className="w-12 h-12 mx-auto mb-3 text-gray-300" />
                  <p>No collaboration history</p>
                </div>
              ) : (
                <div className="space-y-3">
                  {collaborationHistory.map((item, index) => (
                    <div
                      key={index}
                      className="flex items-start space-x-3 p-3 bg-gray-50 rounded-lg"
                    >
                      <div className="flex-shrink-0">
                        {getActionIcon(item.action)}
                      </div>
                      <div className="flex-1 min-w-0">
                        <div className="flex items-center space-x-2 mb-1">
                          <p className="text-sm font-medium text-gray-900">
                            {item.user.name}
                          </p>
                          <span
                            className={`inline-flex items-center px-2 py-1 rounded-full text-xs font-medium ${getActionColor(
                              item.action
                            )}`}
                          >
                            {item.action.replace('_', ' ')}
                          </span>
                        </div>
                        <p className="text-sm text-gray-600">{item.message}</p>
                        <p className="text-xs text-gray-400 mt-1">
                          {formatTime(item.timestamp)}
                        </p>
                      </div>
                    </div>
                  ))}
                </div>
              )}
            </div>
          )}
        </div>
      </div>
    </div>
  );
}
