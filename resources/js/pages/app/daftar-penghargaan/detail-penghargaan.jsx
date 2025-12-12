import { useEffect } from "react";
import { usePage, router } from "@inertiajs/react";
import { route } from "ziggy-js";
import Swal from "sweetalert2";

import AppLayout from "@/layouts/app-layout";
import { Card, CardContent } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { AUTH_TOKEN_KEY } from "@/lib/consts";

export default function DetailPenghargaanPage() {
    const { authToken, penghargaan } = usePage().props;

    useEffect(() => {
        if (authToken) {
            localStorage.setItem(AUTH_TOKEN_KEY, authToken);
        } else {
            window.location.href = route("auth.logout");
        }
    }, [authToken]);

    const statusSudah = penghargaan.status === "Sudah dicairkan";

    const handleDanaDicairkan = () => {
        if (statusSudah) return;

        Swal.fire({
            title: "Konfirmasi Pencairan Dana",
            text: "Dana untuk penghargaan ini akan ditandai sebagai sudah dicairkan. Yakin ingin melanjutkan?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Ya, cairkan",
            cancelButtonText: "Batal",
            confirmButtonColor: "#16a34a",
            cancelButtonColor: "#6b7280",
            background: "#020617",
            color: "#e5e7eb",
        }).then((result) => {
            if (!result.isConfirmed) return;

            router.post(
                `/daftar-penghargaan/${penghargaan.id}/cairkan`,
                {},
                {
                    onStart: () => {
                        Swal.fire({
                            title: "Memproses...",
                            text: "Mohon tunggu sebentar.",
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            didOpen: () => {
                                Swal.showLoading();
                            },
                            background: "#020617",
                            color: "#e5e7eb",
                        });
                    },
                    onSuccess: () => {
                        Swal.fire({
                            title: "Berhasil",
                            text: "Dana berhasil ditandai sebagai sudah dicairkan.",
                            icon: "success",
                            confirmButtonText: "OK",
                            confirmButtonColor: "#16a34a",
                            background: "#020617",
                            color: "#e5e7eb",
                        });
                        // setelah success, backend sudah redirect ke daftar-penghargaan
                    },
                    onError: () => {
                        Swal.fire({
                            title: "Gagal",
                            text: "Terjadi kesalahan saat memproses pencairan dana.",
                            icon: "error",
                            confirmButtonText: "OK",
                            confirmButtonColor: "#dc2626",
                            background: "#020617",
                            color: "#e5e7eb",
                        });
                    },
                }
            );
        });
    };

    return (
        <AppLayout>
            <div className="space-y-6 max-w-4xl">
                {/* Header */}
                <div>
                    <h1 className="text-3xl font-bold mb-2">
                        Form Detail Pencairan Dana
                    </h1>
                    <h2 className="text-xl font-semibold">
                        Data Diri Pengajuan
                    </h2>
                </div>

                {/* Table Detail */}
                <Card>
                    <CardContent className="p-0">
                        <table className="w-full text-sm">
                            <tbody>
                                <tr className="border-b">
                                    <td className="p-4 bg-muted font-semibold w-1/3">
                                        Nama Dosen
                                    </td>
                                    <td className="p-4">
                                        {penghargaan.nama_dosen}
                                    </td>
                                </tr>
                                <tr className="border-b">
                                    <td className="p-4 bg-muted font-semibold">
                                        NIP
                                    </td>
                                    <td className="p-4">{penghargaan.nip}</td>
                                </tr>
                                <tr className="border-b">
                                    <td className="p-4 bg-muted font-semibold">
                                        Fakultas
                                    </td>
                                    <td className="p-4">
                                        {penghargaan.fakultas}
                                    </td>
                                </tr>
                                <tr className="border-b">
                                    <td className="p-4 bg-muted font-semibold">
                                        Prodi
                                    </td>
                                    <td className="p-4">
                                        {penghargaan.prodi}
                                    </td>
                                </tr>
                                <tr className="border-b">
                                    <td className="p-4 bg-muted font-semibold">
                                        Jenis Penghargaan
                                    </td>
                                    <td className="p-4">
                                        {penghargaan.jenis_penghargaan}
                                    </td>
                                </tr>
                                <tr className="border-b">
                                    <td className="p-4 bg-muted font-semibold">
                                        Judul Penghargaan
                                    </td>
                                    <td className="p-4">
                                        {penghargaan.judul_penghargaan}
                                    </td>
                                </tr>
                                <tr className="border-b">
                                    <td className="p-4 bg-muted font-semibold">
                                        Status
                                    </td>
                                    <td className="p-4">
                                        {penghargaan.status}
                                    </td>
                                </tr>
                                <tr className="border-b">
                                    <td className="p-4 bg-muted font-semibold">
                                        Bukti Pengajuan
                                    </td>
                                    <td className="p-4">
                                        <a
                                            href="#"
                                            className="text-blue-500 hover:underline"
                                        >
                                            {penghargaan.bukti_pengajuan}
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td className="p-4 bg-muted font-semibold">
                                        Nominal Disetujui
                                    </td>
                                    <td className="p-4">
                                        {penghargaan.nominal_disetujui}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </CardContent>
                </Card>

                {/* Tombol Aksi */}
                <div className="flex justify-end">
                    <Button
                        size="lg"
                        className="bg-black hover:bg-gray-800 text-white"
                        onClick={handleDanaDicairkan}
                        disabled={statusSudah}
                    >
                        {statusSudah ? "Dana Sudah Dicairkan" : "Dana Dicairkan"}
                    </Button>
                </div>
            </div>
        </AppLayout>
    );
}
