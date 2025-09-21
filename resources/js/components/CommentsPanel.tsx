import { useState, useEffect } from 'react'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'
import { Textarea } from '@/components/ui/textarea'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select'
import { MessageCircle, Reply, CheckCircle, XCircle, Edit, Trash2, MoreVertical } from 'lucide-react'
import { formatDistanceToNow } from 'date-fns'

interface Comment {
  id: number
  content: string
  type: 'general' | 'code_review' | 'suggestion' | 'question'
  metadata?: any
  is_resolved: boolean
  created_at: string
  user: {
    id: number
    name: string
    email: string
  }
  replies?: Comment[]
}

interface Props {
  projectId: number
  isOpen: boolean
  onClose: () => void
}

const commentTypeColors = {
  general: 'bg-gray-100 text-gray-800',
  code_review: 'bg-blue-100 text-blue-800',
  suggestion: 'bg-green-100 text-green-800',
  question: 'bg-yellow-100 text-yellow-800',
}

const commentTypeLabels = {
  general: 'General',
  code_review: 'Code Review',
  suggestion: 'Suggestion',
  question: 'Question',
}

export default function CommentsPanel({ projectId, isOpen, onClose }: Props) {
  const [comments, setComments] = useState<Comment[]>([])
  const [loading, setLoading] = useState(false)
  const [newComment, setNewComment] = useState('')
  const [commentType, setCommentType] = useState<'general' | 'code_review' | 'suggestion' | 'question'>('general')
  const [replyingTo, setReplyingTo] = useState<number | null>(null)
  const [replyContent, setReplyContent] = useState('')
  const [editingComment, setEditingComment] = useState<number | null>(null)
  const [editContent, setEditContent] = useState('')

  useEffect(() => {
    if (isOpen) {
      fetchComments()
    }
  }, [isOpen, projectId])

  const fetchComments = async () => {
    setLoading(true)
    try {
      const response = await fetch(`/projects/${projectId}/comments`)
      const data = await response.json()
      if (data.success) {
        setComments(data.comments)
      }
    } catch (error) {
      console.error('Failed to fetch comments:', error)
    } finally {
      setLoading(false)
    }
  }

  const handleSubmitComment = async (e: React.FormEvent) => {
    e.preventDefault()
    if (!newComment.trim()) return

    try {
      const response = await fetch(`/projects/${projectId}/comments`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
        },
        body: JSON.stringify({
          content: newComment,
          type: commentType,
        }),
      })

      const data = await response.json()
      if (data.success) {
        setComments([data.comment, ...comments])
        setNewComment('')
        setCommentType('general')
      }
    } catch (error) {
      console.error('Failed to submit comment:', error)
    }
  }

  const handleSubmitReply = async (parentId: number) => {
    if (!replyContent.trim()) return

    try {
      const response = await fetch(`/projects/${projectId}/comments`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
        },
        body: JSON.stringify({
          content: replyContent,
          type: 'general',
          parent_id: parentId,
        }),
      })

      const data = await response.json()
      if (data.success) {
        setComments(comments.map(comment => 
          comment.id === parentId 
            ? { ...comment, replies: [...(comment.replies || []), data.comment] }
            : comment
        ))
        setReplyContent('')
        setReplyingTo(null)
      }
    } catch (error) {
      console.error('Failed to submit reply:', error)
    }
  }

  const handleEditComment = async (commentId: number) => {
    if (!editContent.trim()) return

    try {
      const response = await fetch(`/comments/${commentId}`, {
        method: 'PUT',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
        },
        body: JSON.stringify({
          content: editContent,
        }),
      })

      const data = await response.json()
      if (data.success) {
        setComments(comments.map(comment => 
          comment.id === commentId 
            ? { ...comment, content: data.comment.content }
            : comment
        ))
        setEditContent('')
        setEditingComment(null)
      }
    } catch (error) {
      console.error('Failed to edit comment:', error)
    }
  }

  const handleDeleteComment = async (commentId: number) => {
    if (!confirm('Are you sure you want to delete this comment?')) return

    try {
      const response = await fetch(`/comments/${commentId}`, {
        method: 'DELETE',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
        },
      })

      const data = await response.json()
      if (data.success) {
        setComments(comments.filter(comment => comment.id !== commentId))
      }
    } catch (error) {
      console.error('Failed to delete comment:', error)
    }
  }

  const handleToggleResolved = async (commentId: number) => {
    try {
      const response = await fetch(`/comments/${commentId}/toggle-resolved`, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
        },
      })

      const data = await response.json()
      if (data.success) {
        setComments(comments.map(comment => 
          comment.id === commentId 
            ? { ...comment, is_resolved: data.comment.is_resolved }
            : comment
        ))
      }
    } catch (error) {
      console.error('Failed to toggle resolved status:', error)
    }
  }

  if (!isOpen) return null

  return (
    <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div className="bg-white rounded-lg shadow-xl w-full max-w-4xl h-5/6 flex flex-col">
        <div className="flex items-center justify-between p-6 border-b">
          <h2 className="text-xl font-semibold flex items-center gap-2">
            <MessageCircle className="w-5 h-5" />
            Comments & Collaboration
          </h2>
          <Button variant="outline" onClick={onClose}>
            <XCircle className="w-4 h-4" />
          </Button>
        </div>

        <div className="flex-1 overflow-hidden flex">
          {/* Comments List */}
          <div className="flex-1 overflow-y-auto p-6">
            {loading ? (
              <div className="text-center py-8">Loading comments...</div>
            ) : comments.length === 0 ? (
              <div className="text-center py-8 text-gray-500">
                No comments yet. Start the conversation!
              </div>
            ) : (
              <div className="space-y-4">
                {comments.map((comment) => (
                  <div key={comment.id} className="space-y-3">
                    <Card className={comment.is_resolved ? 'opacity-60' : ''}>
                      <CardHeader className="pb-3">
                        <div className="flex items-center justify-between">
                          <div className="flex items-center gap-2">
                            <span className="font-medium">{comment.user.name}</span>
                            <Badge className={commentTypeColors[comment.type]}>
                              {commentTypeLabels[comment.type]}
                            </Badge>
                            {comment.is_resolved && (
                              <Badge variant="outline" className="text-green-600">
                                <CheckCircle className="w-3 h-3 mr-1" />
                                Resolved
                              </Badge>
                            )}
                          </div>
                          <div className="flex items-center gap-2 text-sm text-gray-500">
                            <span>{formatDistanceToNow(new Date(comment.created_at), { addSuffix: true })}</span>
                            <div className="flex gap-1">
                              <Button
                                variant="ghost"
                                size="sm"
                                onClick={() => setReplyingTo(comment.id)}
                              >
                                <Reply className="w-3 h-3" />
                              </Button>
                              <Button
                                variant="ghost"
                                size="sm"
                                onClick={() => {
                                  setEditingComment(comment.id)
                                  setEditContent(comment.content)
                                }}
                              >
                                <Edit className="w-3 h-3" />
                              </Button>
                              <Button
                                variant="ghost"
                                size="sm"
                                onClick={() => handleDeleteComment(comment.id)}
                              >
                                <Trash2 className="w-3 h-3" />
                              </Button>
                              <Button
                                variant="ghost"
                                size="sm"
                                onClick={() => handleToggleResolved(comment.id)}
                              >
                                {comment.is_resolved ? (
                                  <XCircle className="w-3 h-3" />
                                ) : (
                                  <CheckCircle className="w-3 h-3" />
                                )}
                              </Button>
                            </div>
                          </div>
                        </div>
                      </CardHeader>
                      <CardContent>
                        {editingComment === comment.id ? (
                          <div className="space-y-2">
                            <Textarea
                              value={editContent}
                              onChange={(e) => setEditContent(e.target.value)}
                              className="min-h-[100px]"
                            />
                            <div className="flex gap-2">
                              <Button size="sm" onClick={() => handleEditComment(comment.id)}>
                                Save
                              </Button>
                              <Button 
                                size="sm" 
                                variant="outline" 
                                onClick={() => {
                                  setEditingComment(null)
                                  setEditContent('')
                                }}
                              >
                                Cancel
                              </Button>
                            </div>
                          </div>
                        ) : (
                          <p className="text-gray-700 whitespace-pre-wrap">{comment.content}</p>
                        )}
                      </CardContent>
                    </Card>

                    {/* Replies */}
                    {comment.replies && comment.replies.length > 0 && (
                      <div className="ml-6 space-y-2">
                        {comment.replies.map((reply) => (
                          <Card key={reply.id} className="bg-gray-50">
                            <CardContent className="pt-4">
                              <div className="flex items-center justify-between mb-2">
                                <span className="font-medium text-sm">{reply.user.name}</span>
                                <span className="text-xs text-gray-500">
                                  {formatDistanceToNow(new Date(reply.created_at), { addSuffix: true })}
                                </span>
                              </div>
                              <p className="text-sm text-gray-700">{reply.content}</p>
                            </CardContent>
                          </Card>
                        ))}
                      </div>
                    )}

                    {/* Reply Form */}
                    {replyingTo === comment.id && (
                      <div className="ml-6">
                        <Card className="bg-blue-50">
                          <CardContent className="pt-4">
                            <Textarea
                              placeholder="Write a reply..."
                              value={replyContent}
                              onChange={(e) => setReplyContent(e.target.value)}
                              className="min-h-[80px] mb-3"
                            />
                            <div className="flex gap-2">
                              <Button size="sm" onClick={() => handleSubmitReply(comment.id)}>
                                Reply
                              </Button>
                              <Button 
                                size="sm" 
                                variant="outline" 
                                onClick={() => {
                                  setReplyingTo(null)
                                  setReplyContent('')
                                }}
                              >
                                Cancel
                              </Button>
                            </div>
                          </CardContent>
                        </Card>
                      </div>
                    )}
                  </div>
                ))}
              </div>
            )}
          </div>

          {/* New Comment Form */}
          <div className="w-80 border-l p-6">
            <h3 className="font-medium mb-4">Add Comment</h3>
            <form onSubmit={handleSubmitComment} className="space-y-4">
              <Select value={commentType} onValueChange={(value: any) => setCommentType(value)}>
                <SelectTrigger>
                  <SelectValue placeholder="Comment type" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="general">General</SelectItem>
                  <SelectItem value="code_review">Code Review</SelectItem>
                  <SelectItem value="suggestion">Suggestion</SelectItem>
                  <SelectItem value="question">Question</SelectItem>
                </SelectContent>
              </Select>
              
              <Textarea
                placeholder="Write your comment..."
                value={newComment}
                onChange={(e) => setNewComment(e.target.value)}
                className="min-h-[120px]"
              />
              
              <Button type="submit" className="w-full">
                Post Comment
              </Button>
            </form>
          </div>
        </div>
      </div>
    </div>
  )
}
