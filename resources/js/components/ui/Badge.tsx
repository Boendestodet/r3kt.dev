import type React from "react"

interface BadgeProps {
  children: React.ReactNode
  variant?: "ready" | "selected" | "hot" | "coming-soon" | "default"
  className?: string
  role?: string
}

export const Badge = ({
  children,
  variant = "default",
  className = "",
  role,
}: BadgeProps) => {
  const variants = {
    ready: "bg-emerald-500/20 text-emerald-400 border border-emerald-500/30",
    selected: "bg-emerald-500/20 text-emerald-400 border border-emerald-500/30",
    hot: "bg-orange-500 text-white",
    "coming-soon": "bg-slate-600/50 text-slate-400 border border-slate-600/30",
    default: "bg-slate-700 text-slate-300",
  }

  return (
    <span
      className={`inline-flex items-center px-2 py-1 text-xs font-medium rounded-xl ${variants[variant]} ${className}`}
      role={role}
    >
      {children}
    </span>
  )
}
