"use client"

import * as React from "react"
import {
  Camera,
  BarChart3,
  LayoutDashboard,
  Database,
  FileText,
  Folder,
  HelpCircle,
  Layers,
  List,
  Search,
  Settings,
  Users,
} from "lucide-react"

import { NavMain } from "@/components/layout/nav-main"
import { NavUser } from "@/components/layout/nav-user"
import {
  Sidebar,
  SidebarContent,
  SidebarFooter,
  SidebarHeader,
  SidebarMenu,
  SidebarMenuButton,
  SidebarMenuItem,
} from "@/components/ui/sidebar"

const data = {
  user: {
    name: "shadcn",
    email: "m@example.com",
    avatar: "/avatars/shadcn.jpg",
  },
  navMain: [
    {
      title: "Dashboard",
      href: "#",
      icon: LayoutDashboard,
    },
    {
      title: "Lifecycle",
      href: "#",
      icon: List,
    },
    {
      title: "Analytics",
      href: "#",
      icon: BarChart3,
    },
    {
      title: "Projects",
      href: "#",
      icon: Folder,
    },
    {
      title: "Team",
      href: "#",
      icon: Users,
    },
  ],
  navClouds: [
    {
      title: "Capture",
      icon: Camera,
      isActive: true,
      href: "#",
      items: [
        {
          title: "Active Proposals",
          href: "#",
        },
        {
          title: "Archived",
          href: "#",
        },
      ],
    },
    {
      title: "Proposal",
      icon: FileText,
      href: "#",
      items: [
        {
          title: "Active Proposals",
          href: "#",
        },
        {
          title: "Archived",
          href: "#",
        },
      ],
    },
    {
      title: "Prompts",
      icon: FileText,
      href: "#",
      items: [
        {
          title: "Active Proposals",
          href: "#",
        },
        {
          title: "Archived",
          href: "#",
        },
      ],
    },
  ],
  navSecondary: [
    {
      title: "Settings",
      href: "#",
      icon: Settings,
    },
    {
      title: "Get Help",
      href: "#",
      icon: HelpCircle,
    },
    {
      title: "Search",
      href: "#", 
      icon: Search,
    },
  ],
  documents: [
    {
      name: "Data Library",
      href: "#",
      icon: Database,
    },
    {
      name: "Reports",
      href: "#",
      icon: BarChart3,
    },
    {
      name: "Word Assistant",
      href: "#",
      icon: FileText,
    },
  ],
}

export function AppSidebar({ ...props }: React.ComponentProps<typeof Sidebar>) {
  return (
    <Sidebar collapsible="offcanvas" {...props}>
      <SidebarHeader>
        <SidebarMenu>
          <SidebarMenuItem>
            <SidebarMenuButton
              asChild
              className="data-[slot=sidebar-menu-button]:!p-1.5"
            >
              <a href="#">
                <Layers className="!size-5" />
                <span className="text-base font-semibold">Acme Inc.</span>
              </a>
            </SidebarMenuButton>
          </SidebarMenuItem>
        </SidebarMenu>
      </SidebarHeader>
      <SidebarContent>
        <NavMain items={data.navMain} />
      </SidebarContent>
      <SidebarFooter>
        <NavUser user={data.user} />
      </SidebarFooter>
    </Sidebar>
  )
}
