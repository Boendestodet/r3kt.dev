import React from 'react';
import { cn } from '@/lib/utils';

interface LoadingSpinnerProps {
  size?: 'sm' | 'md' | 'lg';
  className?: string;
  'aria-label'?: string;
}

export function LoadingSpinner({ 
  size = 'md', 
  className,
  'aria-label': ariaLabel = 'Loading...'
}: LoadingSpinnerProps) {
  const sizeClasses = {
    sm: 'w-4 h-4',
    md: 'w-6 h-6',
    lg: 'w-8 h-8'
  };

  return (
    <div
      className={cn(
        'animate-spin rounded-full border-2 border-slate-300 border-t-blue-600',
        sizeClasses[size],
        className
      )}
      role="status"
      aria-label={ariaLabel}
    >
      <span className="sr-only">{ariaLabel}</span>
    </div>
  );
}

interface LoadingButtonProps extends React.ButtonHTMLAttributes<HTMLButtonElement> {
  loading?: boolean;
  loadingText?: string;
  children: React.ReactNode;
}

export function LoadingButton({ 
  loading = false, 
  loadingText = 'Loading...',
  children, 
  className,
  disabled,
  ...props 
}: LoadingButtonProps) {
  return (
    <button
      className={cn(
        'inline-flex items-center justify-center gap-2 px-4 py-2 rounded-md font-medium transition-colors',
        'bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed',
        className
      )}
      disabled={disabled || loading}
      aria-busy={loading}
      {...props}
    >
      {loading && (
        <LoadingSpinner size="sm" aria-label="Loading" />
      )}
      <span className={loading ? 'sr-only' : ''}>
        {loading ? loadingText : children}
      </span>
      {loading && (
        <span className="sr-only">{loadingText}</span>
      )}
    </button>
  );
}

interface LoadingOverlayProps {
  loading: boolean;
  children: React.ReactNode;
  message?: string;
  className?: string;
}

export function LoadingOverlay({ 
  loading, 
  children, 
  message = 'Loading...',
  className 
}: LoadingOverlayProps) {
  return (
    <div className={cn('relative', className)}>
      {children}
      {loading && (
        <div 
          className="absolute inset-0 bg-white/80 dark:bg-slate-900/80 backdrop-blur-sm flex items-center justify-center z-50"
          role="status"
          aria-live="polite"
          aria-label={message}
        >
          <div className="flex flex-col items-center gap-3">
            <LoadingSpinner size="lg" aria-label={message} />
            <p className="text-sm text-slate-600 dark:text-slate-400">
              {message}
            </p>
          </div>
        </div>
      )}
    </div>
  );
}

interface ProgressBarProps {
  progress: number; // 0-100
  className?: string;
  'aria-label'?: string;
}

export function ProgressBar({ 
  progress, 
  className,
  'aria-label': ariaLabel = `Progress: ${progress}%`
}: ProgressBarProps) {
  return (
    <div 
      className={cn('w-full bg-slate-200 dark:bg-slate-700 rounded-full h-2', className)}
      role="progressbar"
      aria-valuenow={progress}
      aria-valuemin={0}
      aria-valuemax={100}
      aria-label={ariaLabel}
    >
      <div 
        className="bg-blue-600 h-2 rounded-full transition-all duration-300 ease-out"
        style={{ width: `${Math.min(100, Math.max(0, progress))}%` }}
      />
    </div>
  );
}

interface SkeletonTextProps {
  lines?: number;
  className?: string;
}

export function SkeletonText({ lines = 1, className }: SkeletonTextProps) {
  return (
    <div className={cn('space-y-2', className)}>
      {Array.from({ length: lines }).map((_, index) => (
        <div
          key={index}
          className="h-4 bg-slate-200 dark:bg-slate-700 rounded animate-pulse"
          style={{ 
            width: `${Math.random() * 40 + 60}%` // Random width between 60-100%
          }}
          aria-hidden="true"
        />
      ))}
    </div>
  );
}
