import { useState, useEffect, useCallback, useRef } from 'react'

interface VirtualScrollOptions {
  itemHeight: number
  containerHeight: number
  overscan?: number
}

interface VirtualScrollResult<T> {
  visibleItems: Array<{
    index: number
    item: T
    offsetY: number
  }>
  totalHeight: number
  scrollToIndex: (index: number) => void
  scrollToTop: () => void
  containerRef: React.RefObject<HTMLDivElement>
}

export function useVirtualScroll<T>(
  items: T[],
  options: VirtualScrollOptions
): VirtualScrollResult<T> {
  const { itemHeight, containerHeight, overscan = 5 } = options
  const [scrollTop, setScrollTop] = useState(0)
  const containerRef = useRef<HTMLDivElement>(null)

  const totalHeight = items.length * itemHeight
  const visibleCount = Math.ceil(containerHeight / itemHeight)
  const startIndex = Math.max(0, Math.floor(scrollTop / itemHeight) - overscan)
  const endIndex = Math.min(
    items.length - 1,
    startIndex + visibleCount + overscan * 2
  )

  const visibleItems = items.slice(startIndex, endIndex + 1).map((item, i) => ({
    index: startIndex + i,
    item,
    offsetY: (startIndex + i) * itemHeight,
  }))

  const handleScroll = useCallback((e: Event) => {
    const target = e.target as HTMLDivElement
    setScrollTop(target.scrollTop)
  }, [])

  useEffect(() => {
    const container = containerRef.current
    if (container) {
      container.addEventListener('scroll', handleScroll, { passive: true })
      return () => container.removeEventListener('scroll', handleScroll)
    }
  }, [handleScroll])

  const scrollToIndex = useCallback((index: number) => {
    const container = containerRef.current
    if (container) {
      container.scrollTop = index * itemHeight
    }
  }, [itemHeight])

  const scrollToTop = useCallback(() => {
    const container = containerRef.current
    if (container) {
      container.scrollTop = 0
    }
  }, [])

  return {
    visibleItems,
    totalHeight,
    scrollToIndex,
    scrollToTop,
    containerRef,
  }
}
