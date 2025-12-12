<?php

namespace App\Http\Controllers\App\Penghargaan;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Request;
use App\Models\PenghargaanLppmKelompok5\PenghargaanJurnal;
use App\Models\PenghargaanLppmKelompok5\PenghargaanSeminar;

class StatistikController extends Controller
{
    public function index(Request $request)
    {
        $auth = $request->attributes->get('auth');

        // =====================================================
        // 0. DATA REFERENSI (kampus, fakultas, prodi) – tetap dummy
        // =====================================================
        $kampus = [
            ['id' => 1, 'nama' => 'IT Del'],
        ];

        $fakultas = [
            ['id' => 1, 'kampus_id' => 1, 'nama' => 'Fakultas Informatika dan Teknik Elektro'],
            ['id' => 2, 'kampus_id' => 1, 'nama' => 'Fakultas Teknik Industri'],
            ['id' => 3, 'kampus_id' => 1, 'nama' => 'Fakultas Vokasi'],
            ['id' => 4, 'kampus_id' => 1, 'nama' => 'Fakultas Teknik Bioproses'],
        ];

        $prodi = [
            ['id' => 1, 'fakultas_id' => 1, 'nama' => 'Informatika'],
            ['id' => 2, 'fakultas_id' => 1, 'nama' => 'Sistem Informasi'],
            ['id' => 3, 'fakultas_id' => 2, 'nama' => 'Manajemen Rekayasa'],
            ['id' => 4, 'fakultas_id' => 2, 'nama' => 'Teknik Industri'],
            ['id' => 5, 'fakultas_id' => 3, 'nama' => 'Teknologi Informasi'],
            ['id' => 6, 'fakultas_id' => 3, 'nama' => 'Teknologi Rekayasa Perangkat Lunak'],
            ['id' => 7, 'fakultas_id' => 4, 'nama' => 'Bioproses'],
        ];

        // =====================================================
        // 1. STATISTIK GRAFIK – jumlah yang DISETUJUI per bulan
        // =====================================================

        // JURNAL: hitung per bulan, hanya yang disetujui
        $jurnalRows = PenghargaanJurnal::selectRaw("
                EXTRACT(MONTH FROM tgl_pengajuan_penghargaan) AS bulan_num,
                TO_CHAR(tgl_pengajuan_penghargaan, 'Mon') AS bulan_label,
                COUNT(*) AS total
            ")
            ->where('status_pengajuan', 'disetujui')
            ->groupBy('bulan_num', 'bulan_label')
            ->get();

        // SEMINAR: hitung per bulan, hanya yang disetujui
        $seminarRows = PenghargaanSeminar::selectRaw("
                EXTRACT(MONTH FROM tgl_pengajuan_penghargaan) AS bulan_num,
                TO_CHAR(tgl_pengajuan_penghargaan, 'Mon') AS bulan_label,
                COUNT(*) AS total
            ")
            ->where('status_pengajuan', 'disetujui')
            ->groupBy('bulan_num', 'bulan_label')
            ->get();

        // Gabungkan ke dalam 1 map [bulan_num => data]
        $bulanData = [];

        foreach ($jurnalRows as $row) {
            $key = (int) $row->bulan_num;
            if (!isset($bulanData[$key])) {
                $bulanData[$key] = [
                    'bulan'   => $row->bulan_label,
                    'jurnal'  => 0,
                    'seminar' => 0,
                    'buku'    => 0,
                    'fakultas'=> 'Semua Fakultas',
                    'prodi'   => 'Semua Prodi',
                ];
            }
            $bulanData[$key]['jurnal'] = (int) $row->total;
        }

        foreach ($seminarRows as $row) {
            $key = (int) $row->bulan_num;
            if (!isset($bulanData[$key])) {
                $bulanData[$key] = [
                    'bulan'   => $row->bulan_label,
                    'jurnal'  => 0,
                    'seminar' => 0,
                    'buku'    => 0,
                    'fakultas'=> 'Semua Fakultas',
                    'prodi'   => 'Semua Prodi',
                ];
            }
            $bulanData[$key]['seminar'] = (int) $row->total;
        }

        // Urutkan berdasarkan nomor bulan
        ksort($bulanData);

        // Ubah ke array biasa untuk dikirim ke React
        $statistik = array_values($bulanData);

        // =====================================================
        // 2. SUMMARY – dipakai kartu di sisi kanan
        //    (tidak dibatasi "bulan sekarang", tapi semua data)
        // =====================================================

        // TOTAL SEMUA PENGAJUAN (apapun statusnya)
        $totalPengajuan = PenghargaanJurnal::count()
                         + PenghargaanSeminar::count();

        // TOTAL YANG DISETUJUI
        $totalJurnalDisetujui = PenghargaanJurnal::where('status_pengajuan', 'disetujui')->count();
        $totalSeminarDisetujui = PenghargaanSeminar::where('status_pengajuan', 'disetujui')->count();
        $totalApprove = $totalJurnalDisetujui + $totalSeminarDisetujui;

        // APPROVAL RATE
        $approvalRate = $totalPengajuan > 0
            ? round(($totalApprove / $totalPengajuan) * 100, 1)
            : 0;

        // TOTAL DANA APPROVE
        $totalDanaApprove =
            (int) PenghargaanJurnal::where('status_pengajuan', 'disetujui')->sum('nominal_disetujui')
            +
            (int) PenghargaanSeminar::where('status_pengajuan', 'disetujui')->sum('nominal_disetujui');

        // ANGGARAN & SISA
        $anggaran = 10_000_000; // Rp 10.000.000, sementara hardcode
        $sisaDana = $anggaran - $totalDanaApprove;

        // REKAP JENIS (jumlah yang DISETUJUI per jenis)
        $rekapJenisBulanIni = [
            'jurnal'  => $totalJurnalDisetujui,
            'seminar' => $totalSeminarDisetujui,
            'buku'    => 0,
        ];

        $summary = [
            // dipakai di kartu "Total Penghargaan Bulan Ini"
            'totalBulanIni'          => $totalApprove,

            // total semua pengajuan (untuk teks atas)
            'totalPengajuanBulanIni' => $totalPengajuan,

            'approvalRateBulanIni'   => $approvalRate,
            'totalDanaApprove'       => $totalDanaApprove,
            'sisaDana'               => $sisaDana,
            'anggaran'               => $anggaran,
            'rekapJenisBulanIni'     => $rekapJenisBulanIni,
        ];

        // =====================================================
        // 3. KIRIM KE INERTIA
        // =====================================================
        return Inertia::render('app/penghargaan/statistik-page', [
            'auth'      => Inertia::always($auth),
            'pageName'  => Inertia::always('Statistik Penghargaan'),
            'kampus'    => $kampus,
            'fakultas'  => $fakultas,
            'prodi'     => $prodi,
            'statistik' => $statistik,
            'summary'   => $summary,
        ]);
    }
}
