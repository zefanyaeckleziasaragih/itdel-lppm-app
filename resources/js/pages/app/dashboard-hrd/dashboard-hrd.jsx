import React from "react";
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
import AppLayout from "@/layouts/app-layout";

const formatRupiah = (angka) =>
    new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        maximumFractionDigits: 0,
    }).format(angka ?? 0);

export default function DashboardHRDPage({ statistik, summary }) {
    const approvalRate = summary?.approvalRateBulanIni ?? 0;
    const totalPengajuanBulanIni = summary?.totalPengajuanBulanIni ?? 0;
    const rekapJenis = summary?.rekapJenisBulanIni ?? {};
    const jurnalBlnIni = rekapJenis.jurnal ?? 0;
    const seminarBlnIni = rekapJenis.seminar ?? 0;
    const bukuBlnIni = rekapJenis.buku ?? 0;

    const chartData = statistik ?? [];

    return (
        <AppLayout>
            <div className="flex flex-col gap-6 p-4">
                {/* Pencarian dan Dropdown */}
                <div className="flex flex-wrap gap-4 items-center">
                    <input
                        type="text"
                        placeholder="Type to search"
                        className="p-2 border border-gray-300 rounded-lg flex-grow min-w-[220px]"
                    />
                    <button className="p-2 rounded-lg min-w-[90px] bg-blue-500 text-white">
                        Search
                    </button>

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
                                <div>Buku: {bukuBlnIni}</div>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                {/* Grafik + kartu kanan */}
                <div className="flex flex-col md:flex-row gap-6">
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
                                        <YAxis />
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
                    </div>

                    <div className="md:w-[320px] flex flex-col gap-4">
                        <Card>
                            <CardHeader>
                                <CardTitle>Total Penghargaan Bulan Ini</CardTitle>
                            </CardHeader>
                            <CardContent>
                                <div className="text-3xl font-semibold">
                                    {summary?.totalBulanIni ?? 0}
                                </div>
                            </CardContent>
                        </Card>

                        <Card>
                            <CardHeader>
                                <CardTitle>Total Dana Approve</CardTitle>
                            </CardHeader>
                            <CardContent>
                                <div className="text-lg font-semibold">
                                    {formatRupiah(summary?.totalDanaApprove ?? 0)}
                                </div>
                            </CardContent>
                        </Card>

                        <Card>
                            <CardHeader>
                                <CardTitle>Sisa Dana</CardTitle>
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
            </div>
        </AppLayout>
    );
}
