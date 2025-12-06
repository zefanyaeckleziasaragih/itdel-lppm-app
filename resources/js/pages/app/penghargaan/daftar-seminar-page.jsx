import AppLayout from "@/layouts/app-layout";
import { router, usePage } from "@inertiajs/react";
import { useMemo, useState, useEffect } from "react";

import { Input } from "@/components/ui/input";
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from "@/components/ui/select";
import { Button } from "@/components/ui/button";

import * as Icon from "@tabler/icons-react";
import { toast } from "sonner";
import { route } from "ziggy-js";

const SEARCH_BY_DEFAULT = "judul";
const SORT_BY_DEFAULT = "tanggal-desc";

export default function DaftarSeminarPage() {
    const { seminarList, flash } = usePage().props;

    const [search, setSearch] = useState("");
    const [searchBy, setSearchBy] = useState(SEARCH_BY_DEFAULT);
    const [sortBy, setSortBy] = useState(SORT_BY_DEFAULT);

    // Handle flash messages
    useEffect(() => {
        if (flash.success) {
            toast.success(flash.success);
        }
        if (flash.error) {
            toast.error(flash.error);
        }
    }, [flash]);

    const data = useMemo(() => {
        // 1. Filter
        let filtered = seminarList.filter((item) => {
            if (!search) return true;

            const term = search.toLowerCase();

            if (searchBy === "judul") {
                return item.judul.toLowerCase().includes(term);
            }
            if (searchBy === "penulis") {
                return item.penulis.toLowerCase().includes(term);
            }
            if (searchBy === "status") {
                return item.status.toLowerCase().includes(term);
            }

            return (
                item.judul.toLowerCase().includes(term) ||
                item.penulis.toLowerCase().includes(term)
            );
        });

        // 2. Sorting
        filtered = [...filtered].sort((a, b) => {
            if (sortBy === "tanggal-asc") {
                return a.tanggal_pengajuan.localeCompare(b.tanggal_pengajuan);
            }
            if (sortBy === "tanggal-desc") {
                return b.tanggal_pengajuan.localeCompare(a.tanggal_pengajuan);
            }
            if (sortBy === "status") {
                return a.status.localeCompare(b.status);
            }
            return 0;
        });

        return filtered;
    }, [seminarList, search, searchBy, sortBy]);

    const statusClass = (status) => {
        if (!status) return "text-muted-foreground";
        const lower = status.toLowerCase();
        if (lower.includes("sudah dicairkan")) return "text-green-500";
        if (lower.includes("belum dicairkan")) return "text-yellow-500";
        return "text-muted-foreground";
    };

    return (
        <AppLayout>
            <div className="flex flex-col gap-4">
                {/* HEADER + FILTER BAR */}
                <div className="flex items-center justify-between gap-3">
                    {/* Left side: Search controls */}
                    <div className="flex items-center gap-3 flex-1">
                        <Input
                            value={search}
                            onChange={(e) => setSearch(e.target.value)}
                            placeholder="Type to search"
                            className="max-w-md"
                        />

                        <Button variant="outline">Search</Button>

                        {/* Search by */}
                        <Select value={searchBy} onValueChange={setSearchBy}>
                            <SelectTrigger className="w-[180px]">
                                <SelectValue placeholder="Search by" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="judul">Search by</SelectItem>
                                <SelectItem value="penulis">
                                    Search by: Penulis
                                </SelectItem>
                                <SelectItem value="status">
                                    Search by: Status
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
                                    Sort by
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

                    {/* Right side: Tambahkan button */}
                    <Button
                        onClick={() =>
                            router.visit(route("penghargaan.seminar.pilih"))
                        }
                    >
                        Tambahkan
                    </Button>
                </div>

                {/* LIST CARD SEMINAR */}
                <div className="flex flex-col gap-3">
                    {data.map((item) => (
                        <div
                            key={item.id}
                            className="flex items-center justify-between rounded-xl border bg-card px-4 py-3 shadow-sm"
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
                                        {item.penulis}
                                    </div>
                                </div>
                            </div>

                            {/* Right: status + tanggal */}
                            <div className="flex flex-col items-end gap-1 text-xs">
                                <span className={statusClass(item.status)}>
                                    Status: {item.status}
                                </span>
                                <span className="text-muted-foreground">
                                    dd / mm / yy
                                </span>
                            </div>
                        </div>
                    ))}

                    {data.length === 0 && (
                        <p className="text-sm text-muted-foreground">
                            Tidak ada seminar yang cocok dengan filter.
                        </p>
                    )}
                </div>
            </div>
        </AppLayout>
    );
}
