import { Head, Link } from '@inertiajs/react'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'
import { ArrowLeft, ExternalLink, Eye, Calendar, User, Heart, Share2 } from 'lucide-react'
import { formatDistanceToNow } from 'date-fns'

interface Project {
  id: number
  name: string
  description?: string
  status: 'draft' | 'building' | 'ready' | 'error'
  is_public: boolean
  preview_url?: string
  views_count: number
  created_at: string
  generated_code?: string
  user: {
    id: number
    name: string
    email: string
  }
  containers: Container[]
  prompts: Prompt[]
}

interface Container {
  id: number
  status: 'starting' | 'running' | 'stopped' | 'error'
  url?: string
}

interface Prompt {
  id: number
  prompt: string
  response?: string
  status: 'pending' | 'processing' | 'completed' | 'failed'
  created_at: string
}

interface Props {
  project: Project
  related: Project[]
}

const getCategoryFromProject = (project: Project): string => {
  const name = project.name.toLowerCase()
  const desc = project.description?.toLowerCase() || ''
  
  if (name.includes('portfolio') || desc.includes('portfolio')) return 'Portfolio'
  if (name.includes('shop') || name.includes('store') || desc.includes('ecommerce')) return 'E-commerce'
  if (name.includes('blog') || desc.includes('blog')) return 'Blog'
  if (name.includes('landing') || desc.includes('marketing')) return 'Landing Page'
  
  return 'Portfolio'
}

export default function GalleryShow({ project, related }: Props) {
  const container = project.containers.find(c => c.status === 'running')
  const category = getCategoryFromProject(project)

  const handleShare = async () => {
    if (navigator.share) {
      try {
        await navigator.share({
          title: project.name,
          text: project.description,
          url: window.location.href,
        })
      } catch (err) {
        console.log('Error sharing:', err)
      }
    } else {
      // Fallback: copy to clipboard
      navigator.clipboard.writeText(window.location.href)
      alert('Link copied to clipboard!')
    }
  }

  return (
    <>
      <Head title={`${project.name} - Gallery`} />
      
      <div className="min-h-screen bg-gray-50">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
          {/* Header */}
          <div className="flex items-center gap-4 mb-8">
            <Link href="/gallery">
              <Button variant="outline" size="sm">
                <ArrowLeft className="w-4 h-4 mr-2" />
                Back to Gallery
              </Button>
            </Link>
            <div className="flex-1">
              <div className="flex items-center gap-2 mb-2">
                <h1 className="text-3xl font-bold text-gray-900">{project.name}</h1>
                <Badge variant="outline">{category}</Badge>
              </div>
              <p className="text-gray-600">{project.description}</p>
            </div>
            <div className="flex gap-2">
              <Button variant="outline" onClick={handleShare}>
                <Share2 className="w-4 h-4 mr-2" />
                Share
              </Button>
              {container?.url && (
                <Button onClick={() => window.open(container.url, '_blank')}>
                  <ExternalLink className="w-4 h-4 mr-2" />
                  View Live
                </Button>
              )}
            </div>
          </div>

          <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {/* Main Content */}
            <div className="lg:col-span-2 space-y-6">
              {/* Live Preview */}
              {container?.url ? (
                <Card>
                  <CardHeader>
                    <CardTitle>Live Preview</CardTitle>
                  </CardHeader>
                  <CardContent>
                    <div className="aspect-video bg-gray-100 rounded-lg overflow-hidden">
                      <iframe
                        src={container.url}
                        className="w-full h-full"
                        title={project.name}
                      />
                    </div>
                  </CardContent>
                </Card>
              ) : (
                <Card>
                  <CardContent className="text-center py-12">
                    <div className="mx-auto w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                      <ExternalLink className="w-8 h-8 text-gray-400" />
                    </div>
                    <h3 className="text-lg font-medium text-gray-900 mb-2">No Live Preview</h3>
                    <p className="text-gray-600">This project is not currently deployed</p>
                  </CardContent>
                </Card>
              )}

              {/* Generated Code Preview */}
              {project.generated_code && (
                <Card>
                  <CardHeader>
                    <CardTitle>Generated Code</CardTitle>
                    <CardDescription>
                      Here's the AI-generated HTML code for this project
                    </CardDescription>
                  </CardHeader>
                  <CardContent>
                    <div className="bg-gray-900 text-green-400 p-4 rounded-lg font-mono text-sm overflow-auto max-h-96">
                      <pre className="whitespace-pre-wrap">{project.generated_code}</pre>
                    </div>
                  </CardContent>
                </Card>
              )}

              {/* Prompts Used */}
              {project.prompts.length > 0 && (
                <Card>
                  <CardHeader>
                    <CardTitle>AI Prompts Used</CardTitle>
                    <CardDescription>
                      The prompts that were used to generate this project
                    </CardDescription>
                  </CardHeader>
                  <CardContent>
                    <div className="space-y-3">
                      {project.prompts.map((prompt) => (
                        <div key={prompt.id} className="border rounded-lg p-3">
                          <p className="text-sm text-gray-700">{prompt.prompt}</p>
                          <div className="flex items-center gap-2 mt-2 text-xs text-gray-500">
                            <span>{formatDistanceToNow(new Date(prompt.created_at), { addSuffix: true })}</span>
                            <Badge variant="outline" className="text-xs">
                              {prompt.status}
                            </Badge>
                          </div>
                        </div>
                      ))}
                    </div>
                  </CardContent>
                </Card>
              )}
            </div>

            {/* Sidebar */}
            <div className="space-y-6">
              {/* Project Info */}
              <Card>
                <CardHeader>
                  <CardTitle>Project Info</CardTitle>
                </CardHeader>
                <CardContent className="space-y-4">
                  <div className="flex items-center gap-2 text-sm">
                    <User className="w-4 h-4 text-gray-400" />
                    <span>Created by <strong>{project.user.name}</strong></span>
                  </div>
                  <div className="flex items-center gap-2 text-sm">
                    <Calendar className="w-4 h-4 text-gray-400" />
                    <span>{formatDistanceToNow(new Date(project.created_at), { addSuffix: true })}</span>
                  </div>
                  <div className="flex items-center gap-2 text-sm">
                    <Eye className="w-4 h-4 text-gray-400" />
                    <span>{project.views_count} views</span>
                  </div>
                  <div className="flex items-center gap-2 text-sm">
                    <Badge className="bg-green-100 text-green-800">
                      {project.status}
                    </Badge>
                  </div>
                </CardContent>
              </Card>

              {/* Related Projects */}
              {related.length > 0 && (
                <Card>
                  <CardHeader>
                    <CardTitle>More from {project.user.name}</CardTitle>
                  </CardHeader>
                  <CardContent>
                    <div className="space-y-3">
                      {related.map((relatedProject) => {
                        const relatedContainer = relatedProject.containers.find(c => c.status === 'running')
                        return (
                          <Link
                            key={relatedProject.id}
                            href={`/gallery/${relatedProject.id}`}
                            className="block p-3 border rounded-lg hover:bg-gray-50 transition-colors"
                          >
                            <h4 className="font-medium text-sm mb-1">{relatedProject.name}</h4>
                            <p className="text-xs text-gray-600 line-clamp-2">{relatedProject.description}</p>
                            {relatedContainer?.url && (
                              <div className="mt-2 text-xs text-blue-600 flex items-center gap-1">
                                <ExternalLink className="w-3 h-3" />
                                Live Preview
                              </div>
                            )}
                          </Link>
                        )
                      })}
                    </div>
                  </CardContent>
                </Card>
              )}
            </div>
          </div>
        </div>
      </div>
    </>
  )
}
