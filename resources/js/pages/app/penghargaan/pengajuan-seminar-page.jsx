import AppLayout from "@/layouts/app-layout";
import { router, usePage } from "@inertiajs/react";
import { useState, useEffect } from "react";

import { Button } from "@/components/ui/button";
import { Label } from "@/components/ui/label";
import { Input } from "@/components/ui/input";
import { toast } from "sonner";
import { route } from "ziggy-js";

export default function PengajuanSeminarPage() {
    const { selectedProsiding, flash } = usePage().props;
    const [isSubmitting, setIsSubmitting] = useState(false);

    // Redirect jika tidak ada prosiding yang dipilih
    useEffect(() => {
        if (!selectedProsiding) {
            router.visit(route("penghargaan.seminar.pilih"));
        }
    }, [selectedProsiding]);

    // Handle flash messages
    useEffect(() => {
        if (flash.success) {
            toast.success(flash.success);
        }
        if (flash.error) {
            toast.error(flash.error);
        }
    }, [flash]);

    const handleSubmit = () => {
        if (!selectedProsiding) {
            toast.error("Data prosiding tidak ditemukan");
            return;
        }

        setIsSubmitting(true);
        router.post(
            route("penghargaan.seminar.store"),
            {
                prosiding_id: selectedProsiding.id,
            },
            {
                onFinish: () => {
                    setIsSubmitting(false);
                },
            }
        );
    };

    if (!selectedProsiding) {
        return null;
    }

    return (
        <AppLayout>
            <div className="flex flex-col gap-4">
                {/* Tombol Kembali */}
                <div>
                    <Button
                        variant="outline"
                        onClick={() =>
                            router.visit(route("penghargaan.seminar.pilih"))
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
                                    value={selectedProsiding.sinta_id}
                                    disabled
                                    className="bg-muted"
                                />
                            </div>
                            <div className="space-y-2">
                                <Label>Scopus ID</Label>
                                <Input
                                    value={selectedProsiding.scopus_id}
                                    disabled
                                    className="bg-muted"
                                />
                            </div>
                        </div>

                        {/* Prosiding - Full Width & Bigger */}
                        <div className="space-y-2">
                            <Label>Prosiding</Label>
                            <Input
                                value={selectedProsiding.judul}
                                disabled
                                className="bg-muted h-12 text-base"
                            />
                        </div>

                        {/* Nama Forum - Full Width */}
                        <div className="space-y-2">
                            <Label>Nama Forum</Label>
                            <Input
                                value={selectedProsiding.nama_forum}
                                disabled
                                className="bg-muted"
                            />
                        </div>

                        {/* Penulis - Full Width */}
                        <div className="space-y-2">
                            <Label>Penulis</Label>
                            <Input
                                value={selectedProsiding.penulis}
                                disabled
                                className="bg-muted"
                            />
                        </div>

                        {/* Institusi Penyelenggara - Full Width */}
                        <div className="space-y-2">
                            <Label>Institusi Penyelenggara</Label>
                            <Input
                                value={
                                    selectedProsiding.institusi_penyelenggara
                                }
                                disabled
                                className="bg-muted"
                            />
                        </div>

                        {/* Baris 2: Waktu & Tempat Pelaksanaan */}
                        <div className="grid grid-cols-2 gap-6">
                            <div className="space-y-2">
                                <Label>Waktu Pelaksanaan</Label>
                                <Input
                                    value={selectedProsiding.waktu_pelaksanaan}
                                    disabled
                                    className="bg-muted"
                                />
                            </div>
                            <div className="space-y-2">
                                <Label>Tempat Pelaksanaan</Label>
                                <Input
                                    value={selectedProsiding.tempat_pelaksanaan}
                                    disabled
                                    className="bg-muted"
                                />
                            </div>
                        </div>

                        {/* URL - Full Width */}
                        <div className="space-y-2">
                            <Label>URL</Label>
                            <Input
                                value={selectedProsiding.url}
                                disabled
                                className="bg-muted"
                            />
                        </div>

                        {/* Tombol Simpan - Aligned ke kanan */}
                        <div className="flex justify-end pt-2">
                            <Button
                                onClick={handleSubmit}
                                disabled={isSubmitting}
                                className="px-8"
                            >
                                {isSubmitting
                                    ? "Mengajukan..."
                                    : "Simpan Data & Lanjutkan"}
                            </Button>
                        </div>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
