import AppLayout from "@/layouts/app-layout";
import { usePage, router } from "@inertiajs/react";
import { useState } from "react";

import { Button } from "@/components/ui/button";
import { Label } from "@/components/ui/label";
import { Input } from "@/components/ui/input";
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from "@/components/ui/select";

export default function PilihDataPenghargaanPage() {
    const { jurnalList, sinta_id, scopus_id } = usePage().props;

    const [selectedJurnalId, setSelectedJurnalId] = useState("");

    const handleLanjutkan = () => {
        if (!selectedJurnalId) {
            alert("Silakan pilih jurnal terlebih dahulu");
            return;
        }

        router.visit(
            route("pengajuan.jurnal.form") + `?jurnal_id=${selectedJurnalId}`
        );
    };

    return (
        <AppLayout>
            <div className="flex flex-col gap-4">
                {/* Tombol Kembali */}
                <Button
                    variant="outline"
                    onClick={() =>
                        router.visit(route("pengajuan.jurnal.daftar"))
                    }
                    className="w-fit px-3 py-2 h-auto text-sm"
                >
                    ← Kembali
                </Button>

                {/* Title */}
                <h2 className="text-lg font-semibold text-center">
                    Pengajuan Penghargaan Jurnal oleh Dosen
                </h2>

                {/* Form Container */}
                <div className="max-w-4xl mx-auto w-full px-4">
                    <div className="flex flex-col gap-4">
                        {/* Row 1: Sinta ID & Scopus ID */}
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div className="flex flex-col gap-2">
                                <Label>Sinta ID</Label>
                                <Input
                                    value={sinta_id || ""}
                                    disabled
                                    className="bg-muted"
                                />
                            </div>
                            <div className="flex flex-col gap-2">
                                <Label>Scopus ID</Label>
                                <Input
                                    value={scopus_id || ""}
                                    disabled
                                    className="bg-muted"
                                />
                            </div>
                        </div>

                        {/* Row 2: Pilih Jurnal dari Database */}
                        <div className="flex flex-col gap-2">
                            <Label>Pilih Jurnal Anda</Label>
                            <Select
                                value={selectedJurnalId}
                                onValueChange={setSelectedJurnalId}
                            >
                                <SelectTrigger className="w-full">
                                    <SelectValue placeholder="Pilih jurnal yang akan diajukan..." />
                                </SelectTrigger>
                                <SelectContent>
                                    {jurnalList && jurnalList.length > 0 ? (
                                        jurnalList.map((jurnal) => (
                                            <SelectItem
                                                key={jurnal.id}
                                                value={jurnal.id}
                                            >
                                                {jurnal.label}
                                            </SelectItem>
                                        ))
                                    ) : (
                                        <SelectItem value="no-data" disabled>
                                            Belum ada jurnal tersedia
                                        </SelectItem>
                                    )}
                                </SelectContent>
                            </Select>
                            <p className="text-xs text-muted-foreground">
                                * Hanya jurnal yang belum pernah diajukan untuk
                                penghargaan
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {/* Tombol Lanjutkan */}
            <Button
                onClick={handleLanjutkan}
                disabled={!selectedJurnalId}
                className="fixed bottom-6 right-6 px-8 py-5 text-base shadow-lg"
            >
                Lanjutkan
            </Button>
        </AppLayout>
    );
}
