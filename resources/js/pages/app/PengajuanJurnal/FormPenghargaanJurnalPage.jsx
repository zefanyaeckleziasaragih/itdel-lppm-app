import AppLayout from "@/layouts/app-layout";
import { router, usePage } from "@inertiajs/react";
import { useState } from "react";

import { Input } from "@/components/ui/input";
import { Button } from "@/components/ui/button";

export default function FormPenghargaanJurnalPage() {
    const props = usePage().props;

    // Data auto-fill dari controller
    const jurnalData = {
        jurnal_id: props.jurnal_id || "",
        sintaId: props.sinta_id || "",
        scopusId: props.scopus_id || "",
        judulMakalah: props.judulMakalah || "",
        issn: props.issn || "",
        volume: props.volume || "",
        nomor: props.nomor || "",
        namaJurnal: props.namaJurnal || "",
        jumlahHalaman: props.jumlahHalaman || "",
        url: props.url || "",
        penulis: props.penulis || "",
    };

    const handleSubmit = () => {
        if (!jurnalData.jurnal_id) {
            alert("Data jurnal tidak valid!");
            return;
        }

        // Submit ke backend
        router.post(
            route("pengajuan.jurnal.store"),
            { jurnal_id: jurnalData.jurnal_id },
            {
                onSuccess: () => {
                    alert("Pengajuan penghargaan jurnal berhasil!");
                    router.visit(route("pengajuan.jurnal.daftar"));
                },
                onError: (errors) => {
                    console.error("Error:", errors);
                    alert(
                        "Terjadi kesalahan saat mengajukan penghargaan jurnal"
                    );
                },
            }
        );
    };

    return (
        <AppLayout>
            <div className="flex flex-col gap-3">
                {/* Tombol Kembali */}
                <Button
                    variant="outline"
                    onClick={() =>
                        router.visit(route("pengajuan.jurnal.pilih-data"))
                    }
                    className="w-fit px-3 py-2 h-auto text-sm"
                >
                    ← Kembali
                </Button>

                {/* Title */}
                <h2 className="text-lg font-semibold text-center mb-1">
                    Pengajuan Penghargaan Jurnal oleh Dosen
                </h2>

                {/* Form Container - Data Auto-filled */}
                <div className="border-2 border-blue-500 rounded-xl p-5 max-w-4xl mx-auto w-full">
                    <div className="flex flex-col gap-4">
                        {/* Info Banner */}
                        <div className="bg-blue-50 dark:bg-blue-950 border border-blue-200 dark:border-blue-800 rounded-lg p-3 text-sm text-blue-800 dark:text-blue-200">
                            ✓ Data jurnal di bawah ini diisi otomatis dari
                            database Anda
                        </div>

                        {/* Row 1 - Sinta ID & Scopus ID */}
                        <div className="grid md:grid-cols-2 gap-4">
                            <div className="flex flex-col gap-1.5">
                                <label className="text-sm font-medium">
                                    Sinta ID
                                </label>
                                <Input
                                    value={jurnalData.sintaId}
                                    disabled
                                    className="bg-muted"
                                />
                            </div>
                            <div className="flex flex-col gap-1.5">
                                <label className="text-sm font-medium">
                                    Scopus ID
                                </label>
                                <Input
                                    value={jurnalData.scopusId}
                                    disabled
                                    className="bg-muted"
                                />
                            </div>
                        </div>

                        {/* Judul Makalah */}
                        <div className="flex flex-col gap-1.5">
                            <label className="text-sm font-medium">
                                Judul Makalah
                            </label>
                            <Input
                                value={jurnalData.judulMakalah}
                                disabled
                                className="bg-muted"
                            />
                        </div>

                        {/* Row 2 - ISSN, Volume, Nomor */}
                        <div className="grid md:grid-cols-3 gap-4">
                            <div className="flex flex-col gap-1.5">
                                <label className="text-sm font-medium">
                                    ISSN
                                </label>
                                <Input
                                    value={jurnalData.issn}
                                    disabled
                                    className="bg-muted"
                                />
                            </div>
                            <div className="flex flex-col gap-1.5">
                                <label className="text-sm font-medium">
                                    Volume
                                </label>
                                <Input
                                    value={jurnalData.volume}
                                    disabled
                                    className="bg-muted"
                                />
                            </div>
                            <div className="flex flex-col gap-1.5">
                                <label className="text-sm font-medium">
                                    Nomor
                                </label>
                                <Input
                                    value={jurnalData.nomor}
                                    disabled
                                    className="bg-muted"
                                />
                            </div>
                        </div>

                        {/* Nama Jurnal */}
                        <div className="flex flex-col gap-1.5">
                            <label className="text-sm font-medium">
                                Nama Jurnal
                            </label>
                            <Input
                                value={jurnalData.namaJurnal}
                                disabled
                                className="bg-muted"
                            />
                        </div>

                        {/* Row 3 - Jumlah Halaman & Penulis */}
                        <div className="grid md:grid-cols-2 gap-4">
                            <div className="flex flex-col gap-1.5">
                                <label className="text-sm font-medium">
                                    Jumlah Halaman
                                </label>
                                <Input
                                    value={jurnalData.jumlahHalaman}
                                    disabled
                                    className="bg-muted"
                                />
                            </div>
                            <div className="flex flex-col gap-1.5">
                                <label className="text-sm font-medium">
                                    Penulis
                                </label>
                                <Input
                                    value={jurnalData.penulis}
                                    disabled
                                    className="bg-muted"
                                />
                            </div>
                        </div>

                        {/* URL */}
                        <div className="flex flex-col gap-1.5">
                            <label className="text-sm font-medium">URL</label>
                            <Input
                                value={jurnalData.url}
                                disabled
                                className="bg-muted"
                            />
                        </div>

                        {/* Button Ajukan */}
                        <div className="flex justify-end pt-2">
                            <Button onClick={handleSubmit} className="px-8">
                                Ajukan Penghargaan
                            </Button>
                        </div>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
