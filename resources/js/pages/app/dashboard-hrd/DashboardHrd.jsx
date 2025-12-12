// resources/js/Pages/App/HRD/DashboardHRDPage.jsx

import React, { useMemo } from "react";
import { usePage } from "@inertiajs/react";
import AppLayout from "@/layouts/app-layout";

import { Card, CardHeader, CardContent, CardTitle } from "@/components/ui/card";

import {
    LineChart,
    Line,
    XAxis,
    YAxis,
    CartesianGrid,
    Tooltip,
    Legend,
    ResponsiveContainer,
} from "recharts";

const formatRupiah = (angka) =>
    new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        maximumFractionDigits: 0,
    }).format(angka ?? 0);

export default function DashboardHRDPage() {
    // ambil props dari Inertia
    const { statistik: statistikProp, summary: summaryProp } = usePage().props;

    // ====== SUMMARY (CARD KANAN & KETERANGAN ATAS) ======
    const summary = summaryProp || {
        approvalRateBulanIni: 0,
        totalPengajuanBulanIni: 0,
        rekapJenisBulanIni: { jurnal: 0, seminar: 0, buku: 0 },
        totalBulanIni: 0,
        totalDanaApprove: 0,
        sisaDana: 0,
        anggaran: 0,
    };

    const approvalRate = summary.approvalRateBulanIni ?? 0;
    const totalPengajuanBulanIni = summary.totalPengajuanBulanIni ?? 0;
    const rekapJenis = summary.rekapJenisBulanIni ?? {};
    const jurnalBlnIni = rekapJenis.jurnal ?? 0;
    const seminarBlnIni = rekapJenis.seminar ?? 0;

    // ====== BENTUKKAN DATA UNTUK CHART ======
    const chartData = useMemo(() => {
        // 1. Kalau backend langsung kirim array [{ bulan, jurnal, seminar, buku }]
        if (Array.isArray(statistikProp)) {
            return statistikProp;
        }

        // 2. Kalau backend kirim object { labels:[], datasets:{jurnal:[],...} }
        if (
            statistikProp &&
            Array.isArray(statistikProp.labels) &&
            statistikProp.datasets
        ) {
            const { labels, datasets } = statistikProp;
            const dj = datasets.jurnal || [];
            const ds = datasets.seminar || [];
            const db = datasets.buku || [];

            return labels.map((label, idx) => ({
                bulan: label,
                jurnal: dj[idx] ?? 0,
                seminar: ds[idx] ?? 0,
                buku: db[idx] ?? 0,
            }));
        }

        // fallback: tidak ada data
        return [];
    }, [statistikProp]);

    return (
        <AppLayout>
            <div className="flex flex-col gap-6 p-4">
                {/* Pencarian dan Dropdown (dummy UI, belum terhubung filter) */}
                <div className="flex flex-wrap gap-4 items-center">
                    <select className="p-2 border border-gray-300 rounded-lg min-w-[160px]">
                        <option value="name">Search by Name</option>
                        <option value="date">Search by Date</option>
                        <option value="status">Search by Status</option>
                    </select>

                    <select className="p-2 border border-gray-300 rounded-lg min-w-[160px]">
                        <option value="asc">Sort by Ascending</option>
                        <option value="desc">Sort by Descending</option>
                    </select>
                </div>

                {/* Ringkasan atas */}
                <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <Card>
                        <CardHeader>
                            <CardTitle>Approval Rate Bulan Ini</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div className="text-3xl font-semibold">
                                {approvalRate}%
                            </div>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader>
                            <CardTitle>Total Pengajuan Bulan Ini</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div className="text-3xl font-semibold">
                                {totalPengajuanBulanIni}
                            </div>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader>
                            <CardTitle>Rekap Jenis Bulan Ini</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div className="text-sm space-y-1">
                                <div>Jurnal: {jurnalBlnIni}</div>
                                <div>Seminar: {seminarBlnIni}</div>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                {/* Grafik + kartu kanan */}
                <div className="flex flex-col md:flex-row gap-6">
                    {/* CHART */}
                    <div className="flex-1 min-w-0">
                        <Card className="h-[360px]">
                            <CardHeader>
                                <CardTitle>Statistik Penghargaan</CardTitle>
                            </CardHeader>
                            <CardContent className="h-full">
                                <ResponsiveContainer width="100%" height="100%">
                                    <LineChart data={chartData}>
                                        <CartesianGrid strokeDasharray="3 3" />
                                        <XAxis dataKey="bulan" />
                                        <YAxis allowDecimals={false} />
                                        <Tooltip />
                                        <Legend />
                                        <Line
                                            type="monotone"
                                            dataKey="jurnal"
                                            name="Jurnal"
                                            stroke="#3b82f6"
                                            strokeWidth={2}
                                            activeDot={{ r: 5 }}
                                        />
                                        <Line
                                            type="monotone"
                                            dataKey="seminar"
                                            name="Seminar"
                                            stroke="#ef4444"
                                            strokeWidth={2}
                                        />
                                    </LineChart>
                                </ResponsiveContainer>
                            </CardContent>
                        </Card>
                    </div>

                    {/* KARTU KANAN */}
                    <div className="md:w-[320px] flex flex-col gap-4">
                        <Card>
                            <CardHeader>
                                <CardTitle>
                                    Total Penghargaan Bulan Ini
                                </CardTitle>
                            </CardHeader>
                            <CardContent>
                                <div className="text-3xl font-semibold">
                                    {summary.totalBulanIni ?? 0}
                                </div>
                            </CardContent>
                        </Card>

                        <Card>
                            <CardHeader>
                                <CardTitle>Total Dana Approve</CardTitle>
                            </CardHeader>
                            <CardContent>
                                <div className="text-lg font-semibold">
                                    {formatRupiah(summary.totalDanaApprove ?? 0)}
                                </div>
                            </CardContent>
                        </Card>

                        <Card>
                            <CardHeader>
                                <CardTitle>Sisa Dana</CardTitle>
                            </CardHeader>
                            <CardContent>
                                <div className="text-lg font-semibold">
                                    {formatRupiah(summary.sisaDana ?? 0)}
                                </div>
                                <p className="mt-1 text-xs text-muted-foreground">
                                    dari anggaran{" "}
                                    {formatRupiah(summary.anggaran ?? 0)}
                                </p>
                            </CardContent>
                        </Card>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
