import React, { useState, useCallback } from 'react'
import { Skeleton } from '@/components/ui/skeleton'

interface OptimizedImageProps {
  src: string
  alt: string
  className?: string
  width?: number
  height?: number
  placeholder?: string
  onLoad?: () => void
  onError?: () => void
}

const OptimizedImage: React.FC<OptimizedImageProps> = ({
  src,
  alt,
  className = '',
  width,
  height,
  placeholder,
  onLoad,
  onError,
}) => {
  const [isLoading, setIsLoading] = useState(true)
  const [hasError, setHasError] = useState(false)

  const handleLoad = useCallback(() => {
    setIsLoading(false)
    onLoad?.()
  }, [onLoad])

  const handleError = useCallback(() => {
    setIsLoading(false)
    setHasError(true)
    onError?.()
  }, [onError])

  if (hasError) {
    return (
      <div 
        className={`bg-slate-200 dark:bg-slate-700 flex items-center justify-center ${className}`}
        style={{ width, height }}
      >
        <span className="text-slate-400 text-sm">Failed to load</span>
      </div>
    )
  }

  return (
    <div className={`relative ${className}`} style={{ width, height }}>
      {isLoading && (
        <Skeleton 
          className="absolute inset-0 w-full h-full" 
          style={{ width, height }}
        />
      )}
      
      <img
        src={src}
        alt={alt}
        className={`transition-opacity duration-200 ${
          isLoading ? 'opacity-0' : 'opacity-100'
        } ${className}`}
        style={{ width, height }}
        onLoad={handleLoad}
        onError={handleError}
        loading="lazy" // Enable native lazy loading
        decoding="async" // Enable async decoding
      />
    </div>
  )
}

export default OptimizedImage
