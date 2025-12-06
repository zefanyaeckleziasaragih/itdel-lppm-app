<?php

namespace App\Http\Controllers\App\Penghargaan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardHrdController extends Controller
{
    public function index(Request $request): Response
    {
        $auth = $request->attributes->get('auth');

        // Dummy data chart
        $statistik = [
            ['bulan' => 'Jan', 'jurnal' => 4, 'seminar' => 2, 'buku' => 1],
            ['bulan' => 'Feb', 'jurnal' => 3, 'seminar' => 3, 'buku' => 2],
            ['bulan' => 'Mar', 'jurnal' => 5, 'seminar' => 1, 'buku' => 1],
            ['bulan' => 'Apr', 'jurnal' => 2, 'seminar' => 4, 'buku' => 3],
            ['bulan' => 'Mei', 'jurnal' => 6, 'seminar' => 3, 'buku' => 2],
            ['bulan' => 'Jun', 'jurnal' => 4, 'seminar' => 5, 'buku' => 3],
        ];

        // Dummy summary
        $summary = [
            'approvalRateBulanIni'   => 82,
            'totalPengajuanBulanIni' => 18,
            'rekapJenisBulanIni'     => [
                'jurnal'  => 7,
                'seminar' => 6,
                'buku'    => 5,
            ],
            'totalBulanIni'          => 18,
            'totalDanaApprove'       => 25000000,
            'sisaDana'               => 75000000,
            'anggaran'               => 100000000,
        ];

        return Inertia::render('app/dashboard-hrd/dashboard-hrd', [ // ⬅️ POLANYA SAMA
            'auth'      => Inertia::always($auth),
            'pageName'  => Inertia::always('Dashboard HRD'),
            'statistik' => $statistik,
            'summary'   => $summary,
        ]);
    }
}
    