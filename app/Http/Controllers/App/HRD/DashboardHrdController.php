<?php

namespace App\Http\Controllers\App\HRD;

use App\Helper\ToolsHelper;
use App\Http\Controllers\Controller;
use App\Models\PenghargaanLppmKelompok5\PenghargaanJurnal;
use App\Models\PenghargaanLppmKelompok5\PenghargaanSeminar;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardHrdController extends Controller
{
    public function index(Request $request)
    {
        $auth     = $request->attributes->get('auth');
        $isEditor = $this->checkIsEditor($auth);

        // ==========================================
        // 1. TOTAL PENGAJUAN (SEMUA PERIODE, DATA REAL)
        //    - yang dihitung hanya status_pengajuan = 'disetujui'
        // ==========================================
        $totalJurnal = PenghargaanJurnal::where('status_pengajuan', 'disetujui')->count();
        $totalSeminar = PenghargaanSeminar::where('status_pengajuan', 'disetujui')->count();

        $totalPengajuan = $totalJurnal + $totalSeminar;

        // ==========================================
        // 2. TOTAL YANG SUDAH DICAIRKAN (DATA REAL)
        // ==========================================
        $totalCairJurnal = PenghargaanJurnal::where('status_pengajuan', 'disetujui')
            ->where('status_hrd', 'sudah_dicairkan')
            ->count();

        $totalCairSeminar = PenghargaanSeminar::where('status_pengajuan', 'disetujui')
            ->where('status_hrd', 'sudah_dicairkan')
            ->count();

        $totalCair = $totalCairJurnal + $totalCairSeminar;

        // ==========================================
        // 3. APPROVAL RATE (REAL)
        // ==========================================
        $approvalRate = $totalPengajuan > 0
            ? round(($totalCair / $totalPengajuan) * 100, 1)
            : 0;

        // ==========================================
        // 4. TOTAL DANA APPROVE (REAL DARI DB)
        // ==========================================
        $totalDanaApproveJurnal = (int) PenghargaanJurnal::where('status_pengajuan', 'disetujui')
            ->where('status_hrd', 'sudah_dicairkan')
            ->sum('nominal_disetujui');

        $totalDanaApproveSeminar = (int) PenghargaanSeminar::where('status_pengajuan', 'disetujui')
            ->where('status_hrd', 'sudah_dicairkan')
            ->sum('nominal_disetujui');

        $totalDanaApprove = $totalDanaApproveJurnal + $totalDanaApproveSeminar;

        // ==========================================
        // 5. REKAP JENIS (REAL)
        // ==========================================
        $rekapJenis = [
            'jurnal'  => $totalJurnal,
            'seminar' => $totalSeminar,
            'buku'    => 0,
        ];

        // ==========================================
        // 6. ANGGARAN & SISA DANA
        //     - sisaDana DUMMY: 9.750.000
        //     - anggaran = totalDanaApprove + sisaDana
        // ==========================================
        $sisaDana = 9_750_000; // <--- DUMMY YANG KAMU MAU
        $anggaran = $totalDanaApprove + $sisaDana;

        // ==========================================
        // 7. DATA UNTUK CHART (4 BULAN TERAKHIR, REAL)
        //     - Statistik jumlah pengajuan per bulan
        //     - Tidak tergantung tombol "Dana Dicairkan"
        // ==========================================
        $now        = Carbon::now();
        $endOfMonth = $now->copy()->endOfMonth();
        $chartStart = $now->copy()->subMonths(3)->startOfMonth();

        $jurnalPerBulan = PenghargaanJurnal::selectRaw("
                to_char(tgl_pengajuan_penghargaan, 'YYYY-MM') as bulan,
                count(*) as total
            ")
            ->whereBetween('tgl_pengajuan_penghargaan', [$chartStart, $endOfMonth])
            ->groupBy('bulan')
            ->pluck('total', 'bulan');

        $seminarPerBulan = PenghargaanSeminar::selectRaw("
                to_char(tgl_pengajuan_penghargaan, 'YYYY-MM') as bulan,
                count(*) as total
            ")
            ->whereBetween('tgl_pengajuan_penghargaan', [$chartStart, $endOfMonth])
            ->groupBy('bulan')
            ->pluck('total', 'bulan');

        $labels      = [];
        $dataJurnal  = [];
        $dataSeminar = [];
        $dataBuku    = [];

        $periode = $chartStart->copy();
        while ($periode <= $endOfMonth) {
            $key = $periode->format('Y-m'); // contoh: 2024-09

            $labels[]      = $periode->translatedFormat('F'); // "September", "Oktober", ...
            $dataJurnal[]  = (int) ($jurnalPerBulan[$key]  ?? 0);
            $dataSeminar[] = (int) ($seminarPerBulan[$key] ?? 0);
            $dataBuku[]    = 0;

            $periode->addMonth();
        }

        $statistik = [
            'labels'   => $labels,
            'datasets' => [
                'jurnal'  => $dataJurnal,
                'seminar' => $dataSeminar,
                'buku'    => $dataBuku,
            ],
        ];

        // ==========================================
        // 8. SUMMARY UNTUK FRONTEND
        //     (labelnya masih "... Bulan Ini", tapi
        //      datanya total keseluruhan agar berfungsi)
        // ==========================================
        $summary = (object) [
            'approvalRateBulanIni'   => $approvalRate,
            'totalPengajuanBulanIni' => $totalPengajuan,
            'rekapJenisBulanIni'     => $rekapJenis,
            'totalBulanIni'          => $totalCair,         // total yang sudah dicairkan
            'totalDanaApprove'       => $totalDanaApprove,  // real dari DB
            'sisaDana'               => $sisaDana,          // DUMMY
            'anggaran'               => $anggaran,          // totalDanaApprove + sisaDana
        ];

        return Inertia::render('app/dashboard-hrd/DashboardHrd', [
            'statistik' => fn () => $statistik,
            'summary'   => fn () => $summary,
            'pageName'  => Inertia::always('Dashboard HRD'),
            'auth'      => Inertia::always($auth),
            'isEditor'  => Inertia::always($isEditor),
        ]);
    }

    private function checkIsEditor($auth)
    {
        return ToolsHelper::checkRoles('HRD', $auth->akses ?? []);
    }
}
