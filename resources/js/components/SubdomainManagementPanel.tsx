import { useState, useEffect } from 'react'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Badge } from '@/components/ui/badge'
import { 
  Globe, 
  ExternalLink, 
  CheckCircle, 
  XCircle, 
  Loader2, 
  AlertCircle,
  Settings,
  Copy,
  RefreshCw
} from 'lucide-react'

interface SubdomainManagementPanelProps {
  projectId: number
  currentSubdomain?: string
  customDomain?: string
  dnsConfigured?: boolean
  isOpen: boolean
  onClose: () => void
}

export default function SubdomainManagementPanel({ 
  projectId, 
  currentSubdomain, 
  customDomain, 
  dnsConfigured,
  isOpen, 
  onClose 
}: SubdomainManagementPanelProps) {
  const [subdomain, setSubdomain] = useState(currentSubdomain || '')
  const [customDomainInput, setCustomDomainInput] = useState(customDomain || '')
  const [isCheckingAvailability, setIsCheckingAvailability] = useState(false)
  const [isUpdating, setIsUpdating] = useState(false)
  const [availability, setAvailability] = useState<{
    available: boolean
    message: string
  } | null>(null)
  const [cloudflareStatus, setCloudflareStatus] = useState<{
    configured: boolean
    message: string
  } | null>(null)

  const checkSubdomainAvailability = async (subdomain: string) => {
    if (!subdomain || subdomain.length < 3) {
      setAvailability(null)
      return
    }

    setIsCheckingAvailability(true)
    try {
      const response = await fetch(`/api/subdomain/check?subdomain=${encodeURIComponent(subdomain)}`)
      const data = await response.json()
      setAvailability(data)
    } catch (error) {
      console.error('Failed to check subdomain availability:', error)
      setAvailability({
        available: false,
        message: 'Failed to check availability'
      })
    } finally {
      setIsCheckingAvailability(false)
    }
  }

  const updateSubdomain = async () => {
    if (!subdomain || subdomain === currentSubdomain) return

    setIsUpdating(true)
    try {
      const response = await fetch(`/projects/${projectId}/subdomain`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
        },
        body: JSON.stringify({ subdomain })
      })

      const data = await response.json()
      if (data.success) {
        // Refresh the page to show updated subdomain
        window.location.reload()
      } else {
        alert(data.message || 'Failed to update subdomain')
      }
    } catch (error) {
      console.error('Failed to update subdomain:', error)
      alert('Failed to update subdomain')
    } finally {
      setIsUpdating(false)
    }
  }

  const updateCustomDomain = async () => {
    if (!customDomainInput) return

    setIsUpdating(true)
    try {
      const response = await fetch(`/projects/${projectId}/custom-domain`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
        },
        body: JSON.stringify({ custom_domain: customDomainInput })
      })

      const data = await response.json()
      if (data.success) {
        // Refresh the page to show updated domain
        window.location.reload()
      } else {
        alert(data.message || 'Failed to update custom domain')
      }
    } catch (error) {
      console.error('Failed to update custom domain:', error)
      alert('Failed to update custom domain')
    } finally {
      setIsUpdating(false)
    }
  }

  const removeCustomDomain = async () => {
    setIsUpdating(true)
    try {
      const response = await fetch(`/projects/${projectId}/custom-domain`, {
        method: 'DELETE',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
        }
      })

      const data = await response.json()
      if (data.success) {
        // Refresh the page
        window.location.reload()
      } else {
        alert(data.message || 'Failed to remove custom domain')
      }
    } catch (error) {
      console.error('Failed to remove custom domain:', error)
      alert('Failed to remove custom domain')
    } finally {
      setIsUpdating(false)
    }
  }

  const testCloudflareConnection = async () => {
    try {
      const response = await fetch('/api/cloudflare/test')
      const data = await response.json()
      setCloudflareStatus(data)
    } catch (error) {
      console.error('Failed to test Cloudflare connection:', error)
      setCloudflareStatus({
        configured: false,
        message: 'Failed to test connection'
      })
    }
  }

  const copyToClipboard = (text: string) => {
    navigator.clipboard.writeText(text)
  }

  useEffect(() => {
    if (isOpen) {
      testCloudflareConnection()
    }
  }, [isOpen])

  useEffect(() => {
    const timeoutId = setTimeout(() => {
      checkSubdomainAvailability(subdomain)
    }, 500)

    return () => clearTimeout(timeoutId)
  }, [subdomain])

  if (!isOpen) return null

  const getAvailabilityIcon = () => {
    if (isCheckingAvailability) {
      return <Loader2 className="w-4 h-4 animate-spin" />
    }
    if (availability?.available) {
      return <CheckCircle className="w-4 h-4 text-green-500" />
    }
    if (availability?.available === false) {
      return <XCircle className="w-4 h-4 text-red-500" />
    }
    return null
  }

  const getDnsStatusBadge = () => {
    if (dnsConfigured) {
      return <Badge className="bg-green-100 text-green-800">DNS Configured</Badge>
    }
    return <Badge className="bg-yellow-100 text-yellow-800">DNS Pending</Badge>
  }

  return (
    <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div className="bg-white rounded-lg p-6 max-w-4xl max-h-[90vh] w-full mx-4 overflow-y-auto">
        <div className="flex justify-between items-center mb-6">
          <h2 className="text-2xl font-bold">Domain Management</h2>
          <Button variant="outline" onClick={onClose}>
            Close
          </Button>
        </div>

        <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
          {/* Subdomain Management */}
          <Card>
            <CardHeader>
              <CardTitle className="flex items-center gap-2">
                <Globe className="w-5 h-5" />
                Subdomain Settings
              </CardTitle>
              <CardDescription>
                Manage your project's subdomain (e.g., my-project.r3kt.dev)
              </CardDescription>
            </CardHeader>
            <CardContent className="space-y-4">
              <div>
                <label className="text-sm font-medium">Current Subdomain</label>
                <div className="flex items-center gap-2 mt-1">
                  <Input
                    value={subdomain}
                    onChange={(e) => setSubdomain(e.target.value)}
                    placeholder="my-project"
                    className="flex-1"
                  />
                  {getAvailabilityIcon()}
                </div>
                {availability && (
                  <p className={`text-sm mt-1 ${
                    availability.available ? 'text-green-600' : 'text-red-600'
                  }`}>
                    {availability.message}
                  </p>
                )}
              </div>

              <div className="flex items-center gap-2">
                <Button
                  onClick={updateSubdomain}
                  disabled={!subdomain || subdomain === currentSubdomain || !availability?.available || isUpdating}
                  className="flex-1"
                >
                  {isUpdating ? (
                    <Loader2 className="w-4 h-4 mr-2 animate-spin" />
                  ) : (
                    <RefreshCw className="w-4 h-4 mr-2" />
                  )}
                  Update Subdomain
                </Button>
              </div>

              {currentSubdomain && (
                <div className="p-3 bg-gray-50 rounded-lg">
                  <div className="flex items-center justify-between">
                    <div>
                      <p className="text-sm font-medium">Project URL</p>
                      <p className="text-sm text-gray-600">https://{currentSubdomain}.r3kt.dev</p>
                    </div>
                    <Button
                      variant="outline"
                      size="sm"
                      onClick={() => copyToClipboard(`https://${currentSubdomain}.r3kt.dev`)}
                    >
                      <Copy className="w-4 h-4" />
                    </Button>
                  </div>
                  <div className="mt-2">
                    {getDnsStatusBadge()}
                  </div>
                </div>
              )}
            </CardContent>
          </Card>

          {/* Custom Domain Management */}
          <Card>
            <CardHeader>
              <CardTitle className="flex items-center gap-2">
                <Settings className="w-5 h-5" />
                Custom Domain
              </CardTitle>
              <CardDescription>
                Use your own domain (e.g., myproject.com)
              </CardDescription>
            </CardHeader>
            <CardContent className="space-y-4">
              <div>
                <label className="text-sm font-medium">Custom Domain</label>
                <Input
                  value={customDomainInput}
                  onChange={(e) => setCustomDomainInput(e.target.value)}
                  placeholder="myproject.com"
                  className="mt-1"
                />
                <p className="text-xs text-gray-500 mt-1">
                  Enter your domain without https://
                </p>
              </div>

              <div className="flex gap-2">
                <Button
                  onClick={updateCustomDomain}
                  disabled={!customDomainInput || isUpdating}
                  className="flex-1"
                >
                  {isUpdating ? (
                    <Loader2 className="w-4 h-4 mr-2 animate-spin" />
                  ) : (
                    <Settings className="w-4 h-4 mr-2" />
                  )}
                  {customDomain ? 'Update Domain' : 'Set Domain'}
                </Button>
                
                {customDomain && (
                  <Button
                    onClick={removeCustomDomain}
                    disabled={isUpdating}
                    variant="outline"
                  >
                    Remove
                  </Button>
                )}
              </div>

              {customDomain && (
                <div className="p-3 bg-gray-50 rounded-lg">
                  <div className="flex items-center justify-between">
                    <div>
                      <p className="text-sm font-medium">Custom URL</p>
                      <p className="text-sm text-gray-600">https://{customDomain}</p>
                    </div>
                    <Button
                      variant="outline"
                      size="sm"
                      onClick={() => copyToClipboard(`https://${customDomain}`)}
                    >
                      <Copy className="w-4 h-4" />
                    </Button>
                  </div>
                </div>
              )}
            </CardContent>
          </Card>

          {/* Cloudflare Status */}
          <Card>
            <CardHeader>
              <CardTitle className="flex items-center gap-2">
                <AlertCircle className="w-5 h-5" />
                Cloudflare Status
              </CardTitle>
            </CardHeader>
            <CardContent>
              {cloudflareStatus ? (
                <div className="space-y-3">
                  <div className="flex items-center gap-2">
                    {cloudflareStatus.configured ? (
                      <CheckCircle className="w-4 h-4 text-green-500" />
                    ) : (
                      <XCircle className="w-4 h-4 text-red-500" />
                    )}
                    <span className="text-sm">
                      {cloudflareStatus.configured ? 'Connected' : 'Not Connected'}
                    </span>
                  </div>
                  <p className="text-sm text-gray-600">{cloudflareStatus.message}</p>
                  <Button
                    onClick={testCloudflareConnection}
                    variant="outline"
                    size="sm"
                    className="w-full"
                  >
                    <RefreshCw className="w-4 h-4 mr-2" />
                    Test Connection
                  </Button>
                </div>
              ) : (
                <div className="text-center py-4">
                  <Loader2 className="w-6 h-6 animate-spin mx-auto mb-2" />
                  <p className="text-sm text-gray-600">Checking status...</p>
                </div>
              )}
            </CardContent>
          </Card>

          {/* Instructions */}
          <Card>
            <CardHeader>
              <CardTitle>Setup Instructions</CardTitle>
            </CardHeader>
            <CardContent className="space-y-3 text-sm">
              <div>
                <h4 className="font-medium">For Subdomains:</h4>
                <p className="text-gray-600">
                  Subdomains are automatically configured with Cloudflare DNS. 
                  Your project will be available at https://your-subdomain.r3kt.dev
                </p>
              </div>
              
              <div>
                <h4 className="font-medium">For Custom Domains:</h4>
                <p className="text-gray-600">
                  1. Point your domain's DNS to Cloudflare<br/>
                  2. Add a CNAME record pointing to r3kt.dev<br/>
                  3. Configure the custom domain here
                </p>
              </div>
            </CardContent>
          </Card>
        </div>
      </div>
    </div>
  )
}
