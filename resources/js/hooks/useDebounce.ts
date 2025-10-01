import { useState, useEffect } from 'react'

/**
 * Custom hook for debouncing values
 * @param value - The value to debounce
 * @param delay - The delay in milliseconds
 * @returns The debounced value
 */
export function useDebounce<T>(value: T, delay: number): T {
  const [debouncedValue, setDebouncedValue] = useState<T>(value)

  useEffect(() => {
    const handler = setTimeout(() => {
      setDebouncedValue(value)
    }, delay)

    return () => {
      clearTimeout(handler)
    }
  }, [value, delay])

  return debouncedValue
}

/**
 * Custom hook for debounced search
 * @param searchTerm - The search term
 * @param delay - The delay in milliseconds (default: 300)
 * @returns The debounced search term
 */
export function useDebouncedSearch(searchTerm: string, delay: number = 300): string {
  return useDebounce(searchTerm, delay)
}
