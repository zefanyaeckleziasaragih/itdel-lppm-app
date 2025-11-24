import AppLayout from "@/layouts/app-layout";
import { router, usePage } from "@inertiajs/react";
import { useState } from "react";

import { Button } from "@/components/ui/button";
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from "@/components/ui/select";
import { Label } from "@/components/ui/label";
import { Input } from "@/components/ui/input";
import { route } from "ziggy-js";

export default function PilihProsidingPage() {
    const { prosidingList } = usePage().props;
    const [selectedProsidingId, setSelectedProsidingId] = useState("");
    const [selectedProsiding, setSelectedProsiding] = useState(null);

    const handleProsidingChange = (value) => {
        const prosiding = prosidingList.find((p) => p.id.toString() === value);
        setSelectedProsidingId(value);
        setSelectedProsiding(prosiding);
    };

    const handleNext = () => {
        if (!selectedProsidingId) {
            alert("Silakan pilih prosiding terlebih dahulu");
            return;
        }

        router.visit(
            route("penghargaan.seminar") +
                `?prosiding_id=${selectedProsidingId}`
        );
    };

    return (
        <AppLayout>
            <div className="flex flex-col gap-4">
                {/* Tombol Kembali */}
                <div>
                    <Button
                        variant="outline"
                        onClick={() =>
                            router.visit(route("penghargaan.seminar.daftar"))
                        }
                    >
                        &lt; Kembali
                    </Button>
                </div>

                {/* Header Judul */}
                <div className="text-center">
                    <h1 className="text-xl font-semibold">
                        Pengajuan Penghargaan Seminar oleh Dosen
                    </h1>
                </div>

                {/* Form Container */}
                <div className="max-w-5xl mx-auto w-full p-8">
                    <div className="space-y-6">
                        {/* Baris 1: Sinta ID & Scopus ID */}
                        <div className="grid grid-cols-2 gap-6">
                            <div className="space-y-2">
                                <Label>Sinta ID</Label>
                                <Input
                                    value={selectedProsiding?.sinta_id || ""}
                                    disabled
                                    className="bg-muted"
                                />
                            </div>
                            <div className="space-y-2">
                                <Label>Scopus ID</Label>
                                <Input
                                    value={selectedProsiding?.scopus_id || ""}
                                    disabled
                                    className="bg-muted"
                                />
                            </div>
                        </div>

                        {/* Dropdown Prosiding - Full Width */}
                        <div className="space-y-2">
                            <Label htmlFor="prosiding">Prosiding</Label>
                            <Select
                                value={selectedProsidingId}
                                onValueChange={handleProsidingChange}
                            >
                                <SelectTrigger id="prosiding" className="h-12">
                                    <SelectValue placeholder="" />
                                </SelectTrigger>
                                <SelectContent>
                                    {prosidingList.map((prosiding) => (
                                        <SelectItem
                                            key={prosiding.id}
                                            value={prosiding.id.toString()}
                                        >
                                            {prosiding.judul}
                                        </SelectItem>
                                    ))}
                                </SelectContent>
                            </Select>
                        </div>

                        {/* Tombol Next - Aligned ke kanan */}
                        <div className="flex justify-end pt-2">
                            <Button
                                onClick={handleNext}
                                disabled={!selectedProsidingId}
                                className="px-8"
                            >
                                Lanjutkan
                            </Button>
                        </div>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
