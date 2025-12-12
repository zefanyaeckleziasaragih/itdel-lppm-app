import * as React from "react";

import { NavUser } from "@/components/nav-user";
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarGroup,
    SidebarGroupLabel,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from "@/components/ui/sidebar";
import { cn } from "@/lib/utils";

// -----------------------------
// Typing untuk navigasi
// -----------------------------

interface NavSubItem {
    title: string;
    url: string;
    icon: React.ElementType;
}

interface NavItem {
    title: string;
    url?: string;
    icon: React.ElementType;
    items?: NavSubItem[]; // submenu
}

interface NavGroup {
    title: string;
    items: NavItem[];
}

interface UserData {
    name: string;
    username: string;
    photo: string;
}

interface AppSidebarProps extends React.ComponentProps<typeof Sidebar> {
    active?: string;
    user: UserData;
    appName: string;
    navData: NavGroup[];
}

// -----------------------------
// Komponen Sidebar
// -----------------------------

export function AppSidebar({
    active = "",
    user,
    appName,
    navData,
    ...props
}: AppSidebarProps) {
    // state dropdown: menyimpan dropdown mana yg sedang terbuka
    const [openDropdown, setOpenDropdown] = React.useState<string | null>(null);

    const toggleDropdown = (title: string) => {
        setOpenDropdown((prev: string | null) =>
            prev === title ? null : title
        );
    };

    return (
        <Sidebar collapsible="offcanvas" {...props}>
            {/* HEADER */}
            <SidebarHeader>
                <SidebarMenu>
                    <SidebarMenuItem>
                        <SidebarMenuButton
                            asChild
                            className="h-10 px-2 rounded-md gap-2"
                        >
                            <a href="#" className="flex items-center gap-2">
                                {/* Logo */}
                                <div className="flex items-center justify-center rounded-md bg-muted/70 p-1.5">
                                    <img
                                        src="/img/logo/sdi-logo-dark.png"
                                        alt="ITDel Logo"
                                        className="h-5 w-5 block dark:hidden"
                                    />
                                    <img
                                        src="/img/logo/sdi-logo-light.png"
                                        alt="ITDel Logo"
                                        className="h-5 w-5 hidden dark:block"
                                    />
                                </div>
                                {/* App name */}
                                <span className="text-sm font-semibold tracking-tight text-foreground">
                                    {appName}
                                </span>
                            </a>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarHeader>

            {/* CONTENT */}
            <SidebarContent>
                <SidebarGroup className="group-data-[collapsible=icon]:hidden">
                    <div className="mb-1">
                        {navData.map((navGroup: NavGroup) => (
                            <div className="mb-3" key={`nav-${navGroup.title}`}>
                                <SidebarGroupLabel className="px-2 text-[11px] font-medium uppercase tracking-[0.12em] text-muted-foreground/80">
                                    {navGroup.title}
                                </SidebarGroupLabel>

                                <SidebarMenu className="mt-1 space-y-0.5">
                                    {navGroup.items.map((item: NavItem) => (
                                        <SidebarMenuItem key={item.title}>
                                            {/* Jika item punya submenu */}
                                            {item.items ? (
                                                <>
                                                    <SidebarMenuButton
                                                        className={cn(
                                                            "flex h-9 items-center justify-between rounded-md px-2 text-sm text-muted-foreground transition-colors",
                                                            "hover:bg-accent hover:text-accent-foreground",
                                                            {
                                                                "bg-accent text-accent-foreground":
                                                                    openDropdown ===
                                                                    item.title,
                                                            }
                                                        )}
                                                        onClick={() =>
                                                            toggleDropdown(
                                                                item.title
                                                            )
                                                        }
                                                    >
                                                        <div className="flex items-center gap-2">
                                                            <item.icon className="h-4 w-4" />
                                                            <span className="truncate">
                                                                {item.title}
                                                            </span>
                                                        </div>
                                                        <span className="text-[11px] text-muted-foreground">
                                                            {openDropdown ===
                                                            item.title
                                                                ? "▾"
                                                                : "▸"}
                                                        </span>
                                                    </SidebarMenuButton>

                                                    {/* SUBMENU */}
                                                    {openDropdown ===
                                                        item.title && (
                                                        <div className="mt-1 space-y-0.5 border-l border-border/60 pl-3">
                                                            {item.items.map(
                                                                (
                                                                    sub: NavSubItem
                                                                ) => (
                                                                    <a
                                                                        href={
                                                                            sub.url
                                                                        }
                                                                        key={
                                                                            sub.title
                                                                        }
                                                                        className={cn(
                                                                            "flex h-8 items-center gap-2 rounded-md px-2 text-xs text-muted-foreground transition-colors",
                                                                            "hover:bg-accent/70 hover:text-accent-foreground",
                                                                            {
                                                                                "bg-accent text-accent-foreground":
                                                                                    active ===
                                                                                    sub.title,
                                                                            }
                                                                        )}
                                                                    >
                                                                        <sub.icon className="h-3.5 w-3.5" />
                                                                        <span className="truncate">
                                                                            {
                                                                                sub.title
                                                                            }
                                                                        </span>
                                                                    </a>
                                                                )
                                                            )}
                                                        </div>
                                                    )}
                                                </>
                                            ) : (
                                                // Jika item biasa (tanpa submenu)
                                                <SidebarMenuButton
                                                    asChild
                                                    className={cn(
                                                        "flex h-9 items-center rounded-md px-2 text-sm text-muted-foreground transition-colors",
                                                        "hover:bg-accent hover:text-accent-foreground",
                                                        {
                                                            "bg-accent text-accent-foreground":
                                                                active ===
                                                                item.title,
                                                        }
                                                    )}
                                                >
                                                    <a href={item.url}>
                                                        <div className="flex items-center gap-2">
                                                            <item.icon className="h-4 w-4" />
                                                            <span className="truncate">
                                                                {item.title}
                                                            </span>
                                                        </div>
                                                    </a>
                                                </SidebarMenuButton>
                                            )}
                                        </SidebarMenuItem>
                                    ))}
                                </SidebarMenu>
                            </div>
                        ))}
                    </div>
                </SidebarGroup>
            </SidebarContent>

            {/* FOOTER */}
            <SidebarFooter>
                <NavUser user={user} />
            </SidebarFooter>
        </Sidebar>
    );
}
