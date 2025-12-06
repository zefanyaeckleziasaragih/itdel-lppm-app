<?php

namespace App\Http\Controllers\App\Penghargaan;

use App\Helper\ToolsHelper;
use App\Http\Controllers\Controller;
use App\Models\DosenModel;
use App\Models\SeminarModel;
use App\Models\PenghargaanSeminarModel;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class PengajuanController extends Controller
{
    /**
     * Halaman daftar seminar yang sudah diajukan untuk penghargaan
     */
    public function daftarSeminar(Request $request)
    {
        $auth = $request->attributes->get('auth');
        
        // Cari dosen berdasarkan user_id
        $dosen = DosenModel::where('user_id', $auth->id)->first();
        
        if (!$dosen) {
            return redirect()->route('home')
                ->with('error', 'Data dosen tidak ditemukan. Silakan hubungi admin.');
        }

        // Ambil seminar milik dosen ini yang sudah diajukan penghargaan
        $seminarList = DB::table('p_seminar_user as psu')
            ->join('m_seminar as s', 'psu.seminar_id', '=', 's.id')
            ->leftJoin('t_penghargaan_seminar as ps', 's.id', '=', 'ps.seminar_id')
            ->where('psu.user_id', $auth->id)
            ->select(
                's.id',
                's.nama_forum as judul',
                's.website as url',
                's.created_at as tanggal_pengajuan',
                DB::raw("COALESCE(ps.status, 'Belum Diajukan') as status"),
                DB::raw("CASE 
                    WHEN ps.tgl_cair IS NOT NULL THEN 'Sudah Dicairkan'
                    WHEN ps.tgl_approve_hrd IS NOT NULL THEN 'Disetujui HRD'
                    WHEN ps.tgl_verifikasi_lppm IS NOT NULL THEN 'Diverifikasi LPPM'
                    WHEN ps.id IS NOT NULL THEN 'Menunggu Verifikasi'
                    ELSE 'Belum Diajukan'
                END as status_display")
            )
            ->orderBy('s.created_at', 'desc')
            ->get()
            ->map(function ($seminar) use ($auth) {
                return [
                    'id' => $seminar->id,
                    'judul' => $seminar->judul,
                    'penulis' => $auth->name, // Nama dosen sebagai penulis
                    'status' => $seminar->status_display,
                    'tanggal_pengajuan' => $seminar->tanggal_pengajuan,
                ];
            })
            ->toArray();

        return Inertia::render('app/penghargaan/daftar-seminar-page', [
            'auth' => Inertia::always($auth),
            'pageName' => Inertia::always('Daftar Seminar'),
            'seminarList' => $seminarList,
        ]);
    }

    /**
     * Halaman pilih seminar dari database (bukan prosiding)
     */
    public function pilihProsiding(Request $request)
    {
        $auth = $request->attributes->get('auth');

        // Cari dosen
        $dosen = DosenModel::where('user_id', $auth->id)->first();
        
        if (!$dosen) {
            return redirect()->route('home')
                ->with('error', 'Data dosen tidak ditemukan');
        }

        // Ambil seminar milik dosen yang BELUM diajukan penghargaan
        $prosidingList = DB::table('p_seminar_user as psu')
            ->join('m_seminar as s', 'psu.seminar_id', '=', 's.id')
            ->leftJoin('t_penghargaan_seminar as ps', 's.id', '=', 'ps.seminar_id')
            ->where('psu.user_id', $auth->id)
            ->whereNull('ps.id') // Hanya yang belum ada di tabel penghargaan
            ->select(
                's.id',
                's.nama_forum as judul',
                's.website',
                's.biaya'
            )
            ->get()
            ->map(function($seminar) use ($dosen) {
                return [
                    'id' => $seminar->id,
                    'judul' => $seminar->judul,
                    'sinta_id' => $dosen->sinta_id ?? '',
                    'scopus_id' => $dosen->scopus_id ?? '',
                    'website' => $seminar->website ?? '',
                ];
            })
            ->toArray();

        return Inertia::render('app/penghargaan/pilih-prosiding-page', [
            'auth' => Inertia::always($auth),
            'pageName' => Inertia::always('Pilih Seminar'),
            'prosidingList' => $prosidingList,
        ]);
    }

    /**
     * Form pengajuan penghargaan seminar (auto-fill dari database)
     */
    public function formSeminar(Request $request)
    {
        $auth = $request->attributes->get('auth');
        $prosidingId = $request->query('prosiding_id');

        if (!$prosidingId) {
            return redirect()->route('penghargaan.seminar.pilih')
                ->with('error', 'Pilih seminar terlebih dahulu');
        }

        // Cari dosen
        $dosen = DosenModel::where('user_id', $auth->id)->first();
        
        if (!$dosen) {
            return redirect()->route('home')
                ->with('error', 'Data dosen tidak ditemukan');
        }

        // Ambil data seminar dari database
        $seminar = SeminarModel::find($prosidingId);

        if (!$seminar) {
            return redirect()->route('penghargaan.seminar.pilih')
                ->with('error', 'Seminar tidak ditemukan');
        }

        // Auto-fill data dari database
        $selectedProsiding = [
            'id' => $seminar->id,
            'judul' => $seminar->nama_forum,
            'sinta_id' => $dosen->sinta_id ?? '',
            'scopus_id' => $dosen->scopus_id ?? '',
            'nama_forum' => $seminar->nama_forum,
            'penulis' => $auth->name,
            'institusi_penyelenggara' => 'IT Del', // Bisa disesuaikan
            'waktu_pelaksanaan' => now()->format('Y-m-d'),
            'tempat_pelaksanaan' => 'Sitoluama', // Bisa disesuaikan
            'url' => $seminar->website ?? '',
        ];

        return Inertia::render('app/penghargaan/pengajuan-seminar-page', [
            'auth' => Inertia::always($auth),
            'pageName' => Inertia::always('Pengajuan Penghargaan Seminar'),
            'selectedProsiding' => $selectedProsiding,
        ]);
    }

    /**
     * Simpan pengajuan penghargaan seminar ke database
     */
    public function storeSeminar(Request $request)
    {
        $auth = $request->attributes->get('auth');
        
        // Cari dosen
        $dosen = DosenModel::where('user_id', $auth->id)->first();
        
        if (!$dosen) {
            return redirect()->route('home')
                ->with('error', 'Data dosen tidak ditemukan');
        }

        $request->validate([
            'prosiding_id' => 'required|exists:m_seminar,id',
        ]);

        try {
            DB::beginTransaction();

            // Cek apakah sudah pernah diajukan
            $existing = PenghargaanSeminarModel::where('seminar_id', $request->prosiding_id)->first();
            
            if ($existing) {
                DB::rollBack();
                return redirect()->route('penghargaan.seminar.daftar')
                    ->with('error', 'Seminar ini sudah pernah diajukan untuk penghargaan!');
            }

            // Insert ke tabel t_penghargaan_seminar
            PenghargaanSeminarModel::create([
                'id' => ToolsHelper::generateId(),
                'seminar_id' => $request->prosiding_id,
                'tanggal_diajukan' => now(),
                'status_pengajuan' => 'Diajukan',
                'nominal_usulan' => 0, // Bisa disesuaikan
                'nominal_disetujui' => null,
                'status' => 'belum_diverifikasi',
                'tgl_pengajuan_penghargaan' => now(),
                'tgl_verifikasi_lppm' => null,
                'tgl_approve_hrd' => null,
                'tgl_cair' => null,
            ]);

            DB::commit();

            return redirect()->route('penghargaan.seminar.daftar')
                ->with('success', 'Pengajuan penghargaan seminar berhasil diajukan!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('penghargaan.seminar.pilih')
                ->with('error', 'Gagal mengajukan penghargaan seminar: ' . $e->getMessage());
        }
    }

    // ... (method lain untuk penghargaan, statistik, dll)
}