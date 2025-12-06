import AppLayout from "@/layouts/app-layout";
import { useForm, usePage } from "@inertiajs/react";
import { useState } from "react";
import { route } from "ziggy-js";

import { Input } from "@/components/ui/input";
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from "@/components/ui/select";
import { Button } from "@/components/ui/button";
import {
    Table,
    TableBody,
    TableRow,
    TableCell,
} from "@/components/ui/table";

export default function DetailPengajuanJurnalPage() {
    const { pengajuan } = usePage().props;

    const { data, setData, post, processing } = useForm({
        status: pengajuan.status || "Belum disetujui",
        dana_disetujui: pengajuan.dana_disetujui || "",
    });

    // ====== FORMAT RUPIAH ======
    const formatRupiah = (angka) =>
        new Intl.NumberFormat("id-ID", {
            style: "currency",
            currency: "IDR",
            maximumFractionDigits: 0,
        }).format(angka ?? 0);

    const formatNumber = (angka) =>
        new Intl.NumberFormat("id-ID", {
            maximumFractionDigits: 0,
        }).format(angka ?? 0);

    // state khusus buat tampilan input (pakai titik)
    const [danaDisplay, setDanaDisplay] = useState(
        data.dana_disetujui ? formatNumber(data.dana_disetujui) : ""
    );

    const handleDanaChange = (e) => {
        const input = e.target.value;

        // ambil hanya digit (buang titik, koma, huruf)
        const numericString = input.replace(/\D/g, "");

        if (numericString === "") {
            setDanaDisplay("");
            setData("dana_disetujui", "");
            return;
        }

        const value = parseInt(numericString, 10);

        setData("dana_disetujui", value);
        setDanaDisplay(formatNumber(value)); // contoh: 1000000 -> "1.000.000"
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        post(route("penghargaan.konfirmasi", pengajuan.id));
    };

    // helper: styling cell label
    const labelCellClass =
        "w-1/3 bg-muted/70 font-semibold text-[11px] uppercase tracking-wide text-muted-foreground px-5 py-3 align-top border-r";

    const valueCellClass = "px-5 py-3 text-sm";

    return (
        <AppLayout>
            <form
                onSubmit={handleSubmit}
                className="flex flex-col gap-8 items-center"
            >
                {/* JUDUL HALAMAN */}
                <h1 className="text-xl font-semibold">
                    Form Konfirmasi Jurnal
                </h1>

                {/* KARTU TABEL DETAIL */}
                <div className="w-full max-w-5xl rounded-2xl border bg-card shadow-md overflow-hidden">
                    {/* header tipis di atas tabel */}
                    <div className="flex items-center justify-between border-b px-6 py-3 bg-muted/60">
                        <p className="text-sm font-medium">
                            Detail Pengajuan Penghargaan
                        </p>
                        <p className="text-xs text-muted-foreground">
                            ID Pengajuan: #{pengajuan.id}
                        </p>
                    </div>

                    <Table className="w-full">
                        <TableBody>
                            {/* Nama Dosen */}
                            <TableRow className="border-b last:border-b-0">
                                <TableCell className={labelCellClass}>
                                    Nama Dosen
                                </TableCell>
                                <TableCell className={valueCellClass}>
                                    {pengajuan.nama_dosen}
                                </TableCell>
                            </TableRow>

                            {/* NIP */}
                            <TableRow className="border-b last:border-b-0">
                                <TableCell className={labelCellClass}>
                                    NIP
                                </TableCell>
                                <TableCell className={valueCellClass}>
                                    {pengajuan.nip}
                                </TableCell>
                            </TableRow>

                            {/* NIK */}
                            <TableRow className="border-b last:border-b-0">
                                <TableCell className={labelCellClass}>
                                    NIK
                                </TableCell>
                                <TableCell className={valueCellClass}>
                                    {pengajuan.nik}
                                </TableCell>
                            </TableRow>

                            {/* Jenis Penghargaan */}
                            <TableRow className="border-b last:border-b-0">
                                <TableCell className={labelCellClass}>
                                    Jenis Penghargaan
                                </TableCell>
                                <TableCell className={valueCellClass}>
                                    {pengajuan.jenis_penghargaan}
                                </TableCell>
                            </TableRow>

                            {/* Nama Jurnal */}
                            <TableRow className="border-b last:border-b-0">
                                <TableCell className={labelCellClass}>
                                    Nama Jurnal
                                </TableCell>
                                <TableCell className={valueCellClass}>
                                    {pengajuan.nama_kegiatan}
                                </TableCell>
                            </TableRow>

                            {/* Indeks Prosiding / Jurnal */}
                            <TableRow className="border-b last:border-b-0">
                                <TableCell className={labelCellClass}>
                                    Indeks Prosiding / Jurnal
                                </TableCell>
                                <TableCell className={valueCellClass}>
                                    {pengajuan.indeks}
                                </TableCell>
                            </TableRow>

                            {/* Dana Maksimum */}
                            <TableRow className="border-b last:border-b-0">
                                <TableCell className={labelCellClass}>
                                    Dana Maksimum
                                </TableCell>
                                <TableCell className={valueCellClass}>
                                    {formatRupiah(
                                        pengajuan.dana_maksimum
                                    )}
                                </TableCell>
                            </TableRow>

                            {/* Status (dropdown) */}
                            <TableRow className="border-b last:border-b-0">
                                <TableCell className={labelCellClass}>
                                    Status
                                </TableCell>
                                <TableCell className={valueCellClass}>
                                    <Select
                                        value={data.status}
                                        onValueChange={(val) =>
                                            setData(
                                                "status",
                                                val
                                            )
                                        }
                                    >
                                        <SelectTrigger className="w-[220px]">
                                            <SelectValue />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="Belum disetujui">
                                                Belum disetujui
                                            </SelectItem>
                                            <SelectItem value="Setuju">
                                                Setuju
                                            </SelectItem>
                                            <SelectItem value="Menolak">
                                                Menolak
                                            </SelectItem>
                                        </SelectContent>
                                    </Select>
                                </TableCell>
                            </TableRow>

                            {/* Bukti Pengajuan */}
                            <TableRow>
                                <TableCell className={labelCellClass}>
                                    Bukti Pengajuan
                                </TableCell>
                                <TableCell className={valueCellClass}>
                                    <a
                                        href={pengajuan.bukti_url}
                                        target="_blank"
                                        rel="noreferrer"
                                        className="text-primary underline text-sm"
                                    >
                                        Lihat Dokumen
                                    </a>
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </div>

                {/* DANA YANG DISETUJUI + TOMBOL SIMPAN */}
                <div className="w-full max-w-5xl flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div className="flex flex-col gap-2">
                        <label className="text-sm font-medium">
                            Dana yang disetujui
                        </label>
                        <Input
                            type="text"
                            inputMode="numeric"
                            value={danaDisplay}
                            onChange={handleDanaChange}
                            placeholder="Contoh: 1.000.000"
                            className="w-[260px]"
                        />
                        <p className="text-[11px] text-muted-foreground">
                            Ketik angka saja, sistem akan otomatis
                            menambahkan tanda titik (contoh:
                            1000000 → 1.000.000).
                        </p>
                    </div>

                    <Button
                        type="submit"
                        className="px-10 mt-2 md:mt-6 md:self-end"
                        disabled={processing}
                    >
                        Simpan
                    </Button>
                </div>
            </form>
        </AppLayout>
    );
}
