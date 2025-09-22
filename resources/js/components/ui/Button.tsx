import type React from "react"

interface ButtonProps {
  children: React.ReactNode
  variant?: "ghost" | "default"
  size?: "sm" | "default"
  className?: string
  onClick?: () => void
  disabled?: boolean
}

export const Button = ({
  children,
  variant = "ghost",
  size = "default",
  className = "",
  onClick,
  disabled = false,
}: ButtonProps) => {
  const baseClasses =
    "inline-flex items-center justify-center font-medium transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-orange-400/40 disabled:opacity-50 disabled:pointer-events-none"
  const variants = {
    ghost: "hover:bg-white/5 hover:ring-1 hover:ring-slate-700/60",
    default: "bg-slate-700 hover:bg-slate-600",
  }
  const sizes = {
    sm: "h-6 px-2 text-xs rounded-md",
    default: "h-9 px-4 py-2 rounded-lg",
  }

  return (
    <button
      className={`${baseClasses} ${variants[variant]} ${sizes[size]} ${className}`}
      onClick={onClick}
      disabled={disabled}
    >
      {children}
    </button>
  )
}
