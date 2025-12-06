import { AppSidebar } from "@/components/app-sidebar";
import { Button } from "@/components/ui/button";
import {
    Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectLabel,
    SelectTrigger,
    SelectValue,
} from "@/components/ui/select";
import { Separator } from "@/components/ui/separator";
import {
    SidebarInset,
    SidebarProvider,
    SidebarTrigger,
} from "@/components/ui/sidebar";
import { useTheme } from "@/providers/theme-provider";

import { usePage } from "@inertiajs/react";

import * as Icon from "@tabler/icons-react";
import { Moon, Sun } from "lucide-react";
import { Toaster } from "sonner";
import { route } from "ziggy-js";

export default function AppLayout({ children }) {
    const { auth, appName, pageName } = usePage().props;
    const { theme, colorTheme, toggleTheme, setColorTheme } = useTheme();

    const colorThemes = [
        "blue",
        "green",
        "default",
        "orange",
        "red",
        "rose",
        "violet",
        "yellow",
    ];

    // =========================
    // ROLE HANDLING
    // =========================
    const roles = auth?.akses ?? [];

    const isAdmin = roles.includes("Admin");
    const isHRD = roles.includes("HRD");
    const isLPPM =
        roles.includes("Ketua LPPM") || roles.includes("Anggota LPPM");
    const isDosen = roles.includes("Dosen");

    // =========================
    // NAV DEFAULT (FALLBACK)
    // =========================
    let navData = [
        {
            title: "Main",
            items: [
                {
                    title: "Beranda",
                    url: route("home"),
                    icon: Icon.IconHome,
                },
                {
                    title: "Todo",
                    url: route("todo"),
                    icon: Icon.IconChecklist,
                },
            ],
        },

        // ⭐ PENGHARGAAN
        {
            title: "Penghargaan",
            items: [
                {
                    title: "Beranda HRD",
                    url: route("penghargaan.dashboard-hrd"),
                    icon: Icon.IconLayoutDashboard,
                },
                {
                    title: "Beranda LPPM",
                    url: route("penghargaan.statistik"),
                    icon: Icon.IconChartBar,
                },
                {
                    title: "Daftar Pengajuan",
                    url: route("penghargaan.daftar"),
                    icon: Icon.IconListDetails,
                },
                {
                    title: "Seminar",
                    url: route("penghargaan.seminar.daftar"),
                    icon: Icon.IconPresentation,
                },
            ],
        },

        // ⭐ PENGAJUAN JURNAL
        {
            title: "Pengajuan Jurnal",
            items: [
                {
                    title: "Daftar Jurnal",
                    url: route("pengajuan.jurnal.daftar"),
                    icon: Icon.IconBook,
                },
            ],
        },

        // ⭐ ADMIN
        {
            title: "Admin",
            items: [
                {
                    title: "Hak Akses",
                    url: route("hak-akses"),
                    icon: Icon.IconLock,
                },
                {
                    title: "Daftar Penghargaan",
                    url: route("daftar-penghargaan"),
                    icon: Icon.IconAward,
                },
            ],
        },
    ];

    // =========================
    // ROLE SPESIFIK
    // =========================

    // 0. ADMIN -> Beranda Admin + Bagian Dosen/LPPM/HRD (dropdown) + Admin: Hak Akses
    if (isAdmin) {
        navData = [
            {
                title: "Menu Utama",
                items: [
                    {
                        title: "Beranda Admin",
                        url: route("home"), // ganti ke route dashboard admin kalau ada
                        icon: Icon.IconLayoutDashboard,
                    },
                    {
                        title: "Bagian Dosen",
                        icon: Icon.IconBook,
                        items: [
                            {
                                title: "Beranda Dosen",
                                // route ke halaman yang mewakili beranda dosen
                                url: route("home"),
                                icon: Icon.IconHome,
                            },
                            {
                                title: "Seminar",
                                url: route("penghargaan.seminar.daftar"),
                                icon: Icon.IconPresentation,
                            },
                            {
                                title: "Jurnal", // rename dari "Daftar Jurnal"
                                url: route("pengajuan.jurnal.daftar"),
                                icon: Icon.IconBook,
                            },
                        ],
                    },
                    {
                        title: "Bagian LPPM",
                        icon: Icon.IconChartBar,
                        items: [
                            {
                                title: "Beranda LPPM",
                                url: route("penghargaan.statistik"),
                                icon: Icon.IconChartBar,
                            },
                            {
                                title: "Daftar Pengajuan",
                                url: route("penghargaan.daftar"),
                                icon: Icon.IconListDetails,
                            },
                        ],
                    },
                    {
                        title: "Bagian HRD",
                        icon: Icon.IconAward,
                        items: [
                            {
                                title: "Beranda HRD",
                                url: route("penghargaan.dashboard-hrd"),
                                icon: Icon.IconLayoutDashboard,
                            },
                            {
                                title: "Daftar Penghargaan",
                                url: route("daftar-penghargaan"),
                                icon: Icon.IconAward,
                            },
                        ],
                    },
                ],
            },
            {
                title: "Admin",
                items: [
                    {
                        title: "Hak Akses",
                        url: route("hak-akses"),
                        icon: Icon.IconLock,
                    },
                ],
            },
        ];
    }
    // 1. HRD
    else if (isHRD) {
        navData = [
            {
                title: "Penghargaan",
                items: [
                    {
                        title: "Beranda HRD",
                        url: route("penghargaan.dashboard-hrd"),
                        icon: Icon.IconLayoutDashboard,
                    },
                    {
                        title: "Daftar Penghargaan",
                        url: route("daftar-penghargaan"),
                        icon: Icon.IconAward,
                    },
                ],
            },
            {
                title: "Admin",
                items: [
                    {
                        title: "Hak Akses",
                        url: route("hak-akses"),
                        icon: Icon.IconLock,
                    },
                ],
            },
        ];
    }
    // 2. LPPM
    else if (isLPPM) {
        navData = [
            {
                title: "LPPM",
                items: [
                    {
                        title: "Beranda LPPM",
                        url: route("penghargaan.statistik"),
                        icon: Icon.IconChartBar,
                    },
                    {
                        title: "Daftar Pengajuan",
                        url: route("penghargaan.daftar"),
                        icon: Icon.IconListDetails,
                    },
                ],
            },
            {
                title: "Admin",
                items: [
                    {
                        title: "Hak Akses",
                        url: route("hak-akses"),
                        icon: Icon.IconLock,
                    },
                ],
            },
        ];
    }
    // 3. DOSEN (sudah pas, tetap)
    else if (isDosen) {
        navData = [
            {
                title: "Main",
                items: [
                    {
                        title: "Beranda",
                        url: route("home"),
                        icon: Icon.IconHome,
                    },
                    {
                        title: "Pengajuan Penghargaan",
                        icon: Icon.IconAward,
                        items: [
                            {
                                title: "Seminar",
                                url: route("penghargaan.seminar.daftar"),
                                icon: Icon.IconPresentation,
                            },
                            {
                                title: "Jurnal",
                                url: route("pengajuan.jurnal.daftar"),
                                icon: Icon.IconBook,
                            },
                        ],
                    },
                ],
            },
            {
                title: "Admin",
                items: [
                    {
                        title: "Hak Akses",
                        url: route("hak-akses"),
                        icon: Icon.IconLock,
                    },
                ],
            },
        ];
    }

    return (
        <>
            <SidebarProvider
                style={{
                    "--sidebar-width": "calc(var(--spacing) * 72)",
                    "--header-height": "calc(var(--spacing) * 12)",
                }}
            >
                <AppSidebar
                    active={pageName}
                    user={auth}
                    navData={navData}
                    appName={appName}
                    variant="inset"
                />

                <SidebarInset>
                    <header className="flex h-[var(--header-height)] items-center gap-4 border-b bg-background/95 backdrop-blur-sm sticky top-0 z-50 px-4 lg:px-6">
                        <SidebarTrigger className="-ml-1" />
                        <Separator orientation="vertical" className="h-6" />

                        <h1 className="text-lg font-semibold tracking-tight">
                            {pageName}
                        </h1>

                        <div className="ml-auto flex items-center gap-2">
                            <Select
                                className="capitalize"
                                value={colorTheme}
                                onValueChange={setColorTheme}
                            >
                                <SelectTrigger>
                                    <SelectValue placeholder="Pilih Tema" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectGroup>
                                        <SelectLabel>Tema</SelectLabel>
                                        {colorThemes.map((item) => (
                                            <SelectItem
                                                key={item}
                                                value={item}
                                            >
                                                {item}
                                            </SelectItem>
                                        ))}
                                    </SelectGroup>
                                </SelectContent>
                            </Select>

                            <Button
                                variant="ghost"
                                size="icon"
                                onClick={toggleTheme}
                            >
                                {theme === "light" ? (
                                    <Sun className="h-4 w-4" />
                                ) : (
                                    <Moon className="h-4 w-4" />
                                )}
                                <span className="sr-only">Toggle theme</span>
                            </Button>
                        </div>
                    </header>

                    <div className="flex flex-1 flex-col">
                        <div className="@container/main flex flex-1 flex-col gap-2">
                            <div className="flex flex-col gap-4 py-4 px-4 md:px-6 md:py-6">
                                {children}
                            </div>
                        </div>
                    </div>
                </SidebarInset>
            </SidebarProvider>

            <Toaster richColors position="top-center" />
        </>
    );
}
