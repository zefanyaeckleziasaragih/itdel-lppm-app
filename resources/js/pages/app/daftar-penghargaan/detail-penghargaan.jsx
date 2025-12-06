import { useEffect } from "react";
import { usePage } from "@inertiajs/react";
import { route } from "ziggy-js";
import AppLayout from "@/layouts/app-layout";
import { Card, CardContent } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { AUTH_TOKEN_KEY } from "@/lib/consts";

export default function DetailPenghargaanPage() {
    const { auth, authToken, penghargaan } = usePage().props;

    useEffect(() => {
        if (authToken) {
            localStorage.setItem(AUTH_TOKEN_KEY, authToken);
        } else {
            window.location.href = route("auth.logout");
        }
    }, []);

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
                        <table className="w-full">
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
                                    <td className="p-4">{penghargaan.prodi}</td>
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
                                            className="text-blue-600 hover:underline"
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

                {/* Button */}
                <div className="flex justify-end">
                    <Button
                        size="lg"
                        className="bg-black hover:bg-gray-800 text-white"
                    >
                        Dana Dicairkan
                    </Button>
                </div>
            </div>
        </AppLayout>
    );
}
