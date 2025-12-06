import AppLayout from "@/layouts/app-layout";
import { usePage, router } from "@inertiajs/react";
import { useMemo, useState } from "react";

import { Input } from "@/components/ui/input";
import { Button } from "@/components/ui/button";

import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from "@/components/ui/select";

import * as Icon from "@tabler/icons-react";

export default function DaftarJurnalPage() {
    const { jurnal } = usePage().props;

    const [search, setSearch] = useState("");
    const [searchBy, setSearchBy] = useState("judul");
    const [sortBy, setSortBy] = useState("tanggal-desc");

    const data = useMemo(() => {
        let filtered = jurnal.filter((item) => {
            if (!search) return true;
            const term = search.toLowerCase();

            if (searchBy === "judul")
                return item.judul.toLowerCase().includes(term);
            if (searchBy === "penulis")
                return item.penulis.toLowerCase().includes(term);

            return (
                item.judul.toLowerCase().includes(term) ||
                item.penulis.toLowerCase().includes(term)
            );
        });

        filtered = [...filtered].sort((a, b) => {
            if (sortBy === "tanggal-asc")
                return a.tanggal.localeCompare(b.tanggal);
            if (sortBy === "tanggal-desc")
                return b.tanggal.localeCompare(a.tanggal);
            return 0;
        });

        return filtered;
    }, [jurnal, search, searchBy, sortBy]);

    const statusColor = (status) => {
        if (!status) return "text-muted-foreground";
        const s = status.toLowerCase();
        if (s.includes("sudah")) return "text-green-500";
        if (s.includes("belum")) return "text-red-500";
        return "text-muted-foreground";
    };

    return (
        <AppLayout>
            <div className="flex flex-col gap-4">
                {/* Header */}
                <h2 className="text-lg font-semibold">Daftar Jurnal Dosen</h2>

                {/* FILTER */}
                <div className="flex flex-wrap items-center gap-3">
                    <Input
                        value={search}
                        onChange={(e) => setSearch(e.target.value)}
                        placeholder="Type to search"
                        className="w-full md:w-72"
                    />

                    {/* Search By */}
                    <Select value={searchBy} onValueChange={setSearchBy}>
                        <SelectTrigger className="w-[180px]">
                            <SelectValue placeholder="Search by" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="judul">Judul</SelectItem>
                            <SelectItem value="penulis">Penulis</SelectItem>
                        </SelectContent>
                    </Select>

                    {/* Sort By */}
                    <Select value={sortBy} onValueChange={setSortBy}>
                        <SelectTrigger className="w-[180px]">
                            <SelectValue placeholder="Sort by" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="tanggal-desc">
                                Tanggal Baru → Lama
                            </SelectItem>
                            <SelectItem value="tanggal-asc">
                                Tanggal Lama → Baru
                            </SelectItem>
                        </SelectContent>
                    </Select>
                </div>

                {/* LIST JURNAL */}
                <div className="flex flex-col gap-3">
                    {data.map((item) => (
                        <div
                            key={item.id}
                            className="flex items-center justify-between rounded-xl border bg-card px-4 py-3 shadow-sm"
                        >
                            {/* left */}
                            <div className="flex items-center gap-3">
                                <div className="flex h-10 w-10 items-center justify-center rounded-full border bg-background">
                                    <Icon.IconCircleFilled size={22} />
                                </div>
                                <div>
                                    <div className="font-medium">
                                        {item.judul}
                                    </div>
                                    <div className="text-xs text-muted-foreground">
                                        Penulis: {item.penulis}
                                    </div>
                                </div>
                            </div>

                            {/* right */}
                            <div className="flex flex-col items-end text-xs gap-1">
                                <span className={statusColor(item.status)}>
                                    Status: {item.status}
                                </span>
                                <span className="text-muted-foreground">
                                    {item.tanggal}
                                </span>
                            </div>
                        </div>
                    ))}

                    {data.length === 0 && (
                        <p className="text-sm text-muted-foreground">
                            Tidak ada jurnal ditemukan.
                        </p>
                    )}
                </div>
            </div>

            {/* TOMBOL TAMBAH */}
            <Button
                onClick={() =>
                    router.visit(route("pengajuan.jurnal.pilih-data"))
                }
                className="fixed bottom-6 right-6 px-6 py-5 text-base shadow-lg"
            >
                Tambahkan
            </Button>
        </AppLayout>
    );
}