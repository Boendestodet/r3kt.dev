import { Head, Link } from '@inertiajs/react'
import { useState } from 'react'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'
import StatusIndicator from '@/components/StatusIndicator'
import { Input } from '@/components/ui/input'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select'
import { Search, Eye, Heart, ExternalLink, Star, Filter } from 'lucide-react'
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
  user: {
    id: number
    name: string
    email: string
  }
  containers: Container[]
}

interface Container {
  id: number
  status: 'starting' | 'running' | 'stopped' | 'error'
  url?: string
}

interface Props {
  projects: {
    data: Project[]
    links: any[]
  }
  featured: Project[]
  filters: {
    search: string
    category: string
    sort: string
  }
  categories: Record<string, string>
  sortOptions: Record<string, string>
}

const categoryColors = {
  portfolio: 'bg-blue-100 text-blue-800',
  ecommerce: 'bg-green-100 text-green-800',
  blog: 'bg-purple-100 text-purple-800',
  landing: 'bg-orange-100 text-orange-800',
}

const getCategoryFromProject = (project: Project): string => {
  const name = project.name.toLowerCase()
  const desc = project.description?.toLowerCase() || ''
  
  if (name.includes('portfolio') || desc.includes('portfolio')) return 'portfolio'
  if (name.includes('shop') || name.includes('store') || desc.includes('ecommerce')) return 'ecommerce'
  if (name.includes('blog') || desc.includes('blog')) return 'blog'
  if (name.includes('landing') || desc.includes('marketing')) return 'landing'
  
  return 'portfolio' // default
}

export default function GalleryIndex({ projects, featured, filters, categories, sortOptions }: Props) {
  const [search, setSearch] = useState(filters.search || '')
  const [category, setCategory] = useState(filters.category || 'all')
  const [sort, setSort] = useState(filters.sort || 'latest')

  const handleSearch = () => {
    const params = new URLSearchParams()
    if (search) params.set('search', search)
    if (category !== 'all') params.set('category', category)
    if (sort !== 'latest') params.set('sort', sort)
    
    window.location.href = `/gallery?${params.toString()}`
  }

  return (
    <>
      <Head title="Gallery - Discover Amazing Projects" />
      
      <div className="min-h-screen bg-gray-50">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
          {/* Header */}
          <div className="text-center mb-12">
            <h1 className="text-4xl font-bold text-gray-900 mb-4">
              Discover Amazing Projects
            </h1>
            <p className="text-xl text-gray-600 max-w-2xl mx-auto">
              Explore a curated collection of AI-generated websites created by our community
            </p>
          </div>

          {/* Search and Filters */}
          <div className="bg-white rounded-lg shadow-sm border p-6 mb-8">
            <div className="flex flex-col md:flex-row gap-4">
              <div className="flex-1">
                <div className="relative">
                  <Search className="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 w-4 h-4" />
                  <Input
                    placeholder="Search projects..."
                    value={search}
                    onChange={(e) => setSearch(e.target.value)}
                    className="pl-10"
                    onKeyPress={(e) => e.key === 'Enter' && handleSearch()}
                  />
                </div>
              </div>
              
              <Select value={category} onValueChange={setCategory}>
                <SelectTrigger className="w-full md:w-48">
                  <SelectValue placeholder="Category" />
                </SelectTrigger>
                <SelectContent>
                  {Object.entries(categories).map(([key, label]) => (
                    <SelectItem key={key} value={key}>{label}</SelectItem>
                  ))}
                </SelectContent>
              </Select>
              
              <Select value={sort} onValueChange={setSort}>
                <SelectTrigger className="w-full md:w-48">
                  <SelectValue placeholder="Sort by" />
                </SelectTrigger>
                <SelectContent>
                  {Object.entries(sortOptions).map(([key, label]) => (
                    <SelectItem key={key} value={key}>{label}</SelectItem>
                  ))}
                </SelectContent>
              </Select>
              
              <Button onClick={handleSearch} className="w-full md:w-auto">
                <Filter className="w-4 h-4 mr-2" />
                Apply Filters
              </Button>
            </div>
          </div>

          {/* Featured Projects */}
          {featured.length > 0 && (
            <div className="mb-12">
              <h2 className="text-2xl font-bold text-gray-900 mb-6">Featured Projects</h2>
              <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                {featured.map((project) => {
                  const projectCategory = getCategoryFromProject(project)
                  const container = project.containers.find(c => c.status === 'running')
                  
                  return (
                    <Card key={project.id} className="hover:shadow-lg transition-shadow group">
                      <div className="relative">
                        {container?.url && (
                          <div className="aspect-video bg-gray-100 rounded-t-lg overflow-hidden">
                            <iframe
                              src={container.url}
                              className="w-full h-full"
                              title={project.name}
                            />
                          </div>
                        )}
                        <div className="absolute top-2 right-2">
                          <Badge className={categoryColors[projectCategory as keyof typeof categoryColors]}>
                            {categories[projectCategory]}
                          </Badge>
                        </div>
                        <div className="absolute top-2 left-2">
                          <StatusIndicator status={project.status} size="sm" />
                        </div>
                      </div>
                      
                      <CardHeader>
                        <CardTitle className="text-lg group-hover:text-blue-600 transition-colors">
                          {project.name}
                        </CardTitle>
                        <CardDescription className="line-clamp-2">
                          {project.description}
                        </CardDescription>
                      </CardHeader>
                      
                      <CardContent>
                        <div className="flex items-center justify-between text-sm text-gray-500 mb-4">
                          <span>by {project.user.name}</span>
                          <div className="flex items-center gap-4">
                            <span className="flex items-center gap-1">
                              <Eye className="w-4 h-4" />
                              {project.views_count}
                            </span>
                            <span>{formatDistanceToNow(new Date(project.created_at), { addSuffix: true })}</span>
                          </div>
                        </div>
                        
                        <div className="flex gap-2">
                          <Link href={`/gallery/${project.id}`} className="flex-1">
                            <Button variant="outline" className="w-full">
                              View Details
                            </Button>
                          </Link>
                          {container?.url && (
                            <Button
                              variant="outline"
                              size="sm"
                              onClick={() => window.open(container.url, '_blank')}
                            >
                              <ExternalLink className="w-4 h-4" />
                            </Button>
                          )}
                        </div>
                      </CardContent>
                    </Card>
                  )
                })}
              </div>
            </div>
          )}

          {/* All Projects */}
          <div>
            <h2 className="text-2xl font-bold text-gray-900 mb-6">All Projects</h2>
            
            {projects.data.length === 0 ? (
              <div className="text-center py-12">
                <div className="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                  <Search className="w-8 h-8 text-gray-400" />
                </div>
                <h3 className="text-lg font-medium text-gray-900 mb-2">No projects found</h3>
                <p className="text-gray-600">Try adjusting your search or filter criteria</p>
              </div>
            ) : (
              <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                {projects.data.map((project) => {
                  const projectCategory = getCategoryFromProject(project)
                  const container = project.containers.find(c => c.status === 'running')
                  
                  return (
                    <Card key={project.id} className="hover:shadow-lg transition-shadow group">
                      <div className="relative">
                        {container?.url && (
                          <div className="aspect-video bg-gray-100 rounded-t-lg overflow-hidden">
                            <iframe
                              src={container.url}
                              className="w-full h-full"
                              title={project.name}
                            />
                          </div>
                        )}
                        <div className="absolute top-2 right-2">
                          <Badge className={categoryColors[projectCategory as keyof typeof categoryColors]}>
                            {categories[projectCategory]}
                          </Badge>
                        </div>
                        <div className="absolute top-2 left-2">
                          <StatusIndicator status={project.status} size="sm" />
                        </div>
                      </div>
                      
                      <CardHeader className="pb-3">
                        <CardTitle className="text-lg group-hover:text-blue-600 transition-colors line-clamp-1">
                          {project.name}
                        </CardTitle>
                        <CardDescription className="line-clamp-2 text-sm">
                          {project.description}
                        </CardDescription>
                      </CardHeader>
                      
                      <CardContent className="pt-0">
                        <div className="flex items-center justify-between text-xs text-gray-500 mb-3">
                          <span>by {project.user.name}</span>
                          <div className="flex items-center gap-2">
                            <span className="flex items-center gap-1">
                              <Eye className="w-3 h-3" />
                              {project.views_count}
                            </span>
                          </div>
                        </div>
                        
                        <div className="flex gap-2">
                          <Link href={`/gallery/${project.id}`} className="flex-1">
                            <Button variant="outline" size="sm" className="w-full">
                              View
                            </Button>
                          </Link>
                          {container?.url && (
                            <Button
                              variant="outline"
                              size="sm"
                              onClick={() => window.open(container.url, '_blank')}
                            >
                              <ExternalLink className="w-3 h-3" />
                            </Button>
                          )}
                        </div>
                      </CardContent>
                    </Card>
                  )
                })}
              </div>
            )}
          </div>
        </div>
      </div>
    </>
  )
}
