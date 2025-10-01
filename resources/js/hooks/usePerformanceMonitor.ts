import { useEffect, useRef } from 'react'

interface PerformanceMetrics {
  renderTime: number
  componentName: string
  timestamp: number
}

/**
 * Hook to monitor component performance
 * @param componentName - Name of the component being monitored
 * @param enabled - Whether monitoring is enabled (default: true in development)
 */
export function usePerformanceMonitor(
  componentName: string,
  enabled: boolean = process.env.NODE_ENV === 'development'
) {
  const renderStartTime = useRef<number>(0)
  const renderCount = useRef<number>(0)

  useEffect(() => {
    if (!enabled) return

    renderStartTime.current = performance.now()
    renderCount.current += 1

    return () => {
      const renderTime = performance.now() - renderStartTime.current
      
      // Log performance metrics
      console.log(`[Performance] ${componentName}:`, {
        renderTime: `${renderTime.toFixed(2)}ms`,
        renderCount: renderCount.current,
        timestamp: new Date().toISOString(),
      })

      // Warn about slow renders
      if (renderTime > 16) { // More than one frame (16ms at 60fps)
        console.warn(`[Performance Warning] ${componentName} took ${renderTime.toFixed(2)}ms to render`)
      }
    }
  })

  return {
    renderCount: renderCount.current,
  }
}

/**
 * Hook to measure async operations
 * @param operationName - Name of the operation being measured
 */
export function useAsyncPerformanceMonitor(operationName: string) {
  const startTime = useRef<number>(0)

  const startMeasurement = () => {
    startTime.current = performance.now()
  }

  const endMeasurement = () => {
    const duration = performance.now() - startTime.current
    
    console.log(`[Async Performance] ${operationName}:`, {
      duration: `${duration.toFixed(2)}ms`,
      timestamp: new Date().toISOString(),
    })

    return duration
  }

  return {
    startMeasurement,
    endMeasurement,
  }
}
