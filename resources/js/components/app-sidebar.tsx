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
        setOpenDropdown((prev) => (prev === title ? null : title));
    };

    return (
        <Sidebar collapsible="offcanvas" {...props}>
            {/* HEADER */}
            <SidebarHeader>
                <SidebarMenu>
                    <SidebarMenuItem>
                        <SidebarMenuButton
                            asChild
                            className="data-[slot=sidebar-menu-button]:p-1.5!"
                        >
                            <a href="#">
                                <img
                                    src="/img/logo/sdi-logo-dark.png"
                                    alt="ITDel Logo"
                                    className="w-6 block dark:hidden"
                                />
                                <img
                                    src="/img/logo/sdi-logo-light.png"
                                    alt="ITDel Logo"
                                    className="w-6 hidden dark:block"
                                />
                                <span>{appName}</span>
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
                            <div className="mb-2" key={`nav-${navGroup.title}`}>
                                <SidebarGroupLabel>
                                    {navGroup.title}
                                </SidebarGroupLabel>

                                <SidebarMenu>
                                    {navGroup.items.map((item: NavItem) => (
                                        <SidebarMenuItem key={item.title}>
                                            {/* Jika item punya submenu */}
                                            {item.items ? (
                                                <>
                                                    <SidebarMenuButton
                                                        className={cn(
                                                            "hover:bg-primary/5 hover:text-primary flex justify-between",
                                                            {
                                                                "bg-primary/5":
                                                                    openDropdown ===
                                                                    item.title,
                                                                "text-primary":
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
                                                            <item.icon />
                                                            <span>
                                                                {item.title}
                                                            </span>
                                                        </div>
                                                        <span>
                                                            {openDropdown ===
                                                            item.title
                                                                ? "v"
                                                                : ">"}
                                                        </span>
                                                    </SidebarMenuButton>

                                                    {/* SUBMENU */}
                                                    {openDropdown ===
                                                        item.title && (
                                                        <div className="ml-6 mt-1 border-l pl-3 space-y-1">
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
                                                                            "block py-1 text-sm hover:text-primary hover:bg-primary/5 rounded-md px-2",
                                                                            {
                                                                                "bg-primary/5 text-primary":
                                                                                    active ===
                                                                                    sub.title,
                                                                            }
                                                                        )}
                                                                    >
                                                                        <div className="flex items-center gap-2">
                                                                            <sub.icon />
                                                                            {
                                                                                sub.title
                                                                            }
                                                                        </div>
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
                                                        "hover:bg-primary/5 hover:text-primary",
                                                        {
                                                            "bg-primary/5":
                                                                active ===
                                                                item.title,
                                                            "text-primary":
                                                                active ===
                                                                item.title,
                                                            "border-l border-primary":
                                                                active ===
                                                                item.title,
                                                        }
                                                    )}
                                                >
                                                    <a href={item.url}>
                                                        <item.icon />
                                                        <span>
                                                            {item.title}
                                                        </span>
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
