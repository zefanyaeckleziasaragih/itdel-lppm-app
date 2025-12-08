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
     * Halaman daftar pengajuan penghargaan (untuk LPPM)
     */
    public function daftarPengajuan(Request $request)
    {
        $auth = $request->attributes->get('auth');

        // Dummy data untuk testing - nanti ganti dengan query real
        $pengajuan = [
            [
                'id' => 1,
                'judul' => 'Penelitian Machine Learning',
                'jenis' => 'Jurnal',
                'penulis' => 'Dr. John Doe',
                'status' => 'Menunggu Verifikasi',
                'tanggal' => '2025-01-15',
                'kampus' => 'IT Del',
                'fakultas' => 'Informatika',
                'prodi' => 'Teknik Informatika',
            ],
            [
                'id' => 2,
                'judul' => 'Seminar Nasional AI',
                'jenis' => 'Seminar',
                'penulis' => 'Dr. Jane Smith',
                'status' => 'Disetujui',
                'tanggal' => '2025-01-10',
                'kampus' => 'IT Del',
                'fakultas' => 'Informatika',
                'prodi' => 'Sistem Informasi',
            ],
        ];

        return Inertia::render('app/penghargaan/daftar-pengajuan-page', [
            'auth' => Inertia::always($auth),
            'pageName' => Inertia::always('Daftar Pengajuan Penghargaan'),
            'pengajuan' => $pengajuan,
        ]);
    }

    /**
     * Detail pengajuan
     */
    public function detailPengajuan(Request $request, $id)
    {
        $auth = $request->attributes->get('auth');

        // Dummy data - nanti ganti dengan query real
        $pengajuan = [
            'id' => $id,
            'nama_dosen' => 'Dr. John Doe',
            'nip' => '123456789',
            'nik' => '987654321',
            'jenis_penghargaan' => 'Publikasi Jurnal',
            'nama_kegiatan' => 'Penelitian Machine Learning',
            'indeks' => 'Sinta 2',
            'dana_maksimum' => 5000000,
            'status' => 'Menunggu',
            'bukti_url' => '#',
            'dana_disetujui' => 0,
        ];

        return Inertia::render('app/penghargaan/detail-pengajuan-jurnal-page', [
            'auth' => Inertia::always($auth),
            'pageName' => Inertia::always('Detail Pengajuan'),
            'pengajuan' => $pengajuan,
        ]);
    }

    /**
     * Konfirmasi pengajuan
     */
    public function konfirmasi(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|string',
            'dana_disetujui' => 'nullable|integer',
        ]);

        // TODO: Update status di database
        
        return redirect()->route('penghargaan.daftar')
            ->with('success', 'Status pengajuan berhasil diperbarui');
    }

    /**
     * Halaman daftar seminar yang sudah diajukan (untuk DOSEN)
     */
    public function daftarSeminar(Request $request)
    {
        $auth = $request->attributes->get('auth');
        
        // Cari dosen berdasarkan user_id
        $dosen = DosenModel::where('user_id', $auth->id)->first();
        
        if (!$dosen) {
            // Jika bukan dosen, tampilkan halaman kosong atau redirect
            $seminarList = [];
        } else {
            // Ambil seminar milik dosen ini dengan penghargaan
            $seminarList = DB::table('p_seminar_user as psu')
                ->join('m_seminar as s', 'psu.seminar_id', '=', 's.id')
                ->leftJoin('t_penghargaan_seminar as ps', 's.id', '=', 'ps.seminar_id')
                ->where('psu.user_id', $auth->id)
                ->select(
                    's.id',
                    's.nama_forum as judul',
                    's.website as url',
                    's.created_at as tanggal_pengajuan',
                    DB::raw("CASE 
                        WHEN ps.tgl_cair IS NOT NULL THEN 'Sudah Dicairkan'
                        WHEN ps.tgl_approve_hrd IS NOT NULL THEN 'Disetujui HRD'
                        WHEN ps.tgl_verifikasi_lppm IS NOT NULL THEN 'Diverifikasi LPPM'
                        WHEN ps.id IS NOT NULL THEN 'Menunggu Verifikasi'
                        ELSE 'Belum Diajukan'
                    END as status")
                )
                ->orderBy('s.created_at', 'desc')
                ->get()
                ->map(function ($seminar) use ($auth) {
                    return [
                        'id' => $seminar->id,
                        'judul' => $seminar->judul,
                        'penulis' => $auth->name,
                        'status' => $seminar->status,
                        'tanggal_pengajuan' => $seminar->tanggal_pengajuan,
                    ];
                })
                ->toArray();
        }

        return Inertia::render('app/penghargaan/daftar-seminar-page', [
            'auth' => Inertia::always($auth),
            'pageName' => Inertia::always('Daftar Seminar'),
            'seminarList' => $seminarList,
        ]);
    }

    /**
     * Halaman pilih prosiding (Step 1)
     */
        public function pilihProsiding(Request $request)
        {
            $auth = $request->attributes->get('auth');

            // Cari dosen
            $dosen = DosenModel::where('user_id', $auth->id)->first();
            
            if (!$dosen) {
                return redirect()->route('penghargaan.seminar.daftar')
                    ->with('error', 'Data dosen tidak ditemukan. Silakan hubungi admin.');
            }

            // Ambil list seminar yang belum diajukan penghargaan
            $prosidingList = DB::table('p_seminar_user as psu')
                ->join('m_seminar as s', 'psu.seminar_id', '=', 's.id')
                ->leftJoin('t_penghargaan_seminar as ps', 's.id', '=', 'ps.seminar_id')
                ->where('psu.user_id', $auth->id)
                ->whereNull('ps.id') // Hanya yang belum ada di tabel penghargaan
                ->select('s.id', 's.nama_forum as judul', 's.website')
                ->get()
                ->map(function($p) use ($dosen) {
                    return [
                        'id' => $p->id,
                        'judul' => $p->judul,
                        'sinta_id' => $dosen->sinta_id ?? '',
                        'scopus_id' => $dosen->scopus_id ?? '',
                        'website' => $p->website ?? '',
                    ];
                })
                ->toArray();

            return Inertia::render('app/penghargaan/pilih-prosiding-page', [
                'auth' => Inertia::always($auth),
                'pageName' => Inertia::always('Pilih Prosiding'),
                'prosidingList' => $prosidingList,
            ]);
        }

    /**
     * Form pengajuan seminar (Step 2)
     */
    public function formSeminar(Request $request)
    {
        $auth = $request->attributes->get('auth');
        $prosidingId = $request->query('prosiding_id');

        if (!$prosidingId) {
            return redirect()->route('penghargaan.seminar.pilih')
                ->with('error', 'Pilih prosiding terlebih dahulu');
        }

        // Cari dosen
        $dosen = DosenModel::where('user_id', $auth->id)->first();
        
        if (!$dosen) {
            return redirect()->route('home')
                ->with('error', 'Data dosen tidak ditemukan');
        }

        // Ambil data prosiding
        $selectedProsiding = SeminarModel::find($prosidingId);

        if (!$selectedProsiding) {
            return redirect()->route('penghargaan.seminar.pilih')
                ->with('error', 'Prosiding tidak ditemukan');
        }

        return Inertia::render('app/penghargaan/pengajuan-seminar-page', [
            'auth' => Inertia::always($auth),
            'pageName' => Inertia::always('Pengajuan Penghargaan Seminar'),
            'selectedProsiding' => [
                'id' => $selectedProsiding->id,
                'judul' => $selectedProsiding->nama_forum,
                'sinta_id' => $dosen->sinta_id ?? '',
                'scopus_id' => $dosen->scopus_id ?? '',
                'nama_forum' => $selectedProsiding->nama_forum,
                'penulis' => $auth->name,
                'institusi_penyelenggara' => 'IT Del',
                'waktu_pelaksanaan' => now()->format('Y-m-d'),
                'tempat_pelaksanaan' => 'Sitoluama',
                'url' => $selectedProsiding->website ?? '',
            ],
        ]);
    }

    /**
     * Simpan pengajuan seminar
     */
    public function storeSeminar(Request $request)
    {
        $auth = $request->attributes->get('auth');
        
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
                'nominal_usulan' => 0,
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
}