import { Skeleton } from "@/components/ui/skeleton"

export default function ChatMessageSkeleton() {
  return (
    <div className="flex gap-3 justify-start">
      {/* Avatar */}
      <Skeleton className="w-8 h-8 rounded-full flex-shrink-0" />
      
      {/* Message Content */}
      <div className="bg-slate-800 text-slate-100 rounded-lg px-4 py-2 max-w-xs">
        <div className="space-y-2">
          <Skeleton className="h-4 w-full bg-slate-700" />
          <Skeleton className="h-4 w-3/4 bg-slate-700" />
          <Skeleton className="h-4 w-1/2 bg-slate-700" />
        </div>
      </div>
    </div>
  )
}
