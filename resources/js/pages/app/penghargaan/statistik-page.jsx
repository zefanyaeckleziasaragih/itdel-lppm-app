import AppLayout from "@/layouts/app-layout";
import { usePage } from "@inertiajs/react";
import { useMemo, useState } from "react";

import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from "@/components/ui/select";

import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";

import {
    LineChart,
    Line,
    XAxis,
    YAxis,
    Tooltip,
    Legend,
    ResponsiveContainer,
    CartesianGrid,
} from "recharts";

const FILTER_ALL = "all";

// Opsi fakultas
const FAKULTAS_OPTIONS = [
    { value: FILTER_ALL, label: "Semua Fakultas" },
    {
        value: "Fakultas Informatika dan Teknik Elektro",
        label: "Fakultas Informatika dan Teknik Elektro",
    },
    {
        value: "Fakultas Teknik Industri",
        label: "Fakultas Teknik Industri",
    },
    {
        value: "Fakultas Teknik Bioproses",
        label: "Fakultas Teknik Bioproses",
    },
    {
        value: "Fakultas Vokasi",
        label: "Fakultas Vokasi",
    },
];

// Opsi prodi + mapping fakultasnya
const PRODI_OPTIONS = [
    { value: FILTER_ALL, label: "Semua Prodi", fakultas: FILTER_ALL },

    // FITE
    {
        value: "Informatika",
        label: "Informatika",
        fakultas: "Fakultas Informatika dan Teknik Elektro",
    },
    {
        value: "Teknik Elektro",
        label: "Teknik Elektro",
        fakultas: "Fakultas Informatika dan Teknik Elektro",
    },
    {
        value: "Sistem Informasi",
        label: "Sistem Informasi",
        fakultas: "Fakultas Informatika dan Teknik Elektro",
    },

    // FTI
    {
        value: "Manajemen Rekayasa",
        label: "Manajemen Rekayasa",
        fakultas: "Fakultas Teknik Industri",
    },
    {
        value: "Teknik Metalurgi",
        label: "Teknik Metalurgi",
        fakultas: "Fakultas Teknik Industri",
    },

    // FTB
    {
        value: "Bioproses",
        label: "Bioproses",
        fakultas: "Fakultas Teknik Bioproses",
    },
    {
        value: "Bioteknologi",
        label: "Bioteknologi",
        fakultas: "Fakultas Teknik Bioproses",
    },

    // Vokasi
    {
        value: "Teknologi Komputer",
        label: "Teknologi Komputer",
        fakultas: "Fakultas Vokasi",
    },
    {
        value: "Teknologi Informasi",
        label: "Teknologi Informasi",
        fakultas: "Fakultas Vokasi",
    },
    {
        value: "Teknologi Rekayasa Perangkat Lunak",
        label: "Teknologi Rekayasa Perangkat Lunak",
        fakultas: "Fakultas Vokasi",
    },
];

export default function StatistikPage() {
    const { statistik, summary } = usePage().props;

    const [filterFakultas, setFilterFakultas] = useState(FILTER_ALL);
    const [filterProdi, setFilterProdi] = useState(FILTER_ALL);

    // Prodi yang muncul tergantung fakultas yang dipilih
    const prodiFilteredOptions = useMemo(() => {
        if (filterFakultas === FILTER_ALL) return PRODI_OPTIONS;
        return PRODI_OPTIONS.filter(
            (p) => p.value === FILTER_ALL || p.fakultas === filterFakultas
        );
    }, [filterFakultas]);

    // Data chart yang sudah difilter
    const chartData = useMemo(() => {
        const filtered = statistik.filter((row) => {
            const matchFakultas =
                filterFakultas === FILTER_ALL ||
                row.fakultas === filterFakultas;

            const matchProdi =
                filterProdi === FILTER_ALL || row.prodi === filterProdi;

            return matchFakultas && matchProdi;
        });

        // pastikan field buku selalu ada (kalau belum ada di data -> 0)
        return filtered.map((row) => ({
            ...row,
            buku: row.buku ?? 0,
        }));
    }, [statistik, filterFakultas, filterProdi]);

    const formatRupiah = (angka) =>
        new Intl.NumberFormat("id-ID", {
            style: "currency",
            currency: "IDR",
            maximumFractionDigits: 0,
        }).format(angka ?? 0);

    const approvalRate = summary?.approvalRateBulanIni ?? 0;
    const totalPengajuanBulanIni = summary?.totalPengajuanBulanIni ?? 0;

    const rekapJenis = summary?.rekapJenisBulanIni ?? {};
    const jurnalBlnIni = rekapJenis.jurnal ?? 0;
    const seminarBlnIni = rekapJenis.seminar ?? 0;
    const bukuBlnIni = rekapJenis.buku ?? 0;

    return (
        <AppLayout>
            <div className="flex flex-col gap-4">
                {/* BAR KECIL: total pengajuan & approval rate */}
                <div className="flex flex-wrap gap-4 text-sm text-muted-foreground">
                    <span>
                        Total pengajuan bulan ini:{" "}
                        <span className="font-semibold">
                            {totalPengajuanBulanIni}
                        </span>
                    </span>
                    <span>
                        Approval rate bulan ini:{" "}
                        <span className="font-semibold">
                            {approvalRate.toFixed
                                ? approvalRate.toFixed(1)
                                : approvalRate}
                            %
                        </span>
                    </span>
                </div>

                {/* FILTER BAR */}
                <div className="flex flex-wrap gap-3">
                    {/* Fakultas */}
                    <Select
                        value={filterFakultas}
                        onValueChange={(val) => {
                            setFilterFakultas(val);
                            setFilterProdi(FILTER_ALL);
                        }}
                    >
                        <SelectTrigger className="w-[260px]">
                            <SelectValue placeholder="Fakultas" />
                        </SelectTrigger>
                        <SelectContent>
                            {FAKULTAS_OPTIONS.map((fak) => (
                                <SelectItem key={fak.value} value={fak.value}>
                                    {fak.label}
                                </SelectItem>
                            ))}
                        </SelectContent>
                    </Select>

                    {/* Prodi */}
                    <Select value={filterProdi} onValueChange={setFilterProdi}>
                        <SelectTrigger className="w-[260px]">
                            <SelectValue placeholder="Program Studi" />
                        </SelectTrigger>
                        <SelectContent>
                            {prodiFilteredOptions.map((p) => (
                                <SelectItem key={p.value} value={p.value}>
                                    {p.label}
                                </SelectItem>
                            ))}
                        </SelectContent>
                    </Select>
                </div>

                {/* STATISTIK + PANEL TOTAL BULAN INI */}
                <div className="grid gap-4 lg:grid-cols-[minmax(0,1fr)_260px]">
                    <Card className="mt-2">
                        <CardHeader>
                            <CardTitle>Statistik Penghargaan</CardTitle>
                        </CardHeader>
                        <CardContent className="h-[320px]">
                            <ResponsiveContainer width="100%" height="100%">
                                <LineChart data={chartData}>
                                    <CartesianGrid strokeDasharray="3 3" />
                                    <XAxis dataKey="bulan" />
                                    <YAxis />
                                    <Tooltip />
                                    <Legend />
                                    {/* Biru: Jurnal */}
                                    <Line
                                        type="monotone"
                                        dataKey="jurnal"
                                        name="Jurnal"
                                        stroke="#3b82f6"
                                        strokeWidth={2}
                                        activeDot={{ r: 5 }}
                                    />
                                    {/* Merah: Seminar */}
                                    <Line
                                        type="monotone"
                                        dataKey="seminar"
                                        name="Seminar"
                                        stroke="#ef4444"
                                        strokeWidth={2}
                                    />
                                    {/* Kuning: Buku */}
                                    <Line
                                        type="monotone"
                                        dataKey="buku"
                                        name="Buku"
                                        stroke="#eab308"
                                        strokeWidth={2}
                                    />
                                </LineChart>
                            </ResponsiveContainer>
                        </CardContent>
                    </Card>

                    <div className="flex flex-col gap-3">
                        <Card>
                            <CardHeader className="pb-2">
                                <CardTitle className="text-sm font-medium">
                                    Total Penghargaan Bulan Ini
                                </CardTitle>
                            </CardHeader>
                            <CardContent>
                                <div className="text-3xl font-semibold">
                                    {summary?.totalBulanIni ?? 0}
                                </div>
                            </CardContent>
                        </Card>

                        <Card>
                            <CardHeader className="pb-2">
                                <CardTitle className="text-sm font-medium">
                                    Total Dana Approve
                                </CardTitle>
                            </CardHeader>
                            <CardContent>
                                <div className="text-lg font-semibold">
                                    {formatRupiah(
                                        summary?.totalDanaApprove ?? 0
                                    )}
                                </div>
                            </CardContent>
                        </Card>

                        <Card>
                            <CardHeader className="pb-2">
                                <CardTitle className="text-sm font-medium">
                                    Sisa Dana
                                </CardTitle>
                            </CardHeader>
                            <CardContent>
                                <div className="text-lg font-semibold">
                                    {formatRupiah(summary?.sisaDana ?? 0)}
                                </div>
                                <p className="mt-1 text-xs text-muted-foreground">
                                    dari anggaran{" "}
                                    {formatRupiah(summary?.anggaran ?? 0)}
                                </p>
                            </CardContent>
                        </Card>
                    </div>
                </div>

                {/* RINCIAN KECIL DI BAWAH GRAFIK */}
                <div className="text-xs text-muted-foreground">
                    Rincian bulan ini (semua fakultas & prodi):{" "}
                    <span className="font-medium">
                        Jurnal {jurnalBlnIni}, Seminar {seminarBlnIni}, Buku{" "}
                        {bukuBlnIni}
                    </span>
                </div>
            </div>
        </AppLayout>
    );
}