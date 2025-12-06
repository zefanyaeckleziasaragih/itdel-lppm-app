import AppLayout from "@/layouts/app-layout";
import { router, usePage } from "@inertiajs/react";
import { useState } from "react";

import { Input } from "@/components/ui/input";
import { Button } from "@/components/ui/button";
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from "@/components/ui/select";

export default function FormPenghargaanJurnalPage() {
    // Ambil data dari props (dari halaman sebelumnya atau edit)
    const props = usePage().props;
    const isEdit = props.isEdit || false;
    const jurnalData = props.jurnal || {};
    
    const [formData, setFormData] = useState({
        sintaId: jurnalData.sintaId || props.sinta_id || "",
        scopusId: jurnalData.scopusId || props.scopus_id || "",
        prosiding: jurnalData.prosiding || props.prosiding || "",
        judulMakalah: jurnalData.judulMakalah || "",
        issn: jurnalData.issn || "",
        volume: jurnalData.volume || "",
        penulis: jurnalData.penulis || "",
        nomor: jurnalData.nomor || "",
        halPaper: jurnalData.halPaper || "",
        tempatPelaksanaan: jurnalData.tempatPelaksanaan || "",
        url: jurnalData.url || "",
    });

    const handleChange = (field, value) => {
        setFormData((prev) => ({
            ...prev,
            [field]: value,
        }));
    };

    const handleSubmit = () => {
        // Validasi
        if (!formData.judulMakalah) {
            alert("Judul Makalah wajib diisi!");
            return;
        }
        if (!formData.issn) {
            alert("ISSN wajib diisi!");
            return;
        }

        console.log("Data yang akan dikirim:", formData);

        if (isEdit) {
            // Update data
            router.put(route("pengajuan.jurnal.update", jurnalData.id), formData, {
                onSuccess: () => {
                    alert("Data berhasil diupdate!");
                    router.visit(route("pengajuan.jurnal.daftar"));
                },
                onError: (errors) => {
                    console.error("Error:", errors);
                    alert("Terjadi kesalahan saat mengupdate data");
                },
            });
        } else {
            // Submit data baru
            router.post(route("pengajuan.jurnal.store"), formData, {
                onSuccess: () => {
                    alert("Data berhasil diajukan!");
                    router.visit(route("pengajuan.jurnal.daftar"));
                },
                onError: (errors) => {
                    console.error("Error:", errors);
                    alert("Terjadi kesalahan saat mengajukan data");
                },
            });
        }
    };

    return (
        <AppLayout>
            <div className="flex flex-col gap-3">
                {/* Tombol Kembali */}
                <Button
                    variant="outline"
                    onClick={() => router.visit(route("pengajuan.jurnal.pilih-data"))}
                    className="w-fit px-3 py-2 h-auto text-sm"
                >
                    ← Kembali
                </Button>

                {/* Title - Space lebih compact */}
                <h2 className="text-lg font-semibold text-center mb-1">
                    Pengajuan Penghargaan Jurnal oleh Dosen
                </h2>

                {/* Form Container - Space dikurangi */}
                <div className="border-2 border-blue-500 rounded-xl p-5 max-w-4xl mx-auto w-full">
                    <div className="flex flex-col gap-4">
                        {/* Row 1 - Sinta ID & Scopus ID */}
                        <div className="grid md:grid-cols-2 gap-4">
                            <div className="flex flex-col gap-1.5">
                                <label className="text-sm font-medium">Sinta ID</label>
                                <Input
                                    placeholder="Value"
                                    value={formData.sintaId}
                                    onChange={(e) =>
                                        handleChange("sintaId", e.target.value)
                                    }
                                />
                            </div>
                            <div className="flex flex-col gap-1.5">
                                <label className="text-sm font-medium">Scopus ID</label>
                                <Input
                                    placeholder="Value"
                                    value={formData.scopusId}
                                    onChange={(e) =>
                                        handleChange("scopusId", e.target.value)
                                    }
                                />
                            </div>
                        </div>

                        {/* Judul Makalah */}
                        <div className="flex flex-col gap-1.5">
                            <label className="text-sm font-medium">
                                Judul Makalah <span className="text-red-500">*</span>
                            </label>
                            <Input
                                placeholder="Value"
                                value={formData.judulMakalah}
                                onChange={(e) =>
                                    handleChange("judulMakalah", e.target.value)
                                }
                            />
                        </div>

                        {/* Row 2 - ISSN, Volume, Penulis */}
                        <div className="grid md:grid-cols-3 gap-4">
                            <div className="flex flex-col gap-1.5">
                                <label className="text-sm font-medium">
                                    ISSN <span className="text-red-500">*</span>
                                </label>
                                <Input
                                    placeholder="Value"
                                    value={formData.issn}
                                    onChange={(e) =>
                                        handleChange("issn", e.target.value)
                                    }
                                />
                            </div>
                            <div className="flex flex-col gap-1.5">
                                <label className="text-sm font-medium">Volume</label>
                                <Input
                                    placeholder="Value"
                                    value={formData.volume}
                                    onChange={(e) =>
                                        handleChange("volume", e.target.value)
                                    }
                                />
                            </div>
                            <div className="flex flex-col gap-1.5">
                                <label className="text-sm font-medium">Penulis</label>
                                <Select
                                    value={formData.penulis}
                                    onValueChange={(value) =>
                                        handleChange("penulis", value)
                                    }
                                >
                                    <SelectTrigger>
                                        <SelectValue placeholder="Value" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="penulis1">
                                            Penulis 1
                                        </SelectItem>
                                        <SelectItem value="penulis2">
                                            Penulis 2
                                        </SelectItem>
                                        <SelectItem value="penulis3">
                                            Penulis 3
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>
                        </div>

                        {/* Nomor */}
                        <div className="flex flex-col gap-1.5">
                            <label className="text-sm font-medium">Nomor</label>
                            <Input
                                placeholder="Value"
                                value={formData.nomor}
                                onChange={(e) => handleChange("nomor", e.target.value)}
                            />
                        </div>

                        {/* Row 3 - Hal Paper & Tempat Pelaksanaan */}
                        <div className="grid md:grid-cols-2 gap-4">
                            <div className="flex flex-col gap-1.5">
                                <label className="text-sm font-medium">
                                    Hal Paper
                                </label>
                                <Input
                                    placeholder="Value"
                                    value={formData.halPaper}
                                    onChange={(e) =>
                                        handleChange("halPaper", e.target.value)
                                    }
                                />
                            </div>
                            <div className="flex flex-col gap-1.5">
                                <label className="text-sm font-medium">
                                    Tempat Pelaksanaan
                                </label>
                                <Input
                                    placeholder="Value"
                                    value={formData.tempatPelaksanaan}
                                    onChange={(e) =>
                                        handleChange("tempatPelaksanaan", e.target.value)
                                    }
                                />
                            </div>
                        </div>

                        {/* URL */}
                        <div className="flex flex-col gap-1.5">
                            <label className="text-sm font-medium">URL</label>
                            <Input
                                placeholder="Value"
                                value={formData.url}
                                onChange={(e) => handleChange("url", e.target.value)}
                            />
                        </div>

                        {/* Button Ajukan - Fixed di kanan bawah */}
                        <div className="flex justify-end pt-2">
                            <Button
                                onClick={handleSubmit}
                                className="px-8"
                            >
                                {isEdit ? "Update" : "Ajukan"}
                            </Button>
                        </div>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}