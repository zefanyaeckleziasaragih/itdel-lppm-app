import AppLayout from "@/layouts/app-layout";
import { usePage, router } from "@inertiajs/react";
import { useState } from "react";

import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from "@/components/ui/select";

export default function PilihDataPenghargaanPage() {
    const { sintaList, scopusList, sinta_id, scopus_id, prosiding } = usePage().props;

    const [selectedSinta, setSelectedSinta] = useState(sinta_id || "");
    const [selectedScopus, setSelectedScopus] = useState(scopus_id || "");
    const [selectedJurnal, setSelectedJurnal] = useState(prosiding || "");

    const handleLanjutkan = () => {
        const params = new URLSearchParams();
        
        if (selectedSinta) params.append("sinta_id", selectedSinta);
        if (selectedScopus) params.append("scopus_id", selectedScopus);
        if (selectedJurnal) params.append("prosiding", selectedJurnal);

        router.visit(route("pengajuan.jurnal.form") + "?" + params.toString());
    };

    return (
        <AppLayout>
            <div className="flex flex-col gap-4">
                {/* Tombol Kembali */}
                <Button
                    variant="outline"
                    onClick={() => router.visit(route("pengajuan.jurnal.daftar"))}
                    className="w-fit px-3 py-2 h-auto text-sm"
                >
                    ← Kembali
                </Button>

                {/* Title */}
                <h2 className="text-lg font-semibold text-center">
                    Pengajuan Penghargaan Jurnal oleh Dosen
                </h2>

                {/* Form Container - Sesuai Figma dengan batasan max-width */}
                <div className="max-w-4xl mx-auto w-full px-4">
                    <div className="flex flex-col gap-4">
                        {/* Row 1: Sinta ID & Scopus ID - Sejajar seperti Figma */}
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                            {/* Sinta ID */}
                            <div className="flex flex-col gap-2">
                                <label className="text-sm font-medium">Sinta ID</label>
                                <Input
                                    placeholder="Value"
                                    value={selectedSinta}
                                    onChange={(e) => setSelectedSinta(e.target.value)}
                                />
                            </div>

                            {/* Scopus ID */}
                            <div className="flex flex-col gap-2">
                                <label className="text-sm font-medium">Scopus ID</label>
                                <Input
                                    placeholder="Value"
                                    value={selectedScopus}
                                    onChange={(e) => setSelectedScopus(e.target.value)}
                                />
                            </div>
                        </div>

                        {/* Row 2: Jurnal - Full width memanjang ke samping sesuai Figma */}
                        <div className="flex flex-col gap-2">
                            <label className="text-sm font-medium">Jurnal</label>
                            <Select
                                value={selectedJurnal}
                                onValueChange={setSelectedJurnal}
                            >
                                <SelectTrigger className="w-full">
                                    <SelectValue placeholder="Value" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="jurnal1">Jurnal Internasional Q1</SelectItem>
                                    <SelectItem value="jurnal2">Jurnal Internasional Q2</SelectItem>
                                    <SelectItem value="jurnal3">Jurnal Internasional Q3</SelectItem>
                                    <SelectItem value="jurnal4">Jurnal Internasional Q4</SelectItem>
                                    <SelectItem value="jurnal5">Jurnal Nasional Terakreditasi</SelectItem>
                                    <SelectItem value="jurnal6">Jurnal Nasional Tidak Terakreditasi</SelectItem>
                                </SelectContent>
                            </Select>
                        </div>
                    </div>
                </div>
            </div>

            {/* Tombol Lanjutkan - Fixed di kanan bawah */}
            <Button
                onClick={handleLanjutkan}
                className="fixed bottom-6 right-6 px-8 py-5 text-base shadow-lg"
            >
                Lanjutkan
            </Button>
        </AppLayout>
    );
}