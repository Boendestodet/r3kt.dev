import type React from "react"

interface CardProps {
  children: React.ReactNode
  className?: string
  onClick?: () => void
  selected?: boolean
  disabled?: boolean
}

export const Card = ({
  children,
  className = "",
  onClick,
  selected = false,
  disabled = false,
}: CardProps) => {
  const baseClasses = "rounded-2xl border transition-all duration-200"
  const stateClasses = selected
    ? "ring-2 ring-orange-400/40 border-orange-400/60 bg-slate-800/80"
    : disabled
      ? "border-slate-700/60 bg-slate-800/30 opacity-60"
      : "border-slate-700/60 bg-slate-800/50 hover:ring-1 hover:ring-slate-700/60 hover:bg-slate-800/70"

  return (
    <div
      className={`${baseClasses} ${stateClasses} ${onClick && !disabled ? "cursor-pointer" : ""} ${className}`}
      onClick={disabled ? undefined : onClick}
    >
      {children}
    </div>
  )
}
