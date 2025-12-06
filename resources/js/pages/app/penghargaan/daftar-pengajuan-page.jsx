// resources/js/pages/app/penghargaan/daftar-pengajuan-page.jsx

import AppLayout from "@/layouts/app-layout";
import { Link, usePage } from "@inertiajs/react";
import { useEffect, useMemo, useState } from "react";
import { route } from "ziggy-js";

import { Input } from "@/components/ui/input";
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from "@/components/ui/select";

import * as Icon from "@tabler/icons-react";
import { toast } from "sonner";

const SEARCH_BY_DEFAULT = "judul";
const SORT_BY_DEFAULT = "tanggal-desc";

export default function DaftarPengajuanPage() {
    const { pengajuan, flash } = usePage().props;

    const [search, setSearch] = useState("");
    const [searchBy, setSearchBy] = useState(SEARCH_BY_DEFAULT);
    const [sortBy, setSortBy] = useState(SORT_BY_DEFAULT);

    // === TAMPILKAN POPUP KETIKA ADA flash.success ===
    useEffect(() => {
        if (flash?.success) {
            toast.success(flash.success);
        }
    }, [flash?.success]);

    const data = useMemo(() => {
        const list = Array.isArray(pengajuan) ? pengajuan : [];

        // 1. Filter
        let filtered = list.filter((item) => {
            if (!search) return true;

            const term = search.toLowerCase();

            if (searchBy === "judul") {
                return item.judul.toLowerCase().includes(term);
            }
            if (searchBy === "penulis") {
                return item.penulis.toLowerCase().includes(term);
            }
            if (searchBy === "jenis") {
                return item.jenis.toLowerCase().includes(term);
            }

            // default: cek judul + penulis
            return (
                item.judul.toLowerCase().includes(term) ||
                item.penulis.toLowerCase().includes(term)
            );
        });

        // 2. Sorting sederhana
        filtered = [...filtered].sort((a, b) => {
            if (sortBy === "tanggal-asc") {
                return a.tanggal.localeCompare(b.tanggal);
            }
            if (sortBy === "tanggal-desc") {
                return b.tanggal.localeCompare(a.tanggal);
            }
            if (sortBy === "status") {
                return a.status.localeCompare(b.status);
            }
            return 0;
        });

        return filtered;
    }, [pengajuan, search, searchBy, sortBy]);

    const statusClass = (status) => {
        if (!status) return "text-muted-foreground";
        const lower = status.toLowerCase();
        if (lower.includes("disetujui") || lower.includes("setuju"))
            return "text-green-500";
        if (lower.includes("menolak") || lower.includes("ditolak"))
            return "text-red-500";
        return "text-muted-foreground";
    };

    return (
        <AppLayout>
            <div className="flex flex-col gap-4">
                {/* HEADER + FILTER BAR */}
                <div className="flex flex-col gap-4">
                    <h2 className="text-lg font-semibold">
                        Daftar Dosen Pengajuan Penghargaan
                    </h2>

                    <div className="flex flex-wrap items-center gap-3">
                        {/* Search input */}
                        <div className="flex items-center gap-2 flex-1 min-w-[220px]">
                            <Input
                                value={search}
                                onChange={(e) => setSearch(e.target.value)}
                                placeholder="Type to search"
                                className="w-full"
                            />
                        </div>

                        {/* Search by */}
                        <Select value={searchBy} onValueChange={setSearchBy}>
                            <SelectTrigger className="w-[180px]">
                                <SelectValue placeholder="Search by" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="judul">
                                    Search by: Judul
                                </SelectItem>
                                <SelectItem value="penulis">
                                    Search by: Penulis
                                </SelectItem>
                                <SelectItem value="jenis">
                                    Search by: Jenis
                                </SelectItem>
                            </SelectContent>
                        </Select>

                        {/* Sort by */}
                        <Select value={sortBy} onValueChange={setSortBy}>
                            <SelectTrigger className="w-[180px]">
                                <SelectValue placeholder="Sort by" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="tanggal-desc">
                                    Sort by: Tanggal (baru → lama)
                                </SelectItem>
                                <SelectItem value="tanggal-asc">
                                    Sort by: Tanggal (lama → baru)
                                </SelectItem>
                                <SelectItem value="status">
                                    Sort by: Status
                                </SelectItem>
                            </SelectContent>
                        </Select>
                    </div>
                </div>

                {/* LIST CARD PENGAJUAN */}
                <div className="flex flex-col gap-3">
                    {data.map((item) => (
                        <Link
                            key={item.id}
                            href={route("penghargaan.detail", item.id)}
                            className="flex items-center justify-between rounded-xl border bg-card px-4 py-3 shadow-sm hover:bg-accent transition"
                        >
                            {/* Left: icon + judul + penulis */}
                            <div className="flex items-center gap-3">
                                <div className="flex h-10 w-10 items-center justify-center rounded-full border bg-background">
                                    <Icon.IconFileText size={20} />
                                </div>
                                <div className="flex flex-col">
                                    <div className="font-medium">
                                        {item.judul}
                                    </div>
                                    <div className="text-xs text-muted-foreground">
                                        {item.jenis} • {item.penulis}
                                    </div>
                                </div>
                            </div>

                            {/* Right: status + tanggal + prodi */}
                            <div className="flex flex-col items-end gap-1 text-xs">
                                <span className={statusClass(item.status)}>
                                    Status: {item.status}
                                </span>
                                <span className="text-muted-foreground">
                                    {item.tanggal}
                                </span>
                                <span className="text-[11px] text-muted-foreground text-right">
                                    {item.kampus} / {item.fakultas} /{" "}
                                    {item.prodi}
                                </span>
                            </div>
                        </Link>
                    ))}

                    {data.length === 0 && (
                        <p className="text-sm text-muted-foreground">
                            Tidak ada pengajuan yang cocok dengan filter.
                        </p>
                    )}
                </div>
            </div>
        </AppLayout>
    );
}
