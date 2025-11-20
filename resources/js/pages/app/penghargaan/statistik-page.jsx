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
    // dari controller, minimal kita butuh props: statistik
    // struktur item statistik:
    // { bulan, jurnal, seminar, buku, fakultas, prodi }
    const { statistik } = usePage().props;

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

    return (
        <AppLayout>
            <div className="flex flex-col gap-4">
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

                {/* KARTU STATISTIK */}
                <Card className="mt-2">
                    <CardHeader>
                        <CardTitle>Statistik Penghargaan</CardTitle>
                    </CardHeader>
                    <CardContent className="h-80">
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
                                    stroke="#3b82f6" // tailwind blue-500
                                    strokeWidth={2}
                                    activeDot={{ r: 5 }}
                                />
                                {/* Merah: Seminar */}
                                <Line
                                    type="monotone"
                                    dataKey="seminar"
                                    name="Seminar"
                                    stroke="#ef4444" // tailwind red-500
                                    strokeWidth={2}
                                />
                                {/* Kuning: Buku */}
                                <Line
                                    type="monotone"
                                    dataKey="buku"
                                    name="Buku"
                                    stroke="#eab308" // tailwind yellow-500
                                    strokeWidth={2}
                                />
                            </LineChart>
                        </ResponsiveContainer>
                    </CardContent>
                </Card>
            </div>
        </AppLayout>
    );
}
